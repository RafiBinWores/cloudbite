<div class="max-w-5xl mx-auto px-4 sm:px-6 py-10" x-data="{ showCancel: false }"
    x-on:close-cancel-panel.window="showCancel = false">

    <a href="{{ route('account.orders') }}" class="text-sm text-slate-600 hover:text-slate-900">&larr; Back to orders</a>

    <div class="mt-3 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-semibold">Order {{ $order->order_code }}</h1>
            <p class="text-sm text-slate-500">Placed: {{ $order->created_at->format('d M Y, h:i A') }}</p>
        </div>

        <div class="flex items-center gap-2">
            <span
                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                Payment: {{ ucfirst($order->payment_status) }}
            </span>

            @php
                $statusClass = match ($order->order_status) {
                    'pending', 'processing', 'confirmed', 'packed' => 'bg-blue-100 text-blue-700',
                    'shipped', 'out_for_delivery' => 'bg-indigo-100 text-indigo-700',
                    'delivered', 'completed' => 'bg-emerald-100 text-emerald-700',
                    'cancelled', 'failed' => 'bg-red-100 text-red-700',
                    default => 'bg-gray-100 text-gray-700',
                };
            @endphp

            <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium {{ $statusClass }}">
                Status: {{ ucwords(str_replace('_', ' ', $order->order_status)) }}
            </span>

            {{-- Toggle button visible only when cancellable --}}
            @if ($this->isCancellable)
                <button type="button" @click="showCancel = !showCancel"
                    class="inline-flex items-center rounded-lg bg-red-600 text-white text-xs font-medium px-3 py-2 hover:bg-red-700 cursor-pointer">
                    Cancel Order
                </button>
            @endif
        </div>
    </div>

    {{-- Cancel panel --}}
    @if ($this->isCancellable)
        <div x-show="showCancel" x-transition class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4">
            <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 text-red-600" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor">
                    <path d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>

                <div class="flex-1">
                    <h3 class="font-semibold text-red-700">Cancel this order?</h3>
                    <p class="text-sm text-red-700/80 mt-0.5">
                        You can cancel while the order is not shipped/out for delivery. This action can’t be undone.
                    </p>

                    <form wire:submit.prevent="cancel" class="mt-3 space-y-3">
                        <label class="block text-sm text-slate-700">Reason (optional)</label>
                        <textarea wire:model.defer="reason" rows="3" class="w-full textarea textarea-bordered"
                            placeholder="e.g., Placed by mistake / Need to change items / Wrong address"></textarea>
                        @error('reason')
                            <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center gap-2">
                            <button type="submit" wire:loading.attr="disabled" wire:target="cancel"
                                class="inline-flex items-center rounded-lg bg-red-600 text-white text-sm font-medium px-4 py-2 hover:bg-red-700 disabled:opacity-60 cursor-pointer">
                                <span wire:loading.remove wire:target="cancel">Confirm Cancel</span>
                                <span wire:loading wire:target="cancel">Cancelling…</span>
                            </button>

                            <button type="button" @click="showCancel = false"
                                class="inline-flex items-center cursor-pointer rounded-lg bg-white border text-slate-700 text-sm font-medium px-4 py-2 hover:bg-slate-50">
                                Keep Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    @php
    $status = $order->order_status;

    // Progress steps (includes PREPARING)
    $steps = [
        'pending'           => ['label' => 'Order Placed',     'icon' => '📦'],
        'processing'        => ['label' => 'Processing',       'icon' => '⚡'],
        'confirmed'         => ['label' => 'Confirmed',        'icon' => '✅'],
        'preparing'         => ['label' => 'Preparing',        'icon' => '🍳'],
        'out_for_delivery'  => ['label' => 'Out for Delivery', 'icon' => '🏍️'],
        'delivered'         => ['label' => 'Delivered',        'icon' => '🎉'],
    ];
    $failedStatuses = ['cancelled','returned','failed_to_deliver'];
    $keys = array_keys($steps);
    $currentStepIndex = in_array($status, $failedStatuses, true) ? -1 : array_search($status, $keys, true);
    if ($currentStepIndex === false) $currentStepIndex = 0;
@endphp

