<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-4 flex items-center gap-2" level="1"><img class="w-8"
                src="{{ asset('assets/images/icons/add-ons.png') }}" alt="Coupon Icon">{{ __('Add Ons') }}</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Create modal Button --}}
    <flux:modal.trigger name="addOn-modal">
        <flux:button class="cursor-pointer" icon="add-icon" variant="primary" color="rose"
            wire:click="$dispatch('open-addOn-modal', {mode: 'create'})">
            Add New</flux:button>
    </flux:modal.trigger>

    {{-- Create Modal --}}
    <livewire:admin.add-ons.add-on-form />

    {{-- Delete Confirmation Modal --}}
    <livewire:common.delete-confirmation />

    <!-- Table responsive wrapper -->
    <div class="border dark:border-none bg-white dark:bg-neutral-700 mt-8 p-4 sm:p-6 rounded-2xl">

        <!-- Top controls -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4 mb-4">
            <div class="flex items-center flex-col md:flex-row gap-3">
                <!-- Search -->
                <div class="relative w-full sm:w-64">
                    <label for="inputSearch" class="sr-only">Search</label>
                    <input id="inputSearch" type="text" placeholder="Search..."
                        wire:model.live.debounce.300ms='search'
                        class="block w-full rounded-lg border dark:border-none dark:bg-neutral-600 py-2.5 pl-10 pr-4 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400" />
                    <span class="pointer-events-none absolute left-3 top-1/2 -translate-y-1/2 transform">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="h-4 w-4 text-neutral-500 dark:text-neutral-200">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </span>
                </div>

                <!-- Filter -->
                <div class="relative w-full sm:w-40">
                    <label for="inputFilter" class="sr-only">Filter</label>
                    <select id="inputFilter" wire:model.live="range"
                        class="block w-full rounded-lg border dark:border-none dark:bg-neutral-600 p-2.5 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400">
                        <option value="" selected>Default</option>
                        <option value="last_week">Last week</option>
                        <option value="last_month">Last month</option>
                        <option value="yesterday">Yesterday</option>
                        <option value="last_7_days">Last 7 days</option>
                        <option value="last_30_days">Last 30 days</option>
                    </select>
                </div>
            </div>

            <!-- Per Page -->
            <div class="flex items-center gap-2">
                <label for="inputFilter" class="text-neutral-600 dark:text-neutral-300">Per Page: </label>
                <select id="inputFilter" wire:model.live='perPage'
                    class="block rounded-lg border dark:border-none dark:bg-neutral-600 p-2.5 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400 w-20">
                    <option value="10" selected>10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                    <option value="100">100</option>
                </select>
            </div>
        </div>

        <!-- Mobile list (xs only) -->
        <ul class="sm:hidden space-y-3">
            @forelse ($addOns as $addOn)
                <li class="rounded-xl border dark:border-neutral-600 p-3 bg-white dark:bg-neutral-700">
                    <div class="flex items-center gap-3">
                        <div class="min-w-0 flex-1">
                            <div class="flex items-center justify-between gap-2">
                                <p class="font-medium truncate">{{ $addOn->name }}
                                    <span class="ps-2">
                                        @if (is_null($addOn->price) || $addOn->price == 0)
                                            <span class="text-green-600 font-semibold text-sm">Free</span>
                                        @elseif (intval($addOn->price) == $addOn->price)
                                            <span class="text-yellow-500 font-medium text-sm"><i
                                                    class="fa-regular fa-bangladeshi-taka-sign pe-1"></i>
                                                {{ intval($addOn->price) }}</span>
                                        @else
                                            <span class="text-yellow-500 font-medium text-sm">
                                                <i class="fa-regular fa-bangladeshi-taka-sign pe-1"></i>
                                                {{ rtrim(rtrim(number_format($addOn->price, 2, '.', ''), '0'), '.') }}
                                            </span>
                                        @endif
                                    </span>
                                </p>
                                <flux:badge variant="solid" size="sm"
                                    color="{{ $addOn->status === 'active' ? 'emerald' : 'yellow' }}">
                                    {{ $addOn->status }}
                                </flux:badge>
                            </div>

                            <div class="mt-1 flex flex-wrap items-center gap-2">

                                <span class="text-xs text-neutral-500 dark:text-neutral-300">
                                    {{ $addOn->created_at->format('M d, Y') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="mt-3 flex items-center gap-2">
                        <flux:modal.trigger name="addOn-modal">
                            <flux:button
                                wire:click="$dispatch('open-addOn-modal', {mode: 'view', addOn: {{ $addOn }}})"
                                class="cursor-pointer h-[30px]" variant="primary" color="yellow">
                                view
                            </flux:button>

                            <flux:button
                                wire:click="$dispatch('open-addOn-modal', {mode: 'edit', addOn: {{ $addOn }}})"
                                class="cursor-pointer  h-[30px]" variant="primary" color="blue">
                                Edit
                            </flux:button>
                        </flux:modal.trigger>

                        <flux:modal.trigger name="delete-confirmation-modal">
                            <flux:button
                                wire:click="$dispatch('confirm-delete', {
                                    id: {{ $addOn->id }},
                                    dispatchAction: 'delete-addOn',
                                    modalName: 'delete-confirmation-modal',
                                    heading: 'Delete add-ons?',
                                    message: 'You are about to delete this add-ons: <strong>{{ $addOn->name }}</strong>. This action cannot be reversed.',
                                })"
                                class="cursor-pointer h-[30px]" variant="primary" color="red">
                                Delete
                            </flux:button>
                        </flux:modal.trigger>
                    </div>
                </li>
            @empty
                <li class="text-center py-4">No crusts found.</li>
            @endforelse
        </ul>

        <!-- Desktop table (≥sm) -->
        <div class="overflow-x-auto max-h-[50vh] mt-2 hidden sm:block">
            <table class="min-w-full text-left text-sm whitespace-nowrap">
                <thead
                    class="tracking-wider sticky top-0 bg-white dark:bg-neutral-700 outline-2 outline-neutral-200 dark:outline-neutral-600">
                    <tr>
                        <th scope="col" class="px-4 lg:px-6 py-3">#</th>
                        @include('livewire.common.sortable-th', [
                            'name' => 'name',
                            'displayName' => 'Name',
                        ])
                        <th scope="col" class="px-4 lg:px-6 py-3">Price</th>
                        @include('livewire.common.sortable-th', [
                            'name' => 'status',
                            'displayName' => 'Status',
                        ])
                        @include('livewire.common.sortable-th', [
                            'name' => 'created_at',
                            'displayName' => 'Created At',
                        ])
                        <th scope="col" class="px-4 lg:px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($addOns as $addOn)
                        <tr wire:key="{{ $addOn->id }}" class="border-b dark:border-neutral-600">
                            <th scope="row" class="px-4 lg:px-6 py-3">
                                {{ ($addOns->currentPage() - 1) * $addOns->perPage() + $loop->iteration }}
                            </th>

                            <td class="px-4 lg:px-6 py-3">{{ $addOn->name }}</td>
                            
                            <td class="px-4 lg:px-6 py-3">
                                @if (is_null($addOn->price) || $addOn->price == 0)
                                    <span class="text-green-600 font-semibold">Free</span>
                                @elseif (intval($addOn->price) == $addOn->price)
                                    <span class="text-yellow-500 font-medium"><i
                                            class="fa-regular fa-bangladeshi-taka-sign pe-1"></i>
                                        {{ intval($addOn->price) }}</span>
                                @else
                                    <span class="text-yellow-500 font-medium">
                                        <i class="fa-regular fa-bangladeshi-taka-sign pe-1"></i>
                                        {{ rtrim(rtrim(number_format($addOn->price, 2, '.', ''), '0'), '.') }}
                                    </span>
                                @endif
                            </td>

                            <td class="px-4 lg:px-6 py-3 capitalize">
                                <flux:badge variant="solid" size="sm"
                                    color="{{ $addOn->status === 'active' ? 'green' : 'red' }}">{{ $addOn->status }}
                                </flux:badge>
                            </td>
                            <td class="px-4 lg:px-6 py-3">{{ $addOn->created_at->format('M d, Y') }}</td>
                            <td class="px-4 lg:px-6 py-3">
                                <div class="flex gap-2">
                                    <flux:modal.trigger name="addOn-modal">
                                        <flux:button
                                            wire:click="$dispatch('open-addOn-modal', {mode: 'view', addOn: {{ $addOn }}})"
                                            class="cursor-pointer min-h-[40px]" icon="eye" variant="primary"
                                            color="yellow">
                                        </flux:button>
                                        <flux:button
                                            wire:click="$dispatch('open-addOn-modal', {mode: 'edit', addOn: {{ $addOn }}})"
                                            class="cursor-pointer min-h-[40px]" icon="pencil" variant="primary"
                                            color="blue">
                                        </flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal.trigger name="delete-confirmation-modal">
                                        <flux:button
                                            wire:click="$dispatch('confirm-delete', {
                                                id: {{ $addOn->id }},
                                                dispatchAction: 'delete-addOn',
                                                modalName: 'delete-confirmation-modal',
                                                heading: 'Delete add-ons?',
                                                message: 'You are about to delete this add-ons: <strong>{{ $addOn->name }}</strong>. This action cannot be reversed.',
                                                })"
                                            class="cursor-pointer min-h-[40px]" icon="trash" variant="primary"
                                            color="red">
                                        </flux:button>
                                    </flux:modal.trigger>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 lg:px-6 pt-4 text-center">No crusts found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav class="mt-4">
            <div class="sm:hidden text-center">
                {{ $addOns->onEachSide(0)->links() }}
            </div>
            <div class="hidden sm:block">
                {{ $addOns->links() }}
            </div>
        </nav>
    </div>


    @push('scripts')
        <script>
            function notificationMenu() {
                const STORAGE_KEY = 'cloudbite.notifications.v1';

                return {
                    open: false,
                    items: [], // all items from server/local
                    page: 1,
                    perPage: 8,
                    hasMore: false,

                    get visible() {
                        const end = this.page * this.perPage;
                        return this.items.slice(0, end);
                    },

                    init() {
                        // Hydrate from localStorage (fallback demo seed)
                        const cached = localStorage.getItem(STORAGE_KEY);
                        if (cached) {
                            const parsed = JSON.parse(cached);
                            this.items = parsed.items || [];
                        } else {
                            // Seed demo data (replace with server fetch)
                            this.items = [{
                                    id: cryptoRand(),
                                    type: 'order',
                                    title: 'Order #CB-1042 placed',
                                    message: 'We received your order. Preparing now.',
                                    read: false,
                                    created_at: Date.now() - 1000 * 60 * 3,
                                    url: '/orders/1042'
                                },
                                {
                                    id: cryptoRand(),
                                    type: 'promo',
                                    title: 'Weekend Combo!',
                                    message: 'Get 15% off on all set menus till Sunday.',
                                    read: false,
                                    created_at: Date.now() - 1000 * 60 * 40,
                                    url: '/promos/weekend-combo'
                                },
                                {
                                    id: cryptoRand(),
                                    type: 'system',
                                    title: 'Profile updated',
                                    message: 'Your delivery address has been saved.',
                                    read: true,
                                    created_at: Date.now() - 1000 * 60 * 90,
                                    url: '/profile'
                                },
                            ];
                        }

                        this.persist();
                        this.hasMore = this.items.length > this.visible.length;
                    },

                    toggle() {
                        this.open = !this.open;
                    },

                    unreadCount() {
                        return this.items.filter(i => !i.read).length;
                    },

                    toggleRead(n) {
                        n.read = !n.read;
                        this.persist();
                    },

                    markAllRead() {
                        this.items.forEach(i => i.read = true);
                        this.persist();
                    },

                    remove(id) {
                        const i = this.items.findIndex(x => x.id === id);
                        if (i !== -1) this.items.splice(i, 1);
                        this.persist();
                    },

                    clearAll() {
                        this.items = [];
                        this.page = 1;
                        this.hasMore = false;
                        this.persist();
                    },

                    openItem(n) {
                        if (!n.read) {
                            n.read = true;
                            this.persist();
                        }
                        if (n.url) window.location.href = n.url;
                    },

                    loadMore() {
                        this.page++;
                        this.hasMore = this.items.length > this.visible.length;
                    },

                    onScroll(e) {
                        const nearBottom = e.target.scrollTop + e.target.clientHeight >= e.target.scrollHeight - 24;
                        if (nearBottom && this.hasMore) this.loadMore();
                    },

                    timeago(ts) {
                        const s = Math.floor((Date.now() - ts) / 1000);
                        if (s < 60) return `${s}s ago`;
                        const m = Math.floor(s / 60);
                        if (m < 60) return `${m}m ago`;
                        const h = Math.floor(m / 60);
                        if (h < 24) return `${h}h ago`;
                        const d = Math.floor(h / 24);
                        return `${d}d ago`;
                    },

                    // Persist to localStorage
                    persist() {
                        localStorage.setItem('cloudbite.notifications.v1', JSON.stringify({
                            items: this.items
                        }));
                        this.hasMore = this.items.length > this.visible.length;
                    },
                };

                function cryptoRand() {
                    // simple unique id
                    return (crypto?.randomUUID?.() || 'id-' + Math.random().toString(36).slice(2));
                }
            }
        </script>
    @endpush


</div>
