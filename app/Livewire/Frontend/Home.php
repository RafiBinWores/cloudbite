<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\Dish;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.front-home')]
class Home extends Component
{
    public function render()
    {
        $now = now();
        $dishes = Dish::where('visibility', 'Yes')
            ->where('available_from', '<=', $now)
            ->where('available_till', '>=', $now)
            ->orderByDesc('created_at')
            ->get();

        $heroDishes = Dish::visible()
            ->where('show_in_hero', true)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        return view('livewire.frontend.home', compact('dishes', 'heroDishes'))
            ->title('Home - CloudBite');
    }
}
