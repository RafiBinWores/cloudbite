<div class="max-w-5xl mx-auto px-4 sm:px-6 py-10">
        <a href="{{ route('account.orders') }}" class="text-sm text-slate-600 hover:text-slate-900">&larr; Back to orders</a>

        <div class="mt-3 flex flex-col md:flex-row md:items-center md:justify-between gap-3">
            <div>
                <h1 class="text-2xl font-semibold">Order {{ $order->order_code }}</h1>
                <p class="text-sm text-slate-500">Placed: {{ $order->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                    {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                    Payment: {{ ucfirst($order->payment_status) }}
                </span>
                <span class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                    @class([
                      'bg-blue-100 text-blue-700' => in_array($order->order_status, ['pending','processing','confirmed','packed']),
                      'bg-indigo-100 text-indigo-700' => in_array($order->order_status, ['shipped','out_for_delivery']),
                      'bg-emerald-100 text-emerald-700' => in_array($order->order_status, ['delivered','completed']),
                      'bg-red-100 text-red-700' => in_array($order->order_status, ['cancelled','failed']),
                    ])">
                    {{ ucwords(str_replace('_',' ', $order->order_status)) }}
                </span>
            </div>
        </div>

        {{-- Summary cards --}}
        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="rounded-xl border p-4">
                <div class="text-sm text-slate-500">Subtotal</div>
                <div class="text-xl font-semibold">৳{{ number_format($order->subtotal, 2) }}</div>
            </div>
            <div class="rounded-xl border p-4">
                <div class="text-sm text-slate-500">Discount</div>
                <div class="text-xl font-semibold">- ৳{{ number_format($order->discount_total, 2) }}</div>
            </div>
            <div class="rounded-xl border p-4">
                <div class="text-sm text-slate-500">Shipping</div>
                <div class="text-xl font-semibold">৳{{ number_format($order->shipping_total, 2) }}</div>
            </div>
            <div class="rounded-xl border p-4">
                <div class="text-sm text-slate-500">Grand Total</div>
                <div class="text-xl font-semibold">৳{{ number_format($order->grand_total, 2) }}</div>
            </div>
        </div>

        {{-- Items --}}
        <div class="mt-8 rounded-xl border overflow-hidden">
            <div class="px-4 py-3 bg-slate-50 font-semibold">Items</div>
            <div class="divide-y">
                @foreach($order->items as $item)
                    <div class="p-4 flex items-start justify-between gap-4">
                        <div>
                            <div class="font-medium">{{ $item->dish->name ?? 'Dish #'.$item->dish_id }}</div>
                            <div class="text-sm text-slate-500">
                                Qty: {{ $item->qty }}
                                @if($item->crust) • Crust: {{ $item->crust->name }} @endif
                                @if($item->bun) • Bun: {{ $item->bun->name }} @endif
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
                    {{ data_get($order->shipping_address, 'line1') }}<br>
                    {{ data_get($order->shipping_address, 'city') }} {{ data_get($order->shipping_address, 'postcode') }}
                </div>
            </div>
            <div class="rounded-xl border p-4">
                <div class="font-semibold mb-2">Contact</div>
                <div class="text-sm">
                    {{ $order->contact_name }}<br>
                    {{ $order->phone }}<br>
                    {{ $order->email }}
                </div>
            </div>
        </div>

        @if($order->customer_note)
            <div class="mt-6 rounded-xl border p-4">
                <div class="font-semibold mb-1">Customer Note</div>
                <div class="text-sm text-slate-700">{{ $order->customer_note }}</div>
            </div>
        @endif
    </div>