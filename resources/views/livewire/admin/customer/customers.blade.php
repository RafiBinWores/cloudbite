<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-4 flex items-center gap-2" level="1"><img class="w-8"
                src="{{ asset('assets/images/icons/customer.png') }}" alt="c Icon">{{ __('Customers') }}</flux:heading>
        <flux:separator variant="subtle" />
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
                    class="tracking-wider sticky top-0 bg-white dark:bg-neutral-700 outline-2 outline-neutral-200 dark:outline-neutral-600">
                    <tr>
                        <th class="px-4 lg:px-6 py-3">#</th>
                        <th class="px-4 lg:px-6 py-3">Customer Name</th>
                        <th class="px-4 lg:px-6 py-3">Customer Info</th>
                        <th class="px-4 lg:px-6 py-3">Total Order</th>
                        <th class="px-4 lg:px-6 py-3">Total Amount</th>
                        <th class="px-4 lg:px-6 py-3">Actions</th>
                    </tr>
                </thead>

                <tbody>
                    @forelse ($customers as $c)
                        <tr wire:key="c-{{ $c->id }}" class="border-b dark:border-neutral-600">
                            <th class="px-4 lg:px-6 py-3">
                                {{ ($customers->currentPage() - 1) * $customers->perPage() + $loop->iteration }}
                            </th>

                            <td class="px-4 lg:px-6 py-3 flex items-center gap-3">
                                <img src="{{ asset($c->image ?? 'assets/images/placeholders/user-placeholder.png') }}"
                                    alt="{{ $c->name }}" class="h-12 w-12 rounded object-cover">
                                <p>
                                    {{ $c->name }}
                                </p>
                            </td>

                            <td class="px-4 lg:px-6 py-3">
                                <a href="mailto:{{ $c->email }}" class="text-rose-500 hover:underline">
                                    {{ $c->email ?? '-' }}
                                </a>
                                <br>
                                <a href="tel:{{ $c->phone }}" class="dark:text-gray-300 text-white hover:underline">
                                    {{ $c->phone ?? '-' }}
                                </a>
                            </td>

                            <td class="px-4 lg:px-6 py-3">
                                {{ number_format($c->orders_count) ?? '0' }}
                            </td>
                            <td class="px-4 lg:px-6 py-3">
                               <span class="font-medium font-oswald">৳</span> {{ number_format($c->orders_amount, 2) ?? '0.00' }}
                            </td>

                            <td class="px-4 lg:px-6 py-3">
                                <div class="flex gap-2">
                                    <flux:button href="" wire:navigate class="min-h-[40px]" icon="eye"
                                        variant="primary" color="yellow">
                                    </flux:button>

                                    <flux:button href="" wire:navigate class="min-h-[40px]" icon="pencil"
                                        variant="primary" color="blue" />
                                    <flux:modal.trigger name="delete-confirmation-modal">
                                        <flux:button
                                            wire:click="$dispatch('confirm-delete', {
                                                id: {{ $c->id }},
                                                dispatchAction: 'delete-c',
                                                modalName: 'delete-confirmation-modal',
                                                heading: 'Delete c?',
                                                message: 'You are about to delete this c: <strong>{{ $c->title }}</strong>. This action cannot be reversed.',
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
                            <td colspan="6" class="px-4 lg:px-6 pt-4 text-center">No customers found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <!-- Pagination -->
        <nav class="mt-4">
            <div class="sm:hidden text-center">
                {{ $customers->onEachSide(0)->links() }}
            </div>
            <div class="hidden sm:block">
                {{ $customers->links() }}
            </div>
        </nav>
    </div>
</div>
