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

    // optional cancel reason input
    public string $reason = '';

    public function mount(string $code): void
    {
        $this->code = $code;

        $order = Order::with(['items.dish', 'items.crust', 'items.bun'])
            ->where('order_code', $code)
            ->firstOrFail();

        abort_if($order->user_id !== Auth::id(), 403);

        $this->order = $order;
    }

    /** Computed: whether user can cancel */
    public function getIsCancellableProperty(): bool
    {
        return in_array(
            strtolower($this->order->order_status),
            ['pending', 'processing', 'confirmed', 'packed'],
            true
        );
    }

    /** Livewire action to cancel the order */
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

        // Update status
        $this->order->update([
            'order_status'     => 'cancelled',
            'cancelled_at'     => now(),
            'cancelled_reason' => trim($this->reason ?? ''),
        ]);

        // IMPORTANT: reassign a fresh instance so Livewire detects change and re-renders
        $this->order = Order::with(['items.dish', 'items.crust', 'items.bun'])
            ->findOrFail($this->order->id);

        // clear form + close panel (Alpine)
        $this->reason = '';
        $this->dispatch('close-cancel-panel');

        $this->success(
            title: 'Order cancelled successfully.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    public function render()
    {
        return view('livewire.frontend.account.order-details', [
            'order'          => $this->order,
            'isCancellable'  => $this->isCancellable,
        ])->title('Order ' . $this->order->order_code . ' - CloudBite');
    }
}
