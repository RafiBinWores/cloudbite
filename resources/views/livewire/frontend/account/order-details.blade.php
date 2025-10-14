<div class="max-w-5xl mx-auto px-4 sm:px-6 py-10"
     x-data="{ showCancel: false }"
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
                <button
                    type="button"
                    @click="showCancel = !showCancel"
                    class="inline-flex items-center rounded-lg bg-red-600 text-white text-xs font-medium px-3 py-2 hover:bg-red-700"
                >
                    Cancel Order
                </button>
            @endif
        </div>
    </div>

    {{-- Cancel panel --}}
    @if ($this->isCancellable)
        <div x-show="showCancel" x-transition class="mt-4 rounded-xl border border-red-200 bg-red-50 p-4">
            <div class="flex items-start gap-3">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 text-red-600" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path d="M12 9v4m0 4h.01M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>

                <div class="flex-1">
                    <h3 class="font-semibold text-red-700">Cancel this order?</h3>
                    <p class="text-sm text-red-700/80 mt-0.5">
                        You can cancel while the order is not shipped/out for delivery. This action can’t be undone.
                    </p>

                    <form wire:submit.prevent="cancel" class="mt-3 space-y-3">
                        <label class="block text-sm text-slate-700">Reason (optional)</label>
                        <textarea
                            wire:model.defer="reason"
                            rows="3"
                            class="w-full textarea textarea-bordered"
                            placeholder="e.g., Placed by mistake / Need to change items / Wrong address"
                        ></textarea>
                        @error('reason') <p class="text-xs text-red-600 mt-1">{{ $message }}</p> @enderror

                        <div class="flex items-center gap-2">
                            <button
                                type="submit"
                                wire:loading.attr="disabled"
                                wire:target="cancel"
                                class="inline-flex items-center rounded-lg bg-red-600 text-white text-sm font-medium px-4 py-2 hover:bg-red-700 disabled:opacity-60"
                            >
                                <span wire:loading.remove wire:target="cancel">Confirm Cancel</span>
                                <span wire:loading wire:target="cancel">Cancelling…</span>
                            </button>

                            <button
                                type="button"
                                @click="showCancel = false"
                                class="inline-flex items-center rounded-lg bg-white border text-slate-700 text-sm font-medium px-4 py-2 hover:bg-slate-50"
                            >
                                Keep Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

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
                        <img src="{{ asset($item->dish->thumbnail) }}" alt="{{ $item->dish->title }}" class="size-16 object-cover rounded">
                        <div>
                            <div class="font-medium">{{ $item->dish->title ?? 'Dish #' . $item->dish_id }}</div>
                            <div class="text-sm text-slate-500">
                                Qty: {{ $item->qty }}
                                @if ($item->crust) • Crust: {{ $item->crust->name }} @endif
                                @if ($item->bun)   • Bun:   {{ $item->bun->name }}   @endif
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
                    <span class="font-semibold ml-2">Postal Code:</span> {{ data_get($order->shipping_address, 'postcode') }}
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
