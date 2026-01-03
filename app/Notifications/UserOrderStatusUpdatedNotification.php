<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\BroadcastMessage;

class UserOrderStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public $order, public string $oldStatus, public string $newStatus) {}

    public function via($notifiable): array
    {
        return ['database', 'broadcast'];
    }

    public function toDatabase($notifiable): array
    {
        return [
            'type' => 'order_status',
            'order_id' => $this->order->id,
            'order_code' => $this->order->order_code,
            'old_status' => $this->oldStatus,
            'new_status' => $this->newStatus,
            'message' => "Order {$this->order->order_code} status updated: {$this->newStatus}",
            'url' => route('account.orders.show', $this->order->order_code),
        ];
    }

    public function toBroadcast($notifiable): BroadcastMessage
    {
        return new BroadcastMessage($this->toDatabase($notifiable));
    }
}
