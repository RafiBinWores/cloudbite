<flux:dropdown x-data align="end">
    {{-- Bell Button --}}
    <flux:button variant="subtle" square class="relative group" aria-label="Notifications">
        <flux:icon.bell class="text-zinc-500 dark:text-white cursor-pointer" />

        @if($unread > 0)
            <span
                class="absolute -top-1 -right-1 min-w-[18px] h-[18px] px-1 rounded-full bg-red-600 text-white text-[11px] leading-[18px] text-center">
                {{ $unread > 99 ? '99+' : $unread }}
            </span>
        @endif
    </flux:button>

    {{-- Dropdown --}}
    <flux:menu class="w-[360px] p-0 overflow-hidden">
        <div class="flex items-center justify-between px-4 py-3 border-b border-zinc-200 dark:border-zinc-700">
            <div class="font-semibold">Notifications</div>

            <button wire:click="markAllRead"
                class="text-xs text-zinc-600 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">
                Mark all read
            </button>
        </div>

        <div class="max-h-[360px] overflow-auto">
            @forelse($items as $n)
                @php
                    $data = $n->data ?? [];
                    $url = $data['url'] ?? null;
                    $msg = $data['message'] ?? 'Notification';
                    $isUnread = is_null($n->read_at);
                @endphp

                <a href="{{ $url ?: 'javascript:void(0)' }}"
                   @if($url) wire:navigate @endif
                   wire:click="markAsRead('{{ $n->id }}')"
                   class="block px-4 py-3 border-b border-zinc-200/70 dark:border-zinc-700/70
                          hover:bg-zinc-50 dark:hover:bg-zinc-800">
                    <div class="flex items-start gap-3">
                        <span class="mt-1 inline-block w-2 h-2 rounded-full {{ $isUnread ? 'bg-blue-500' : 'bg-transparent' }}"></span>

                        <div class="flex-1">
                            <p class="text-sm {{ $isUnread ? 'font-semibold text-zinc-900 dark:text-white' : 'text-zinc-600 dark:text-zinc-300' }}">
                                {{ $msg }}
                            </p>
                            <p class="text-xs text-zinc-400 mt-1">
                                {{ $n->created_at?->diffForHumans() }}
                            </p>
                        </div>
                    </div>
                </a>
            @empty
                <div class="px-4 py-8 text-sm text-zinc-500 dark:text-zinc-300 text-center">
                    No notifications yet.
                </div>
            @endforelse
        </div>

        <div class="px-4 py-3 bg-zinc-50 dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-700">
            <a href="{{ route('orders.index') }}" wire:navigate
               class="text-sm text-zinc-700 hover:text-zinc-900 dark:text-zinc-300 dark:hover:text-white">
                View all orders →
            </a>
        </div>
    </flux:menu>
</flux:dropdown>
