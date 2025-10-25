<div>
    {{-- Page Heading --}}
    <div class="relative mb-6 w-full">
        <flux:heading size="xl" class="mb-4 flex items-center gap-2" level="1"><img class="w-8"
                src="{{ asset('assets/images/icons/checklist.png') }}" alt="order Icon">{{ __('Orders') }}</flux:heading>
        <flux:separator variant="subtle" />
    </div>

    {{-- Status wise order count with filter --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
        {{-- Precessing --}}
        <div class="bg-accent/60 dark:bg-neutral-700  rounded-xl px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/icons/order-processing.png') }}" alt="Processing icon"
                        class="w-8">
                    <p class="font-medium text-base text-white">Confirmed</p>
                </div>

                <p class="font-medium text-md text-white">100</p>
            </div>
        </div>

        {{-- Precessing --}}
        <div class="bg-accent/60 dark:bg-neutral-700  rounded-xl px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/icons/confirm.png') }}" alt="Confirm icon" class="w-8">
                    <p class="font-medium text-base text-white">Confirmed</p>
                </div>

                <p class="font-medium text-md text-white">100</p>
            </div>
        </div>


        {{-- Preparing --}}
        <div class="bg-accent/60 dark:bg-neutral-700  rounded-xl px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/icons/cooking.png') }}" alt="Cooking icon" class="w-8">
                    <p class="font-medium text-base text-white">Preparing</p>
                </div>

                <p class="font-medium text-md text-white">100</p>
            </div>
        </div>

        {{-- Out for delivery --}}
        <div class="bg-accent/60 dark:bg-neutral-700  rounded-xl px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/icons/delivery.png') }}" alt="delivery icon" class="w-8">
                    <p class="font-medium text-base text-white">Out For Delivery</p>
                </div>

                <p class="font-medium text-md text-white">100</p>
            </div>
        </div>

        {{-- Delivered --}}
        <div class="bg-accent/60 dark:bg-neutral-700  rounded-xl px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/icons/complete.png') }}" alt="Delivered icon" class="w-8">
                    <p class="font-medium text-base text-white">Delivered</p>
                </div>

                <p class="font-medium text-md text-white">100</p>
            </div>
        </div>

        {{-- Cancelled --}}
        <div class="bg-accent/60 dark:bg-neutral-700  rounded-xl px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/icons/cancel-order.png') }}" alt="Cancelled icon" class="w-8">
                    <p class="font-medium text-base text-white">Cancelled</p>
                </div>

                <p class="font-medium text-md text-white">100</p>
            </div>
        </div>

        {{-- Returned --}}
        <div class="bg-accent/60 dark:bg-neutral-700  rounded-xl px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/icons/return.png') }}" alt="Returned icon" class="w-8">
                    <p class="font-medium text-base text-white">Returned</p>
                </div>

                <p class="font-medium text-md text-white">100</p>
            </div>
        </div>

        {{-- Failed To Delivery --}}
        <div class="bg-accent/60 dark:bg-neutral-700  rounded-xl px-4 py-6">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <img src="{{ asset('assets/images/icons/delivery-failed.png') }}" alt="Failed delivery icon"
                        class="w-8">
                    <p class="font-medium text-base text-white">Failed To Delivery</p>
                </div>

                <p class="font-medium text-md text-white">100</p>
            </div>
        </div>
    </div>

    @php
        $orderStatusBadge = function (string $status): string {
            $styles = [
                'pending' =>
                    'bg-amber-100 text-amber-800 ring-amber-200 dark:bg-amber-950/50 dark:text-amber-300 dark:ring-amber-900/60',
                'processing' =>
                    'bg-blue-100 text-blue-800 ring-blue-200 dark:bg-blue-950/50 dark:text-blue-300 dark:ring-blue-900/60',
                'confirmed' =>
                    'bg-sky-100 text-sky-800 ring-sky-200 dark:bg-sky-950/50 dark:text-sky-300 dark:ring-sky-900/60',
                'preparing' =>
                    'bg-indigo-100 text-indigo-800 ring-indigo-200 dark:bg-indigo-950/50 dark:text-indigo-300 dark:ring-indigo-900/60',
                'out_for_delivery' =>
                    'bg-fuchsia-100 text-fuchsia-800 ring-fuchsia-200 dark:bg-fuchsia-950/50 dark:text-fuchsia-300 dark:ring-fuchsia-900/60',
                'delivered' =>
                    'bg-emerald-100 text-emerald-800 ring-emerald-200 dark:bg-emerald-950/50 dark:text-emerald-300 dark:ring-emerald-900/60',
                'cancelled' =>
                    'bg-slate-200 text-slate-700 ring-slate-300 dark:bg-slate-800/70 dark:text-slate-300 dark:ring-slate-700',
                'returned' =>
                    'bg-zinc-200 text-zinc-700 ring-zinc-300 dark:bg-zinc-800/70 dark:text-zinc-300 dark:ring-zinc-700',
                'failed_to_deliver' =>
                    'bg-rose-100 text-rose-800 ring-rose-200 dark:bg-rose-950/50 dark:text-rose-300 dark:ring-rose-900/60',
            ];

            $cls =
                $styles[$status] ??
                'bg-slate-200 text-slate-700 ring-slate-300 dark:bg-slate-800/70 dark:text-slate-300 dark:ring-slate-700';
            $label = \Illuminate\Support\Str::headline($status);

            // Return safe HTML (escape label)
            return '<span class="inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-medium ring-1 ' .
                $cls .
                '">
                    <span class="size-1.5 rounded-full bg-current/60"></span>' .
                e($label) .
                '</span>';
        };
    @endphp


    <!-- Top controls: Date range + Status + Clear + Export -->
    <div class="border dark:border-none bg-white dark:bg-neutral-700 mt-8 p-4 sm:p-6 rounded-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 sm:gap-4">

            <div class="flex items-center flex-col md:flex-row gap-3 w-full dark:text-white">
                <!-- From -->
                <div class="w-full sm:w-48">
                    <label for="dateFrom" class="sr-only">From</label>
                    <input type="date" id="dateFrom" wire:model.live="dateFrom"
                        class="block w-full rounded-lg border dark:border-none dark:bg-neutral-600 p-2.5 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400" />
                </div>

                <!-- To -->
                <div class="w-full sm:w-48">
                    <label for="dateTo" class="sr-only">To</label>
                    <input type="date" id="dateTo" wire:model.live="dateTo"
                        class="block w-full rounded-lg border dark:border-none dark:bg-neutral-600 p-2.5 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400" />
                </div>

                <!-- Status -->
                <div class="w-full sm:w-56">
                    <label for="filterStatus" class="sr-only">Status</label>
                    <select id="filterStatus" wire:model.live="status"
                        class="block w-full rounded-lg border dark:border-none dark:bg-neutral-600 p-2.5 text-sm focus:border-rose-400 focus:outline-none focus:ring-1 focus:ring-rose-400">
                        <option value="">All Statuses</option>
                        <option value="pending">Pending</option>
                        <option value="processing">Processing</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="preparing">Preparing</option>
                        <option value="out_for_delivery">Out For Delivery</option>
                        <option value="delivered">Delivered</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="returned">Returned</option>
                        <option value="failed_to_deliver">Failed To Deliver</option>
                    </select>
                </div>

                <!-- Clear -->
                <button type="button" wire:click="clearFilters"
                    class="inline-flex items-center rounded-lg border px-3 py-2 text-sm dark:border-none dark:bg-neutral-600 hover:opacity-90">
                    Clear
                </button>
            </div>

            <!-- Export -->
            <!-- Export -->
            <div class="flex items-center gap-2 dark:text-white">
                <!-- Excel -->
                <button type="button" wire:click="exportExcel" wire:loading.attr="disabled"
                    wire:target="exportExcel"
                    class="inline-flex items-center rounded-lg border px-3 py-2 text-sm dark:border-none dark:bg-neutral-600 hover:opacity-90 cursor-pointer hover:bg-accent/70 hover:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                    aria-busy="false">
                    <!-- idle label -->
                    <span wire:loading.remove wire:target="exportExcel">Export Excel</span>

                    <!-- loading label + spinner -->
                    <span class="inline-flex items-center gap-2" wire:loading wire:target="exportExcel">
                        <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="3" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z" />
                        </svg>
                        Preparing…
                    </span>
                </button>

                <!-- PDF -->
                <button type="button" wire:click="exportPdf" wire:loading.attr="disabled" wire:target="exportPdf"
                    class="inline-flex items-center rounded-lg border px-3 py-2 text-sm dark:border-none dark:bg-neutral-600 cursor-pointer hover:opacity-90 hover:bg-accent/70 hover:text-white disabled:opacity-60 disabled:cursor-not-allowed"
                    aria-busy="false">
                    <span wire:loading.remove wire:target="exportPdf">Export PDF</span>
                    <span class="inline-flex items-center gap-2" wire:loading wire:target="exportPdf">
                        <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="3" />
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z" />
                        </svg>
                        Preparing…
                    </span>
                </button>
            </div>

        </div>
    </div>


    <!-- Tiny center popup (place once, near your component root) -->
    <div wire:loading wire:target="exportExcel,exportPdf"
        class="fixed z-[60] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2" role="status" aria-live="polite">
        <div class="w-64 rounded-2xl shadow-xl border bg-white/95 dark:bg-neutral-800/95 dark:text-white p-4">
            <div class="flex items-center gap-3">
                <svg class="size-5 animate-spin" viewBox="0 0 24 24" fill="none" aria-hidden="true">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="3"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z"></path>
                </svg>

                <!-- Generic label -->
                <span class="font-medium">Exporting…</span>
            </div>

            <!-- Per-action hint (optional) -->
            <div class="mt-1 text-xs text-slate-600 dark:text-slate-300">
                <span wire:loading wire:target="exportExcel">Preparing Excel file</span>
                <span wire:loading wire:target="exportPdf">Preparing PDF file</span>
            </div>

            <!-- Subtle progress bar (animated pulse) -->
            <div class="mt-3 h-1 w-full rounded bg-slate-200 dark:bg-neutral-700 overflow-hidden">
                <div class="h-full w-1/3 animate-pulse bg-slate-400 dark:bg-neutral-500 rounded"></div>
            </div>
        </div>
    </div>



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
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                            stroke-width="1.5" stroke="currentColor"
                            class="h-4 w-4 text-neutral-500 dark:text-neutral-200">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </span>
                </div>
            </div>

            <!-- Per Page -->
            <div class="flex items-center gap-2 dark:text-white">
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
                    <tr class="dark:text-white">
                        <th class="px-4 lg:px-6 py-3">#</th>
                        <th class="px-4 lg:px-6 py-3">Order ID</th>
                        <th class="px-4 lg:px-6 py-3">Order Date</th>
                        <th class="px-4 lg:px-6 py-3">Customer Info</th>
                        <th class="px-4 lg:px-6 py-3">Total Amount</th>
                        <th class="px-4 lg:px-6 py-3">Order Status</th>
                        <th class="px-4 lg:px-6 py-3">Actions</th>
                    </tr>
                </thead>

                <tbody class="text-neutral-700 dark:text-white">
                    @forelse ($orders as $order)
                        <tr wire:key="order-{{ $order->id }}" class="border-b dark:border-neutral-600">
                            <th class="px-4 lg:px-6 py-3">
                                {{ ($orders->currentPage() - 1) * $orders->perPage() + $loop->iteration }}
                            </th>

                            <td class="px-4 lg:px-6 py-3">
                                <div class="font-medium text-accent">{{ $order->order_code }}</div>
                            </td>

                            <td class="px-4 lg:px-6 py-3">
                                <div class="font-medium">{{ $order->created_at->format('d M Y') }}</div>
                                <div class="font-medium">{{ $order->created_at->format('h:i a') }}</div>
                            </td>
                            <td class="px-4 lg:px-6 py-3">
                                <div class="font-medium">
                                    {{ $order->contact_name }} <br>
                                    <a href="tel:{{ $order->phone }}">{{ $order->phone }}</a>
                                </div>
                            </td>

                            <td class="px-4 lg:px-6 py-3">
                                <div class="font-medium">
                                    <span><span class="font-medium font-oswald">৳</span> {{ number_format($order->grand_total, 2) }}</span>
                                    <br />
                                    @if ($order->payment_status == 'paid')
                                        <span class="text-green-600 font-medium">Paid</span>
                                    @else
                                        <span class="text-red-600 font-medium">Unpaid</span>
                                    @endif
                                </div>
                            </td>

                            <td class="px-4 lg:px-6 py-3">
                                <div class="font-medium">{!! $orderStatusBadge($order->order_status) !!}
                                </div>
                            </td>

                            <td class="px-4 lg:px-6 py-3">
                                <div class="flex gap-2">
                                    <flux:button href="{{ route('orders.show', $order->order_code) }}" wire:navigate class="min-h-[40px]" icon="eye"
                                        variant="primary" color="yellow"></flux:button>

                                    <flux:button onclick="window.open('{{ route('orders.print', $order->order_code) }}','_blank','noopener')" wire:navigate class="min-h-[40px]" icon="printer"
                                        variant="primary" class="cursor-pointer" />
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-4 lg:px-6 pt-4 text-center">No orders found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>


        <!-- Pagination -->
        <nav class="mt-4">
            <div class="sm:hidden text-center">
                {{ $orders->onEachSide(0)->links() }}
            </div>
            <div class="hidden sm:block">
                {{ $orders->links() }}
            </div>
        </nav>
    </div>
</div>
