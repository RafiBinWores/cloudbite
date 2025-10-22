<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\Addon;
use Illuminate\Support\Collection;
use Livewire\Component;

class Show extends Component
{
    public string $code;
    public Order $order;

    public Collection $addons;

    // UI state
    public string $order_status = '';
    public ?int $cooking_time_min = null;
    public ?int $cooking_end_at_ms = null;   // for client resume
    public bool $is_paid = false;            // paid/unpaid toggle

    public array $statuses = [
        'pending',
        'processing',
        'confirmed',
        'preparing',
        'out_for_delivery',
        'delivered',
        'cancelled',
        'returned',
        'failed_to_deliver',
    ];

    public function mount(string $code): void
    {
        $this->code = $code;

        $this->order = Order::with(['user', 'items.dish', 'items.crust', 'items.bun'])
            ->where('order_code', $code)
            ->firstOrFail();

        // Prefill UI state
        $this->order_status     = (string) ($this->order->order_status ?? '');
        $this->cooking_time_min = $this->order->cooking_time_min;
        $this->is_paid = strtolower((string) ($this->order->payment_status ?? 'unpaid')) === 'paid';

        if ($this->order->cooking_end_at && $this->order->cooking_end_at->isFuture()) {
            $this->cooking_end_at_ms = $this->order->cooking_end_at->valueOf();
        } else {
            $this->cooking_end_at_ms = null; // expired or not set
        }

        // Collect all add-on IDs from items and fetch once
        $addonIds = $this->order->items
            ->pluck('addon_ids')
            ->filter()
            ->flatten()
            ->unique()
            ->values();

        $this->addons = Addon::whereIn('id', $addonIds)->get()->keyBy('id');
    }

public function saveStatus(): void
{
    $this->validate([
        'order_status' => ['required', 'in:' . implode(',', $this->statuses)],
    ]);

    $prev = (string) ($this->order->order_status ?? '');
    $this->order->order_status = $this->order_status;

    // If leaving preparing, clear persisted end time
    if ($prev === 'preparing' && $this->order_status !== 'preparing') {
        $this->order->cooking_time_min = null;
        $this->order->cooking_end_at   = null;
        $this->cooking_time_min        = null;
        $this->cooking_end_at_ms       = null;
    }

    $this->order->save();
    $this->dispatch('toast', type: 'success', message: 'Order status updated.');
}


    /** Save payment status (from toggle) */
    public function savePaymentStatus(): void
    {
        $this->order->payment_status = $this->is_paid ? 'paid' : 'unpaid';
        $this->order->save();

        $this->dispatch('toast', type: 'success', message: 'Payment status updated.');
    }

    public function saveCookingTime(): void
    {
        $this->validate([
            'cooking_time_min' => ['required', 'integer', 'min:0', 'max:600'],
        ]);

        if ($this->order_status !== 'preparing') {
            $this->addError('cooking_time_min', 'Cooking time can only be set when status is "preparing".');
            return;
        }

        $minutes = (int) $this->cooking_time_min;

        // Persist both minutes and end time (server clock)
        $endAt = now()->addMinutes($minutes);

        $this->order->cooking_time_min = $minutes;
        $this->order->cooking_end_at   = $endAt;
        $this->order->save();

        // Expose to Alpine so it can start/resume after any re-render or reload
        $this->cooking_end_at_ms = $endAt->valueOf();

        // Kick the browser timer (also helps after partial re-renders)
        $this->dispatch('cooking-time:started', end_at_ms: $this->cooking_end_at_ms);
        $this->dispatch('toast', type: 'success', message: 'Cooking time started.');
    }


    public function render()
    {
        return view('livewire.admin.orders.show', [
            'addons' => $this->addons,
        ]);
    }
}
