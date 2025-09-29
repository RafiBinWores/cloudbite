<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use App\Models\Dish;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        $categories = Category::where('status', 'active')
        ->orderByDesc('created_at')
        ->get();

        $now = now();
        $dishes = Dish::where('visibility', 'Yes')
            ->where('available_from', '<=', $now)
            ->where('available_till', '>=', $now)
            ->orderByDesc('created_at')
            ->get();
        
        return view('livewire.frontend.home', compact('categories', 'dishes'))
            ->layout('components.layouts.front-home', ['title' => 'Home | Cloudbite']);
    }
}
