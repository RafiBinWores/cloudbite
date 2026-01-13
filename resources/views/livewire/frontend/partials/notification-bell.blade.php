<div class="relative" x-data>
    <button class="relative" @click="$dispatch('toggle-notify')">
        <div
            class="grid border rounded-full border-slate-900 size-9 sm:w-12 sm:h-12 place-items-center hover:bg-slate-900 group duration-200 flex-none cursor-pointer">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6 group-hover:text-white">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0" />
            </svg>
        </div>


        @if ($unread > 0)
            <span class="absolute -top-2 -right-2 text-xs bg-red-600 text-white px-2 py-0.5 rounded-full">
                {{ $unread }}
            </span>
        @endif
    </button>

    <div x-data="{ open: false }" @toggle-notify.window="open = !open"
        class="absolute right-0 mt-2 w-80 bg-white border rounded-xl shadow-lg overflow-hidden" x-show="open" x-cloak>
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
