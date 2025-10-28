<div>

    <!-- Breadcrumb -->
    <div
        class="bg-[url(/assets/images/breadcrumb-bg.jpg)] py-20 md:py-32 bg-no-repeat bg-cover bg-center text-center text-white grid place-items-center font-oswald">
        <h4 class="text-4xl md:text-6xl font-medium">Cart</h4>
        <div class="breadcrumbs text-sm mt-3 font-medium">
            <ul class="flex items-center">
                <li>
                    <a>
                        <i class="fa-regular fa-house"></i>
                        Home
                    </a>
                </li>
                <li>Cart</li>
            </ul>
        </div>
    </div>


    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-20">
        <h1 class="text-2xl font-semibold mb-4">Your Cart</h1>

        @if (!$cart || $cart->items->isEmpty())
            <div class="rounded-xl border p-6 text-center bg-customRed-100/10">
                <i class="fa-regular fa-box-open text-8xl mb-4 text-neutral-500"></i>
                <p class="opacity-90 text-lg font-medium mb-2">Your cart is empty!</p>
                <p class="text-sm opacity-70">Please add some dishes from menu.</p>
                <a href="{{ url('/') }}" class="inline-block bg-customRed-100 text-white mt-2 px-8 py-3 rounded">Explore Our Dishes</a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Items -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($cart->items as $item)
                        <div class="rounded-xl border p-4">
                            <div class="flex items-start gap-4">
                                <!-- Thumb -->
                                <img src="{{ asset($item->dish?->thumbnail ?? 'https://placehold.co/120x90') }}"
                                    alt="{{ $item->dish?->title }}" class="w-28 h-20 object-cover rounded-lg" />

                                <div class="flex-1">
                                    <div class="flex items-start justify-between">
                                        <div>
                                            <h3 class="font-semibold text-lg">{{ $item->dish?->title }}</h3>
                                            @if ($item->dish?->short_description)
                                                <p class="text-sm opacity-70">{{ $item->dish->short_description }}</p>
                                            @endif
                                        </div>

                                        <!-- Remove -->
                                        <button class="btn btn-ghost btn-sm"
                                            wire:click="removeItem({{ $item->id }})" title="Remove item">
                                            ✕
                                        </button>
                                    </div>

                                    <!-- Selections -->
                                    <div class="mt-2 text-sm space-y-1">
                                        @if ($item->crust)
                                            <div class="flex gap-2">
                                                <span class="opacity-70">Crust:</span>
                                                <span>{{ $item->crust->name }}</span>
                                                @if (data_get($item->meta, 'crust_extra', 0) > 0)
                                                    <span
                                                        class="opacity-70">(+{{ number_format(data_get($item->meta, 'crust_extra', 0), 2) }}
                                                        ৳)</span>
                                                @endif
                                            </div>
                                        @endif

                                        @if ($item->bun)
                                            <div class="flex gap-2">
                                                <span class="opacity-70">Bun:</span>
                                                <span>{{ $item->bun->name }}</span>
                                            </div>
                                        @endif

                                        @php
                                            $addonNames = $item->selectedAddOns()->pluck('name')->all();
                                            $addonsExtra = (float) data_get($item->meta, 'addons_extra', 0);
                                        @endphp
                                        @if (!empty($addonNames))
                                            <div class="flex gap-2">
                                                <span class="opacity-70">Add-ons:</span>
                                                <span>{{ implode(', ', $addonNames) }}</span>
                                                @if ($addonsExtra > 0)
                                                    <span class="opacity-70">(+{{ number_format($addonsExtra, 2) }}
                                                        ৳)</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Price + Qty + Line total -->
                                    <div class="mt-4 flex flex-wrap items-center justify-between gap-4">
                                        {{-- <div class="text-sm">
                                            <div class="opacity-70">Unit price</div>
                                            <div class="font-medium">
                                                <span
                                                    class="font-oswald">৳</span> 
                                                {{ number_format($item->unit_price, 2) }} 
                                            </div>
                                        </div> --}}

                                        @php
                                            $crustExtra = (float) data_get($item->meta, 'crust_extra', 0);
                                            $addonsExtra = (float) data_get($item->meta, 'addons_extra', 0);
                                            $extrasTotal = $crustExtra + $addonsExtra;

                                            // Original (no-discount) base the repo saved
                                            $originalBase = (float) data_get(
                                                $item->meta,
                                                'base',
                                                $item->unit_price - $extrasTotal,
                                            );

                                            // Discounted base (if present)
                                            $discountBase = data_get($item->meta, 'display_price_with_discount');
                                            $discountBase = is_null($discountBase) ? null : (float) $discountBase;

                                            $hasDiscount = !is_null($discountBase) && $discountBase < $originalBase;

                                            $originalUnit = $originalBase + $extrasTotal;
                                            $discountedUnit =
                                                ($hasDiscount ? $discountBase : $originalBase) + $extrasTotal;
                                        @endphp

                                        <div class="text-sm">
                                            <div class="opacity-70">Unit Price:</div>
                                            <div class="font-medium flex items-center gap-2">
                                                @if ($hasDiscount)
                                                    <span class="text-lg">
                                                        {{ number_format($discountedUnit, 2) }} <span
                                                            class="font-oswald">৳</span>
                                                    </span>
                                                    <del class="opacity-60">
                                                        {{ number_format($originalUnit, 2) }} <span
                                                            class="font-oswald">৳</span>
                                                    </del>
                                                @else
                                                    <span class="text-lg">
                                                        {{ number_format($originalUnit, 2) }} <span
                                                            class="font-oswald">৳</span>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>


                                        <div class="flex items-center gap-2">
                                            <button class="font-medium cursor-pointer"
                                                @click.prevent="$wire.decrementQty({{ $item->id }})">–</button>

                                            <input type="number" min="1" max="99"
                                                class="rounded-lg w-16 text-center focus:outline-none focus:ring-customRed-100 border border-gray-300"
                                                value="{{ $item->qty }}"
                                                oninput="this.value = Math.max(1, Math.min(99, parseInt(this.value || 1)));"
                                                @change.prevent="$wire.changeQty({{ $item->id }}, parseInt($event.target.value))" />

                                            <button class="font-medium cursor-pointer"
                                                @click.prevent="$wire.incrementQty({{ $item->id }})">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-between">
                        <button class="btn btn-ghost font-medium cursor-pointer" wire:click="clearCart">Clear cart</button>
                        <a href="{{ url('/') }}" class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium tracking-wide transition-colors bg-white border-2 rounded-md text-slate-900 hover:text-white border-slate-900 hover:bg-slate-900 duration-300">Continue Shopping</a>
                    </div>
                </div>

                <!-- Summary -->
                <div class="space-y-3">
                    <!-- Coupon box -->
                    <div class="rounded-lg border p-3 space-y-2">
                        @php
                            $applied = data_get($cart?->meta, 'coupon.code');
                            $calculated = (float) data_get($cart?->meta, 'coupon.calculated', 0);
                        @endphp

                        @if ($applied)
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm opacity-70">Applied coupon</div>
                                    <div class="font-medium">{{ $applied }}</div>
                                    @if ($calculated > 0)
                                        <div class="text-xs opacity-70">Saves {{ number_format($calculated, 2) }} ৳
                                        </div>
                                    @endif
                                </div>
                                <button class="btn btn-ghost btn-sm" wire:click="removeCoupon">Remove</button>
                            </div>
                        @else
                            <div class="flex items-center border rounded-lg gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-10 ps-3" viewBox="0 0 24 24"
                                    fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                    stroke-linejoin="round"
                                    class="lucide lucide-ticket-percent-icon lucide-ticket-percent">
                                    <path
                                        d="M2 9a3 3 0 1 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 1 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                                    <path d="M9 9h.01" />
                                    <path d="m15 9-6 6" />
                                    <path d="M15 15h.01" />
                                </svg>
                                <input type="text"
                                    class="border-0 ring-0 focus:ring-0 focus:outline-none focus:border-0 outline-none w-full"
                                    placeholder="Enter coupon code" wire:model.defer="coupon_code" />
                                <button class="bg-customRed-100 text-white px-2 py-2 rounded cursor-pointer hover:bg-customRed-200" wire:click="applyCoupon">Apply</button>
                            </div>
                            @if ($coupon_feedback)
                                <div
                                    class="text-xs {{ str_contains(strtolower($coupon_feedback), 'applied') ? 'text-success' : 'text-error' }}">
                                    {{ $coupon_feedback }}
                                </div>
                            @endif
                        @endif
                    </div>

                    <div class="rounded-xl border p-4 space-y-3">
                        <h4 class="font-semibold text-lg">Summary</h4>

                        <div class="flex items-center justify-between">
                            <span class="opacity-80">Item price</span>
                            <span>{{ number_format($this->product_price_subtotal, 2) }} <span
                                    class="font-oswald">৳</span></span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="opacity-80">Discount</span>
                            <span>(-) {{ number_format($this->product_discount_subtotal, 2) }} <span
                                    class="font-oswald">৳</span></span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="opacity-80">Add-ons</span>
                            <span>(+) {{ number_format($this->addons_subtotal, 2) }} <span
                                    class="font-oswald">৳</span></span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="opacity-80">Coupon discount</span>
                            <span>(-) {{ number_format($this->coupon_discount_total, 2) }} <span
                                    class="font-oswald">৳</span></span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="opacity-80">Vat/Tax</span>
                            <span>(+) {{ number_format($this->tax_total, 2) }} <span
                                    class="font-oswald">৳</span></span>
                        </div>

                        <div class="border-t pt-3 flex items-center justify-between">
                            <span class="font-semibold">Grand total</span>
                            <span class="text-xl font-semibold text-red-500">
                                {{ number_format($this->grand_total, 2) }} <span class="font-oswald">৳</span>
                            </span>
                        </div>

                        <div class="flex text-center">
                            <a href="{{ route('checkout') }}" class="bg-customRed-100 hover:bg-customRed-200 duration-200 text-white w-full py-3 rounded-xl">
                            Checkout
                        </a>
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>



</div>
