<div>
    @push('styles')
        <style>
            @page {
                size: A4;
                margin: 12mm;
            }

            @media print {

                html,
                body {
                    background: #fff !important;
                    -webkit-print-color-adjust: exact;
                    print-color-adjust: exact;
                }

                body * {
                    visibility: hidden !important;
                }

                #mpb-print,
                #mpb-print * {
                    visibility: visible !important;
                }

                #mpb-print {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    box-shadow: none !important;
                    border: 0 !important;
                    background: #fff !important;
                }

                .print\:hidden {
                    display: none !important;
                }
            }
        </style>
    @endpush

    @php
        $statusColor = match ($booking->status) {
            'pending' => 'bg-amber-100 text-amber-800 dark:bg-amber-400/15 dark:text-amber-300',
            'confirmed' => 'bg-blue-100 text-blue-800 dark:bg-blue-400/15 dark:text-blue-300',
            'completed' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-400/15 dark:text-emerald-300',
            'cancelled' => 'bg-rose-100 text-rose-800 dark:bg-rose-400/15 dark:text-rose-300',
            default => 'bg-slate-100 text-slate-700 dark:bg-slate-400/15 dark:text-slate-300',
        };

        $paymentPill =
            strtolower($booking->payment_status) === 'paid'
                ? 'bg-emerald-100 text-emerald-800 dark:bg-emerald-400/15 dark:text-emerald-300'
                : 'bg-amber-100 text-amber-800 dark:bg-amber-400/15 dark:text-amber-300';

        $addr = $booking->shipping_address ?? null;
        if (is_string($addr)) {
            $decoded = json_decode($addr, true);
            $addr = json_last_error() === JSON_ERROR_NONE ? $decoded : $addr;
        }
        $addressLine = '-';
        if (is_array($addr)) {
            $addressLine = implode(
                ', ',
                array_filter([$addr['line1'] ?? null, $addr['city'] ?? null, $addr['postcode'] ?? null]),
            );
            $addressLine = $addressLine === '' ? '-' : $addressLine;
        } elseif (is_string($addr) && trim($addr) !== '') {
            $addressLine = $addr;
        }

        // Flatten all items for a compact table
        $days = $booking->days ?? [];
        $slotsOrder = ['breakfast', 'lunch', 'tiffin', 'dinner'];
        $slotLabels = [
            'breakfast' => 'Breakfast',
            'lunch' => 'Lunch',
            'tiffin' => 'Tiffin',
            'dinner' => 'Dinner',
        ];
        $flatItems = [];

        foreach ($days as $dayIndex => $day) {
            $dayName = $day['name'] ?? 'Day ' . ($dayIndex + 1);
            $slots = $day['slots'] ?? [];
            foreach ($slotsOrder as $slotKey) {
                $slotData = $slots[$slotKey] ?? ['items' => []];
                $items = $slotData['items'] ?? [];
                foreach ($items as $item) {
                    $flatItems[] = [
                        'dayName' => $dayName,
                        'slotKey' => $slotKey,
                        'item' => $item,
                    ];
                }
            }
        }
    @endphp

    {{-- Top bar (hidden on print) --}}
    <div class="flex items-center justify-between gap-3 mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-semibold">
                Meal Booking #{{ $booking->booking_code }}
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-300">
                Placed: {{ $booking->created_at?->format('d M Y, h:i A') }}
            </p>
        </div>

        {{-- <div class="flex flex-col md:flex-row gap-2">
            <flux:button href="{{ route('meal-plan-bookings.details', $booking->booking_code) }}"
                icon="arrow-path" wire:navigate>
                Refresh
            </flux:button>

            <flux:button href="{{ route('mealBooking.index') }}" icon="arrow-uturn-left"
                wire:navigate>
                Back to list
            </flux:button>

            <flux:button type="button" icon="printer" class="cursor-pointer" onclick="window.print()">
                Print (A4)
            </flux:button>
        </div> --}}
        <div class="flex flex-col md:flex-row gap-2">
            <flux:button href="{{ route('meal-plan-bookings.details', $booking->booking_code) }}" icon="arrow-path"
                wire:navigate>
                Refresh
            </flux:button>

            <flux:button href="{{ route('mealBooking.index') }}" icon="arrow-uturn-left" wire:navigate>
                Back to list
            </flux:button>

            <flux:button type="button" icon="printer" class="cursor-pointer" onclick="window.print()">
                Print (A4)
            </flux:button>

            <flux:button type="button" icon="printer" variant="primary" class="cursor-pointer"
                onclick="window.open('{{ route('meal-plan-bookings.thermalPrint', $booking->booking_code) }}','_blank','noopener')">
                Thermal Print (80mm)
            </flux:button>

        </div>

    </div>

    {{-- Layout --}}
    <div class="grid md:grid-cols-3 gap-6" id="mpb-print">
        {{-- Left: Booking "invoice" --}}
        <div
            class="md:col-span-2 bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border dark:border-neutral-700 print:shadow-none print:border-0">

            {{-- Header --}}
            <div class="rounded-2xl border-b dark:border-neutral-700 px-6 py-5 bg-slate-50/60 dark:bg-neutral-700/40">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                    <div>
                        <h2 class="text-lg font-semibold">
                            Meal Booking
                        </h2>
                        <p class="text-xs text-slate-500 dark:text-slate-300 mt-0.5">
                            Booking code:
                            <span class="font-mono font-semibold">#{{ $booking->booking_code }}</span><br>
                            Plan type:
                            <span class="font-semibold">
                                {{ ucfirst($booking->plan_type) }}
                            </span>
                            @if ($booking->start_date)
                                <span class="text-slate-600">
                                    · starts {{ $booking->start_date->format('d M Y') }}
                                </span>
                            @endif
                        </p>
                    </div>

                    <div class="text-right space-y-1">
                        <p class="text-xs text-slate-500 dark:text-slate-300 uppercase tracking-[0.14em]">
                            Grand Total
                        </p>
                        <p class="text-2xl font-semibold text-slate-900 dark:text-slate-50">
                            {{ number_format($booking->grand_total, 2) }} ৳
                        </p>
                        <p class="text-[11px] text-slate-500 dark:text-slate-300">
                            Paid:
                            <span class="text-emerald-600 font-semibold">
                                {{ number_format($booking->pay_now, 2) }} ৳
                            </span>
                            · Due:
                            <span class="text-rose-500 font-semibold">
                                {{ number_format($booking->due_amount, 2) }} ৳
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- Customer / Booking info --}}
            <div class="px-6 py-5 grid sm:grid-cols-2 gap-4 text-sm">
                <div class="rounded-xl p-4 bg-slate-50 dark:bg-neutral-700/70">
                    <p class="font-semibold mb-2">Customer</p>
                    <div class="space-y-1.5 text-xs sm:text-sm">
                        <p>
                            <span class="font-medium">Name:</span>
                            {{ $booking->contact_name ?? 'Guest' }}
                        </p>
                        <p>
                            <span class="font-medium">Phone:</span>
                            {{ $booking->phone ?? '-' }}
                        </p>
                        <p>
                            <span class="font-medium">Email:</span>
                            {{ $booking->email ?? '-' }}
                        </p>
                        <p class="text-slate-600 dark:text-slate-300">
                            <span class="font-medium">Address:</span>
                            {{ $addressLine }}
                        </p>
                    </div>
                </div>

                <div class="rounded-xl p-4 bg-slate-50 dark:bg-neutral-700/70">
                    <p class="font-semibold mb-2">Booking Info</p>
                    <div class="space-y-1.5 text-xs sm:text-sm">
                        <p class="flex items-center gap-2">
                            <span class="font-medium">Status:</span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-semibold {{ $statusColor }}">
                                {{ ucfirst($booking->status) }}
                            </span>
                        </p>
                        <p class="flex items-center gap-2">
                            <span class="font-medium">Payment:</span>
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-semibold {{ $paymentPill }}">
                                {{ ucfirst($booking->payment_status) }}
                            </span>
                        </p>
                        <p>
                            <span class="font-medium">Payment method:</span>
                            {{ strtoupper($booking->payment_method) === 'COD' ? 'Cash on Delivery' : 'SSLCommerz' }}
                        </p>
                        <p>
                            <span class="font-medium">Payment option:</span>
                            {{ $booking->payment_option === 'half' ? '50% now, 50% later' : 'Full payment' }}
                        </p>
                        <p>
                            <span class="font-medium">Placed:</span>
                            {{ $booking->created_at?->format('d M Y, h:i A') }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- Items table (flat list with full meta + prices) --}}
            <div class="px-6 pb-6">
                <div class="mt-2 overflow-x-auto rounded-xl border dark:border-neutral-700">
                    <table class="w-full text-xs sm:text-sm">
                        <thead
                            class="text-left text-slate-600 dark:text-slate-300 bg-slate-50/70 dark:bg-neutral-700/60">
                            <tr>
                                <th class="py-3 px-3 sm:px-4">#</th>
                                <th class="py-3 px-3 sm:px-4">Day</th>
                                <th class="py-3 px-3 sm:px-4">Slot</th>
                                <th class="py-3 px-3 sm:px-4">Item & Options</th>
                                <th class="py-3 px-3 sm:px-4 text-right">Unit Price</th>
                                <th class="py-3 px-3 sm:px-4 text-center">Qty</th>
                                <th class="py-3 px-3 sm:px-4 text-right">Line Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y dark:divide-neutral-700 bg-white dark:bg-neutral-800/80">
                            @forelse ($flatItems as $index => $row)
                                @php
                                    $item = $row['item'];
                                    $dayName = $row['dayName'];
                                    $slotKey = $row['slotKey'];

                                    $dishId = $item['dish_id'] ?? null;
                                    $dish = $dishId ? $dishesById->get((int) $dishId) : null;
                                    $qty = (int) ($item['qty'] ?? 1);

                                    $variantInfo = $dish
                                        ? $this->getVariantInfo($dish, $item['variant_key'] ?? null)
                                        : ['label' => null, 'group' => null, 'price' => 0];
                                    $crustInfo = $dish
                                        ? $this->getCrustInfo($dish, $item['crust_key'] ?? null)
                                        : ['name' => null, 'price' => 0];
                                    $bunInfo = $dish
                                        ? $this->getBunInfo($dish, $item['bun_key'] ?? null)
                                        : ['name' => null, 'price' => 0];
                                    $addonInfos = $dish
                                        ? $this->getAddonInfo($dish, (array) ($item['addon_keys'] ?? []))
                                        : [];

                                    $basePrice = $dish ? (float) $dish->price_with_discount : 0;
                                    $unitPrice = $dish ? $this->calculateItemUnitPrice($dish, $item) : 0;
                                    $lineTotal = $unitPrice * $qty;
                                @endphp

                                <tr class="align-top hover:bg-slate-50/60 dark:hover:bg-neutral-700/50">
                                    <td class="py-3 px-3 sm:px-4 text-center align-middle">
                                        {{ $index + 1 }}
                                    </td>
                                    <td class="py-3 px-3 sm:px-4 text-xs align-middle whitespace-nowrap">
                                        {{ $dayName }}
                                    </td>
                                    <td class="py-3 px-3 sm:px-4 align-middle whitespace-nowrap">
                                        {{ $slotLabels[$slotKey] ?? ucfirst($slotKey) }}
                                    </td>
                                    <td class="py-3 px-3 sm:px-4">
                                        <div class="font-medium text-slate-900 dark:text-slate-50">
                                            {{ $dish?->title ?? 'Unknown item' }}
                                        </div>
                                        <div class="mt-1 space-y-0.5 text-[11px] text-slate-600 dark:text-slate-300">
                                            <div>
                                                <span class="font-medium">Base:</span>
                                                {{ number_format($basePrice, 2) }} ৳
                                            </div>

                                            @if ($variantInfo['label'])
                                                <div>
                                                    <span class="font-medium">
                                                        {{ $variantInfo['group'] ?? 'Variant' }}:
                                                    </span>
                                                    {{ $variantInfo['label'] }}
                                                    @if ($variantInfo['price'] != 0)
                                                        <span class="text-slate-400">
                                                            ({{ $variantInfo['price'] > 0 ? '+' : '' }}{{ number_format($variantInfo['price'], 2) }}
                                                            ৳)
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($crustInfo['name'])
                                                <div>
                                                    <span class="font-medium">Crust:</span>
                                                    {{ $crustInfo['name'] }}
                                                    @if ($crustInfo['price'] != 0)
                                                        <span class="text-slate-400">
                                                            ({{ $crustInfo['price'] > 0 ? '+' : '' }}{{ number_format($crustInfo['price'], 2) }}
                                                            ৳)
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if ($bunInfo['name'])
                                                <div>
                                                    <span class="font-medium">Bun:</span>
                                                    {{ $bunInfo['name'] }}
                                                    @if ($bunInfo['price'] != 0)
                                                        <span class="text-slate-400">
                                                            ({{ $bunInfo['price'] > 0 ? '+' : '' }}{{ number_format($bunInfo['price'], 2) }}
                                                            ৳)
                                                        </span>
                                                    @endif
                                                </div>
                                            @endif

                                            @if (!empty($addonInfos))
                                                <div>
                                                    <span class="font-medium">Add-ons:</span>
                                                    <ul class="ml-4 list-disc">
                                                        @foreach ($addonInfos as $ao)
                                                            <li>
                                                                {{ $ao['name'] }}
                                                                @if (($ao['price'] ?? 0) != 0)
                                                                    <span class="text-slate-400">
                                                                        (+{{ number_format($ao['price'], 2) }} ৳)
                                                                    </span>
                                                                @endif
                                                            </li>
                                                        @endforeach
                                                    </ul>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="py-3 px-3 sm:px-4 text-right align-middle whitespace-nowrap">
                                        {{ number_format($unitPrice, 2) }} ৳
                                    </td>
                                    <td class="py-3 px-3 sm:px-4 text-center align-middle">
                                        {{ $qty }}
                                    </td>
                                    <td class="py-3 px-3 sm:px-4 text-right align-middle whitespace-nowrap">
                                        <span class="font-semibold">
                                            {{ number_format($lineTotal, 2) }} ৳
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="py-4 px-4 text-center text-xs text-slate-500">
                                        No items found in this booking.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                {{-- Totals summary (bottom) --}}
                <div class="mt-4 flex flex-col items-end">
                    <div
                        class="w-full sm:w-80 rounded-xl border dark:border-neutral-700 p-4 bg-slate-50/60 dark:bg-neutral-700/40 text-sm">
                        <div class="flex justify-between py-1">
                            <span>Plan subtotal</span>
                            <span>{{ number_format($booking->plan_subtotal, 2) }} ৳</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span>Shipping</span>
                            <span>{{ number_format($booking->shipping_total, 2) }} ৳</span>
                        </div>
                        <div class="border-t border-slate-200 dark:border-neutral-600 my-2"></div>
                        <div class="flex justify-between py-1 text-base font-semibold">
                            <span>Total</span>
                            <span>{{ number_format($booking->grand_total, 2) }} ৳</span>
                        </div>
                        <div class="flex justify-between py-1 text-xs text-slate-500 dark:text-slate-300 mt-1">
                            <span>Paid now</span>
                            <span>{{ number_format($booking->pay_now, 2) }} ৳</span>
                        </div>
                        <div class="flex justify-between py-1 text-xs text-slate-500 dark:text-slate-300">
                            <span>Due amount</span>
                            <span>{{ number_format($booking->due_amount, 2) }} ৳</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: status / payment controls (hidden on print) --}}
        <div class="space-y-6 print:hidden">
            {{-- Payment toggle --}}
            <div class="rounded-2xl border dark:border-neutral-700 p-6 bg-slate-50/60 dark:bg-neutral-700/40">
                <h3 class="font-semibold mb-3">Payment</h3>

                <div class="flex items-center justify-between gap-4">
                    <div class="text-sm">
                        <div class="text-slate-600 dark:text-slate-300">Payment Status</div>
                        <div class="text-xs text-slate-500 dark:text-slate-400">
                            Toggle to mark as paid / pending.
                        </div>
                    </div>

                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" class="sr-only peer" wire:model.live="is_paid"
                            wire:change="savePaymentStatus">
                        <span
                            class="h-6 w-11 rounded-full bg-slate-300 peer-checked:bg-emerald-500 transition-colors
                                after:content-[''] after:absolute after:h-5 after:w-5 after:rounded-full after:bg-white
                                after:top-0.5 after:left-0.5 after:transition-transform
                                peer-checked:after:translate-x-5 relative"
                            aria-hidden="true"></span>
                        <span class="ml-3 text-sm">{{ $is_paid ? 'Paid' : 'Pending' }}</span>
                    </label>
                </div>
            </div>

            {{-- Booking status --}}
            <div class="rounded-2xl border dark:border-neutral-700 p-6 bg-slate-50/60 dark:bg-neutral-700/40">
                <h3 class="font-semibold mb-3">Booking Status</h3>

                <div class="space-y-3">
                    <label class="text-sm">Status</label>
                    <select wire:model.defer="status"
                        class="block w-full rounded-lg border dark:border-none dark:bg-neutral-700 p-2.5 text-sm focus:border-rose-400 focus:ring-rose-400">
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}">{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>

                    <flux:button wire:click="saveStatus" icon="check-circle" variant="primary"
                        class="relative cursor-pointer">
                        <span wire:loading.remove wire:target="saveStatus">Save Status</span>
                        <span wire:loading wire:target="saveStatus" class="inline-flex items-center gap-2">
                            <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="3" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z" />
                            </svg>
                            Saving…
                        </span>
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    {{-- Small loading popup only for status / payment actions --}}
    <div wire:loading wire:target="saveStatus,savePaymentStatus"
        class="fixed z-[60] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 print:hidden">
        <div class="w-56 rounded-2xl shadow-xl border bg-white/95 dark:bg-neutral-800/95 dark:text-white p-4">
            <div class="flex items-center gap-3">
                <svg class="size-5 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                        stroke-width="3" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z" />
                </svg>
                <span class="font-medium">Working…</span>
            </div>
        </div>
    </div>
</div>
