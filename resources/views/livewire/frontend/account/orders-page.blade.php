<div>
    <!-- Breadcrumb -->
    <div
        class="bg-[url(/assets/images/breadcrumb-bg.jpg)] py-20 md:py-32 bg-no-repeat bg-cover bg-center text-center text-white grid place-items-center font-oswald">
        <h4 class="text-4xl md:text-6xl font-medium">My Order</h4>
        <div class="breadcrumbs text-sm mt-3 font-medium">
            <ul class="flex items-center gap-2">
                <li>
                    <a href="/" class="hover:underline">
                        <i class="fa-regular fa-house"></i>
                        Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('account') }}" class="hover:underline">
                        <i class="fa-regular fa-user"></i>
                        Account
                    </a>
                </li>
                <li>Orders</li>
            </ul>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-10">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-2xl font-semibold">My Orders</h1>
        </div>

        <div 
            x-data="{
                tab: @entangle('tab').live,  // sync with Livewire ('ongoing' | 'delivered')
                markerLeft: 0,
                markerWidth: 0,
                setMarker(btn) {
                    if (!btn) return;
                    this.markerLeft = btn.offsetLeft;
                    this.markerWidth = btn.offsetWidth;
                },
            }"
            x-init="
                // initial position
                requestAnimationFrame(() => setMarker($refs.ongoingBtn));
                // on tab change
                $watch('tab', v => {
                    const btn = v === 'ongoing' ? $refs.ongoingBtn : $refs.deliveredBtn;
                    requestAnimationFrame(() => setMarker(btn));
                });
                // on resize
                window.addEventListener('resize', () => {
                    const btn = tab === 'ongoing' ? $refs.ongoingBtn : $refs.deliveredBtn;
                    setMarker(btn);
                });
            "
            class="relative w-full"
        >
            <!-- Tabs Header -->
            <div class="relative inline-grid items-center justify-center w-full h-10 grid-cols-2 p-1 text-gray-600 bg-gray-100 rounded-lg select-none max-w-sm mx-auto">
                <button
                    x-ref="ongoingBtn"
                    @click="tab = 'ongoing'"
                    :class="tab === 'ongoing' ? 'text-white' : 'text-gray-700'"
                    class="relative z-20 inline-flex items-center justify-center w-full h-8 px-3 text-sm font-medium transition-all rounded-md cursor-pointer whitespace-nowrap"
                    type="button"
                >
                    On-Going ({{ $ongoingCount }})
                </button>

                <button
                    x-ref="deliveredBtn"
                    @click="tab = 'delivered'"
                    :class="tab === 'delivered' ? 'text-white' : 'text-gray-700'"
                    class="relative z-20 inline-flex items-center justify-center w-full h-8 px-3 text-sm font-medium transition-all rounded-md cursor-pointer whitespace-nowrap"
                    type="button"
                >
                    History ({{ $deliveredCount }})
                </button>

                <!-- Moving Marker -->
                <div class="absolute z-10 h-full duration-300 ease-out top-0" :style="`left:${markerLeft}px; width:${markerWidth}px;`">
                    <div class="w-full h-full bg-customRed-100 text-white rounded-md shadow-sm"></div>
                </div>
            </div>

            <!-- Content -->
            <div class="relative w-full mt-6 space-y-6">
                <!-- Ongoing -->
                <div x-show="tab === 'ongoing'" x-transition class="space-y-2">
                    @forelse($orders as $order)
                        <div class="border rounded-lg shadow-sm bg-white p-4">
                            <div class="flex justify-between items-center gap-4">
                                <div>
                                    <p class="font-semibold">Order #{{ $order->order_code }}</p>
                                    <p class="text-sm capitalize mt-1">
                                        <span class="px-2 py-0.5 rounded-full text-white
                                            @switch($order->order_status)
                                                @case('pending') bg-amber-500 @break
                                                @case('processing') bg-blue-500 @break
                                                @case('confirmed') bg-indigo-500 @break
                                                @case('out_for_delivery') bg-teal-600 @break
                                                @default bg-gray-500
                                            @endswitch">
                                            {{ str_replace('_', ' ', $order->order_status) }}
                                        </span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Placed: {{ $order->created_at->format('d M, Y h:i A') }}</p>
                                </div>

                                <div class="text-right">
                                    <p class="font-medium text-customRed-100">৳{{ number_format($order->grand_total, 2) }}</p>
                                    <p class="text-xs mt-1">
                                        @if ($order->payment_status === 'paid')
                                            <span class="bg-green-500 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">Paid</span>
                                        @elseif ($order->payment_status === 'unpaid')
                                            <span class="bg-yellow-500 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">Unpaid</span>
                                        @else
                                            <span class="bg-red-500 text-white text-xs font-semibold px-2.5 py-0.5 rounded-full">{{ ucfirst($order->payment_status) }}</span>
                                        @endif
                                    </p>
                                    <a href="{{ route('account.orders.show', $order->order_code) }}"
                                       class="inline-flex items-center justify-center btn btn-sm bg-gray-900 text-white rounded-lg mt-2 px-3 py-1.5 hover:opacity-90">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-500">
                            <i class="fa-regular fa-box-open text-3xl mb-2"></i>
                            <p>No ongoing orders found.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Delivered -->
                <div x-show="tab === 'delivered'" x-transition class="space-y-2">
                    @forelse($orders as $order)
                        <div class="border rounded-lg shadow-sm bg-white p-4">
                            <div class="flex justify-between items-center gap-4">
                                <div>
                                    <p class="font-semibold">Order #{{ $order->order_code }}</p>
                                    <p class="text-sm mt-1">
                                        <span class="px-2 py-0.5 rounded-full text-white bg-green-600">Delivered</span>
                                    </p>
                                    <p class="text-xs text-gray-500 mt-1">Delivered: {{ $order->created_at->format('d M, Y h:i A') }}</p>
                                </div>

                                <div class="text-right">
                                    <p class="font-medium text-customRed-100">৳{{ number_format($order->grand_total, 2) }}</p>
                                    <p class="text-xs text-gray-500 mt-1">{{ ucfirst($order->payment_status) }}</p>
                                    <a href="{{ route('account.orders.show', $order->order_code) }}"
                                       class="inline-flex items-center justify-center btn btn-sm bg-gray-900 text-white rounded-lg mt-2 px-3 py-1.5 hover:opacity-90">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-10 text-gray-500">
                            <i class="fa-regular fa-truck text-3xl mb-2"></i>
                            <p>No delivered orders found.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Pagination -->
                <div>
                    {{ $orders->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
