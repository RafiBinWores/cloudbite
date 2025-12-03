<div class="min-h-[70vh] bg-slate-50 flex items-center justify-center px-4 py-16">

    <div class="max-w-xl w-full bg-white rounded-3xl shadow-lg p-8 text-center">

        {{-- Success Icon --}}
        <div class="mx-auto bg-customRed-100/10 size-20 rounded-full flex items-center justify-center mb-6">
            <svg xmlns="http://www.w3.org/2000/svg" class="size-12 text-customRed-100" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="m9 12 2 2 4-4"/>
                <path d="M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z"/>
            </svg>
        </div>

        <h1 class="text-2xl md:text-3xl font-bold text-slate-900">
            Meal Plan Booking Confirmed!
        </h1>

        <p class="text-slate-600 mt-2 text-sm">
            Thank you for choosing our meal plan service.
            Your booking details have been successfully recorded.
        </p>

        {{-- Booking Code --}}
        <div class="mt-6 p-4 bg-slate-100 rounded-xl border border-slate-200">
            <p class="text-xs text-slate-500 uppercase font-semibold tracking-wide">Booking Code</p>
            <p class="text-lg font-bold text-slate-900 mt-1">
                {{ $booking->booking_code }}
            </p>
        </div>

        {{-- Summary --}}
        <div class="mt-6 text-sm text-left space-y-3 bg-slate-50 p-5 rounded-xl border border-slate-200">

            <div class="flex justify-between">
                <span class="opacity-70">Plan type</span>
                <span class="font-semibold">{{ ucfirst($booking->plan_type) }}</span>
            </div>

            <div class="flex justify-between">
                <span class="opacity-70">Start date</span>
                <span class="font-semibold">
                    {{ \Carbon\Carbon::parse($booking->start_date)->format('d M, Y') }}
                </span>
            </div>

            <div class="flex justify-between">
                <span class="opacity-70">Grand total</span>
                <span class="font-semibold">
                    {{ number_format($booking->grand_total, 2) }} ৳
                </span>
            </div>

            <div class="flex justify-between">
                <span class="opacity-70">Paid now</span>
                <span class="font-semibold text-green-600">
                    {{ number_format($booking->pay_now, 2) }} ৳
                </span>
            </div>

            <div class="flex justify-between">
                <span class="opacity-70">Due later</span>
                <span class="font-semibold text-red-500">
                    {{ number_format($booking->due_amount, 2) }} ৳
                </span>
            </div>
        </div>

        <a href="{{ route('home') }}"
           class="mt-8 inline-flex items-center justify-center w-full h-12 rounded-xl bg-customRed-100 text-white font-semibold hover:bg-customRed-200 transition">
            Back to Home
        </a>

        <p class="text-xs text-slate-400 mt-4">
            You will receive updates about your plan via SMS/Email.
        </p>

    </div>
</div>
