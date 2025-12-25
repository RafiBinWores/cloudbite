<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-4 flex items-center gap-2" level="1"><img class="w-8"
                src="{{ asset('assets/images/icons/banners.png') }}" alt="Banners Icon">{{ __('Banners') }}</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Model Button --}}
    <flux:modal.trigger name="banner-modal">
        <flux:button class="cursor-pointer" variant="primary" color="rose" icon="add-icon"
            wire:click="$dispatch('open-banner-modal', {mode: 'create'})">
            Add New</flux:button>
    </flux:modal.trigger>


    {{-- Model body --}}
    <livewire:admin.banners.banner-form />

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

        <!-- Desktop table (≥sm) -->
        <div class="overflow-x-auto max-h-[50vh] mt-2">
            <table class="min-w-full text-left text-sm whitespace-nowrap">
                <thead
                    class="tracking-wider sticky top-0 bg-white dark:bg-neutral-700 outline-2 outline-neutral-200 dark:outline-neutral-600">
                    <tr>
                        <th scope="col" class="px-4 lg:px-6 py-3">#</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Image</th>
                        @include('livewire.common.sortable-th', [
                            'name' => 'title',
                            'displayName' => 'Title',
                        ])
                        <th scope="col" class="px-4 lg:px-6 py-3">Item Type</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Item</th>
                        @include('livewire.common.sortable-th', [
                            'name' => 'status',
                            'displayName' => 'Status',
                        ])
                        <th scope="col" class="px-4 lg:px-6 py-3">Schedule</th>

                        @include('livewire.common.sortable-th', [
                            'name' => 'created_at',
                            'displayName' => 'Created At',
                        ])
                        <th scope="col" class="px-4 lg:px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($banners as $banner)
                        <tr wire:key="{{ $banner->id }}" class="border-b dark:border-neutral-600">
                            <th scope="row" class="px-4 lg:px-6 py-3">
                                {{ ($banners->currentPage() - 1) * $banners->perPage() + $loop->iteration }}
                            </th>
                            <td class="px-4 lg:px-6 py-3">
                                @if (!empty($banner->image))
                                    <img src="{{ asset($banner->image) }}" alt="{{ $banner->title }}"
                                        class="h-12 w-12 rounded object-contain">
                                @else
                                    <img src="{{ asset('assets/images/placeholders/cat-placeholder.png') }}"
                                        alt="placeholder" class="h-12 w-12 rounded object-cover">
                                @endif
                            </td>
                            <td class="px-4 lg:px-6 py-3">{{ $banner->title }}</td>
                            <td class="px-4 lg:px-6 py-3">{{ ucfirst($banner->item_type) }}</td>
                            <td class="px-4 lg:px-6 py-3">
                                @if ($banner->item_type === 'category')
                                    <flux:link href="{{ route('categories.index') }}" variant="ghost" wire:navigate>
                                        {{ ucfirst($banner->item_name) }}
                                    </flux:link>
                                @elseif ($banner->item_type === 'dish')
                                    <flux:link href="{{ route('dishes.show', $banner->item_slug) }}" variant="ghost"
                                        wire:navigate>{{ ucfirst($banner->item_name) }}</flux:link>
                                @endif
                            </td>
                            <td class="px-4 lg:px-6 py-3 capitalize" x-data="{ on: @js($banner->status === 'active') }">
                                <flux:switch x-model="on" @change="$wire.setStatus({{ $banner->id }}, on)"
                                    class="cursor-pointer" />
                            </td>
                            <td class="px-4 lg:px-6 py-3">
                                <div class="text-sm leading-tight">
                                    <div class="font-medium text-neutral-800 dark:text-neutral-100">
                                        {{ $banner->start_at ? \Carbon\Carbon::parse($banner->start_at)->format('M d, Y • h:i A') : '—' }}
                                    </div>

                                    <div class="text-neutral-500 dark:text-neutral-300">
                                        →
                                        {{ $banner->end_at ? \Carbon\Carbon::parse($banner->end_at)->format('M d, Y • h:i A') : 'No end date' }}
                                    </div>

                                    @php
                                        $now = now();
                                        $isActive =
                                            (!$banner->start_at || $now->gte($banner->start_at)) &&
                                            (!$banner->end_at || $now->lte($banner->end_at));
                                    @endphp

                                    <span
                                        class="inline-flex mt-1 items-center rounded-full px-2 py-0.5 text-xs font-medium
            {{ $isActive ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-600' }}">
                                        {{ $isActive ? 'Active now' : 'Inactive' }}
                                    </span>
                                </div>
                            </td>


                            <td class="px-4 lg:px-6 py-3">{{ $banner->created_at->format('M d, Y') }}</td>
                            <td class="px-4 lg:px-6 py-3">
                                <div class="flex gap-2">
                                    <flux:modal.trigger name="banner-modal">
                                        <flux:button
                                            wire:click="$dispatch('open-banner-modal', {mode: 'view', banner: {{ $banner }}})"
                                            class="cursor-pointer min-h-[40px]" icon="eye" variant="primary"
                                            color="yellow">
                                        </flux:button>
                                        <flux:button
                                            wire:click="$dispatch('open-banner-modal', {mode: 'edit', banner: {{ $banner }}})"
                                            class="cursor-pointer min-h-[40px]" icon="pencil" variant="primary"
                                            color="blue">
                                        </flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal.trigger name="delete-confirmation-modal">
                                        <flux:button
                                            wire:click="$dispatch('confirm-delete', {
                                                id: {{ $banner->id }},
                                                dispatchAction: 'delete-banner',
                                                modalName: 'delete-confirmation-modal',
                                                heading: 'Delete banner?',
                                                message: 'You are about to delete this banner: <strong>{{ $banner->name }}</strong>. This action cannot be reversed.',
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
                            <td colspan="6" class="px-4 lg:px-6 pt-4 text-center">No banners found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav class="mt-4">
            <div class="sm:hidden text-center">
                {{ $banners->onEachSide(0)->links() }}
            </div>
            <div class="hidden sm:block">
                {{ $banners->links() }}
            </div>
        </nav>
    </div>

</div>
