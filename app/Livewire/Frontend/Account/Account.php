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
        $totalOrders = Order::where('user_id', Auth::user()->id)->count();
        return view('livewire.frontend.account.account', compact('totalOrders'));
    }
}
