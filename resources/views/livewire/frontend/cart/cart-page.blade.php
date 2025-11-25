<div>

    @push('styles')
        {{-- Marquee animation (only applied when overflow=true) --}}
        <style>
            @keyframes marqueeScroll {
                0%   { transform: translateX(0); }
                100% { transform: translateX(-100%); }
            }
            .animate-marquee {
                animation: marqueeScroll 12s linear infinite;
                padding-left: 100%;
            }
        </style>
    @endpush

    <!-- Breadcrumb -->
    {{-- <div
        class="bg-[url(/assets/images/breadcrumb-bg.jpg)] py-20 md:py-32 bg-no-repeat bg-cover bg-center text-center text-white grid place-items-center font-oswald">
        <h4 class="text-4xl md:text-6xl font-medium">Cart</h4>
        <div class="breadcrumbs text-sm mt-3 font-medium">
            <ul class="flex items-center">
                <li><a><i class="fa-regular fa-house"></i> Home</a></li>
                <li>Cart</li>
            </ul>
        </div>
    </div> --}}

    <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-10 pb-20">
        <h1 class="text-2xl font-semibold mb-4">Your Cart</h1>

        @if (!$cart || $cart->items->isEmpty())
            <div class="rounded-xl border p-6 text-center bg-customRed-100/10">
                <i class="fa-regular fa-box-open text-8xl mb-4 text-neutral-500"></i>
                <p class="opacity-90 text-lg font-medium mb-2">Your cart is empty!</p>
                <p class="text-sm opacity-70">Please add some dishes from menu.</p>
                <a href="{{ url('/') }}" class="inline-block bg-customRed-100 text-white mt-2 px-8 py-3 rounded">
                    Explore Our Dishes
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

                <!-- Items -->
                <div class="lg:col-span-2 space-y-4">
                    @foreach ($cart->items as $item)
                        @php
                            // ---------- PRICE BUILD ----------
                            $crustExtra   = (float) data_get($item->meta, 'crust_extra', 0);
                            $bunExtra     = (float) data_get($item->meta, 'bun_extra', 0);
                            $addonsExtra  = (float) data_get($item->meta, 'addons_extra', 0);
                            $extrasTotal  = $crustExtra + $bunExtra + $addonsExtra;

                            $baseOriginal = (float) data_get(
                                $item->meta,
                                'base_original',
                                data_get($item->meta, 'base', $item->unit_price - $extrasTotal)
                            );

                            $baseAfterDiscount = (float) data_get(
                                $item->meta,
                                'base_after_discount',
                                data_get($item->meta, 'display_price_with_discount', $baseOriginal)
                            );

                            // Final unit price (after discount + extras)
                            $displayUnit = $baseAfterDiscount + $extrasTotal;

                            // Line total
                            $qty       = (int) $item->qty;
                            $lineTotal = $displayUnit * $qty;

                            // ---------- SELECTION TEXT (crust, bun, variations, add-ons with qty) ----------

                            // Add-on qty map from meta/accessor
                            $addonQtyMap = $item->addon_quantities ?? data_get($item->meta, 'addon_qty', []);

                            // Build labels like: "Cheese × 2", "Sausage × 1"
                            $addonLabelParts = [];
                            foreach ($item->selectedAddOns() as $addon) {
                                $perUnitQty = max(1, (int) ($addonQtyMap[$addon->id] ?? 1));
                                $label = $addon->name;

                                if ($perUnitQty > 1) {
                                    $label .= ' × ' . $perUnitQty;
                                }

                                $addonLabelParts[] = $label;
                            }

                            $variationSelection = data_get(
                                $item->meta,
                                'variation_selection',
                                $item->variation_selection ?? []
                            );
                            $dishVariations = (array) ($item->dish?->variations ?? []);

                            $selectionParts = [];

                            // Crust
                            if ($item->crust) {
                                $t = "Crust: {$item->crust->name}";
                                if ($crustExtra > 0) {
                                    $t .= ' (+' . number_format($crustExtra, 2) . ' ৳)';
                                }
                                $selectionParts[] = $t;
                            }

                            // Bun
                            if ($item->bun) {
                                $t = "Bun: {$item->bun->name}";
                                if ($bunExtra > 0) {
                                    $t .= ' (+' . number_format($bunExtra, 2) . ' ৳)';
                                }
                                $selectionParts[] = $t;
                            }

                            // Variations (supports numeric or named keys)
                            if (!empty($variationSelection) && !empty($dishVariations)) {
                                foreach ($dishVariations as $gIndex => $group) {
                                    $groupName = data_get($group, 'name', 'Variation');
                                    $groupKey  = data_get($group, 'key', $groupName);

                                    // Try numeric index, then group key or name
                                    $optIndex =
                                        $variationSelection[$gIndex] ??
                                        ($variationSelection[$groupKey] ?? ($variationSelection[$groupName] ?? null));

                                    if ($optIndex === null) {
                                        continue;
                                    }

                                    $opt = data_get($group, "options.$optIndex");
                                    if (!$opt) {
                                        continue;
                                    }

                                    $optName  = data_get($opt, 'name');
                                    $optPrice = (float) data_get($opt, 'price', 0);

                                    if ($optName) {
                                        $t = "{$groupName}: {$optName}";
                                        if ($optPrice > 0) {
                                            $t .= ' (+' . number_format($optPrice, 2) . ' ৳)';
                                        }
                                        $selectionParts[] = $t;
                                    }
                                }
                            }

                            // Add-ons with quantities
                            if (!empty($addonLabelParts)) {
                                $t = 'Add-ons: ' . implode(', ', $addonLabelParts);
                                if ($addonsExtra > 0) {
                                    $t .= ' (+' . number_format($addonsExtra, 2) . ' ৳)';
                                }
                                $selectionParts[] = $t;
                            }

                            $selectionText = implode(' • ', $selectionParts);
                        @endphp

                        <div class="rounded-xl border p-3 md:p-4">
                            <div class="flex flex-col sm:flex-row sm:items-start gap-3 sm:gap-4">

                                <!-- Thumb -->
                                <img src="{{ asset($item->dish?->thumbnail ?? 'https://placehold.co/120x90') }}"
                                     alt="{{ $item->dish?->title }}"
                                     class="w-full sm:w-28 h-28 sm:h-20 object-cover rounded-lg" />

                                <div class="flex-1 w-full">
                                    <div class="flex items-start justify-between gap-2">
                                        <h3 class="font-semibold text-base md:text-lg">
                                            {{ $item->dish?->title }}
                                        </h3>

                                        <!-- Line total in header right (old position) -->
                                        <div class="text-right">
                                            {{-- <div class="text-[11px] md:text-xs opacity-70">Total</div> --}}
                                            <div class="font-semibold text-base md:text-lg text-slate-900">
                                                {{ number_format($lineTotal, 2) }}
                                                <span class="font-oswald">৳</span>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Selections + Qty --}}
                                    <div class="mt-2 flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3">

                                        {{-- Left: Selections (marquee when overflow) --}}
                                        <div class="flex-1 min-w-0">
                                            @if ($selectionText)
                                                <div
                                                    x-data="{
                                                        overflow: false,
                                                        init() {
                                                            this.$nextTick(() => {
                                                                const el = this.$refs.marq;
                                                                const check = () => {
                                                                    this.overflow = el.scrollWidth > el.clientWidth;
                                                                };
                                                                check();
                                                                window.addEventListener('resize', check);
                                                            });
                                                        }
                                                    }"
                                                    class="text-xs sm:text-sm text-slate-700 overflow-hidden"
                                                >
                                                    <div
                                                        x-ref="marq"
                                                        :class="overflow
                                                            ? 'inline-block whitespace-nowrap animate-marquee'
                                                            : 'block whitespace-normal break-words'"
                                                        class="will-change-transform"
                                                    >
                                                        <div class="text-sm">
                                                            {{ $selectionText }}
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <p class="text-xs sm:text-sm text-slate-500 opacity-70">
                                                    No extra selections.
                                                </p>
                                            @endif
                                        </div>

                                        {{-- Right: Qty controls (with trash when qty = 1) --}}
                                        <div class="flex items-center gap-2 sm:gap-3 shrink-0">
                                            @if ($item->qty > 1)
                                                {{-- Minus button --}}
                                                <button
                                                    class="font-medium cursor-pointer w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 text-sm"
                                                    @click.prevent="$wire.decrementQty({{ $item->id }})">
                                                    –
                                                </button>
                                            @else
                                                {{-- Trash when qty == 1 --}}
                                                <button
                                                    class="font-medium cursor-pointer w-8 h-8 rounded-full border border-red-300 flex items-center justify-center hover:bg-red-50 text-red-500 text-sm"
                                                    @click.prevent="$wire.removeItem({{ $item->id }})"
                                                    title="Remove item">
                                                    <i class="fa-regular fa-trash-can text-[13px]"></i>
                                                </button>
                                            @endif

                                            <input type="number" min="1" max="99"
                                                   class="rounded-lg w-14 sm:w-16 text-center focus:outline-none focus:ring-customRed-100 border border-gray-300 text-xs sm:text-sm py-1"
                                                   value="{{ $item->qty }}"
                                                   oninput="this.value = Math.max(1, Math.min(99, parseInt(this.value || 1)));"
                                                   @change.prevent="$wire.changeQty({{ $item->id }}, parseInt($event.target.value))" />

                                            <button
                                                class="font-medium cursor-pointer w-8 h-8 rounded-full border border-gray-300 flex items-center justify-center hover:bg-gray-100 text-sm"
                                                @click.prevent="$wire.incrementQty({{ $item->id }})">
                                                +
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach

                    <div class="flex items-center justify-between mt-2">
                        <button class="btn btn-ghost font-medium cursor-pointer" wire:click="clearCart">
                            Clear cart
                        </button>
                        <a href="{{ route('fontDishes.index') }}"
                           class="inline-flex items-center justify-center px-4 py-2 text-sm font-medium tracking-wide transition-colors bg-white border rounded-md text-slate-900 hover:text-white border-slate-900 hover:bg-slate-900 duration-300">
                            Continue Shopping
                        </a>
                    </div>
                </div>

                <!-- Summary -->
                <div class="space-y-3">
                    <!-- Coupon box -->
                    <div class="rounded-lg border p-3 space-y-2">
                        @php
                            $applied    = data_get($cart?->meta, 'coupon.code');
                            $calculated = (float) data_get($cart?->meta, 'coupon.calculated', 0);
                        @endphp

                        @if ($applied)
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-sm opacity-70">Applied coupon</div>
                                    <div class="font-medium">{{ $applied }}</div>
                                    @if ($calculated > 0)
                                        <div class="text-xs opacity-70">
                                            Saves {{ number_format($calculated, 2) }} ৳
                                        </div>
                                    @endif
                                </div>
                                <button class="btn btn-ghost btn-sm" wire:click="removeCoupon">Remove</button>
                            </div>
                        @else
                            <div class="flex items-center border rounded-lg gap-2">
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-10 ps-3" viewBox="0 0 24 24"
                                     fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"
                                     stroke-linejoin="round">
                                    <path
                                        d="M2 9a3 3 0 1 1 0 6v2a2 2 0 0 0 2 2h16a2 2 0 0 0 2-2v-2a3 3 0 1 1 0-6V7a2 2 0 0 0-2-2H4a2 2 0 0 0-2 2Z" />
                                    <path d="M9 9h.01" />
                                    <path d="m15 9-6 6" />
                                    <path d="M15 15h.01" />
                                </svg>
                                <input type="text"
                                       class="border-0 ring-0 focus:ring-0 focus:outline-none focus:border-0 outline-none w-full"
                                       placeholder="Enter coupon code"
                                       wire:model.defer="coupon_code" />
                                <button
                                    class="bg-customRed-100 text-white px-2 py-2 rounded cursor-pointer hover:bg-customRed-200"
                                    wire:click="applyCoupon">
                                    Apply
                                </button>
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
                            <span>
                                {{ number_format($this->product_price_subtotal, 2) }}
                                <span class="font-oswald">৳</span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="opacity-80">Discount</span>
                            <span>
                                (-) {{ number_format($this->product_discount_subtotal, 2) }}
                                <span class="font-oswald">৳</span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="opacity-80">Add-ons</span>
                            <span>
                                (+) {{ number_format($this->addons_subtotal, 2) }}
                                <span class="font-oswald">৳</span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="opacity-80">Coupon discount</span>
                            <span>
                                (-) {{ number_format($this->coupon_discount_total, 2) }}
                                <span class="font-oswald">৳</span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between">
                            <span class="opacity-80">Vat/Tax</span>
                            <span>
                                (+) {{ number_format($this->tax_total, 2) }}
                                <span class="font-oswald">৳</span>
                            </span>
                        </div>

                        <div class="border-t pt-3 flex items-center justify-between">
                            <span class="font-semibold">Grand total</span>
                            <span class="text-xl font-semibold text-red-500">
                                {{ number_format($this->grand_total, 2) }}
                                <span class="font-oswald">৳</span>
                            </span>
                        </div>

                        <div class="flex text-center">
                            <a href="{{ route('checkout') }}"
                               class="bg-customRed-100 hover:bg-customRed-200 duration-200 text-white w-full py-3 rounded-xl flex gap-2 items-center justify-center font-semibold">
                                Proceed to checkout
                            </a>
                        </div>
                    </div>
                </div>

            </div>
        @endif
    </div>

</div>
