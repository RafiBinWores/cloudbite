<?php

namespace App\Livewire\Admin\Dishes;

use App\Models\Dish;
use Carbon\Carbon;
use Flux\Flux;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class Dishes extends Component
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

    #[On('dishes:refresh')]
    #[On('dishes:deleted')]
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
        $dishes = Dish::search($this->search)
            ->when($this->range, function ($q) {
                [$from, $to] = $this->dateBounds($this->range);
                if ($from && $to) {
                    $q->whereBetween('created_at', [$from, $to]);
                }
            })
            ->orderBy($this->sortBy, $this->sortDir)
            ->paginate($this->perPage);

        return view('livewire.admin.dishes.dishes', compact('dishes'));
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

    public function setVisibility(int $id, bool $on): void
    {
        $dish = Dish::find($id);
        if (!$dish) {
            $this->dispatch('toast', type: 'error', message: 'Dish not found.');
            return;
        }

        $dish->visibility = $on ? 'Yes' : 'No';
        $dish->save();

        // Optional: toast/refresh if your table needs it
        // $this->dispatch('dishs:refresh');
        $this->dispatch('toast', type: 'success', message: 'Visibility updated to ' . $dish->visibility . '.');
    }

    #[On('delete-dish')]
    public function deleteDish(int $id): void
    {
        $dish = Dish::find($id);

        if (!$dish) {
            $this->dispatch('toast', type: 'error', message: 'Dish not found!');
            // optional: Flux::modal('delete-confirmation-modal')->close();
            return;
        }

        // Delete stored files (saved on 'public' disk)
        if (!empty($dish->thumbnail)) {
            Storage::disk('public')->delete($dish->thumbnail);
        }

        if (is_array($dish->gallery) && !empty($dish->gallery)) {
            // delete() accepts an array of paths
            Storage::disk('public')->delete($dish->gallery);
        }

        // Clean up pivot tables (optional but recommended)
        $dish->buns()->detach();
        $dish->crusts()->detach();
        $dish->addOns()->detach();
        $dish->relatedDishes()->detach();

        // Finally delete the dish
        $dish->delete();

        // UI events
        $this->dispatch('dishes:deleted');
        $this->dispatch('toast', type: 'success', message: 'Dish deleted successfully.');
        Flux::modal('delete-confirmation-modal')->close();
    }
}
