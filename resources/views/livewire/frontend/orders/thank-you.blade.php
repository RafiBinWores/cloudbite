<x-layouts.frontend>
    <div class="max-w-3xl mx-auto px-4 sm:px-6 py-16 text-center">

        <div class="mx-auto mb-6 grid place-items-center">
            <div class="size-14 rounded-full bg-customRed-100/15 grid place-items-center">
                <svg xmlns="http://www.w3.org/2000/svg" class="size-7 text-customRed-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                    <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5"/>
                </svg>
            </div>
        </div>

        <h1 class="text-3xl md:text-4xl font-semibold mb-2">Thank you for your purchase</h1>
        <p class="text-neutral-600">
            We’ve received your order. It will take 30-45min to deliver.
            <br>
            Your order number is <span class="font-semibold">#{{ $order->order_code }}</span>
        </p>

        <div class="mt-8 mx-auto w-full">
            <div class="mx-auto max-w-2xl rounded-2xl border bg-white text-left shadow-sm">
                <div class="px-6 py-4 border-b">
                    <h2 class="text-lg font-semibold">Order Summary</h2>
                </div>

                <div class="divide-y">
                    @foreach ($order->items as $item)
                        <div class="px-6 py-4 flex items-center gap-4">
                            {{-- Thumb from related dish (fallback placeholder) --}}
                            <img
                                class="w-16 h-16 rounded-lg object-cover"
                                src="{{ asset($item->dish?->thumbnail ?? 'https://placehold.co/80x80') }}"
                                alt="{{ $item->dish?->title ?? 'Item' }}"
                            />

                            <div class="flex-1 min-w-0">
                                <p class="font-medium truncate">
                                    {{ $item->dish?->title ?? 'Item' }}
                                </p>
                                <p class="text-sm text-neutral-500">
                                    Qty: {{ $item->qty }}
                                </p>
                            </div>

                            <div class="text-right font-medium whitespace-nowrap">
                                {{ number_format($item->line_total, 2) }} <span class="font-oswald">৳</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="px-6 pt-4 flex items-center justify-between">
                    <span class="font-semibold">Delivery Charge</span>
                    <span class="font-medium">
                        (+) {{ number_format($order->shipping_total, 2) }} <span class="font-oswald">৳</span>
                    </span>
                </div>
                <div class="px-6 pb-4 flex items-center justify-between">
                    <span class="font-semibold">Grand Total</span>
                    <span class="font-semibold">
                        {{ number_format($order->grand_total, 2) }} <span class="font-oswald">৳</span>
                    </span>
                </div>
            </div>
        </div>

        <a href="{{ url('/') }}"
           class="btn bg-customRed-100 text-white mt-6 px-5 py-3 rounded-md inline-block">
            Back to Home
        </a>
    </div>
</x-layouts.frontend>
