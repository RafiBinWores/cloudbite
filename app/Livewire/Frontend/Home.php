<?php

namespace App\Livewire\Frontend;

use App\Models\Banner;
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
            ->take(16)
            ->get();

        $heroDishes = Dish::visible()
            ->where('show_in_hero', true)
            ->orderByDesc('id')
            ->take(5)
            ->get();

        $menuDishes = Dish::where('visibility', 'Yes')
            ->where('available_from', '<=', $now)
            ->where('available_till', '>=', $now)
            ->where('show_in_menu', true)
            ->with('category:id,name')
            ->orderBy('menu_sort')
            ->get();

        $sliderBanners = Banner::where('status', 'active')
            ->where('is_slider', true)
            ->with(['category:id,slug',])
            ->orderBy('created_at', 'desc')
            ->get();

        $singleBanner = Banner::where('status', 'active')
            ->where('is_slider', false)
            ->with(['category:id,slug',])
            ->orderBy('created_at', 'desc')
            ->first();


        return view('livewire.frontend.home', compact('dishes', 'heroDishes', 'menuDishes', 'sliderBanners', 'singleBanner'));
    }
}
