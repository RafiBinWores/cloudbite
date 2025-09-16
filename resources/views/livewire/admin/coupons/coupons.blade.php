<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-4 flex items-center gap-2" level="1"><img class="w-8"
                src="{{ asset('assets/images/icons/coupon.png') }}" alt="Coupon Icon">{{ __('Coupons') }}</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Coupon Model Button --}}
    <flux:modal.trigger name="coupon-modal">
        <flux:button class="cursor-pointer" variant="primary" color="rose" icon="add-icon" wire:click="$dispatch('open-coupon-modal', {mode: 'create'})">
            Add New</flux:button>
    </flux:modal.trigger>


    {{-- Model body --}}
    <livewire:admin.coupons.coupon-form />

    {{-- Delete Confirmation Modal --}}
    <livewire:common.delete-confirmation />


    {{-- Table --}}
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
            <div class="flex items-center gap-2 ">
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
        <div class="overflow-x-auto max-h-[50vh] mt-2 hidden sm:block">
            <table class="min-w-full text-left text-sm whitespace-nowrap">
                <thead
                    class="uppercase tracking-wider sticky top-0 bg-white dark:bg-neutral-700 outline-2 outline-neutral-200 dark:outline-neutral-600">
                    <tr>
                        <th scope="col" class="px-4 lg:px-6 py-3">#</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Coupon</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Discount</th>
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
                    @forelse ($coupons as $coupon)
                        <tr wire:key="{{ $coupon->id }}" class="border-b dark:border-neutral-600">
                            <th scope="row" class="px-4 lg:px-6 py-3">
                                {{ ($coupons->currentPage() - 1) * $coupons->perPage() + $loop->iteration }}
                            </th>
                            <td class="px-4 lg:px-6 py-3">
                                <strong>Code: {{ $coupon->coupon_code }}</strong><br>
                                <small>{{ $coupon->title }}</small>
                            </td>
                            <td class="px-4 lg:px-6 py-3">
                                @if ($coupon->discount_type === 'percent')
                                    <span class="text-yellow-500 font-medium">
                                        {{ rtrim(rtrim(number_format($coupon->discount, 2, '.', ''), '0'), '.') }} %
                                    </span>
                                @elseif (intval($coupon->discount) == $coupon->discount)
                                    <span class="text-yellow-500 font-medium">
                                        {{ intval($coupon->discount) }}
                                        <i class="fa-regular fa-bangladeshi-taka-sign ps-1"></i>
                                    </span>
                                @else
                                    <span class="text-yellow-500 font-medium">
                                        {{ rtrim(rtrim(number_format($coupon->discount, 2, '.', ''), '0'), '.') }}
                                        <i class="fa-regular fa-bangladeshi-taka-sign ps-1"></i>
                                    </span>
                                @endif

                            </td>

                            <td class="px-4 lg:px-6 py-3 capitalize" x-data="{ on: @js($coupon->status === 'active') }">
                                <flux:switch x-model="on" @change="$wire.setStatus({{ $coupon->id }}, on)" class="cursor-pointer" />
                            </td>

                            <td class="px-4 lg:px-6 py-3">{{ $coupon->created_at->format('M d, Y') }}</td>
                            <td class="px-4 lg:px-6 py-3">
                                <div class="flex gap-2">
                                    <flux:modal.trigger name="coupon-modal">
                                        <flux:button
                                            wire:click="$dispatch('open-coupon-modal', {mode: 'view', coupon: {{ $coupon }}})"
                                            class="cursor-pointer min-h-[40px]" icon="eye" variant="primary"
                                            color="yellow">
                                        </flux:button>
                                        <flux:button
                                            wire:click="$dispatch('open-coupon-modal', {mode: 'edit', coupon: {{ $coupon }}})"
                                            class="cursor-pointer min-h-[40px]" icon="pencil" variant="primary"
                                            color="blue">
                                        </flux:button>
                                    </flux:modal.trigger>

                                    <flux:modal.trigger name="delete-confirmation-modal">
                                        <flux:button
                                            wire:click="$dispatch('confirm-delete', {
                                                id: {{ $coupon->id }},
                                                dispatchAction: 'delete-coupon',
                                                modalName: 'delete-confirmation-modal',
                                                heading: 'Delete coupon?',
                                                message: 'You are about to delete this coupon: <strong>{{ $coupon->title }}</strong>. This action cannot be reversed.',
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
                            <td colspan="6" class="px-4 lg:px-6 pt-4 text-center">No coupons found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav class="mt-4">
            <div class="sm:hidden text-center">
                {{ $coupons->onEachSide(0)->links() }}
            </div>
            <div class="hidden sm:block">
                {{ $coupons->links() }}
            </div>
        </nav>
    </div>

</div>
