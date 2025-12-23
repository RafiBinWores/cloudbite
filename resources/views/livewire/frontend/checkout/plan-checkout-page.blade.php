{{-- resources/views/livewire/frontend/checkout/plan-checkout-page.blade.php --}}
<div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left column --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Delivery Information --}}
                <section class="rounded-2xl border border-slate-200/70 bg-white/70 shadow-sm backdrop-blur p-6">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div
                                class="size-10 rounded-xl grid place-items-center bg-gradient-to-tr from-customRed-100/25 to-customRed-200/10 text-customRed-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M14 18V6a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2v11a1 1 0 0 0 1 1h2" />
                                    <path d="M15 18H9" />
                                    <path
                                        d="M19 18h2a1 1 0 0 0 1-1v-3.65a1 1 0 0 0-.22-.624l-3.48-4.35A1 1 0 0 0 17.52 8H14" />
                                    <circle cx="17" cy="18" r="2" />
                                    <circle cx="7" cy="18" r="2" />
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold leading-none">Delivery Information</h2>
                                <p class="text-xs opacity-70 mt-1">
                                    Your saved address for this meal plan.
                                </p>
                            </div>
                        </div>

                        @php
                            $selected =
                                collect($addresses ?? [])->firstWhere('id', $selectedAddressId) ??
                                (collect($addresses ?? [])->firstWhere('is_default', true) ??
                                    collect($addresses ?? [])->first());
                        @endphp

                        @if ($addresses && $addresses->count())
                            <button type="button" class="text-amber-500 hover:text-amber-600 text-sm font-medium cursor-pointer"
                                x-data
                                @click="$dispatch('address-modal:open', { selectedId: {{ $selectedAddressId ?? 'null' }} })">
                                Update Info
                            </button>
                        @endif
                    </div>

                    @if ($selected)
                        <div class="space-y-4 text-[15px]">
                            {{-- Name --}}
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-70" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <div class="font-medium">
                                    {{ $selected->contact_name ?: $contact_name ?? '' }}
                                </div>
                            </div>

                            <div class="border-t border-slate-200/70"></div>

                            {{-- Phone --}}
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-[18px] opacity-70"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path
                                        d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384" />
                                </svg>
                                <div class="opacity-80">
                                    {{ $selected->contact_phone ?: $phone ?? '' }}
                                </div>
                            </div>

                            <div class="border-t border-slate-200/70"></div>

                            {{-- Address --}}
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-9 opacity-70" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round">
                                    <path
                                        d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0" />
                                    <circle cx="12" cy="10" r="3" />
                                </svg>
                                <div>
                                    <div class="font-medium">{{ $selected->address }}</div>
                                    <div class="opacity-70 text-sm">
                                        {{ $selected->city }}
                                        {{ $selected->postal_code ? ' - ' . $selected->postal_code : '' }}
                                        {{ $selected->country ? ', ' . $selected->country : '' }}
                                    </div>
                                </div>
                            </div>

                            <div class="border-t border-slate-200/70"></div>

                            {{-- Note --}}
                            <div x-data="{ note: @entangle('customer_note').live }">
                                <label class="text-sm font-medium">Note to rider (optional)</label>
                                <div class="flex items-center justify-between">
                                    <span class="text-[11px] opacity-60">You can update this for this plan.</span>
                                    <span class="text-[11px] opacity-60" x-text="`${(note || '').length}/200`"></span>
                                </div>
                                <textarea x-model="note" @blur="$wire.set('customer_note', note, true)" rows="3" maxlength="200"
                                    class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-400 focus:ring-2 focus:ring-red-200"></textarea>
                                @error('customer_note')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg border border-amber-200 bg-amber-50 text-amber-900 text-sm px-3 py-2">
                            You don’t have any saved address yet. Please
                            <a class="text-red-600 underline underline-offset-2"
                                href="{{ route('address.create') }}">add one</a> to continue.
                        </div>
                    @endif
                </section>

                {{-- Address Select Modal --}}
                @if ($addresses && $addresses->count())
                    <livewire:frontend.checkout.modal.address-modal :addresses="$addresses" :selected-address-id="$selectedAddressId" />
                @endif

                {{-- Payment --}}
                <section class="rounded-2xl border border-slate-200/70 bg-white/70 shadow-sm backdrop-blur p-6">
                    <header class="flex items-center gap-3 mb-5">
                        <div
                            class="size-10 rounded-xl grid place-items-center bg-gradient-to-tr from-customRed-100/25 to-customRed-200/10 text-customRed-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none"
                                stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path
                                    d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1" />
                                <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold leading-none">Payment method</h2>
                            <p class="text-xs opacity-70">Choose how you want to pay for this plan.</p>
                        </div>
                    </header>

                    {{-- Payment option: full / half --}}
                    <div class="mb-5">
                        <h3 class="text-sm font-medium">Payment option</h3>
                        <div class="mt-2 flex flex-wrap gap-3">
                            <button type="button" wire:click="$set('payment_option','full')"
                                class="px-3 py-1.5 rounded-full text-xs border cursor-pointer
                                    {{ $payment_option === 'full'
                                        ? 'bg-slate-900 text-white border-slate-900'
                                        : 'border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                                Pay full amount
                            </button>
                            <button type="button" wire:click="$set('payment_option','half')"
                                class="px-3 py-1.5 rounded-full text-xs border cursor-pointer
                                    {{ $payment_option === 'half'
                                        ? 'bg-slate-900 text-white border-slate-900'
                                        : 'border-slate-300 text-slate-700 hover:bg-slate-100' }}">
                                Pay 50% now, rest later
                            </button>
                        </div>
                        @error('payment_option')
                            <p class="text-error text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Payment method radio --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label
                            class="group relative cursor-pointer rounded-xl border border-customRed-100/70 p-4 hover:border-customRed-100/90 transition">
                            <input type="radio" class="radio absolute opacity-0" value="cod"
                                wire:model="payment_method">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-lg grid place-items-center bg-customRed-100 text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <rect width="20" height="12" x="2" y="6" rx="2" />
                                        <circle cx="12" cy="12" r="2" />
                                        <path d="M6 12h.01M18 12h.01" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium leading-none">Cash on delivery</p>
                                    <p class="text-xs opacity-70 mt-1">Pay at your doorstep</p>
                                </div>
                                <div
                                    class="ml-auto size-5 rounded-full border group-has-[input:checked]:bg-customRed-100/90 group-has-[input:checked]:border-customRed-100/90">
                                </div>
                            </div>
                        </label>

                        <label
                            class="group relative cursor-pointer rounded-xl border border-customRed-100/70 p-4 hover:border-customRed-200/90 transition">
                            <input type="radio" class="radio absolute opacity-0" value="sslcommerz"
                                wire:model="payment_method">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-lg grid place-items-center bg-customRed-100 text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                        fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                        stroke-linejoin="round">
                                        <path
                                            d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z" />
                                        <path d="m9 12 2 2 4-4" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium leading-none">SSLCommerz</p>
                                    <p class="text-xs opacity-70 mt-1">Secure online payment</p>
                                </div>
                                <div
                                    class="ml-auto size-5 rounded-full border group-has-[input:checked]:bg-customRed-100/90 group-has-[input:checked]:border-customRed-100/90">
                                </div>
                            </div>
                        </label>
                    </div>

                    @error('payment_method')
                        <p class="text-error text-xs mt-3">{{ $message }}</p>
                    @enderror
                </section>
            </div>

            {{-- Right column --}}
            <div>
                <div class="rounded-xl border p-6 space-y-3">
                    <h2 class="text-lg font-semibold">Plan Summary</h2>

                    <p class="text-xs opacity-70 mb-2">
                        {{ ucfirst($planType) }} plan starting from
                        {{ \Carbon\Carbon::parse($startDate)->format('d M, Y') }}.
                    </p>

                    @php
                        $payNow = $payment_option === 'half' ? $grand_total / 2 : $grand_total;
                        $dueLater = max(0, $grand_total - $payNow);
                    @endphp

                    {{-- Plan Total --}}
                    {{-- Subtotal (before coupon) --}}
                    {{-- <div class="flex items-center justify-between text-sm">
    <span class="opacity-80">Subtotal</span>
    <span>
        {{ number_format($plan_total, 2) }}
        <span class="font-oswald">৳</span>
    </span>
</div> --}}

                    {{-- Coupon Discount --}}
                    {{-- @if (!empty($couponApplied) && ($coupon_discount_total ?? 0) > 0)
    <div class="flex items-center justify-between text-sm">
        <span class="opacity-80">
            Coupon discount
            @if (!empty($couponCode))
                <span class="text-xs opacity-60">({{ $couponCode }})</span>
            @endif
        </span>
        <span class="text-emerald-600">
            (-) {{ number_format($coupon_discount_total, 2) }}
            <span class="font-oswald">৳</span>
        </span>
    </div>
@endif --}}

                    {{-- Meal Plan Total (after coupon) --}}
                    <div class="flex items-center justify-between text-sm">
                        <span class="opacity-80">Meal plan total</span>
                        <span class="font-semibold">
                            {{ number_format($net_total, 2) }}
                            <span class="font-oswald">৳</span>
                        </span>
                    </div>


                    {{-- Delivery Fee --}}
                    @if ($shipSetting)
                        @php
                            $freeMin = (float) ($shipSetting->free_minimum ?? 0);
                            $isFree = (bool) ($shipSetting->free_delivery ?? false);
                        @endphp

                        <div class="flex items-center justify-between text-sm">
                            <span class="opacity-80">
                                Delivery fee
                                @if ($isFree)
                                    <span class="text-xs opacity-60">
                                        (Free over {{ number_format($freeMin, 2) }} ৳)
                                    </span>
                                @endif
                            </span>

                            @if ($shipping_total == 0)
                                <span class="text-success">
                                    0.00 <span class="font-oswald">৳</span>
                                </span>
                            @else
                                <span>
                                    (+) {{ number_format($shipping_total, 2) }}
                                    <span class="font-oswald">৳</span>
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Grand Total --}}
                    <div class="border-t pt-3 flex items-center justify-between text-sm">
                        <span class="font-semibold">Grand total</span>
                        <span class="font-semibold">
                            {{ number_format($grand_total, 2) }}
                            <span class="font-oswald">৳</span>
                        </span>
                    </div>

                    {{-- Pay now / Due later --}}
                    <div class="mt-2 flex items-center justify-between text-sm">
                        <span class="opacity-80">
                            Pay now
                            <span class="text-[11px] ml-1 px-2 py-0.5 rounded-full bg-slate-900 text-white">
                                {{ $payment_option === 'half' ? '50% upfront' : 'Full payment' }}
                            </span>
                        </span>
                        <span class="text-lg font-semibold text-red-500">
                            {{ number_format($payNow, 2) }}
                            <span class="font-oswald">৳</span>
                        </span>
                    </div>
                    <div class="flex items-center justify-between text-xs opacity-70">
                        <span>Remaining (due later)</span>
                        <span>
                            {{ number_format($dueLater, 2) }}
                            <span class="font-oswald">৳</span>
                        </span>
                    </div>

                    {{-- <button wire:click="placePlanBooking" wire:loading.attr="disabled" wire:target="placePlanBooking"
                        class="btn bg-customRed-100 text-white w-full h-12 rounded-xl disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2 mt-4 font-oswald uppercase lg:text-lg cursor-pointer">
                        <span wire:loading.remove wire:target="placePlanBooking">
                            Confirm plan booking
                        </span>
                        <span wire:loading wire:target="placePlanBooking" class="inline-flex items-center gap-2">
                            <svg class="animate-spin size-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z" />
                            </svg>
                            Booking…
                        </span>
                    </button> --}}

                    <button type="button" wire:click="placePlanBooking" wire:loading.attr="disabled" wire:target="placePlanBooking"
                        class="group relative inline-flex w-full items-center justify-center rounded-md px-8 md:px-10 py-3 overflow-hidden bg-customRed-100 font-oswald text-white no-underline focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 focus-visible:ring-white/60 cursor-pointer">
                        <span class="pointer-events-none absolute inset-0 bg-slate-900 transform origin-center scale-0 rotate-45 transition-transform duration-500 ease-out group-hover:scale-1125"></span>
                        <span class="relative z-10 transition-colors duration-300 group-hover:text-white uppercase">
                            Confirm plan booking
                        </span>
                    </button>

                    <p class="text-xs opacity-60 text-center">
                        By confirming this plan, you agree to our Terms & Refund Policy.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
