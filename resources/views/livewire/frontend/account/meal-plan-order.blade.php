<div class="max-w-6xl mx-auto px-4 sm:px-6 py-10">

    <h1 class="text-2xl md:text-3xl font-semibold text-slate-900 mb-6">
        Meal Plan Orders
    </h1>

    @if($bookings->isEmpty())
        <div class="rounded-2xl border border-dashed border-slate-300 bg-slate-50 p-8 text-center">
            <p class="text-slate-600 text-sm">
                You don’t have any meal plan bookings yet.
            </p>
            <a href="{{ route('meal.plans') }}"
               class="inline-flex items-center justify-center mt-4 px-4 py-2 rounded-xl bg-customRed-100 text-white text-sm font-medium hover:bg-customRed-200">
                Create your first meal plan
            </a>
        </div>
    @else
        <div class="space-y-4">
            @foreach($bookings as $booking)
                @php
                    $days       = $booking->days ?? [];
                    $prefs      = $booking->meal_prefs ?? [];
                    $mealNames  = collect($prefs)
                        ->filter(fn($v) => $v)
                        ->keys()
                        ->map(fn($k) => ucfirst($k))
                        ->values()
                        ->all();

                    $statusColor = match ($booking->status) {
                        'pending'   => 'bg-amber-100 text-amber-800',
                        'confirmed' => 'bg-blue-100 text-blue-800',
                        'completed' => 'bg-emerald-100 text-emerald-800',
                        'cancelled' => 'bg-red-100 text-red-800',
                        default     => 'bg-slate-100 text-slate-700',
                    };
                @endphp

                <div class="rounded-2xl border border-slate-200 bg-white shadow-sm overflow-hidden">
                    {{-- Top row: summary --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-5 py-4">
                        <div class="space-y-1">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-xs font-semibold uppercase tracking-wide text-slate-500">
                                    Booking Code
                                </span>
                                <span class="text-sm font-semibold text-slate-900">
                                    <a href="{{ route('meal-plan.booking.show', $booking->booking_code) }}"
                                       class="hover:underline">
                                        #{{ $booking->booking_code }}
                                    </a>
                                </span>
                                <span class="text-[11px] px-2 py-0.5 rounded-full {{ $statusColor }}">
                                    {{ ucfirst($booking->status) }}
                                </span>
                            </div>

                            <div class="text-xs text-slate-500">
                                Placed on
                                {{ $booking->created_at?->format('d M, Y h:i A') }}
                            </div>
                        </div>

                        <div class="flex flex-wrap items-center gap-4 text-sm">
                            <div class="flex flex-col">
                                <span class="text-xs text-slate-500">Plan type</span>
                                <span class="font-medium">{{ ucfirst($booking->plan_type) }}</span>
                            </div>

                            <div class="w-px h-8 bg-slate-200 hidden md:block"></div>

                            <div class="flex flex-col">
                                <span class="text-xs text-slate-500">Start date</span>
                                <span class="font-medium">
                                    {{ optional($booking->start_date)->format('d M, Y') }}
                                </span>
                            </div>

                            <div class="w-px h-8 bg-slate-200 hidden md:block"></div>

                            <div class="flex flex-col">
                                <span class="text-xs text-slate-500">Grand total</span>
                                <span class="font-semibold text-slate-900">
                                    {{ number_format($booking->grand_total, 2) }} ৳
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="border-t border-slate-200"></div>

                    {{-- Middle row: payment + actions --}}
                    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-3 px-5 py-3 text-sm">

                        <div class="space-y-1">
                            <div class="flex flex-wrap items-center gap-4">
                                <div>
                                    <span class="text-xs text-slate-500 block">Payment method</span>
                                    <span class="font-medium">
                                        {{ strtoupper($booking->payment_method) === 'COD'
                                            ? 'Cash on delivery'
                                            : 'SSLCommerz' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-500 block">Payment option</span>
                                    <span class="font-medium">
                                        {{ $booking->payment_option === 'half'
                                            ? '50% now, 50% later'
                                            : 'Full payment' }}
                                    </span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-500 block">Paid now</span>
                                    <span class="font-medium text-emerald-600">
                                        {{ number_format($booking->pay_now, 2) }} ৳
                                    </span>
                                </div>

                                <div>
                                    <span class="text-xs text-slate-500 block">Due later</span>
                                    <span class="font-medium text-red-500">
                                        {{ number_format($booking->due_amount, 2) }} ৳
                                    </span>
                                </div>
                            </div>

                            @if(!empty($mealNames))
                                <div class="text-xs text-slate-500 mt-1">
                                    Meals:
                                    <span class="font-medium">
                                        {{ implode(', ', $mealNames) }}
                                    </span>
                                </div>
                            @endif
                        </div>

                        <div class="flex items-center gap-2 justify-end">
                            {{-- Re-order button (only if completed) --}}
                            {{-- @if($booking->status === 'completed')
                                <button
                                    wire:click="reorder('{{ $booking->booking_code }}')"
                                    class="px-3 py-2 rounded-xl border border-customRed-100 text-customRed-100 text-xs font-medium hover:bg-customRed-50 transition"
                                >
                                    Re-order this plan
                                </button>
                            @endif --}}

                            <a href="{{ route('meal-plan.booking.show', $booking->booking_code) }}"
                               class="px-3 py-2 rounded-xl text-xs font-medium border border-slate-200 hover:bg-slate-50 flex items-center gap-1">
                                <span>View details</span>
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="size-4"
                                     viewBox="0 0 24 24"
                                     fill="none"
                                     stroke="currentColor"
                                     stroke-width="2"
                                     stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path d="M9 18l6-6-6-6"/>
                                </svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    <div class="mt-6">
        {{ $bookings->links() }}
    </div>

</div>
