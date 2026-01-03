<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class AdminNewOrderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $order) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'new_order',
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'total' => $this->order->grand_total ?? null,
            'message' => "New order placed: {$this->order->order_code}",
            'url' => route('orders.show', $this->order->order_code),
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
