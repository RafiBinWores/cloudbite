<div>
    <!-- Breadcrumb -->
    <div
        class="bg-[url(/assets/images/breadcrumb-bg.jpg)] py-20 md:py-32 bg-no-repeat bg-cover bg-center text-center text-white grid place-items-center font-oswald">
        <h4 class="text-4xl md:text-6xl font-medium">My Order</h4>
        <div class="breadcrumbs text-sm mt-3 font-medium">
            <ul class="flex items-center">
                <li>
                    <a href="/">
                        <i class="fa-regular fa-house"></i>
                        Home
                    </a>
                </li>
                <li>
                    <a href="{{ route('account') }}">
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

{{-- Tabs --}}
<div class="flex gap-2 border-b mb-6">
    <button
        wire:click="setTab('ongoing')"
        class="px-4 py-2 -mb-px border-b-2 transition cursor-pointer
               {{ $tab === 'ongoing' ? 'border-customRed-100 text-customRed-100' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
        Ongoing
        <span class="ml-1 inline-flex items-center justify-center text-xs rounded-full px-2 py-0.5 bg-slate-100">
            {{ $ongoingCount ?? 0 }}
        </span>
    </button>

    <button
        wire:click="setTab('delivered')"
        class="px-4 py-2 -mb-px border-b-2 transition cursor-pointer
               {{ $tab === 'delivered' ? 'border-customRed-100 text-customRed-100' : 'border-transparent text-slate-600 hover:text-slate-900' }}">
        Delivered
        <span class="ml-1 inline-flex items-center justify-center text-xs rounded-full px-2 py-0.5 bg-slate-100">
            {{ $deliveredCount ?? 0 }}
        </span>
    </button>
</div>


        {{-- List --}}
        <div class="space-y-4">
            @forelse($orders as $order)
                <div class="rounded-xl border p-4 md:p-5 bg-white/5 hover:bg-white/10 transition">
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3">
                        <div class="space-y-1">
                            <div class="text-sm text-slate-500">Order Code</div>
                            <div class="text-lg font-semibold tracking-wide">{{ $order->order_code }}</div>
                            <div class="text-sm text-slate-500">
                                Placed: {{ $order->created_at->format('d M Y, h:i A') }}
                            </div>
                        </div>

                        <div class="flex items-center gap-3">
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                {{ $order->payment_status === 'paid' ? 'bg-green-100 text-green-700' : 'bg-yellow-100 text-yellow-700' }}">
                                Payment: {{ ucfirst($order->payment_status) }}
                            </span>
                            <span
                                class="inline-flex items-center rounded-full px-3 py-1 text-xs font-medium
                                @class([
                                    'bg-blue-100 text-blue-700' => in_array($order->order_status, [
                                        'pending',
                                        'processing',
                                        'confirmed',
                                        'packed',
                                    ]),
                                    'bg-indigo-100 text-indigo-700' => in_array($order->order_status, [
                                        'shipped',
                                        'out_for_delivery',
                                    ]),
                                    'bg-emerald-100 text-emerald-700' => in_array($order->order_status, [
                                        'delivered',
                                        'completed',
                                    ]),
                                    'bg-red-100 text-red-700' => in_array($order->order_status, [
                                        'cancelled',
                                        'failed',
                                    ]),
                                ])">
                                {{ ucwords(str_replace('_', ' ', $order->order_status)) }}
                            </span>
                        </div>

                        <div class="text-right">
                            <div class="text-sm text-slate-500">Total</div>
                            <div class="text-xl font-semibold">৳{{ number_format($order->grand_total, 2) }}</div>
                        </div>
                    </div>

                    <div class="mt-4 flex justify-end">
                        <a href="{{ route('account.orders.show', $order->order_code) }}"
                            class="btn bg-customRed-100 text-white hover:brightness-110">
                            View details
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-16">
                    <p class="text-slate-600">No {{ $tab === 'ongoing' ? 'ongoing' : 'delivered' }} orders yet.</p>
                </div>
            @endforelse
        </div>

        <div class="mt-6">
            {{ $orders->links() }}
        </div>
    </div>

</div>
