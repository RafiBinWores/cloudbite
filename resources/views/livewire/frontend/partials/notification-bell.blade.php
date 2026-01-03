<div class="relative" x-data>
    <button class="relative" @click="$dispatch('toggle-notify')">
        🔔
        @if($unread > 0)
            <span class="absolute -top-2 -right-2 text-xs bg-red-600 text-white px-2 py-0.5 rounded-full">
                {{ $unread }}
            </span>
        @endif
    </button>

    <div x-data="{ open:false }"
         @toggle-notify.window="open = !open"
         class="absolute right-0 mt-2 w-80 bg-white border rounded-xl shadow-lg overflow-hidden"
         x-show="open" x-cloak>
        <div class="flex items-center justify-between px-4 py-3 border-b">
            <p class="font-semibold">Notifications</p>
            <button wire:click="markAllRead" class="text-sm text-slate-600 hover:text-slate-900">
                Mark all read
            </button>
        </div>

        <div class="max-h-80 overflow-auto">
            @forelse($items as $n)
                <a href="{{ $n->data['url'] ?? '#' }}" class="block px-4 py-3 border-b hover:bg-slate-50">
                    <p class="text-sm {{ $n->read_at ? 'text-slate-600' : 'text-slate-900 font-medium' }}">
                        {{ $n->data['message'] ?? 'Notification' }}
                    </p>
                    <p class="text-xs text-slate-400 mt-1">{{ $n->created_at->diffForHumans() }}</p>
                </a>
            @empty
                <div class="px-4 py-6 text-sm text-slate-500">No notifications yet.</div>
            @endforelse
        </div>
    </div>

    <script>
      window.addEventListener('toast', (e) => {
        // Replace with your toast UI
        console.log('TOAST:', e.detail.message);
      });
    </script>
</div>
