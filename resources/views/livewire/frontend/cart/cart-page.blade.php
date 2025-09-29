<div>

    <!-- Breadcrumb -->
    <div
        class="bg-[url(/assets/images/breadcrumb-bg.jpg)] py-20 md:py-32 bg-no-repeat bg-cover bg-center text-center text-white grid place-items-center font-oswald">
        <h4 class="text-4xl md:text-6xl font-medium">Cart</h4>
        <div class="breadcrumbs text-sm mt-3 font-oswald font-medium">
            <ul class="flex items-center gap-2">
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
            <div class="rounded-xl border p-6 text-center">
                <p class="opacity-70">Your cart is empty.</p>
                <a href="{{ url('/') }}" class="btn btn-primary mt-4">Continue Shopping</a>
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
                                        <div class="text-sm">
                                            <div class="opacity-70">Unit price</div>
                                            <div class="font-medium">
                                                {{ number_format($item->unit_price, 2) }} <span
                                                    class="font-oswald">৳</span>
                                            </div>
                                        </div>

                                        <div class="flex items-center gap-2">
                                            <button class="btn btn-circle btn-ghost"
                                                @click.prevent="$wire.decrementQty({{ $item->id }})">–</button>

                                            <input type="number" min="1" max="99"
                                                class="input input-bordered input-sm w-16 text-center"
                                                value="{{ $item->qty }}"
                                                oninput="this.value = Math.max(1, Math.min(99, parseInt(this.value || 1)));"
                                                @change.prevent="$wire.changeQty({{ $item->id }}, parseInt($event.target.value))" />

                                            <button class="btn btn-circle btn-ghost"
                                                @click.prevent="$wire.incrementQty({{ $item->id }})">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-between">
                        <button class="btn btn-ghost" wire:click="clearCart">Clear cart</button>
                        <a href="{{ url('/') }}" class="btn btn-outline">Continue Shopping</a>
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
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-10 ps-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-ticket-percent-icon lucide-ticket-percent"><path d="M2 9a3 3 0 1 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 1 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z"/><path d="M9 9h.01"/><path d="m15 9-6 6"/><path d="M15 15h.01"/></svg>
                                <input type="text"
                                    class="border-0 ring-0 focus:ring-0 focus:outline-none focus:border-0 outline-none w-full"
                                    placeholder="Enter coupon code" wire:model.defer="coupon_code" />
                                <button class="btn bg-customRed-100 text-white" wire:click="applyCoupon">Apply</button>
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

                        <button class="btn bg-customRed-100 text-white w-full h-12 rounded-xl">
                            Checkout
                        </button>
                    </div>
                </div>

            </div>
        @endif
    </div>



</div>
