<?php

namespace App\Livewire\Frontend\Account;

use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.frontend')]
class Favorites extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 12;

    #[On('favorite-toggled')]
    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function remove(int $dishId): void
    {
        $user = Auth::user();
        if (!$user) return;

        $user->favorites()->detach($dishId);
        $this->info(
            title: 'Removed from Favorites',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    public function getFavoritesProperty()
    {
        $user = Auth::user();

        return $user->favorites()
            ->when($this->search !== '', function ($q) {
                $q->where(function ($qq) {
                    $qq->where('title', 'like', '%' . $this->search . '%')
                        ->orWhere('slug', 'like', '%' . $this->search . '%');
                });
            })
            ->with([]) // add relations if you need (e.g., images)
            ->orderByDesc('favorites.created_at')
            ->paginate($this->perPage);
    }

    public function render()
    {
        return view('livewire.frontend.account.favorites', [
            'favorites' => $this->favorites,
        ])->title('Favorites - CloudBite');
    }
}
