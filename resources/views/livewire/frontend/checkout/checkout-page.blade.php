<div>
    {{-- Breadcrumb --}}
    <div
        class="bg-[url(/assets/images/breadcrumb-bg.jpg)] py-20 md:py-32 bg-no-repeat bg-cover bg-center text-center text-white grid place-items-center font-oswald">
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
                            <div
                                class="size-10 rounded-xl grid place-items-center bg-gradient-to-tr from-customRed-100/25 to-customRed-200/10 text-customRed-100">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-truck-icon lucide-truck">
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
                                <p class="text-xs opacity-70 mt-1">Your selected shipping details.</p>
                            </div>
                        </div>

                        @php
                            $selected =
                                collect($addresses ?? [])->firstWhere('id', $selectedAddressId) ??
                                (collect($addresses ?? [])->firstWhere('is_default', true) ??
                                    collect($addresses ?? [])->first());
                        @endphp

                        <button type="button" class="text-amber-500 hover:text-amber-600 text-sm font-medium" x-data
                            @click="$dispatch('address-modal:open', { selectedId: {{ $selectedAddressId ?? 'null' }} })">
                            Update Info
                        </button>
                    </div>

                    @if ($selected)
                        <div class="space-y-4 text-[15px]">
                            {{-- Name --}}
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-5 opacity-70" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-user-icon lucide-user">
                                    <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                    <circle cx="12" cy="7" r="4" />
                                </svg>
                                <div class="font-medium">{{ $selected->contact_name ?: $contact_name ?? '' }}</div>
                            </div>

                            <div class="border-t border-slate-200/70"></div>

                            {{-- Phone --}}
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-[18px] opacity-70"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round"
                                    class="lucide lucide-phone-icon lucide-phone">
                                    <path
                                        d="M13.832 16.568a1 1 0 0 0 1.213-.303l.355-.465A2 2 0 0 1 17 15h3a2 2 0 0 1 2 2v3a2 2 0 0 1-2 2A18 18 0 0 1 2 4a2 2 0 0 1 2-2h3a2 2 0 0 1 2 2v3a2 2 0 0 1-.8 1.6l-.468.351a1 1 0 0 0-.292 1.233 14 14 0 0 0 6.392 6.384" />
                                </svg>
                                <div class="opacity-80">{{ $selected->contact_phone ?: $phone ?? '' }}</div>
                            </div>

                            <div class="border-t border-slate-200/70"></div>

                            {{-- Address --}}
                            <div class="flex items-start gap-3">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-9 opacity-70" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round" class="lucide lucide-map-pin-icon lucide-map-pin">
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
                                    <span class="text-[11px] opacity-60">You can update this for this order.</span>
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
                <livewire:frontend.checkout.modal.address-modal :addresses="$addresses" :selected-address-id="$selectedAddressId" />

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
                            <p class="text-xs opacity-70">Choose how you want to pay.</p>
                        </div>
                    </header>

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
                    <h2 class="text-lg font-semibold">Order Summary</h2>

                    {{-- Item Grand Total (cart total before delivery) --}}
                    <div class="flex items-center justify-between text-sm">
                        <span class="opacity-80">Items grand total</span>
                        <span>
                            {{ number_format((float) ($cart?->grand_total ?? 0), 2) }}
                            <span class="font-oswald">৳</span>
                        </span>
                    </div>

                    {{-- Delivery Fee --}}
                    @if ($shipSetting)
                        @php
                            $itemsGrand = (float) ($cart?->grand_total ?? 0);
                            $freeMin = (float) ($shipSetting->free_minimum ?? 0);
                            $isFree = (bool) ($shipSetting->free_delivery ?? false);

                            $deliveryFee =
                                $isFree && $itemsGrand >= $freeMin
                                    ? 0.0
                                    : (float) ($shipping_total ?? ($shipSetting->base_fee ?? 0));
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

                            @if ($deliveryFee == 0)
                                <span class="text-success">0.00 <span class="font-oswald">৳</span></span>
                            @else
                                <span>
                                    (+) {{ number_format($deliveryFee, 2) }}
                                    <span class="font-oswald">৳</span>
                                </span>
                            @endif
                        </div>
                    @endif

                    {{-- Payable Total --}}
                    <div class="border-t pt-3 flex items-center justify-between">
                        <span class="font-semibold">Payable total</span>
                        <span class="text-xl font-semibold text-red-500">
                            {{ number_format((float) ($grand_total ?? 0), 2) }}
                            <span class="font-oswald">৳</span>
                        </span>
                    </div>

                    <button wire:click="placeOrder" wire:loading.attr="disabled" wire:target="placeOrder"
                        class="btn bg-customRed-100 text-white w-full h-12 rounded-xl disabled:opacity-70 disabled:cursor-not-allowed flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="placeOrder">Place order</span>
                        <span wire:loading wire:target="placeOrder" class="inline-flex items-center gap-2">
                            <svg class="animate-spin size-4" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10"
                                    stroke="currentColor" stroke-width="4" />
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 0 1 8-8v4a4 4 0 0 0-4 4H4z" />
                            </svg>
                            Placing…
                        </span>
                    </button>

                    <p class="text-xs opacity-60">
                        By placing this order, you agree to our Terms & Refund Policy.
                    </p>
                </div>
            </div>

        </div>
    </div>
</div>
