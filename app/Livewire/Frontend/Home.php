<?php

namespace App\Livewire\Frontend;

use App\Models\Category;
use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        $categories = Category::where('status', 'active')
        ->orderByDesc('created_at')
        ->get();
        
        return view('livewire.frontend.home', compact('categories'))
            ->layout('components.layouts.frontend', ['title' => 'Home']);
    }
}
