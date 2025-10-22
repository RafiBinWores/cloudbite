<?php

namespace App\Livewire\Admin\Orders;

use App\Models\Order;
use App\Models\Addon; // <-- add this
use Illuminate\Support\Collection;
use Livewire\Component;

class Show extends Component
{
    public string $code;
    public Order $order;

    public Collection $addons;

    public string $order_status = '';
    public ?int $cooking_time_min = null;

    public array $statuses = [
        'pending',
        'processing',
        'confirmed',
        'preparing',
        'out_for_delivery',
        'delivered',
        'cancelled',
        'returned',
        'failed_to_deliver'
    ];

    public function mount(string $code): void
    {
        $this->code = $code;

        $this->order = Order::with(['user', 'items.dish', 'items.crust', 'items.bun'])
            ->where('order_code', $code)
            ->firstOrFail();

        // Prefill
        $this->order_status     = (string) ($this->order->order_status ?? '');
        $this->cooking_time_min = $this->order->cooking_time_min;

        // Collect all addon IDs from items and fetch once
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

        $this->order->order_status = $this->order_status;
        $this->order->save();

        $this->dispatch('toast', type: 'success', message: 'Order status updated.');
    }

    public function saveCookingTime(): void
    {
        $this->validate([
            'cooking_time_min' => ['nullable', 'integer', 'min:0', 'max:600'],
        ]);

        $this->order->cooking_time_min = $this->cooking_time_min;
        $this->order->save();

        $this->dispatch('toast', type: 'success', message: 'Cooking time updated.');
    }

    public function render()
    {
        return view('livewire.admin.orders.show', [
            'addons' => $this->addons,
        ]);
    }
}
