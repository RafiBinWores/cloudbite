<?php

namespace App\Livewire\Frontend\Partials;

use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class NotificationBell extends Component
{
    use WithTcToast;
    
    public int $unread = 0;

    protected $listeners = ['rt-notification' => 'onRealtime'];

    public function mount(): void
    {
        $this->refreshCount();
    }

    public function onRealtime($payload = null): void
    {
        $this->refreshCount();

        // Optional: browser event to show toast via Alpine/JS
        $this->success(
                title: $payload['message'] ?? 'New notification',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
            
        $this->dispatch('toast', message: $payload['message'] ?? 'New notification');
    }

    public function refreshCount(): void
    {
        $this->unread = Auth::user()?->unreadNotifications()->count() ?? 0;
    }

    public function markAllRead(): void
    {
        Auth::user()?->unreadNotifications->markAsRead();
        $this->refreshCount();
    }

    public function render()
    {
        $items = Auth::user()?->notifications()->latest()->take(8)->get() ?? collect();

        return view('livewire.frontend.partials.notification-bell', compact('items'));
    }
}
