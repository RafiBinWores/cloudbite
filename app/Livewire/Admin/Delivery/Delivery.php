<?php

namespace App\Livewire\Admin\Delivery;

use App\Models\DeliveryMan;
use Carbon\Carbon;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Delivery extends Component
{
    use WithPagination, WithTcToast;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $range = '';

    #[On('deliveries:refresh')]
    #[On('deliveries:deleted')]
    public function refreshList()
    {
        $this->resetPage();
    }

    // Reset pagination when any of these change
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }
    public function updatedRange()
    {
        $this->resetPage();
    }

    /**
     * Map filter key -> [from, to] Carbon ranges (inclusive day window).
     */
    private function dateBounds(string $key): array
    {
        $today = Carbon::today();
        $now   = Carbon::now();

        return match ($key) {
            'yesterday'     => [Carbon::yesterday()->startOfDay(), Carbon::yesterday()->endOfDay()],
            'last_week'     => [Carbon::now()->subWeek()->startOfWeek(), Carbon::now()->subWeek()->endOfWeek()],
            'last_month'    => [Carbon::now()->subMonthNoOverflow()->startOfMonth(), Carbon::now()->subMonthNoOverflow()->endOfMonth()],
            'last_7_days'   => [$today->copy()->subDays(7)->startOfDay(), $now],
            'last_30_days'  => [$today->copy()->subDays(30)->startOfDay(), $now],
            default         => [null, null],
        };
    }

    public function render()
    {
        $deliveryMans = DeliveryMan::search($this->search)
            ->when($this->range, function ($q) {
                [$from, $to] = $this->dateBounds($this->range);
                if ($from && $to) {
                    $q->whereBetween('created_at', [$from, $to]);
                }
            })
            ->orderBy('created_at', 'DESC')
            ->paginate($this->perPage);

        $total    = DeliveryMan::count();
        $active   = DeliveryMan::where('status', 'active')->count();
        $inactive = DeliveryMan::where('status', 'disable')->count();

        return view('livewire.admin.delivery.delivery', compact('deliveryMans', 'total', 'active', 'inactive'));
    }
}
