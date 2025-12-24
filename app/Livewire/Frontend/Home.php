<?php

namespace App\Livewire\Frontend;

use App\Models\Dish;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.front-home')]
class Home extends Component
{
    public function render()
    {
        $now = now();

        // ✅ keep your old query (unchanged)
        $dishes = Dish::where('visibility', 'Yes')
            ->where('available_from', '<=', $now)
            ->where('available_till', '>=', $now)
            ->orderByDesc('created_at')
            ->get();

        // ✅ keep your old query (unchanged)
        $heroDishes = Dish::visible()
            ->where('show_in_hero', true)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        // ✅ NEW: only menu dishes, sorted, eager-load category
        $menuDishes = Dish::where('visibility', 'Yes')
            ->where('available_from', '<=', $now)
            ->where('available_till', '>=', $now)
            ->where('show_in_menu', true)
            ->with('category:id,name')
            ->orderBy('menu_sort')
            ->get();

        return view('livewire.frontend.home', compact('dishes', 'heroDishes', 'menuDishes'))
            ->title('Home - CloudBite');
    }
}
