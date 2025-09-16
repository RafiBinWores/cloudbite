<?php

namespace App\Livewire\Admin\Coupons;

use App\Models\Coupon;
use Carbon\Carbon;
use Flux\Flux;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Coupons extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public $range = '';

    #[Url(history: true)]
    public $sortBy = 'created_at';

    #[Url(history: true)]
    public $sortDir = 'DESC';

    #[On('coupons:refresh')]
    #[On('coupons:deleted')]
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

    public function setSortBy($sortByField)
    {

        if ($this->sortBy === $sortByField) {
            $this->sortDir = ($this->sortDir == "ASC") ? "DESC" : "ASC";
            return;
        }
        $this->sortBy = $sortByField;
        $this->sortDir = 'DESC';
    }

    public function render()
    {
        $coupons = Coupon::search($this->search)
            ->when($this->range, function ($q) {
                [$from, $to] = $this->dateBounds($this->range);
                if ($from && $to) {
                    $q->whereBetween('created_at', [$from, $to]);
                }
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.admin.coupons.coupons', compact('coupons'));
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

    // Status toggle
    public function setStatus(int $id, bool $on): void
    {
        $coupon = Coupon::find($id);
        if (!$coupon) {
            $this->dispatch('toast', type: 'error', message: 'Coupon not found.');
            return;
        }

        $coupon->status = $on ? 'active' : 'disable';
        $coupon->save();

        $this->dispatch('toast', type: 'success', message: 'Coupon updated to ' . $coupon->status . '.');
        $this->dispatch('coupons:refresh');
    }

    #[On('delete-coupon')]
    public function deleteCoupon($id)
    {
        $coupon = Coupon::find($id);

        if ($coupon) {

            $coupon->delete();
            $this->dispatch('coupons:deleted');
            $this->dispatch('toast', type: 'success', message: 'Coupon deleted successfully.');

            Flux::modal('delete-confirmation-modal')->close();
        } else {
            $this->dispatch('toast', type: 'success', message: 'Coupon not found!');
        }
    }
}
