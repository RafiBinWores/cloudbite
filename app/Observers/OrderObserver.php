<?php

namespace App\Observers;

use App\Models\Order;
use App\Notifications\UserOrderStatusUpdatedNotification;

class OrderObserver
{
    public function updated(Order $order): void
    {
        if ($order->wasChanged('order_status')) {
            $old = $order->getOriginal('order_status');
            $new = $order->order_status;

            if ($order->user) {
                $order->user->notify(new UserOrderStatusUpdatedNotification($order, $old, $new));
            }
        }
    }
}
