<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Illuminate\Http\Request;

class OrderThankYouController extends Controller
{
    public function __invoke(string $code)
    {
        $order = Order::where('order_code', $code)->firstOrFail();
        return view('livewire.frontend.orders.thank-you', compact('order'));
    }
}
