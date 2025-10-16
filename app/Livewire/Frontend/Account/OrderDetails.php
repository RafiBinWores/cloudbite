<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Order;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class OrderDetails extends Component
{
    use WithTcToast;

    public string $code;
    public Order $order;

    // Live props for the view
    public ?int $etaStartAtMs = null;   // absolute ms timestamp
    public ?int $etaEndAtMs   = null;   // absolute ms timestamp
    public ?float $distanceKm = null;   // for display

    public string $reason = '';

    public function mount(string $code): void
    {
        $this->code = $code;

        $order = Order::with(['items.dish', 'items.crust', 'items.bun'])
            ->where('order_code', $code)
            ->firstOrFail();

        abort_if($order->user_id !== Auth::id(), 403);

        $this->order = $order;

        $this->computeAndPersistEtaWindow(); // <- persist + hydrate ETA on first render
    }

    /** Whether user can cancel */
    public function getIsCancellableProperty(): bool
    {
        return in_array(
            strtolower($this->order->order_status),
            ['pending', 'processing', 'confirmed', 'preparing'],
            true
        );
    }

    /** Poll target: refresh order from DB and recompute ETA if needed */
    public function refreshOrder(): void
    {
        $this->order->refresh();
        $this->order->loadMissing(['items.dish', 'items.crust', 'items.bun']);

        $this->computeAndPersistEtaWindow(); // <- keep ETA consistent or recalc if status changed
    }

    /** Cancel order */
    public function cancel(): void
    {
        if (! $this->isCancellable) {
            $this->info(
                title: 'This order can no longer be cancelled.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
            return;
        }

        $this->validate([
            'reason' => ['nullable', 'string', 'max:500'],
        ]);

        $this->order->update([
            'order_status'     => 'cancelled',
            'cancelled_at'     => now(),
            'cancelled_reason' => trim($this->reason ?? ''),
        ]);

        // Reload the order & ETA
        $this->order = Order::with(['items.dish', 'items.crust', 'items.bun'])
            ->findOrFail($this->order->id);

        $this->computeAndPersistEtaWindow();

        $this->reason = '';
        $this->dispatch('close-cancel-panel');

        $this->success(
            title: 'Order cancelled successfully.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    /** Compute distance + ETA window; persist absolute start/end into order.meta so refresh doesn't reset */
    protected function computeAndPersistEtaWindow(): void
    {
        // Ensure cast: protected $casts = ['meta' => 'array'] in Order model
        $meta   = is_array($this->order->meta) ? $this->order->meta : (json_decode($this->order->meta ?? '[]', true) ?: []);
        $status = $this->order->order_status;

        // Terminal statuses => just hydrate previously saved ETA (don't compute new)
        $terminal = ['delivered', 'cancelled', 'returned', 'failed_to_deliver'];
        if (in_array($status, $terminal, true)) {
            $this->etaStartAtMs = $meta['eta']['start_at_ms'] ?? null;
            $this->etaEndAtMs   = $meta['eta']['end_at_ms'] ?? null;
            $this->distanceKm   = $meta['eta']['distance_km'] ?? null;
            return;
        }

        // If we already have an ETA window for this SAME status, reuse it (even if end time has passed)
        $existing   = $meta['eta'] ?? null;
        $sameStatus = $existing && ($existing['status'] ?? null) === $status;
        if ($sameStatus) {
            $this->etaStartAtMs = $existing['start_at_ms'] ?? null;
            $this->etaEndAtMs   = $existing['end_at_ms'] ?? null;
            $this->distanceKm   = $existing['distance_km'] ?? null;
            return;
        }

        // Otherwise compute a fresh window for the current status and persist it
        // Kitchen coords (ideally from config/DB)
        $kitchenLat = $meta['kitchen']['lat'] ?? 23.7925;
        $kitchenLng = $meta['kitchen']['lng'] ?? 90.4078;

        // Customer coords from order.meta
        $custLat = $meta['lat'] ?? null;
        $custLng = $meta['lng'] ?? null;

        // Distance (Haversine, km)
        $distanceKm = null;
        if (!is_null($custLat) && !is_null($custLng) && !is_null($kitchenLat) && !is_null($kitchenLng)) {
            $R = 6371;
            $dLat = deg2rad($custLat - $kitchenLat);
            $dLng = deg2rad($custLng - $kitchenLng);
            $a = sin($dLat / 2) ** 2 + cos(deg2rad($kitchenLat)) * cos(deg2rad($custLat)) * sin($dLng / 2) ** 2;
            $c = 2 * atan2(sqrt($a), sqrt(1 - $a));
            $distanceKm = $R * $c;
        }
        $this->distanceKm = $distanceKm;

        // Travel minutes @ rider speed
        $riderSpeed = 20; // km/h
        $travelMin  = $distanceKm ? (int) ceil(($distanceKm / max($riderSpeed, 1)) * 60) : null;

        // Prep windows by status
        $prepMinMap = [
            'pending'           => [60, 90],
            'processing'        => [50, 60],
            'confirmed'         => [35, 45],
            'preparing'         => [20, 30],
            'out_for_delivery'  => [0,  0],
        ];
        $prepRange = $prepMinMap[$status] ?? [30, 60];

        // Compute ETA range (minutes)
        if (!is_null($travelMin)) {
            [$prepLo, $prepHi] = $prepRange;
            $etaLo = max(5, $prepLo + $travelMin);
            $etaHi = max($etaLo + 5, $prepHi + $travelMin + 10);
        } else {
            if ($status === 'out_for_delivery') {
                $etaLo = 45;
                $etaHi = 50;
            } elseif (in_array($status, ['preparing', 'confirmed', 'processing'], true)) {
                $etaLo = 45;
                $etaHi = 75;
            } else {
                $etaLo = 30;
                $etaHi = 60;
            }
        }

        // Round to 5-min buckets
        $bucket = fn($m) => (int) ceil(max(1, $m) / 5) * 5;
        $etaLo  = $bucket($etaLo ?? 30);
        $etaHi  = $bucket($etaHi ?? max(($etaLo ?? 30) + 5, 45));

        // Absolute timestamps (ms)
        $startAtMs = (int) (now()->copy()->addMinutes($etaLo)->timestamp * 1000);
        $endAtMs   = (int) (now()->copy()->addMinutes($etaHi)->timestamp * 1000);

        // Persist to meta so refresh never resets countdown for this status
        $meta['eta'] = [
            'status'      => $status,
            'start_at_ms' => $startAtMs,
            'end_at_ms'   => $endAtMs,
            'distance_km' => $distanceKm,
            'computed_at' => now()->toIso8601String(),
        ];
        $this->order->forceFill(['meta' => $meta])->save();

        $this->etaStartAtMs = $startAtMs;
        $this->etaEndAtMs   = $endAtMs;
    }


    public function render()
    {
        return view('livewire.frontend.account.order-details', [
            'order'         => $this->order,
            'isCancellable' => $this->isCancellable,
            // expose ETA to blade (already set on props)
            'etaStartAtMs'  => $this->etaStartAtMs,
            'etaEndAtMs'    => $this->etaEndAtMs,
            'distanceKm'    => $this->distanceKm,
        ])->title('Order ' . $this->order->order_code . ' - CloudBite');
    }
}
