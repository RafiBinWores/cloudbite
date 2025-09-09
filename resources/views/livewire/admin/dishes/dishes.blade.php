<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" level="1">{{ __('Dishes') }}</flux:heading>
        <flux:subheading size="lg" class="mb-6">{{ __('Manage all of the dishes') }}</flux:subheading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Create modal Button --}}
    <flux:button :href="route('dishes.create')" wire:navigate class="cursor-pointer" icon="add-icon" variant="primary"
        color="rose">
        Create</flux:button>


    {{-- Delete Confirmation Modal --}}
    <livewire:common.delete-confirmation />


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
        <div class="overflow-x-auto mt-2">
            <table class="min-w-full text-left text-sm whitespace-nowrap">
                <thead
                    class="uppercase tracking-wider sticky top-0 bg-white dark:bg-neutral-700 outline-2 outline-neutral-200 dark:outline-neutral-600">
                    <tr>
                        <th class="px-4 lg:px-6 py-3">#</th>
                        <th class="px-4 lg:px-6 py-3">Image</th>
                        @include('livewire.common.sortable-th', [
                            'name' => 'title',
                            'displayName' => 'Name',
                        ])
                        <th class="px-4 lg:px-6 py-3">Price</th>
                        @include('livewire.common.sortable-th', [
                            'name' => 'visibility',
                            'displayName' => 'Visibility',
                        ])
                        @include('livewire.common.sortable-th', [
                            'name' => 'created_at',
                            'displayName' => 'Created At',
                        ])
                        <th class="px-4 lg:px-6 py-3">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($dishes as $dish)
                        <tr wire:key="dish-{{ $dish->id }}" class="border-b dark:border-neutral-600">
                            <th class="px-4 lg:px-6 py-3">
                                {{ ($dishes->currentPage() - 1) * $dishes->perPage() + $loop->iteration }}
                            </th>

                            <td class="px-4 lg:px-6 py-3">
                                <img src="{{ asset($dish->thumbnail) }}" alt="{{ $dish->title }}"
                                    class="h-12 w-12 rounded object-cover">
                            </td>

                            <td class="px-4 lg:px-6 py-3">
                                <div class="font-medium">{{ $dish->title }}</div>
                            </td>
                            <td class="px-4 lg:px-6 py-3">
                                <div class="font-medium">
                                    @php
                                        $basePrice = (float) ($dish->price ?? 0);

                                        // Apply discount
                                        if ($dish->discount_type && $dish->discount > 0) {
                                            if ($dish->discount_type === 'percent') {
                                                $afterDiscount = max(
                                                    0,
                                                    $basePrice - $basePrice * ($dish->discount / 100),
                                                );
                                            } else {
                                                $afterDiscount = max(0, $basePrice - (float) $dish->discount);
                                            }
                                        } else {
                                            $afterDiscount = $basePrice;
                                        }

                                        // Apply VAT (assume $dish->vat = percent, e.g. 15)
                                        $vatPercent = (float) ($dish->vat ?? 0);
                                        $finalPrice = $afterDiscount + $afterDiscount * ($vatPercent / 100);

                                        // Format function
                                        $money = fn($v) => fmod($v, 1) == 0
                                            ? number_format($v, 0)
                                            : number_format($v, 2);
                                    @endphp

                                    <!-- Show price -->
                                    <div class="text-lg font-semibold text-emerald-600">
                                        ৳{{ $money($finalPrice) }}
                                    </div>

                                    @if ($dish->vat)
                                        <div class="text-xs text-neutral-500">
                                            (includes {{ $dish->vat }}% VAT)
                                        </div>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 lg:px-6 py-3" x-data="{ on: @js($dish->visibility === 'Yes') }">
                                <flux:switch x-model="on" @change="$wire.setVisibility({{ $dish->id }}, on)" />
                            </td>

                            <td class="px-4 lg:px-6 py-3">
                                {{ $dish->created_at?->format('M d, Y') }}
                            </td>

                            <td class="px-4 lg:px-6 py-3">
                                <div class="flex gap-2">
                                    <flux:button href="{{ route('dishes.show', $dish->slug) }}" class="min-h-[40px]"
                                        icon="eye" variant="primary" color="yellow"></flux:button>

                                    <flux:button wire:click="editDish({{ $dish->id }})" class="min-h-[40px]"
                                        icon="pencil" variant="primary" color="blue" />
                                    <flux:modal.trigger name="delete-confirmation-modal">
                                        <flux:button
                                            wire:click="$dispatch('confirm-delete', {
                                                id: {{ $dish->id }},
                                                dispatchAction: 'delete-dish',
                                                modalName: 'delete-confirmation-modal',
                                                heading: 'Delete dish?',
                                                message: 'You are about to delete this dish: <strong>{{ $dish->title }}</strong>. This action cannot be reversed.',
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
                            <td colspan="6" class="px-4 lg:px-6 pt-4 text-center">No dishes found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <!-- Pagination -->
        <nav class="mt-4">
            <div class="sm:hidden text-center">
                {{ $dishes->onEachSide(0)->links() }}
            </div>
            <div class="hidden sm:block">
                {{ $dishes->links() }}
            </div>
        </nav>
    </div>


</div>
