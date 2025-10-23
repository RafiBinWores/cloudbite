<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-4 flex items-center gap-2" level="1"><img class="w-8"
                src="{{ asset('assets/images/icons/delivery-man.png') }}" alt="Delivery man Icon">{{ __('Delivery Man') }}
        </flux:heading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Create Button --}}
    <flux:button :href="route('delivery.create')" wire:navigate class="cursor-pointer" icon="add-icon" variant="primary"
        color="rose">
        Add New</flux:button>

    {{-- Delete Confirmation Modal --}}
    <livewire:common.delete-confirmation />


    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mt-6">
            {{-- Total --}}
            <div
                class="!p-0 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-2xl hover:shadow-md transition">
                <div class="flex items-center gap-4 p-4">
                    <div class="rounded-2xl p-3 bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400">
                        {{-- Users icon --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-icon lucide-user"><path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                    </div>
                    <div class="flex-1 text-left">
                        <div class="text-sm text-slate-500 dark:text-slate-300">Total Delivery Men</div>
                        <div class="text-2xl font-semibold text-slate-900 dark:text-white">
                            {{ number_format($total) }}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Active --}}
            <div
                class="!p-0 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-2xl hover:shadow-md transition">
                <div class="flex items-center gap-4 p-4">
                    <div
                        class="rounded-2xl p-3 bg-emerald-50 dark:bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">
                        {{-- Check Circle --}}
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-check-icon lucide-user-round-check"><path d="M2 21a8 8 0 0 1 13.292-6"/><circle cx="10" cy="8" r="5"/><path d="m16 19 2 2 4-4"/></svg>
                    </div>
                    <div class="flex-1 text-left">
                        <div class="text-sm text-slate-500 dark:text-slate-300">Active Deliveryman</div>
                        <div class="text-2xl font-semibold text-slate-900 dark:text-white">
                            {{ number_format($active) }}
                        </div>
                    </div>
                    <span
                        class="mr-4 text-xs px-2 py-1 rounded-full bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-300">
                        {{ $total ? round(($active / max(1, $total)) * 100) : 0 }}%
                    </span>
                </div>
            </div>

            {{-- Inactive --}}
            <div
                class="!p-0 bg-white dark:bg-neutral-800 border border-gray-200 dark:border-neutral-700 rounded-2xl hover:shadow-md transition">
                <div class="flex items-center gap-4 p-4">
                    <div class="rounded-2xl p-3 bg-rose-50 dark:bg-rose-500/10 text-rose-600 dark:text-rose-400">
                        {{-- Ban --}}
                       <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-user-round-x-icon lucide-user-round-x"><path d="M2 21a8 8 0 0 1 11.873-7"/><circle cx="10" cy="8" r="5"/><path d="m17 17 5 5"/><path d="m22 17-5 5"/></svg>
                    </div>
                    <div class="flex-1 text-left">
                        <div class="text-sm text-slate-500 dark:text-slate-300">Inactive Deliveryman</div>
                        <div class="text-2xl font-semibold text-slate-900 dark:text-white">
                            {{ number_format($inactive) }}
                        </div>
                    </div>
                    <span
                        class="mr-4 text-xs px-2 py-1 rounded-full bg-rose-100 dark:bg-rose-500/20 text-rose-700 dark:text-rose-300">
                        {{ $total ? round(($inactive / max(1, $total)) * 100) : 0 }}%
                    </span>
                </div>
            </div>
        </div>

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
                        <th scope="col" class="px-4 lg:px-6 py-3">Name</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Contact Info</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Join Date</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Total Orders</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Cancel</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Completed</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Total Order Amount</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Status</th>
                        <th scope="col" class="px-4 lg:px-6 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($deliveryMans as $deliveryMan)
                        <tr wire:key="{{ $deliveryMan->id }}" class="border-b dark:border-neutral-600">
                            <th scope="row" class="px-4 lg:px-6 py-3">
                                {{ ($deliveryMans->currentPage() - 1) * $deliveryMans->perPage() + $loop->iteration }}
                            </th>
                            <td class="px-4 lg:px-6 py-3 flex items-center gap-3">
                                @if (!empty($deliveryMan->profile_image))
                                    <img src="{{ asset($deliveryMan->profile_image) }}"
                                        alt="{{ $deliveryMan->first_name }}" class="h-12 w-12 rounded object-cover">
                                @else
                                    <img src="{{ asset('assets/images/placeholders/cat-placeholder.png') }}"
                                        alt="placeholder" class="h-12 w-12 rounded object-cover">
                                @endif
                            </td>
                            <td class="px-4 lg:px-6 py-3">
                                <p>{{ $deliveryMan->first_name }} {{ $deliveryMan->last_name }}</p>
                            </td>
                            <td class="px-4 lg:px-6 py-3">
                                <div class="flex flex-col gap-1">
                                    <flux:link href="mailto:{{ $deliveryMan->email }}">{{ $deliveryMan->email }}
                                    </flux:link>
                                    <flux:link href="tel:{{ $deliveryMan->phone_number }}">
                                        {{ $deliveryMan->phone_number }}</flux:link>
                                </div>
                            </td>
                            <td class="px-4 lg:px-6 py-3">{{ $deliveryMan->created_at->format('d M, Y') }}</td>
                            <td class="px-4 lg:px-6 py-3 text-center">10</td>
                            <td class="px-4 lg:px-6 py-3 text-center">100</td>
                            <td class="px-4 lg:px-6 py-3 text-center">50</td>
                            <td class="px-4 lg:px-6 py-3 text-center">150</td>
                            <td class="px-4 lg:px-6 py-3 capitalize">
                                <flux:badge variant="solid" size="sm"
                                    color="{{ $deliveryMan->status === 'active' ? 'green' : 'red' }}">
                                    {{ $deliveryMan->status }}
                                </flux:badge>
                            </td>
                            <td class="px-4 lg:px-6 py-3">
                                <div class="flex gap-2">
                                    <flux:modal.trigger name="banner-modal">
                                        <flux:button wire:navigate class="cursor-pointer min-h-[40px]" icon="eye"
                                            variant="primary" color="yellow">
                                        </flux:button>
                                        <flux:button :href="route('delivery.edit', $deliveryMan->id)" wire:navigate
                                            class="cursor-pointer min-h-[40px]" icon="pencil" variant="primary"
                                            color="blue">
                                        </flux:button>
                                    </flux:modal.trigger>

                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 lg:px-6 pt-4 text-center">No deliveryMans found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <nav class="mt-4">
            <div class="sm:hidden text-center">
                {{ $deliveryMans->onEachSide(0)->links() }}
            </div>
            <div class="hidden sm:block">
                {{ $deliveryMans->links() }}
            </div>
        </nav>
    </div>

</div>