<div class="mt-8 rounded-xl border overflow-hidden"
     wire:poll.20s="refreshOrder"
     wire:key="order-{{ $order->id }}-eta"
     x-data="{

        startAt: {{ $etaStartAtMs ? (int) $etaStartAtMs : 'null' }},
        endAt:   {{ $etaEndAtMs   ? (int) $etaEndAtMs   : 'null' }},
        status: @js($status),
        dist: {{ $distanceKm ? number_format($distanceKm, 1, '.', '') : 'null' }},

        now: Date.now(),
        _timer: null,

        minsLeft(ms) {
            if (!ms) return null;
            return Math.max(0, Math.round((ms - this.now) / 60000));
        },

        label() {
            if (this.status === 'delivered') return 'Delivered';
            if (['cancelled','returned','failed_to_deliver'].includes(this.status)) return '';

            const lo = this.minsLeft(this.startAt);
            const hi = this.minsLeft(this.endAt);

            if (lo === null || hi === null) return 'Calculating…';
            if (hi <= 0) return 'Arriving now';
            if (lo >= hi) return `${hi} min`;
            return `${lo}–${hi} min`;
        },

        init() {
            // kick immediately
            this.now = Date.now();
            if (this._timer) clearInterval(this._timer);

            // update clock every 30s so label recomputes
            this._timer = setInterval(() => {
                this.now = Date.now();

                // stop ticking on terminal states
                if (['delivered','cancelled','returned','failed_to_deliver'].includes(this.status)) {
                    clearInterval(this._timer);
                }
            }, 30000);
        }
     }"
>
    <div class="px-4 py-3 bg-slate-50 font-semibold">Delivery Information</div>

    <div class="p-4">
        <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            {{-- Progress Steps (hide for failed/cancelled/returned) --}}
            <div class="flex-1">
                <div class="flex items-center justify-between mb-2">
                    @if(!in_array($status, $failedStatuses, true))
                        @foreach($steps as $st => $step)
                            @php
                                $stepIndex = array_search($st, $keys, true);
                                $isCompleted = $stepIndex <= $currentStepIndex;
                                $isCurrent   = $stepIndex === $currentStepIndex;
                            @endphp

                            <div class="flex flex-col items-center text-center" style="width: {{ 100 / count($steps) }}%">
                                <div class="flex flex-col items-center">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-sm
                                        {{ $isCompleted ? 'bg-customRed-100 text-white' : 'bg-gray-200 text-gray-500' }}
                                        {{ $isCurrent ? 'ring-2 ring-customRed-100 ring-offset-2' : '' }}">
                                        {{ $step['icon'] }}
                                    </div>
                                    <span class="text-xs mt-1 {{ $isCompleted ? 'text-customRed-100 font-medium' : 'text-gray-500' }}">
                                        {{ $step['label'] }}
                                    </span>
                                </div>
                            </div>

                            @if(!$loop->last)
                                <div class="flex-1 h-1 mx-1 {{ $stepIndex < $currentStepIndex ? 'bg-customRed-100' : 'bg-gray-200' }}"></div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- ETA & Status Display --}}
            <div class="bg-red-50 border border-red-100 rounded-lg p-4 min-w-[220px]">
                @switch($status)
                    @case('delivered')
                        <div class="text-sm text-red-700 font-medium mb-1">Delivery Status</div>
                        <div class="text-lg font-semibold text-green-600">Delivered</div>
                        <div class="text-xs text-gray-500">
                            {{ $order->updated_at->format('M j, Y \a\t g:i A') }}
                        </div>
                    @break

                    @case('out_for_delivery')
                    @case('preparing')
                    @case('confirmed')
                    @case('processing')
                    @case('pending')
                        <div class="text-sm text-red-700 font-medium mb-1">Estimated Arrival</div>
                        <div class="text-lg font-semibold text-customRed-100" x-text="label()"></div>
                        <div class="text-xs text-gray-500" x-show="dist">
                            ~<span x-text="dist"></span> km away • live ETA
                        </div>
                        @if($status === 'out_for_delivery')
                            <div class="mt-2 text-xs text-green-600 font-medium">
                                🏍️ Your order is with our delivery partner
                            </div>
                        @elseif($status === 'preparing')
                            <div class="mt-2 text-xs text-blue-600">🍳 Your order is being prepared</div>
                        @elseif($status === 'confirmed')
                            <div class="mt-2 text-xs text-blue-600">✅ Order confirmed and queued for kitchen</div>
                        @elseif($status === 'processing')
                            <div class="mt-2 text-xs text-blue-600">⚡ We’re processing your order</div>
                        @endif
                    @break

                    @default
                        {{-- cancelled | returned | failed_to_deliver --}}
                        <div class="text-sm text-red-700 font-medium mb-1">Order Status</div>
                        <div class="text-lg font-semibold text-red-600">
                            {{ ucwords(str_replace('_',' ', $status)) }}
                        </div>
                        <div class="text-xs text-gray-500 mt-1">
                            @if($status === 'cancelled')
                                Order was cancelled
                            @elseif($status === 'returned')
                                Item was returned
                            @else
                                Failed to deliver
                            @endif
                        </div>
                @endswitch
            </div>
        </div>
    </div>
