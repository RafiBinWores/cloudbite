<?php

namespace App\Livewire\Admin\Notification;

use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use Livewire\Component;

class NotificationBell extends Component
{
    use WithTcToast;

    public int $unread = 0;
    public bool $afterCommit = true;

    public function mount(): void
    {
        $this->refreshCounts();
    }

    /**
     * ✅ This is the ONLY listener you need (don’t also use $listeners)
     * We accept payload so you can toast message.
     */
    #[On('rt-notification')]
    public function onRealtime(array $payload = []): void
    {
        // Update unread count
        $this->refreshCounts();

        // Optional toast
        $msg = $payload['message'] ?? 'New notification';
        $this->success(
            title: $msg,
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );

        // ✅ Force a re-render (makes Flux dropdown list update)
        $this->dispatch('$refresh');
    }

    public function refreshCounts(): void
    {
        $user = Auth::user();
        $this->unread = $user ? $user->unreadNotifications()->count() : 0;
    }

    public function markAllRead(): void
    {
        $user = Auth::user();
        if ($user) {
            $user->unreadNotifications->markAsRead();
        }

        $this->refreshCounts();
        $this->dispatch('$refresh');
    }

    public function markAsRead(string $id): void
    {
        $user = Auth::user();
        if (!$user) return;

        $n = $user->notifications()->where('id', $id)->first();
        if ($n && is_null($n->read_at)) {
            $n->markAsRead();
        }

        $this->refreshCounts();
        $this->dispatch('$refresh');
    }

    public function render()
    {
        $user = Auth::user();

        $items = $user
            ? $user->notifications()->latest()->take(10)->get()
            : collect();

        return view('livewire.admin.notification.notification-bell', compact('items'));
    }
}
