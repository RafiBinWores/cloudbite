<div>
    @push('styles')
        <style>
            /* A4 page setup */
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
                #invoice-print,
                #invoice-print * {
                    visibility: visible !important;
                }
                #invoice-print {
                    position: absolute;
                    left: 0;
                    top: 0;
                    width: 100%;
                    /* fits within @page margin */
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

    {{-- Top bar (hidden on print) --}}
    <div class="flex items-center justify-between gap-3 mb-6 print:hidden">
        <div>
            <h1 class="text-2xl font-semibold">Invoice #{{ $order->order_code }}</h1>
            <p class="text-sm text-slate-500 dark:text-slate-300">
                Placed: {{ $order->created_at->format('d M Y, h:i A') }}
            </p>
        </div>

        <div class="flex items-center flex-col md:flex-row gap-2">
            <flux:button href="{{ route('orders.show', $order->order_code) }}" icon="arrow-path" wire:navigate>
                Refresh
            </flux:button>

            {{-- A4 / standard print --}}
            <flux:button type="button" onclick="window.print()" icon="printer" class="cursor-pointer">
                Print (A4)
            </flux:button>

            {{-- Thermal (80mm) --}}
            <flux:button type="button" icon="printer" variant="primary" class="cursor-pointer"
                onclick="window.open('{{ route('orders.print', $order->order_code) }}','_blank','noopener')">
                Thermal Print (80mm)
            </flux:button>
        </div>

    </div>

    {{-- Invoice layout --}}
    <div class="grid md:grid-cols-3 gap-6" id="invoice-print">
        {{-- Left: invoice details --}}
        <div
            class="md:col-span-2 bg-white dark:bg-neutral-800 rounded-2xl shadow-sm border dark:border-none print:shadow-none print:border-0">

            {{-- Brand header (logo centered above company name) --}}
            <div class="rounded-2xl border dark:border-neutral-700 p-6 bg-slate-50/60 dark:bg-neutral-700/40">
                <div class="flex flex-col items-center text-center">

                    @if ($businessSetting?->logo_dark)
                        <img src="{{ asset($businessSetting->logo_dark) }}" alt="Logo"
                            class="h-14 w-auto block dark:hidden">
                    @endif

                    @if ($businessSetting?->logo_light)
                        <img src="{{ asset($businessSetting->logo_light) }}" alt="Logo"
                            class="h-14 w-auto hidden dark:block">
                    @endif
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
                $addr = $order->shipping_address ?? null;
                if (is_string($addr)) {
                    $decoded = json_decode($addr, true);
                    $addr = json_last_error() === JSON_ERROR_NONE ? $decoded : $addr;
                }
                // Build a human-readable line
                $addressLine = '-';
                if (is_array($addr)) {
                    $addressLine = implode(
                        ', ',
                        array_filter([$addr['line1'] ?? null, $addr['city'] ?? null, $addr['postcode'] ?? null]),
                    );
                    $addressLine = $addressLine === '' ? '-' : $addressLine;
                } elseif (is_string($addr) && trim($addr) !== '') {
                    $addressLine = $addr; // fallback raw string
                }

                $status = (string) ($order->order_status ?? 'pending');
                $statusClasses =
                    [
                        'pending' => 'bg-amber-100 text-amber-700 dark:bg-amber-400/15 dark:text-amber-300',
                        'processing' => 'bg-blue-100 text-blue-700 dark:bg-blue-400/15 dark:text-blue-300',
                        'confirmed' => 'bg-sky-100 text-sky-700 dark:bg-sky-400/15 dark:text-sky-300',
                        'preparing' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-400/15 dark:text-indigo-300',
                        'out_for_delivery' =>
                            'bg-fuchsia-100 text-fuchsia-700 dark:bg-fuchsia-400/15 dark:text-fuchsia-300',
                        'delivered' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-400/15 dark:text-emerald-300',
                        'cancelled' => 'bg-rose-100 text-rose-700 dark:bg-rose-400/15 dark:text-rose-300',
                        'returned' => 'bg-orange-100 text-orange-700 dark:bg-orange-400/15 dark:text-orange-300',
                        'failed_to_deliver' => 'bg-red-100 text-red-700 dark:bg-red-400/15 dark:text-red-300',
                    ][$status] ?? 'bg-slate-100 text-slate-700 dark:bg-slate-400/15 dark:text-slate-300';
            @endphp

            {{-- Bill to / order info --}}
            <div class="mt-6 grid sm:grid-cols-2 gap-4 text-sm">
                <div class="rounded-xl p-4 bg-slate-50 dark:bg-neutral-700">
                    <p class="font-medium mb-1">Billed To</p>
                    <div class="space-y-1.5">
                        <p><span class="font-medium">Name:</span> {{ $order->contact_name ?? 'Guest' }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $order->phone ?? 'Guest' }}</p>
                        <p class="text-slate-600 dark:text-slate-300"><span class="font-medium">Address:</span>
                            {{ $addressLine }}</p>
                    </div>
                </div>
                <div class="rounded-xl p-4 bg-slate-50 dark:bg-neutral-700">
                    <p class="font-medium mb-1">Order Info</p>
                    <div class="space-y-1.5">
                        <p class="flex items-center gap-2">
                            <span class="font-medium">Status:</span>
                            <span
                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $statusClasses }}">
                                {{ str_replace('_', ' ', ucfirst($status)) }}
                            </span>
                        </p>
                        <p><span class="font-medium">Payment:</span> {{ strtoupper($order->payment_method ?? 'cod') }}
                        </p>
                        <p><span class="font-medium">Payment Status:</span>
                            {{ ucfirst($order->payment_status ?? '-') }}</p>
                        <p><span class="font-medium">Phone:</span> {{ $order->phone ?? '-' }}</p>
                        <p><span class="font-medium">Placed:</span>
                            {{ optional($order->created_at)->format('d M Y, h:i A') }}</p>
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
                                if (
                                    is_array($item->meta ?? null) &&
                                    isset($item->meta['addons']) &&
                                    is_array($item->meta['addons'])
                                ) {
                                    $metaAddons = $item->meta['addons']; // ['<id>' => ['price'=>..,'quantity'=>..]]
                                }
                                $addonsTotal = 0.0;
                            @endphp
                            <tr class="hover:bg-slate-50/50 dark:hover:bg-neutral-700/40">
                                <td class="py-3 px-4 align-top">
                                    <div class="font-medium">{{ $item->dish->title ?? ($item->dish->name ?? 'Dish') }}
                                    </div>
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
                                                            $aoName = $ao->name ?? ($ao->title ?? 'Add‑on');
                                                            $aoPrice =
                                                                (float) ($override['price'] ?? ($ao->price ?? 0));
                                                            $aoQty =
                                                                (int) ($override['quantity'] ??
                                                                    ($override['qty'] ?? 1));
                                                            $line = $aoPrice * $aoQty;
                                                            $addonsTotal += $line;
                                                        @endphp
                                                        <li>
                                                            {{ $aoName }} × {{ $aoQty }} —
                                                            {{ number_format($aoPrice, 2) }}
                                                            @if ($aoQty > 1 || $aoPrice > 0)
                                                                <span
                                                                    class="text-slate-400">({{ number_format($line, 2) }})</span>
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
                <div
                    class="w-full sm:w-96 rounded-xl border dark:border-neutral-700 p-4 bg-slate-50/60 dark:bg-neutral-700/40">
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
            {{-- Payment toggle --}}
            <div class="rounded-2xl border dark:border-neutral-700 p-6 bg-slate-50/60 dark:bg-neutral-700/40">
                <h3 class="font-semibold mb-3">Payment</h3>

                <div class="flex items-center justify-between gap-4">
                    <div class="text-sm">
                        <div class="text-slate-600 dark:text-slate-300">Payment Status</div>
                    </div>

                    {{-- Toggle (saves immediately) --}}
                    <label class="relative inline-flex items-center cursor-pointer select-none">
                        <input type="checkbox" class="sr-only peer" wire:model.live="is_paid"
                            wire:change="savePaymentStatus">
                        <span
                            class="h-6 w-11 rounded-full bg-slate-300 peer-checked:bg-emerald-500 transition-colors
               after:content-[''] after:absolute after:h-5 after:w-5 after:rounded-full after:bg-white
               after:top-0.5 after:left-0.5 after:transition-transform
               peer-checked:after:translate-x-5 relative"
                            aria-hidden="true"></span>
                        <span class="ml-3 text-sm">{{ $is_paid ? 'Paid' : 'Unpaid' }}</span>
                    </label>
                </div>
            </div>

            {{-- Assign delivery man --}}
            <div class="rounded-2xl border dark:border-neutral-700 p-6 bg-slate-50/60 dark:bg-neutral-700/40">
                <h3 class="font-semibold mb-3">Assign Deliveryman</h3>

                @if ($order->deliveryMan)
                    {{-- Currently assigned --}}
                    <div class="space-y-3">
                        <div class="flex items-center gap-3">
                            <div class="shrink-0 flex items-center justify-center">
                                <img src="{{ asset($order->deliveryMan->profile_image ?? '') }}" alt=""
                                    class="h-10 w-10 rounded-full object-cover">
                            </div>
                            <div class="text-sm space-y-1">
                                <p class="font-medium text-lg">
                                    {{ trim(($order->deliveryMan->first_name ?? '') . ' ' . ($order->deliveryMan->last_name ?? '')) }}
                                </p>
                                <div>
                                    <flux:link href="tel:{{ $order->deliveryMan->phone_number ?? '—' }}">{{ $order->deliveryMan->phone_number ?? '—' }}</flux:link>
                                </div>
                                <div>
                                    <flux:link href="mailto:{{ $order->deliveryMan->email }}">
                                    {{ $order->deliveryMan->email ?? '-' }}</flux:link>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col gap-2 pt-2">
                            <x-select wire:model.live="delivery_man_id" :options="$deliveryMenOptions" option-label="label"
                                option-value="id" option-avatar="avatar" placeholder="— Change delivery man —"
                                searchable
                                class="w-full rounded-lg !py-[9px] !bg-white/10
                           {{ $errors->has('delivery_man_id')
                               ? '!border-red-500 focus:!ring-red-500'
                               : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-rose-400' }}" />

                            <div class="flex gap-2 items-center">
                                <flux:button wire:click="assignDeliveryMan" icon="check-circle" variant="primary"
                                    class="whitespace-nowrap cursor-pointer">
                                    <span wire:loading.remove wire:target="assignDeliveryMan">Update</span>
                                    <span wire:loading wire:target="assignDeliveryMan" class="inline-flex gap-2">
                                        <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"
                                            aria-hidden="true">
                                            <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="3" />
                                            <path class="opacity-75" fill="currentColor"
                                                d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z" />
                                        </svg> Saving…
                                    </span>
                                </flux:button>

                                <flux:button wire:click="clearDeliveryMan" icon="x-circle"
                                    class="whitespace-nowrap cursor-pointer">
                                    Remove
                                </flux:button>
                            </div>
                        </div>
                    </div>
                @else
                    {{-- Not assigned yet --}}
                    <div class="space-y-3">
                        <label class="text-sm">Select delivery man</label>

                        <x-select wire:model.live="delivery_man_id" :options="$deliveryMenOptions" option-label="label"
                            option-value="id" option-avatar="avatar" placeholder="— Select —" searchable
                            class="w-full rounded-lg !py-[9px] !bg-white/10
                       {{ $errors->has('delivery_man_id')
                           ? '!border-red-500 focus:!ring-red-500'
                           : '!border-neutral-300 dark:!border-neutral-500 focus:!ring-rose-400' }}" />

                        <flux:button wire:click="assignDeliveryMan" icon="truck" variant="primary"
                            class="cursor-pointer">
                            <span wire:loading.remove wire:target="assignDeliveryMan">Assign</span>
                            <span wire:loading wire:target="assignDeliveryMan" class="inline-flex items-center gap-2">
                                <svg class="size-4 animate-spin" viewBox="0 0 24 24" fill="none"
                                    aria-hidden="true">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="3" />
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 0 1 8-8v3a5 5 0 0 0-5 5H4z" />
                                </svg> Saving…
                            </span>
                        </flux:button>
                    </div>
                @endif
            </div>



            {{-- Status updater --}}
            <div class="rounded-2xl border dark:border-neutral-700 p-6 bg-slate-50/60 dark:bg-neutral-700/40">
                <h3 class="font-semibold mb-3">Update Order Status</h3>
                <div class="space-y-3">
                    <label class="text-sm">Status</label>
                    {{-- Using defer to avoid requests while typing/selecting --}}
                    <select wire:model.defer="order_status"
                        class="block w-full rounded-lg border dark:border-none dark:bg-neutral-700 p-2.5 text-sm focus:border-rose-400 focus:ring-rose-400">
                        @foreach ($statuses as $s)
                            <option value="{{ $s }}">{{ str_replace('_', ' ', ucfirst($s)) }}</option>
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

            {{-- Cooking time --}}
            <div x-data="{
                status: @entangle('order_status'),
                get canEdit() { return this.status === 'preparing' }
            }"
                class="rounded-2xl border dark:border-neutral-700 p-6 bg-slate-50/60 dark:bg-neutral-700/40">
                <h3 class="font-semibold mb-3">Cooking Time</h3>

                <div class="space-y-4">
                    <div class="space-y-2">
                        <label class="text-sm">Minutes (0–600)</label>

                        <input type="number" min="0" max="600" wire:model.defer="cooking_time_min"
                            x-model.number="minutes" x-bind:disabled="!canEdit"
                            class="block w-full rounded-lg border dark:border-none dark:bg-neutral-700 p-2.5 text-sm
                    focus:border-rose-400 focus:ring-rose-400
                    disabled:opacity-60 disabled:cursor-not-allowed"
                            placeholder="e.g., 25" />

                        <p class="text-xs text-slate-500 dark:text-slate-300" x-show="!canEdit">
                            You can set cooking time only when status is <span class="font-medium">preparing</span>.
                        </p>
                    </div>

                    {{-- Countdown UI (kept under wire:ignore) --}}
                    <div x-data="cookTimer({
                        initialMinutes: @js($cooking_time_min ?? 0),
                        endAtMs: @js($cooking_end_at_ms ?? null) {{-- persisted anchor --}}
                    })" x-init="if (endMs) start()" wire:ignore class="flex flex-col gap-2">
                        <div class="flex items-center justify-between text-sm">
                            <span class="text-slate-600 dark:text-slate-300">Remaining</span>
                            <span class="font-medium tabular-nums" x-text="endMs ? format() : '—'"></span>
                        </div>

                        <div class="h-2 w-full rounded-full bg-slate-200 dark:bg-neutral-600 overflow-hidden">
                            <div class="h-full bg-rose-500 transition-[width] duration-300"
                                :style="`width: ${progress()}%`"></div>
                        </div>

                        <div class="text-xs text-slate-500 dark:text-slate-300">
                            <template x-if="endMs">
                                <span>ETA: <span
                                        x-text="new Date(endMs).toLocaleTimeString([], {hour:'2-digit', minute:'2-digit'})"></span></span>
                            </template>
                            <template x-if="!endMs">
                                <span>Set minutes and click “Save Cooking Time” to start.</span>
                            </template>
                        </div>
                    </div>

                    {{-- IMPORTANT: use x-bind:disabled on Blade component --}}
                    <flux:button wire:click="saveCookingTime" icon="check-circle" variant="primary"
                        x-bind:disabled="!canEdit"
                        class="disabled:opacity-60 disabled:cursor-not-allowed cursor-pointer">
                        <span wire:loading.remove wire:target="saveCookingTime">Save Cooking Time</span>
                        <span wire:loading wire:target="saveCookingTime" class="inline-flex items-center gap-2">
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

    {{-- Loading popup — now scoped ONLY to button actions (won’t appear while typing/selecting) --}}
    <div wire:loading wire:target="saveStatus,saveCookingTime"
        class="fixed z-[60] left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2">
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

    @push('scripts')
        <script>
            function cookTimer({
                initialMinutes = 0,
                endAtMs = null
            }) {
                return {
                    minutes: initialMinutes,
                    endMs: endAtMs,
                    remainingSec: 0,
                    _id: null,

                    start() {
                        if (!this.endMs && this.minutes > 0) {
                            this.endMs = Date.now() + (this.minutes * 60_000);
                        }
                        this._tick();
                        if (this._id) clearInterval(this._id);
                        this._id = setInterval(() => this._tick(), 1000);
                    },

                    _tick() {
                        if (!this.endMs) {
                            this.remainingSec = 0;
                            return;
                        }
                        const diff = Math.max(0, Math.floor((this.endMs - Date.now()) / 1000));
                        this.remainingSec = diff;
                        if (diff <= 0 && this._id) {
                            clearInterval(this._id);
                            this._id = null;
                        }
                    },

                    format() {
                        const m = Math.floor(this.remainingSec / 60);
                        const s = this.remainingSec % 60;
                        return `${m.toString().padStart(2,'0')}:${s.toString().padStart(2,'0')}`;
                    },

                    progress() {
                        const total = Math.max(1, Math.floor(this.minutes * 60));
                        const remaining = Math.max(0, Math.min(total, this.remainingSec));
                        return Math.round((remaining / total) * 100);
                    }

                }
            }

            window.addEventListener('cooking-time:started', (e) => {
                const detail = e.detail || {};
                const end = detail.end_at_ms;
                if (!end) return;

                document.querySelectorAll('[x-data^="cookTimer"]').forEach(el => {
                    const st = Alpine.$data(el);
                    st.endMs = end;
                    // recompute minutes from server ETA to keep progress correct
                    st.minutes = Math.max(0, Math.round((end - Date.now()) / 60_000));
                    st.start();
                });
            });
        </script>
    @endpush
</div>