</div>



    {{-- Summary cards --}}
    <div class="mt-6 grid grid-cols-2 md:grid-cols-4 gap-4">
        <div class="rounded-xl shadow p-4 bg-red-500/10">
            <div class="text-sm text-slate-500 font-medium">Subtotal</div>
            <div class="text-xl font-semibold text-customRed-100">
                <span class="font-oswald">৳</span>{{ number_format($order->subtotal, 2) }}
            </div>
        </div>
        <div class="rounded-xl shadow p-4 bg-red-500/10">
            <div class="text-sm text-slate-500 font-medium">Discount</div>
            <div class="text-xl font-semibold text-customRed-100">
                - <span class="font-oswald">৳</span>{{ number_format($order->discount_total, 2) }}
            </div>
        </div>
        <div class="rounded-xl shadow p-4 bg-red-500/10">
            <div class="text-sm text-slate-500 font-medium">Shipping</div>
            <div class="text-xl font-semibold text-customRed-100">
                <span class="font-oswald">৳</span>{{ number_format($order->shipping_total, 2) }}
            </div>
        </div>
        <div class="rounded-xl shadow p-4 bg-red-500/10">
            <div class="text-sm text-slate-500 font-medium">Grand Total</div>
            <div class="text-xl font-semibold text-customRed-100">
                <span class="font-oswald">৳</span>{{ number_format($order->grand_total, 2) }}
            </div>
        </div>
    </div>

    {{-- Items --}}
    <div class="mt-8 rounded-xl border overflow-hidden">
        <div class="px-4 py-3 bg-slate-50 font-semibold">Item Info</div>
        <div class="divide-y">
            @foreach ($order->items as $item)
                <div class="p-4 flex items-start justify-between gap-4">
                    <div class="flex items-center gap-3">
                        <img src="{{ asset($item->dish->thumbnail) }}" alt="{{ $item->dish->title }}"
                            class="size-16 object-cover rounded">
                        <div>
                            <div class="font-medium">{{ $item->dish->title ?? 'Dish #' . $item->dish_id }}</div>
                            <div class="text-sm text-slate-500">
                                Qty: {{ $item->qty }}
                                @if ($item->crust)
                                    • Crust: {{ $item->crust->name }}
                                @endif
                                @if ($item->bun)
                                    • Bun: {{ $item->bun->name }}
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm text-slate-500">Line total</div>
                        <div class="font-semibold">৳{{ number_format($item->line_total, 2) }}</div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    {{-- Shipping & Contact --}}
    <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="rounded-xl border p-4">
            <div class="font-semibold mb-2">Shipping Address</div>
            <div class="text-sm">
                <p><span class="font-semibold">Address:</span> {{ data_get($order->shipping_address, 'line1') }}</p>
                <p>
                    <span class="font-semibold">City:</span> {{ data_get($order->shipping_address, 'city') }}
                    <span class="font-semibold ml-2">Postal Code:</span>
                    {{ data_get($order->shipping_address, 'postcode') }}
                </p>
            </div>
        </div>
        <div class="rounded-xl border p-4">
            <div class="font-semibold mb-2">Contact</div>
            <div class="text-sm">
                <p><span class="font-semibold">Name:</span> {{ $order->contact_name }}</p>
                <p><span class="font-semibold">Phone:</span> {{ $order->phone }}</p>
                <p><span class="font-semibold">Email:</span> {{ $order->email }}</p>
            </div>
        </div>
    </div>

    @if ($order->customer_note)
        <div class="mt-6 rounded-xl border p-4">
            <div class="font-semibold mb-1">Customer Note</div>
            <div class="text-sm text-slate-700">{{ $order->customer_note }}</div>
        </div>
    @endif
</div>
