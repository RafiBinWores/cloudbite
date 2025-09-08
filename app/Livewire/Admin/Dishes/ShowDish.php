<?php

namespace App\Livewire\Admin\Dishes;

use App\Models\Dish;
use Livewire\Component;

class ShowDish extends Component
{
    public $dish;

    public function mount($slug)
    {
        $this->dish = Dish::where('slug', $slug)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.admin.dishes.show-dish');
    }
}
