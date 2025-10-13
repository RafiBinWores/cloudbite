<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class OrderDetails extends Component
{
    public string $code;
    public Order $order;

    public function mount(string $code)
    {
        $this->code = $code;

        $order = Order::with(['items.dish', 'items.crust', 'items.bun'])
            ->where('order_code', $code)
            ->firstOrFail();

        // Authorize ownership
        abort_if($order->user_id !== Auth::id(), 403);

        $this->order = $order;
    }

    public function render()
    {
        return view('livewire.frontend.account.order-details', [
            'order' => $this->order,
        ])->title('Order ' . $this->order->order_code . ' - CloudBite');
    }
}
