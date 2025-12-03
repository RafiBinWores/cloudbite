<div class="max-w-7xl mx-auto px-4 sm:px-6 py-10 space-y-8">

    @php
        $statusColor = match ($booking->status) {
            'pending'   => 'bg-amber-100 text-amber-800 border-amber-200',
            'confirmed' => 'bg-blue-100 text-blue-800 border-blue-200',
            'completed' => 'bg-emerald-100 text-emerald-800 border-emerald-200',
            'cancelled' => 'bg-red-100 text-red-800 border-red-200',
            default     => 'bg-slate-100 text-slate-700 border-slate-200',
        };
    @endphp

    {{-- Header / Hero --}}
    <div
        class="rounded-3xl border border-slate-200 bg-white shadow-[0_18px_45px_rgba(15,23,42,0.08)] overflow-hidden relative">
        {{-- Top gradient bar --}}
        <div class="h-1.5 bg-gradient-to-r from-customRed-100 to-customRed-200"></div>

        <div class="px-5 sm:px-8 py-6 sm:py-7 flex flex-col md:flex-row md:items-center md:justify-between gap-5">

            <div class="space-y-2">
                <div class="inline-flex items-center gap-2 rounded-full bg-slate-50 px-3 py-1 border border-slate-200">
                    <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">
                        Meal Plan Booking
                    </span>
                    <span
                        class="text-[11px] font-mono px-2 py-0.5 rounded-full bg-slate-900 text-white">
                        #{{ $booking->booking_code }}
                    </span>
                </div>

                <div class="flex items-center gap-3 flex-wrap mt-1.5">
                    <h1 class="text-2xl md:text-3xl font-semibold text-slate-900">
                        {{ ucfirst($booking->plan_type) }} Plan
                    </h1>

                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] border {{ $statusColor }}">
                        <span class="size-1.5 rounded-full bg-current"></span>
                        <span class="font-semibold tracking-wide">
                            {{ ucfirst($booking->status) }}
                        </span>
                    </span>
                </div>

                <p class="text-xs text-slate-500">
                    Placed on
                    <span class="font-medium">
                        {{ $booking->created_at?->format('d M, Y h:i A') }}
                    </span>
                    @if($booking->start_date)
                        · Plan starts
                        <span class="font-medium">
                            {{ $booking->start_date->format('d M, Y') }}
                        </span>
                    @endif
                </p>
            </div>

            <div class="flex flex-col items-start md:items-end gap-3">
                <div class="text-right space-y-1">
                    <p class="text-xs text-slate-500 uppercase tracking-[0.16em]">
                        Grand total
                    </p>
                    <p class="text-2xl font-semibold text-slate-900">
                        {{ number_format($booking->grand_total, 2) }}
                        <span class="font-oswald text-[18px]">৳</span>
                    </p>

                    <p class="text-[11px] text-slate-500">
                        Paid:
                        <span class="text-emerald-600 font-semibold">
                            {{ number_format($booking->pay_now, 2) }} ৳
                        </span>
                        · Due:
                        <span class="text-red-500 font-semibold">
                            {{ number_format($booking->due_amount, 2) }} ৳
                        </span>
                    </p>
                </div>

                <div class="flex flex-wrap gap-2 justify-end">
                    @if($booking->status === 'completed')
                        <button
                            wire:click="reorder"
                            class="inline-flex items-center justify-center px-4 py-2 rounded-xl border border-slate-200 text-xs font-medium text-slate-700 hover:bg-slate-50 transition">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-4 mr-1.5" viewBox="0 0 24 24"
                                 fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                 stroke-linejoin="round">
                                <polyline points="1 4 1 10 7 10" />
                                <polyline points="23 20 23 14 17 14" />
                                <path
                                    d="M20.49 9A9 9 0 0 0 5.64 5.64L1 10m22 4-4.64 4.36A9 9 0 0 1 3.51 15" />
                            </svg>
                            Re-order this plan
                        </button>
                    @endif

                    <a href="{{ route('meal-plan.history') }}"
                       class="inline-flex items-center justify-center px-3 py-2 rounded-xl text-xs font-medium text-slate-600 hover:bg-slate-50 border border-slate-200">
                        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 mr-1.5" viewBox="0 0 24 24"
                             fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                             stroke-linejoin="round">
                            <polyline points="15 18 9 12 15 6" />
                        </svg>
                        Back to bookings
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Top summary: payment + shipping --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">

        {{-- Payment summary --}}
        <div
            class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-[0_6px_18px_rgba(15,23,42,0.04)] space-y-2">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Payment</h2>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full bg-slate-50 border border-slate-200 text-[10px] uppercase tracking-[0.14em] text-slate-500">
                    {{ strtoupper($booking->payment_status) }}
                </span>
            </div>

            <div class="mt-2 space-y-1.5 text-xs">
                <p class="text-slate-500">
                    Method:
                    <span class="font-medium text-slate-800">
                        {{ strtoupper($booking->payment_method) === 'COD'
                            ? 'Cash on delivery'
                            : 'SSLCommerz' }}
                    </span>
                </p>
                <p class="text-slate-500">
                    Option:
                    <span class="font-medium text-slate-800">
                        {{ $booking->payment_option === 'half'
                            ? '50% now, 50% later'
                            : 'Full payment' }}
                    </span>
                </p>
                <p class="text-emerald-600">
                    Paid now:
                    <span class="font-semibold">
                        {{ number_format($booking->pay_now, 2) }} ৳
                    </span>
                </p>
                <p class="text-red-500">
                    Due:
                    <span class="font-semibold">
                        {{ number_format($booking->due_amount, 2) }} ৳
                    </span>
                </p>
            </div>
        </div>

        {{-- Address --}}
        <div
            class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-[0_6px_18px_rgba(15,23,42,0.04)] space-y-2">
            <div class="flex items-center justify-between">
                <h2 class="text-sm font-semibold text-slate-900">Delivery address</h2>
                <span class="inline-flex items-center justify-center size-7 rounded-full bg-customRed-100/10 text-customRed-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-4" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path
                            d="M20 10c0 5-8 13-8 13S4 15 4 10a8 8 0 0 1 16 0Z" />
                        <circle cx="12" cy="10" r="3" />
                    </svg>
                </span>
            </div>

            @php
                $addr = $booking->shipping_address ?? [];
            @endphp

            <div class="mt-2 space-y-1.5 text-xs">
                <p class="text-slate-800 font-medium">
                    {{ $booking->contact_name }}
                </p>
                <p class="text-slate-500">
                    {{ $booking->phone }}
                    @if($booking->email)
                        • {{ $booking->email }}
                    @endif
                </p>

                <p class="text-slate-600 mt-1">
                    {{ $addr['line1'] ?? '' }}
                </p>
                <p class="text-slate-500">
                    {{ $addr['city'] ?? '' }}
                    @if(!empty($addr['postcode']))
                        - {{ $addr['postcode'] }}
                    @endif
                </p>
            </div>
        </div>

        {{-- Totals --}}
        <div
            class="rounded-2xl border border-slate-200 bg-slate-900 text-slate-50 p-4 sm:p-5 shadow-[0_8px_24px_rgba(15,23,42,0.45)] relative overflow-hidden">
            <div class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full bg-gradient-to-tr from-customRed-100/25 to-customRed-200/10 pointer-events-none"></div>

            <div class="relative space-y-2">
                <h2 class="text-sm font-semibold">Totals</h2>

                <div class="flex items-center justify-between text-xs text-slate-200">
                    <span>Plan subtotal</span>
                    <span class="font-medium">
                        {{ number_format($booking->plan_subtotal, 2) }} ৳
                    </span>
                </div>

                <div class="flex items-center justify-between text-xs text-slate-200">
                    <span>Shipping</span>
                    <span class="font-medium">
                        {{ number_format($booking->shipping_total, 2) }} ৳
                    </span>
                </div>

                <div class="border-t border-slate-700 my-2"></div>

                <div class="flex items-center justify-between text-sm">
                    <span class="font-semibold text-slate-50">Grand total</span>
                    <span class="font-semibold text-customRed-100">
                        {{ number_format($booking->grand_total, 2) }} ৳
                    </span>
                </div>
            </div>
        </div>
    </div>

    {{-- Day-by-day items --}}
    <div class="rounded-3xl border border-slate-200 bg-white p-5 sm:p-6 shadow-[0_12px_30px_rgba(15,23,42,0.06)] space-y-4">
        <div class="flex items-center justify-between gap-3">
            <div>
                <h2 class="text-sm font-semibold text-slate-900">
                    Day-by-day meals
                </h2>
                <p class="text-[11px] text-slate-500 mt-0.5">
                    Slot-wise breakdown of your booked dishes.
                </p>
            </div>
            @php
                $prefs = $booking->meal_prefs ?? [];
                $activeMeals = collect($prefs)
                    ->filter(fn($v) => $v)
                    ->keys()
                    ->map(fn($k) => ucfirst($k))
                    ->values()
                    ->all();
            @endphp
            @if(!empty($activeMeals))
                <div class="hidden sm:flex flex-wrap gap-1.5 justify-end">
                    @foreach($activeMeals as $mealName)
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] bg-customRed-100/5 text-customRed-100 border border-customRed-100/20">
                            {{ $mealName }}
                        </span>
                    @endforeach
                </div>
            @endif
        </div>

        @php
            $days = $booking->days ?? [];
            $slotsOrder = ['breakfast', 'lunch', 'tiffin', 'dinner'];
        @endphp

        @if(empty($days))
            <p class="text-xs text-slate-500">
                No detailed plan data available.
            </p>
        @else
            <div class="space-y-4">
                @foreach($days as $dayIndex => $day)
                    @php
                        $slots = $day['slots'] ?? [];
                        $dayItemCount = 0;
                        foreach ($slots as $slotData) {
                            $dayItemCount += count($slotData['items'] ?? []);
                        }
                    @endphp

                    @if($dayItemCount === 0)
                        @continue
                    @endif

                    <div class="border border-slate-200 rounded-2xl overflow-hidden bg-slate-50/60">
                        <div class="px-4 sm:px-5 py-2.5 bg-slate-50 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span
                                    class="inline-flex items-center justify-center size-6 rounded-full bg-customRed-100/10 text-customRed-100 text-[11px] font-semibold">
                                    {{ $dayIndex + 1 }}
                                </span>
                                <div class="text-sm font-semibold text-slate-900">
                                    {{ $day['name'] ?? 'Day '.($dayIndex+1) }}
                                </div>
                            </div>
                            <div class="text-[11px] text-slate-500">
                                {{ $dayItemCount }} item{{ $dayItemCount !== 1 ? 's' : '' }}
                            </div>
                        </div>

                        <div class="divide-y divide-slate-100 bg-white">
                            @foreach($slotsOrder as $slotKey)
                                @php
                                    $slotData = $slots[$slotKey] ?? ['items' => []];
                                    $items = $slotData['items'] ?? [];
                                @endphp

                                @if(empty($items))
                                    @continue
                                @endif

                                <div class="px-4 sm:px-5 py-3">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="inline-flex items-center gap-1.5">
                                            <span class="text-[11px] font-semibold uppercase tracking-[0.14em] text-slate-500">
                                                {{ ucfirst($slotKey) }}
                                            </span>
                                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 text-slate-600">
                                                {{ count($items) }} item{{ count($items) !== 1 ? 's' : '' }}
                                            </span>
                                        </div>
                                    </div>

                                    <div class="space-y-2.5">
                                        @foreach($items as $item)
    @php
        $dishId = $item['dish_id'] ?? null;
        $dish   = $dishId ? $dishesById->get((int) $dishId) : null;
        $qty    = (int) ($item['qty'] ?? 1);

        // Price breakdown helpers
        $variantInfo = $dish ? $this->getVariantInfo($dish, $item['variant_key'] ?? null) : ['label' => null, 'group' => null, 'price' => 0];
        $crustInfo   = $dish ? $this->getCrustInfo($dish, $item['crust_key'] ?? null) : ['name' => null, 'price' => 0];
        $bunInfo     = $dish ? $this->getBunInfo($dish, $item['bun_key'] ?? null) : ['name' => null, 'price' => 0];
        $addonsInfo  = $dish ? $this->getAddonInfo($dish, (array) ($item['addon_keys'] ?? [])) : [];

        $unitPrice = $dish ? $this->calculateItemUnitPrice($dish, $item) : 0;
        $lineTotal = $unitPrice * $qty;
    @endphp

    <div class="flex items-start gap-3">
        {{-- Thumbnail --}}
        <div
            class="w-11 h-11 rounded-xl bg-slate-100 overflow-hidden flex-shrink-0 border border-slate-200/60">
            @if($dish && $dish->thumbnail)
                <img src="{{ asset('storage/'.$dish->thumbnail) }}"
                     alt="{{ $dish->title }}"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full grid place-items-center text-[10px] text-slate-400">
                    N/A
                </div>
            @endif
        </div>

        {{-- Content --}}
        <div class="flex-1 min-w-0">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <div class="text-sm font-medium text-slate-900 truncate">
                        {{ $dish?->title ?? 'Unknown item' }}
                    </div>
                    <div class="text-[11px] text-slate-500 mt-0.5">
                        Qty:
                        <span class="font-medium">
                            {{ $qty }}
                        </span>
                    </div>
                    @if($dish)
                        <div class="text-[11px] text-slate-500">
                            Base:
                            <span class="font-semibold">
                                {{ number_format($dish->price_with_discount, 2) }} ৳
                            </span>
                        </div>
                    @endif
                </div>

                {{-- Price column --}}
                @if($dish)
                    <div class="text-right text-xs sm:text-sm leading-tight">
                        <div class="font-semibold text-slate-900">
                            {{ number_format($unitPrice, 2) }}
                            <span class="font-oswald text-[11px]">৳</span>
                        </div>
                        <div class="text-[11px] text-slate-500">
                            Total:
                            <span class="font-semibold text-customRed-100">
                                {{ number_format($lineTotal, 2) }} ৳
                            </span>
                        </div>
                    </div>
                @endif
            </div>

            {{-- Variant / crust / bun / add-ons with prices --}}
            @if($dish)
                <ul class="mt-1.5 space-y-0.5 text-[11px] text-slate-500 leading-snug">

                    {{-- Variant --}}
                    @if(!empty($variantInfo['label']))
                        <li>
                            <span class="font-medium text-slate-600">
                                {{ $variantInfo['group'] ?? 'Variant' }}:
                            </span>
                            {{ $variantInfo['label'] }}
                            @if(($variantInfo['price'] ?? 0) > 0)
                                <span class="text-customRed-100 font-semibold">
                                    (+{{ number_format($variantInfo['price'], 2) }} ৳)
                                </span>
                            @endif
                        </li>
                    @endif

                    {{-- Crust --}}
                    @if(!empty($crustInfo['name']))
                        <li>
                            <span class="font-medium text-slate-600">
                                Crust:
                            </span>
                            {{ $crustInfo['name'] }}
                            @if(($crustInfo['price'] ?? 0) > 0)
                                <span class="text-customRed-100 font-semibold">
                                    (+{{ number_format($crustInfo['price'], 2) }} ৳)
                                </span>
                            @endif
                        </li>
                    @endif

                    {{-- Bun --}}
                    @if(!empty($bunInfo['name']))
                        <li>
                            <span class="font-medium text-slate-600">
                                Bun:
                            </span>
                            {{ $bunInfo['name'] }}
                            @if(($bunInfo['price'] ?? 0) > 0)
                                <span class="text-customRed-100 font-semibold">
                                    (+{{ number_format($bunInfo['price'], 2) }} ৳)
                                </span>
                            @endif
                        </li>
                    @endif

                    {{-- Add-ons --}}
                    @if(!empty($addonsInfo))
                        <li>
                            <span class="font-medium text-slate-600">
                                Add-ons:
                            </span>
                            @foreach($addonsInfo as $addon)
                                <span>
                                    {{ $addon['name'] }}
                                    @if(($addon['price'] ?? 0) > 0)
                                        <span class="text-customRed-100 font-semibold">
                                            (+{{ number_format($addon['price'], 2) }} ৳)
                                        </span>
                                    @endif
                                    @if(!$loop->last), @endif
                                </span>
                            @endforeach
                        </li>
                    @endif

                </ul>
            @endif
        </div>
    </div>
@endforeach

                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @if($booking->customer_note)
        <div class="rounded-2xl border border-slate-200 bg-white p-4 sm:p-5 shadow-[0_6px_18px_rgba(15,23,42,0.04)]">
            <div class="flex items-center gap-2 mb-1.5">
                <span
                    class="inline-flex items-center justify-center size-6 rounded-full bg-customRed-100/10 text-customRed-100">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5" viewBox="0 0 24 24"
                         fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                         stroke-linejoin="round">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2z" />
                    </svg>
                </span>
                <h2 class="text-sm font-semibold text-slate-900">
                    Note from you
                </h2>
            </div>
            <p class="text-xs text-slate-600 leading-relaxed">
                {{ $booking->customer_note }}
            </p>
        </div>
    @endif

</div>
