<x-layouts.frontend>
    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-16">

        {{-- Top icon + message --}}
        <div class="text-center">
            <div class="mx-auto mb-6 grid place-items-center">
                <div class="size-14 rounded-full bg-customRed-100/15 grid place-items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="size-7 text-customRed-100" viewBox="0 0 24 24" fill="none" stroke="currentColor">
                        <path stroke-width="2" stroke-linecap="round" stroke-linejoin="round" d="M20 6L9 17l-5-5" />
                    </svg>
                </div>
            </div>

            <h1 class="text-3xl md:text-4xl font-semibold mb-2">Thank you for your purchase</h1>
            <p class="text-neutral-600">
                We’ve received your order. It will take <span class="font-semibold">30–45 minutes</span> to deliver.
                <br>
                Your order number is <span class="font-semibold">#{{ $order->order_code }}</span>
            </p>
        </div>

        @php
            $formatCurrency = function ($amount) {
                return number_format((float) $amount, 2);
            };

            $orderItemsBaseTotal = 0.0; // sum of base_after_discount * qty
            $orderExtrasTotal    = 0.0; // sum of all extras (crust + bun + addons) for all items
        @endphp

        <div class="mt-10 grid gap-6 lg:grid-cols-[2fr,1.2fr] items-start">

            {{-- LEFT: items + details --}}
            <div class="w-full">
                <div class="rounded-2xl border bg-white shadow-sm">
                    <div class="px-6 py-4 border-b flex items-center justify-between">
                        <h2 class="text-lg font-semibold">Order Summary</h2>
                        <span class="text-xs rounded-full bg-emerald-50 text-emerald-700 px-2.5 py-1">
                            Status: {{ ucfirst($order->order_status ?? 'pending') }}
                        </span>
                    </div>

                    {{-- Items --}}
                    <div class="divide-y">
                        @foreach ($order->items as $item)
                            @php
                                $meta = $item->meta ?? [];
                                $qty  = (int) $item->qty;

                                // BASE PRICES
                                $baseOriginal = (float) data_get(
                                    $meta,
                                    'base_original',
                                    data_get($meta, 'base', $item->unit_price)
                                );

                                $baseAfterDiscount = (float) data_get(
                                    $meta,
                                    'base_after_discount',
                                    data_get($meta, 'display_price_with_discount', $baseOriginal)
                                );

                                $perUnitDiscount = max(0.0, $baseOriginal - $baseAfterDiscount);
                                $baseTotal       = $baseAfterDiscount * $qty;

                                // VARIATIONS FROM meta.variation_details
                                $variationDetails = (array) data_get($meta, 'variation_details', []);
                                $variationLines   = [];

                                if (!empty($variationDetails)) {
                                    foreach ($variationDetails as $vd) {
                                        $group = $vd['group_name'] ?? null;
                                        $label = $vd['label'] ?? null;
                                        $price = (float) ($vd['price'] ?? 0);

                                        if (!$label && !$group) {
                                            continue;
                                        }

                                        if ($group && $label) {
                                            $line = $group . ': ' . $label;
                                        } else {
                                            $line = $label ?? $group;
                                        }

                                        if ($price > 0) {
                                            $line .= ' (+' . number_format($price, 2) . ' ৳)';
                                        }

                                        $variationLines[] = $line;
                                    }
                                } else {
                                    // fallback for older orders
                                    $sizeLabel = data_get($meta, 'size')
                                        ?? data_get($meta, 'size_label')
                                        ?? data_get($meta, 'selected_size.label')
                                        ?? data_get($meta, 'selected_size')
                                        ?? data_get($meta, 'variant_label')
                                        ?? data_get($meta, 'variant.option_label')
                                        ?? data_get($meta, 'variant.label')
                                        ?? null;

                                    if ($sizeLabel) {
                                        $variationLines[] = 'Size: ' . $sizeLabel;
                                    }

                                    $simpleVariation = data_get($meta, 'variation_name')
                                        ?? data_get($meta, 'variation_label')
                                        ?? data_get($meta, 'variant_name')
                                        ?? data_get($meta, 'variant_label');

                                    if ($simpleVariation && !is_array($simpleVariation)) {
                                        $variationLines[] = (string) $simpleVariation;
                                    }
                                }

                                $variationLines = array_values(array_unique(array_filter($variationLines)));

                                // CRUST & BUN LABELS
                                $crustLabel = $item->crust?->name
                                    ?? $item->crust?->title
                                    ?? data_get($meta, 'crust')
                                    ?? data_get($meta, 'crust_label')
                                    ?? data_get($meta, 'crust.name')
                                    ?? data_get($meta, 'crust_title')
                                    ?? null;

                                $bunLabel = $item->bun?->name
                                    ?? $item->bun?->title
                                    ?? data_get($meta, 'bun')
                                    ?? data_get($meta, 'bun_label')
                                    ?? data_get($meta, 'bun.name')
                                    ?? data_get($meta, 'bun_title')
                                    ?? null;

                                // EXTRAS
                                $crustExtra  = (float) data_get($meta, 'crust_extra', 0);
                                $bunExtra    = (float) data_get($meta, 'bun_extra', 0);
                                $addonsExtra = (float) data_get($meta, 'addons_extra', 0);

                                $extrasPerUnit = $crustExtra + $bunExtra + $addonsExtra;
                                $extrasTotal   = $extrasPerUnit * $qty;

                                // accumulate order-level totals
                                $orderItemsBaseTotal += $baseTotal;
                                $orderExtrasTotal    += $extrasTotal;

                                // ADD-ONS DETAIL
                                $addonQtyMap = (array) data_get($meta, 'addon_qty', []);

                                $addonIds = (array) ($item->addon_ids ?? []);
                                $addonIds = array_filter($addonIds);

                                if (empty($addonIds) && !empty($addonQtyMap)) {
                                    $addonIds = array_keys($addonQtyMap);
                                }

                                $addonModels = collect();
                                if (!empty($addonIds)) {
                                    try {
                                        $addonModels = \App\Models\Addon::whereIn('id', $addonIds)->get();
                                    } catch (\Throwable $e) {
                                        $addonModels = collect();
                                    }
                                }

                                $totalAddonQtyPerDish = !empty($addonQtyMap)
                                    ? array_sum($addonQtyMap)
                                    : 0;

                                $perAddonUnitExtra = ($totalAddonQtyPerDish > 0)
                                    ? ($addonsExtra / $totalAddonQtyPerDish)
                                    : 0.0;

                                $addonLines = [];

                                foreach ($addonModels as $addon) {
                                    $addonId = $addon->id;
                                    $addonQtyPerDish = (int) ($addonQtyMap[$addonId] ?? 0);
                                    if ($addonQtyPerDish <= 0) continue;

                                    $addonName = $addon->title ?? $addon->name ?? ('Addon #'.$addonId);

                                    $addonTotalQty   = $addonQtyPerDish * $qty;
                                    $addonTotalPrice = $perAddonUnitExtra * $addonTotalQty;

                                    $addonLines[] = [
                                        'name'  => $addonName,
                                        'qty'   => $addonTotalQty,
                                        'total' => $addonTotalPrice,
                                    ];
                                }

                                if (empty($addonLines) && !empty($addonQtyMap)) {
                                    foreach ($addonQtyMap as $addonId => $addonQtyPerDish) {
                                        $addonTotalQty   = (int) $addonQtyPerDish * $qty;
                                        $addonTotalPrice = $perAddonUnitExtra * $addonTotalQty;

                                        $addonLines[] = [
                                            'name'  => 'Addon #'.$addonId,
                                            'qty'   => $addonTotalQty,
                                            'total' => $addonTotalPrice,
                                        ];
                                    }
                                }

                                $lineTotal = (float) $item->line_total;
                            @endphp

                            <div class="px-6 py-4 flex flex-col sm:flex-row gap-4 sm:items-start">
                                {{-- Thumb --}}
                                <img
                                    class="w-20 h-20 rounded-lg object-cover flex-shrink-0"
                                    src="{{ asset($item->dish?->thumbnail ?? 'https://placehold.co/80x80') }}"
                                    alt="{{ $item->dish?->title ?? 'Item' }}"
                                />

                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-start justify-between gap-2">
                                        <div class="min-w-0">
                                            <p class="font-medium truncate text-sm md:text-base">
                                                {{ $item->dish?->title ?? 'Item' }}
                                            </p>
                                            <p class="text-xs text-neutral-500">
                                                Qty: {{ $qty }}
                                            </p>
                                        </div>

                                        <div class="text-right text-sm md:text-base font-semibold whitespace-nowrap">
                                            {{ $formatCurrency($lineTotal) }} <span class="font-oswald">৳</span>
                                        </div>
                                    </div>

                                    {{-- Details: variations, bun, crust, add-ons --}}
                                    <div class="mt-2 space-y-1 text-xs text-neutral-600">
                                        @if (!empty($variationLines))
                                            <div>
                                                @foreach ($variationLines as $vline)
                                                    <div>{{ $vline }}</div>
                                                @endforeach
                                            </div>
                                        @endif

                                        @if ($bunLabel)
                                            <div>Bun: <span class="font-medium">{{ $bunLabel }}</span></div>
                                        @endif

                                        @if ($crustLabel)
                                            <div>Crust: <span class="font-medium">{{ $crustLabel }}</span></div>
                                        @endif

                                        @if (!empty($addonLines))
                                            <div>
                                                Add-ons:
                                                @foreach ($addonLines as $al)
                                                    <div class="pl-3 flex items-center gap-2">
                                                        <span>{{ $al['name'] }} × {{ $al['qty'] }}</span>
                                                        =
                                                        @if ($al['total'] > 0)
                                                            <span class="text-neutral-500">
                                                                {{ $formatCurrency($al['total']) }} ৳
                                                            </span>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Per item price breakdown --}}
                                    <div class="mt-2 text-[11px] text-neutral-600">
                                        <div>
                                            Item price:
                                            <span class="font-medium">
                                                {{ $formatCurrency($baseAfterDiscount) }} ৳
                                            </span>
                                            @if ($perUnitDiscount > 0)
                                                <span class="line-through text-neutral-400 ml-1">
                                                    {{ $formatCurrency($baseOriginal) }} ৳
                                                </span>
                                            @endif
                                            <span class="text-neutral-500">
                                                × {{ $qty }} = {{ $formatCurrency($baseTotal) }} ৳
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- RIGHT: Totals only --}}
            <div class="w-full space-y-4">

                {{-- Totals --}}
                <div class="rounded-2xl border bg-white shadow-sm">
                    <div class="px-6 py-4 border-b">
                        <h2 class="text-lg font-semibold">Payment Summary</h2>
                    </div>

                    <div class="px-6 py-4 text-sm">
                        <div class="flex items-center justify-between py-1">
                            <span class="text-neutral-600">Item Price</span>
                            <span class="font-medium">
                                {{ $formatCurrency($orderItemsBaseTotal) }} <span class="font-oswald">৳</span>
                            </span>
                        </div>

                        @if ($orderExtrasTotal > 0)
                            <div class="flex items-center justify-between py-1">
                                <span class="text-neutral-600">
                                    Extras Total (crust + bun + add-ons)
                                </span>
                                <span class="font-medium">
                                    {{ $formatCurrency($orderExtrasTotal) }} <span class="font-oswald">৳</span>
                                </span>
                            </div>
                        @endif

                        @if (($order->discount_total ?? 0) > 0)
                            <div class="flex items-center justify-between py-1">
                                <span class="text-neutral-600">Discount</span>
                                <span class="font-medium text-red-500">
                                    - {{ $formatCurrency($order->discount_total) }} <span class="font-oswald">৳</span>
                                </span>
                            </div>
                        @endif

                        <div class="flex items-center justify-between py-1">
                            <span class="text-neutral-600">Delivery Charge</span>
                            <span class="font-medium">
                                (+) {{ $formatCurrency($order->shipping_total) }} <span class="font-oswald">৳</span>
                            </span>
                        </div>

                        <div class="flex items-center justify-between py-1">
                            <span class="text-neutral-600">VAT / Tax</span>
                            <span class="font-medium">
                                {{ $formatCurrency($order->tax_total) }} <span class="font-oswald">৳</span>
                            </span>
                        </div>

                        <div class="mt-3 pt-3 border-t border-neutral-200 flex items-center justify-between text-primary">
                            <span class="font-semibold text-base">Grand Total</span>
                            <span class="font-semibold text-base">
                                {{ $formatCurrency($order->grand_total) }} <span class="font-oswald">৳</span>
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <div class="mt-8 text-center">
            <a href="/"
               class="inline-flex items-center justify-center bg-customRed-100 text-white mt-2 px-5 py-3 rounded-md text-sm font-medium hover:bg-customRed-100/90 transition">
                Back to Home
            </a>
        </div>
    </div>
</x-layouts.frontend>
