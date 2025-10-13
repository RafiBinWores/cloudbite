<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Order;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class Account extends Component
{
    public function render()
    {
        $user = Auth::user();

        $totalOrders = Order::where('user_id', $user->id)->count();
        $favoriteCount = $user->favorites()->count();

        return view('livewire.frontend.account.account', compact('totalOrders', 'favoriteCount'));
    }
}
