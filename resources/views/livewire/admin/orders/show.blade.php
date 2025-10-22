<div>
    {{-- Top bar (hidden on print) --}}
    <div class="flex items-center justify-between gap-3 mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-semibold">Invoice #{{ $order->order_code }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-300">
                Placed: {{ $order->created_at->format('d M Y, h:i A') }}
            </p>
        </div>

        <div class="flex items-center gap-2">
            <flux:button href="{{ route('orders.show', $order->order_code) }}" wire:navigate>
                Refresh
            </flux:button>
            <flux:button onclick="window.print()" icon="printer">Print</flux:button>
        </div>
    </div>

    {{-- Invoice layout --}}
    <div class="grid md:grid-cols-3 gap-6">
        {{-- Left: invoice details --}}
        <div class="md:col-span-2 bg-white dark:bg-neutral-800 rounded-2xl p-6 shadow-sm border dark:border-none print:shadow-none print:border-0">

            {{-- Brand header (logo centered above company name) --}}
            <div class="rounded-2xl border dark:border-neutral-700 p-6 bg-slate-50/60 dark:bg-neutral-700/40">
                <div class="flex flex-col items-center text-center">
                    <img src="{{ asset($businessSetting->logo) }}" alt="{{ $businessSetting->company_name }}" class="h-14 w-auto mb-2" />
                    <h2 class="text-xl font-semibold tracking-wide">{{ $businessSetting->company_name }}</h2>
                    <p class="text-sm text-slate-600 dark:text-slate-300">
                        {{ $businessSetting->address ?? 'Address' }}
                    </p>
                </div>

                <div class="mt-4 grid grid-cols-2 gap-4 text-sm">
                    <div class="space-y-1">
                        <p class="uppercase text-xs tracking-wide text-slate-500 dark:text-slate-300">Invoice No.</p>
                        <p class="font-semibold">{{ $order->order_code }}</p>
                    </div>
                    <div class="space-y-1 text-right">
                        <p class="uppercase text-xs tracking-wide text-slate-500 dark:text-slate-300">Placed</p>
                        <p class="font-semibold">{{ optional($order->created_at)->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            </div>

            @php
                // Accept both JSON string or array (depending on how it's stored/cast)
                $addr = $order->shipping_address ?? null;
                if (is_string($addr)) {
                    $decoded = json_decode($addr, true);
                    $addr = json_last_error() === JSON_ERROR_NONE ? $decoded : $addr;
                }
                // Build a human-readable line
                $addressLine = '-';
                if (is_array($addr)) {
                    $addressLine = implode(', ', array_filter([
                        $addr['line1'] ?? null,
                        $addr['city'] ?? null,
                        $addr['postcode'] ?? null,
                    ]));
                    $addressLine = $addressLine === '' ? '-' : $addressLine;
                } elseif (is_string($addr) && trim($addr) !== '') {
                    $addressLine = $addr; // fallback raw string
                }

                // status badge color map
                $status = (string)($order->order_status ?? 'pending');
                $statusClasses = [
                    'pending'            => 'bg-amber-100 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300',
                    'processing'         => 'bg-blue-100 text-blue-700 dark:bg-blue-400/15 dark:text-blue-300',
                    'confirmed'          => 'bg-sky-100 text-sky-700 dark:bg-sky-400/15 dark:text-sky-300',
                    'preparing'          => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-400/15 dark:text-indigo-300',
                    'out_for_delivery'   => 'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-400/15 dark:text-fuchsia-300',
                    'delivered'          => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300',
                    'cancelled'          => 'bg-rose-100 text-rose-700 dark:bg-rose-400/15 dark:text-rose-300',
                    'returned'           => 'bg-orange-100 text-orange-700 dark:bg-orange-400/15 dark:text-orange-300',
                    'failed_to_deliver'  => 'bg-red-100 text-red-700 dark:bg-red-400/15 dark:text-red-300',
                ][$status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-400/15 dark:text-slate-300';
            @endphp

            {{-- Bill to / order info --}}
            <div class="mt-6 grid sm:grid-cols-2 gap-4 text-sm">
                <div class="rounded-xl p-4 bg-slate-50 dark:bg-neutral-700">
                    <p class="font-medium mb-1">Billed To</p>
                    <div class="space-y-1.5">
                        <p><span class="font-medium">Name:</span> {{ $order->contact_name ?? 'Guest' }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $order->phone ?? 'Guest' }}</p>
                        <p class="text-slate-600 dark:text-slate-300"><span class="font-medium">Address:</span> {{ $addressLine }}</p>
                    </div>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 dark:bg-neutral-700">
                    <p class="font-medium mb-1">Order Info</p>
                    <div class="space-y-1.5">
                        <p class="flex items-center gap-2">
                            <span class="font-medium">Status:</span>
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClasses }}">
                                {{ str_replace('_', ' ', ucfirst($status)) }}
                            </span>
                        </p>
                        <p><span class="font-medium">Payment:</span> {{ ucfirst($order->payment_method ?? 'cod') }}</p>
                        <p><span class="font-medium">Payment Status:</span> {{ ucfirst($order->payment_status ?? '-') }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $order->phone ?? '-' }}</p>
                        <p><span class="font-medium">Placed:</span> {{ optional($order->created_at)->format('d M Y, h:i A') }}</p>
                    </div>
                </div>
            </div>

            {{-- Items table --}}
            <div class="mt-6 overflow-x-auto rounded-xl border dark:border-neutral-700">
                <table class="w-full text-sm">
                    <thead class="text-left text-slate-600 dark:text-slate-300 bg-slate-50/70 dark:bg-neutral-700/50">
                        <tr>
                            <th class="py-3 px-4">Item</th>
                            <th class="py-3 px-4">Qty</th>
                            <th class="py-3 px-4">Unit</th>
                            <th class="py-3 px-4 text-right">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y dark:divide-neutral-700">
                        @foreach ($order->items as $item)
                            @php
                                $qty = $item->qty ?? ($item->quantity ?? 1);
                                $unit = (float) ($item->unit_price ?? 0);
                                // Resolve add-ons by IDs
                                $ids = is_array($item->addon_ids ?? null) ? $item->addon_ids : [];
                                $itemAddons = collect($ids)->map(fn($id) => $addons[$id] ?? null)->filter();
                                // Optional per-item addon meta
                                $metaAddons = [];
                                if (is_array($item->meta ?? null) && isset($item->meta['addons']) && is_array($item->meta['addons'])) {
                                    $metaAddons = $item->meta['addons']; // ['<id>' => ['price'=>..,'quantity'=>..]]
                                }
                                $addonsTotal = 0.0;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-neutral-700/40">
                                <td class="py-3 px-4 align-top">
                                    <div class="font-medium">{{ $item->dish->title ?? ($item->dish->name ?? 'Dish') }}</div>
                                    <div class="text-xs text-slate-500 space-y-0.5">
                                        @if ($item->crust)
                                            <div><span class="font-medium">Crust:</span> {{ $item->crust->name }}</div>
                                        @endif
                                        @if ($item->bun)
                                            <div><span class="font-medium">Bun:</span> {{ $item->bun->name }}</div>
                                        @endif
                                        @if ($itemAddons->isNotEmpty())
                                            <div class="mt-1">
                                                <span class="font-medium">Add‑ons:</span>
                                                <ul class="ml-4 list-disc">
                                                    @foreach ($itemAddons as $ao)
                                                        @php
                                                            $override = $metaAddons[$ao->id] ?? null;
                                                            $aoName   = $ao->name ?? ($ao->title ?? 'Add‑on');
                                                            $aoPrice  = (float) ($override['price'] ?? ($ao->price ?? 0));
                                                            $aoQty    = (int) ($override['quantity'] ?? ($override['qty'] ?? 1));
                                                            $line     = $aoPrice * $aoQty;
                                                            $addonsTotal += $line;
                                                        @endphp
                                                        <li>
                                                            {{ $aoName }} × {{ $aoQty }} — {{ number_format($aoPrice, 2) }}
                                                            @if ($aoQty > 1 || $aoPrice > 0)
                                                                <span class="text-slate-400">({{ number_format($line, 2) }})</span>
                                                            @endif
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="py-3 px-4">{{ $qty }}</td>
                                <td class="py-3 px-4">{{ number_format($unit, 2) }}</td>
                                @php
                                    $computedLine = $unit * $qty + $addonsTotal;
                                    $lineTotal = isset($item->line_total) ? (float) $item->line_total : $computedLine;
                                @endphp
                                <td class="py-3 px-4 text-right font-medium">{{ number_format($lineTotal, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- Totals --}}
            <div class="mt-5 flex flex-col items-end">
                <div class="w-full sm:w-96 rounded-xl border dark:border-neutral-700 p-4 bg-slate-50/60 dark:bg-neutral-700/40">
                    <div class="flex justify-between py-1 text-sm">
                        <span>Subtotal</span><span>{{ number_format($order->subtotal, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-1 text-sm">
                        <span>Delivery</span><span>(+) {{ number_format($order->shipping_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-1 text-sm">
                        <span>Discount</span><span>(-) {{ number_format($order->discount_total, 2) }}</span>
                    </div>
                    <div class="flex justify-between py-2 text-base font-semibold border-t mt-2">
                        <span>Total</span><span>{{ number_format($order->grand_total, 2) }}</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right: controls (hidden on print) --}}
        <div class="space-y-6 print:hidden">
            {{-- Status updater --}}
            <div class="bg-white dark:bg-neutral-800 rounded-2xl p-5 shadow-sm border dark:border-none">
                <h3 class="font-semibold mb-3">Update Order Status</h3>
                <div class="space-y-3">
                    <label class="text-sm">Status</label>
                    {{-- Using defer to avoid requests while typing/selecting --}}
                    <select wire:model.defer="order_status" class="block w-full rounded-lg border dark:border-none dark:bg-neutral-700 p-2.5 text-sm focus:border-rose-400 focus:ring-rose-400">
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}">{{ str_replace('_', ' ', ucfirst($s)) }}</option>
                        @endforeach
                    </select>

                    <flux:button wire:click="saveStatus" icon="check-circle" variant="primary" class="relative">
                        <span wire:loading.remove wire:target="saveStatus">Save Status</span>
                        <span wire:loading wire:target="saveStatus" class="inline-flex items-center gap-2">
                            <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z"/></svg>
                            Saving…
                        </span>
                    </flux:button>
                </div>
            </div>

            {{-- Cooking time --}}
            <div class="bg-white dark:bg-neutral-800 rounded-2xl p-5 shadow-sm border dark:border-none">
                <h3 class="font-semibold mb-3">Cooking Time</h3>
                <div class="space-y-3">
                    <label class="text-sm">Minutes (0–600)</label>
                    {{-- Use defer to stop triggering requests on each keypress --}}
                    <input type="number" min="0" max="600" wire:model.defer="cooking_time_min" class="block w-full rounded-lg border dark:border-none dark:bg-neutral-700 p-2.5 text-sm focus:border-rose-400 focus:ring-rose-400" placeholder="e.g., 25" />

                    <flux:button wire:click="saveCookingTime" variant="primary">
                        <span wire:loading.remove wire:target="saveCookingTime">Save Cooking Time</span>
                        <span wire:loading wire:target="saveCookingTime" class="inline-flex items-center gap-2">
                            <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z"/></svg>
                            Saving…
                        </span>
                    </flux:button>
                </div>
            </div>
        </div>
    </div>

    {{-- Loading popup — now scoped ONLY to button actions (won’t appear while typing/selecting) --}}
    <div wire:loading wire:target="saveStatus,saveCookingTime" class="fixed z-[60] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
        <div class="w-56 rounded-2xl shadow-xl border bg-white/95 dark:bg-neutral-800/95 dark:text-white p-4">
            <div class="flex items-center gap-3">
                <svg class="size-5 animate-spin" viewBox="0 0 24 24" fill="none">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="3" />
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z" />
                </svg>
                <span class="font-medium">Working…</span>
            </div>
        </div>
    </div>

    {{-- Print tweaks --}}
    <style>
        @media print {
            .print\:hidden { display: none !important; }
            body { background: #fff !important; }
        }
    </style>
</div>
