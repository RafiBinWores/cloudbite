<?php

namespace App\Http\Controllers;

use App\Models\AddOn;
use App\Models\CompanyInfo;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderPrintController extends Controller
{
    public function show(string $code)
    {
        $order = Order::with(['items.dish','items.crust','items.bun'])->where('order_code',$code)->firstOrFail();
        // load company / business settings dynamic logo, name, address
        $businessSetting = CompanyInfo::first(); // adjust if you use a different accessor
        // prefetch addons map (id => model), if you store addon_ids in items
        $addons = AddOn::query()->get()->keyBy('id');

        return view('livewire.admin.orders.exports.order-receipt', compact('order','businessSetting','addons'));
    }
}
