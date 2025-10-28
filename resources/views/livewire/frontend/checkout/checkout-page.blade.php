<div>
    {{-- Breadcrumb --}}
    <div class="bg-[url(/assets/images/breadcrumb-bg.jpg)] py-20 md:py-32 bg-no-repeat bg-cover bg-center text-center text-white grid place-items-center font-oswald">
        <h4 class="text-4xl md:text-6xl font-medium">Checkout</h4>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            {{-- Left column --}}
            <div class="lg:col-span-2 space-y-8">

                {{-- Delivery Information --}}
                <section class="rounded-2xl border border-slate-200/70 bg-white/70 shadow-sm backdrop-blur p-6">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        <div class="flex items-center gap-3">
                            <div class="size-10 rounded-xl grid place-items-center bg-gradient-to-tr from-customRed-100/25 to-customRed-200/10 text-customRed-100">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                    <circle cx="12" cy="10" r="3"/>
                                </svg>
                            </div>
                            <div>
                                <h2 class="text-lg font-semibold leading-none">Delivery Information</h2>
                                <p class="text-xs opacity-70 mt-1">Your selected shipping details.</p>
                            </div>
                        </div>

                        @php
                            $selected =
                                collect($addresses ?? [])->firstWhere('id', $selectedAddressId)
                                ?? collect($addresses ?? [])->firstWhere('is_default', true)
                                ?? collect($addresses ?? [])->first();
                        @endphp

                        <button
                            type="button"
                            class="text-amber-500 hover:text-amber-600 text-sm font-medium"
                            x-data
                            @click="$dispatch('address-modal:open', { selectedId: {{ $selectedAddressId ?? 'null' }} })"
                        >
                            Update Info
                        </button>
                    </div>

                    @if ($selected)
                        <div class="space-y-4 text-[15px]">
                            {{-- Name --}}
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 size-5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <circle cx="12" cy="8" r="4"/>
                                    <path d="M6 20a6 6 0 0 1 12 0"/>
                                </svg>
                                <div class="font-medium">{{ $selected->contact_name ?: ($contact_name ?? '') }}</div>
                            </div>

                            <div class="border-t border-slate-200/70"></div>

                            {{-- Phone --}}
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 size-5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.11 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.98.35 1.94.66 2.87a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.21-1.23a2 2 0 0 1 2.11-.45c.93.31 1.89.53 2.87.66A2 2 0 0 1 22 16.92z"/>
                                </svg>
                                <div class="opacity-80">{{ $selected->contact_phone ?: ($phone ?? '') }}</div>
                            </div>

                            <div class="border-t border-slate-200/70"></div>

                            {{-- Address --}}
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="mt-0.5 size-5 opacity-70" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M20 10c0 4.993-5.539 10.193-7.399 11.799a1 1 0 0 1-1.202 0C9.539 20.193 4 14.993 4 10a8 8 0 0 1 16 0"/>
                                    <circle cx="12" cy="10" r="3"/>
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
                                    <span class="text-[11px] opacity-60">You can update this for this order.</span>
                                    <span class="text-[11px] opacity-60" x-text="`${(note || '').length}/200`"></span>
                                </div>
                                <textarea
                                    x-model="note"
                                    @blur="$wire.set('customer_note', note, true)"
                                    rows="3"
                                    maxlength="200"
                                    class="mt-1 block w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-red-400 focus:ring-2 focus:ring-red-200"
                                ></textarea>
                                @error('customer_note')
                                    <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    @else
                        <div class="rounded-lg border border-amber-200 bg-amber-50 text-amber-900 text-sm px-3 py-2">
                            You don’t have any saved address yet. Please
                            <a class="text-red-600 underline underline-offset-2" href="{{ route('address.create') }}">add one</a> to continue.
                        </div>
                    @endif
                </section>

                {{-- Address Select Modal --}}
                <livewire:frontend.checkout.modal.address-modal :addresses="$addresses" :selected-address-id="$selectedAddressId" />

                {{-- Payment --}}
                <section class="rounded-2xl border border-slate-200/70 bg-white/70 shadow-sm backdrop-blur p-6">
                    <header class="flex items-center gap-3 mb-5">
                        <div class="size-10 rounded-xl grid place-items-center bg-gradient-to-tr from-customRed-100/25 to-customRed-200/10 text-customRed-100">
                            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 7V4a1 1 0 0 0-1-1H5a2 2 0 0 0 0 4h15a1 1 0 0 1 1 1v4h-3a2 2 0 0 0 0 4h3a1 1 0 0 0 1-1v-2a1 1 0 0 0-1-1"/>
                                <path d="M3 5v14a2 2 0 0 0 2 2h15a1 1 0 0 0 1-1v-4"/>
                            </svg>
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold leading-none">Payment method</h2>
                            <p class="text-xs opacity-70">Choose how you want to pay.</p>
                        </div>
                    </header>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <label class="group relative cursor-pointer rounded-xl border border-customRed-100/70 p-4 hover:border-customRed-100/90 transition">
                            <input type="radio" class="radio absolute opacity-0" value="cod" wire:model="payment_method">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-lg grid place-items-center bg-customRed-100 text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <rect width="20" height="12" x="2" y="6" rx="2"/>
                                        <circle cx="12" cy="12" r="2"/>
                                        <path d="M6 12h.01M18 12h.01"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium leading-none">Cash on delivery</p>
                                    <p class="text-xs opacity-70 mt-1">Pay at your doorstep</p>
                                </div>
                                <div class="ml-auto size-5 rounded-full border group-has-[input:checked]:bg-customRed-100/90 group-has-[input:checked]:border-customRed-100/90"></div>
                            </div>
                        </label>

                        <label class="group relative cursor-pointer rounded-xl border border-customRed-100/70 p-4 hover:border-customRed-200/90 transition">
                            <input type="radio" class="radio absolute opacity-0" value="sslcommerz" wire:model="payment_method">
                            <div class="flex items-center gap-3">
                                <div class="size-10 rounded-lg grid place-items-center bg-customRed-100 text-white">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M20 13c0 5-3.5 7.5-7.66 8.95a1 1 0 0 1-.67-.01C7.5 20.5 4 18 4 13V6a1 1 0 0 1 1-1c2 0 4.5-1.2 6.24-2.72a1.17 1.17 0 0 1 1.52 0C14.51 3.81 17 5 19 5a1 1 0 0 1 1 1z"/>
                                        <path d="m9 12 2 2 4-4"/>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-medium leading-none">SSLCommerz</p>
                                    <p class="text-xs opacity-70 mt-1">Secure online payment</p>
                                </div>
                                <div class="ml-auto size-5 rounded-full border group-has-[input:checked]:bg-customRed-100/90 group-has-[input:checked]:border-customRed-100/90"></div>
                            </div>
                        </label>
                    </div>

                    @error('payment_method') <p class="text-error text-xs mt-3">{{ $message }}</p> @enderror
                </section>
            </div>

            {{-- Right column --}}
            <div>
                <div class="rounded-xl border p-6 space-y-3">
                    <h2 class="text-lg font-semibold">Order Summary</h2>

                    <div class="flex items-center justify-between text-sm">
                        <span class="opacity-80">Items subtotal</span>
                        <span>{{ number_format($subtotal, 2) }} <span class="font-oswald">৳</span></span>
                    </div>

                    <div class="flex items-center justify-between text-sm">
                        <span class="opacity-80">Discount</span>
                        <span>(-) {{ number_format($discount_total, 2) }} <span class="font-oswald">৳</span></span>
                    </div>

                    @if ($shipSetting)
                        <div class="flex items-center justify-between text-sm">
                            <span class="opacity-80">Delivery fee
                                @if ($shipSetting->free_delivery)
                                    <span class="text-xs opacity-60">(Free over {{ number_format($shipSetting->free_minimum, 2) }} ৳)</span>
                                @endif
                            </span>
                            @if ($shipSetting->free_delivery && $subtotal >= $shipSetting->free_minimum)
                                <span class="text-success">0.00 <span class="font-oswald">৳</span></span>
                            @else
                                <span>(+) {{ number_format($shipping_total, 2) }} <span class="font-oswald">৳</span></span>
                            @endif
                        </div>
                    @endif

                    <div class="border-t pt-3 flex items-center justify-between">
                        <span class="font-semibold">Grand total</span>
                        <span class="text-xl font-semibold text-red-500">
                            {{ number_format($grand_total, 2) }} <span class="font-oswald">৳</span>
                        </span>
                    </div>

                    <button
                        wire:click="placeOrder"
                        wire:loading.attr="disabled"
                        wire:target="placeOrder"
                        class="btn bg-customRed-100 text-white w-full h-12 rounded-xl disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove wire:target="placeOrder">Place order</span>
                        <span wire:loading wire:target="placeOrder" class="inline-flex items-center gap-2">
                            <svg class="animate-spin size-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z"/>
                            </svg>
                            Placing…
                        </span>
                    </button>

                    <p class="text-xs opacity-60">By placing this order, you agree to our Terms & Refund Policy.</p>
                </div>
            </div>
        </div>
    </div>
</div>
