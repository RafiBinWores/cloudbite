<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.frontend')]
class OrdersPage extends Component
{
    use WithPagination;

    public string $tab = 'ongoing'; // 'ongoing' | 'delivered'
    public int $perPage = 10;

    // Exact filters you requested
    protected array $ongoingStatuses   = ['pending', 'processing', 'confirmed', 'out_for_delivery'];
    protected array $deliveredStatuses = ['delivered'];

    public function setTab(string $tab): void
    {
        $this->tab = in_array($tab, ['ongoing', 'delivered']) ? $tab : 'ongoing';
        $this->resetPage();
    }

    public function render()
    {
        $base = Order::query()
            ->where('user_id', Auth::id())
            ->select(['id', 'order_code', 'grand_total', 'payment_status', 'order_status', 'created_at']);

        // counts for badges
        $ongoingCount = (clone $base)->whereIn('order_status', $this->ongoingStatuses)->count();
        $deliveredCount = (clone $base)->whereIn('order_status', $this->deliveredStatuses)->count();

        // paginated list for current tab
        $listQuery = (clone $base)->latest('id');
        if ($this->tab === 'delivered') {
            $listQuery->whereIn('order_status', $this->deliveredStatuses);
        } else {
            $listQuery->whereIn('order_status', $this->ongoingStatuses);
        }
        $orders = $listQuery->paginate($this->perPage);

        return view('livewire.frontend.account.orders-page', [
            'orders'         => $orders,
            'ongoingCount'   => $ongoingCount,
            'deliveredCount' => $deliveredCount,
        ])->title('My Orders - CloudBite');
    }
}
