<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Meal Plan #{{ $booking->booking_code }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style>
        /* ===== Thermal 80mm preset ===== */
        @page {
            size: 80mm auto;
            margin: 0;
        }

        html, body {
            padding: 0;
            margin: 0;
            background: #fff;
        }

        .receipt {
            width: 80mm;
            padding: 6mm 4mm;
            box-sizing: border-box;
            font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
            font-size: 12px;
            line-height: 1.3;
            color: #000;
        }

        .center { text-align: center; }
        .right  { text-align: right; }
        .bold   { font-weight: 700; }
        .muted  { color: #000; opacity: .85; }
        .hr     { border-top: 1px dashed #000; margin: 6px 0; }

        .logo {
            display: block;
            margin: 0 auto 4px auto;
            max-width: 38mm;
        }

        .title { font-size: 13px; font-weight: 700; margin-bottom: 2px; }
        .sm    { font-size: 11px; }
        .xs    { font-size: 10px; }

        .row { display: flex; justify-content: space-between; gap: 6px; }
        .mt4 { margin-top: 4px; }
        .mt6 { margin-top: 6px; }
        .mt8 { margin-top: 8px; }

        .item { margin: 4px 0; }
        .item .name { display: block; }
        .item .meta { margin-left: 8px; }
    </style>

    <script>
        window.addEventListener('load', () => {
            window.print();
        });
        window.addEventListener('afterprint', () => {
            setTimeout(() => window.close(), 50);
        });
    </script>
</head>
<body>
@php
    /** @var \App\Models\MealPlanBooking $booking */

    // Decode address
    $addr = $booking->shipping_address ?? null;
    if (is_string($addr)) {
        $decoded = json_decode($addr, true);
        $addr = json_last_error() === JSON_ERROR_NONE ? $decoded : $addr;
    }
    $addressLine = '-';
    if (is_array($addr)) {
        $addressLine = implode(', ', array_filter([
            $addr['line1'] ?? null,
            $addr['city'] ?? null,
            $addr['postcode'] ?? null,
        ]));
        $addressLine = $addressLine === '' ? '-' : $addressLine;
    } elseif (is_string($addr) && trim($addr) !== '') {
        $addressLine = $addr;
    }

    // Collect dish IDs from booking days/slots
    $days = $booking->days ?? [];
    $ids  = [];
    foreach ($days as $day) {
        $slots = $day['slots'] ?? [];
        foreach ($slots as $slotData) {
            foreach (($slotData['items'] ?? []) as $it) {
                if (!empty($it['dish_id'])) {
                    $ids[] = (int) $it['dish_id'];
                }
            }
        }
    }
    $ids = array_values(array_unique($ids));

    $dishesById = \App\Models\Dish::query()
        ->whereIn('id', $ids)
        ->with([
            'crusts:id,name,price',
            'buns:id,name',
            'addOns:id,name,price',
        ])
        ->get()
        ->keyBy('id');

    // Slot labels
    $slotLabels = [
        'breakfast' => 'Breakfast',
        'lunch'     => 'Lunch',
        'tiffin'    => 'Tiffin',
        'dinner'    => 'Dinner',
    ];

    // Helpers (same logic as Livewire, compacted)
    $resolveVariant = function (\App\Models\Dish $dish, $variantKey): array {
        if ($variantKey === null || $variantKey === '') {
            return ['label' => null, 'group' => null, 'price' => 0.0];
        }

        $vars = $dish->variations ?? [];
        if (is_string($vars)) {
            $decoded = json_decode($vars, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $vars = $decoded;
            } else {
                $vars = [];
            }
        }

        $candidateOptions = [];
        if (is_array($vars)) {
            if (isset($vars['variants']) && is_array($vars['variants'])) {
                $candidateOptions = $vars['variants'];
            } elseif (isset($vars['options']) && is_array($vars['options'])) {
                $candidateOptions = $vars['options'];
            } else {
                foreach ($vars as $group) {
                    if (!empty($group['options']) && is_array($group['options'])) {
                        foreach ($group['options'] as $opt) {
                            $opt['_group'] = $group['name'] ?? null;
                            $candidateOptions[] = $opt;
                        }
                    }
                }
            }
        }

        foreach ($candidateOptions as $idx => $opt) {
            $optKey = $opt['key'] ?? ($opt['id'] ?? $idx);
            if ((string) $optKey === (string) $variantKey) {
                return [
                    'label' => $opt['label'] ?? ($opt['name'] ?? null),
                    'group' => $opt['_group'] ?? ($opt['group'] ?? null),
                    'price' => (float) ($opt['price'] ?? 0),
                ];
            }
        }

        return ['label' => null, 'group' => null, 'price' => 0.0];
    };

    $resolveCrust = function (\App\Models\Dish $dish, $crustKey): array {
        if (!$crustKey) return ['name' => null, 'price' => 0.0];
        $crust = $dish->crusts->firstWhere('id', (int) $crustKey);
        if (!$crust) return ['name' => null, 'price' => 0.0];
        return ['name' => $crust->name, 'price' => (float) ($crust->price ?? 0)];
    };

    $resolveBun = function (\App\Models\Dish $dish, $bunKey): array {
        if (!$bunKey) return ['name' => null, 'price' => 0.0];
        $bun = $dish->buns->firstWhere('id', (int) $bunKey);
        if (!$bun) return ['name' => null, 'price' => 0.0];
        return ['name' => $bun->name, 'price' => 0.0]; // update if buns later have price
    };

    $resolveAddons = function (\App\Models\Dish $dish, array $addonKeys): array {
        $addonIds = array_map('intval', $addonKeys);
        $res      = [];
        foreach ($dish->addOns as $addon) {
            if (in_array((int) $addon->id, $addonIds, true)) {
                $res[] = [
                    'name'  => $addon->name,
                    'price' => (float) ($addon->price ?? 0),
                ];
            }
        }
        return $res;
    };

    $calcUnitPrice = function (\App\Models\Dish $dish, array $item) use ($resolveVariant, $resolveCrust, $resolveBun, $resolveAddons): float {
        $base  = (float) $dish->price_with_discount;
        $total = $base;

        $variantKey = $item['variant_key'] ?? null;
        if ($variantKey !== null && $variantKey !== '') {
            $v = $resolveVariant($dish, $variantKey);
            $total += $v['price'] ?? 0;
        }

        if (!empty($item['crust_key'])) {
            $c = $resolveCrust($dish, $item['crust_key']);
            $total += $c['price'] ?? 0;
        }

        if (!empty($item['bun_key'])) {
            $b = $resolveBun($dish, $item['bun_key']);
            $total += $b['price'] ?? 0;
        }

        if (!empty($item['addon_keys'])) {
            $ads = $resolveAddons($dish, (array) $item['addon_keys']);
            foreach ($ads as $a) {
                $total += $a['price'] ?? 0;
            }
        }

        return $total;
    };
@endphp

<div class="receipt">
    {{-- Brand header --}}
    <div class="center">
        @if (!empty($businessSetting?->logo))
            <img src="{{ asset($businessSetting->logo) }}" class="logo" alt="Logo">
        @endif
        <div class="title">{{ $businessSetting->company_name ?? config('app.name', 'CloudBite') }}</div>
        @if (!empty($businessSetting?->address))
            <div class="xs muted">{{ $businessSetting->address }}</div>
        @endif
        @if (!empty($businessSetting?->phone))
            <div class="xs muted">{{ $businessSetting->phone }}</div>
        @endif
    </div>

    <div class="hr"></div>

    {{-- Booking meta --}}
    <div class="xs">
        <div class="row">
            <div><span class="bold">Booking:</span> {{ $booking->booking_code }}</div>
            <div class="right">{{ $booking->created_at?->format('d M Y, h:i A') }}</div>
        </div>
        <div class="row mt4">
            <div><span class="bold">Plan:</span> {{ ucfirst($booking->plan_type) }}</div>
            @if ($booking->start_date)
                <div class="right xs">Start: {{ $booking->start_date->format('d M Y') }}</div>
            @endif
        </div>
    </div>

    {{-- Customer --}}
    <div class="mt4 xs">
        <div><span class="bold">Name:</span> {{ $booking->contact_name ?? 'Guest' }}</div>
        <div><span class="bold">Phone:</span> {{ $booking->phone ?? '-' }}</div>
        @if ($booking->email)
            <div><span class="bold">Email:</span> {{ $booking->email }}</div>
        @endif
        <div><span class="bold">Address:</span> {{ $addressLine }}</div>
    </div>

    <div class="hr"></div>

    {{-- Items: day/slot wise, with variant/crust/bun/addons + prices --}}
    <div class="xs">
        @php
            $computedSubtotal = 0.0;
            $slotsOrder = ['breakfast', 'lunch', 'tiffin', 'dinner'];
        @endphp

        @foreach ($days as $dayIndex => $day)
            @php
                $dayName   = $day['name'] ?? ('Day ' . ($dayIndex + 1));
                $slots     = $day['slots'] ?? [];
                $dayHasAny = false;
                foreach ($slots as $sd) {
                    if (!empty($sd['items'])) {
                        $dayHasAny = true;
                        break;
                    }
                }
            @endphp

            @if (! $dayHasAny)
                @continue
            @endif

            <div class="bold mt6">{{ $dayName }}</div>

            @foreach ($slotsOrder as $slotKey)
                @php
                    $slotData = $slots[$slotKey] ?? ['items' => []];
                    $items    = $slotData['items'] ?? [];
                @endphp

                @if (empty($items))
                    @continue
                @endif

                <div class="mt4 sm bold">{{ $slotLabels[$slotKey] ?? ucfirst($slotKey) }}</div>

                @foreach ($items as $item)
                    @php
                        $dishId = $item['dish_id'] ?? null;
                        $dish   = $dishId ? $dishesById->get((int) $dishId) : null;
                        $qty    = (int) ($item['qty'] ?? 1);

                        $variantInfo = $dish
                            ? $resolveVariant($dish, $item['variant_key'] ?? null)
                            : ['label' => null, 'group' => null, 'price' => 0];
                        $crustInfo = $dish
                            ? $resolveCrust($dish, $item['crust_key'] ?? null)
                            : ['name' => null, 'price' => 0];
                        $bunInfo = $dish
                            ? $resolveBun($dish, $item['bun_key'] ?? null)
                            : ['name' => null, 'price' => 0];
                        $addonInfos = $dish
                            ? $resolveAddons($dish, (array) ($item['addon_keys'] ?? []))
                            : [];

                        $basePrice  = $dish ? (float) $dish->price_with_discount : 0;
                        $unitPrice  = $dish ? $calcUnitPrice($dish, $item) : 0;
                        $lineTotal  = $unitPrice * $qty;
                        $computedSubtotal += $lineTotal;
                    @endphp

                    <div class="item">
                        <span class="name bold">{{ $dish?->title ?? 'Item' }}</span>

                        {{-- Options --}}
                        <div class="meta">• Base: {{ number_format($basePrice, 2) }}</div>

                        @if ($variantInfo['label'])
                            <div class="meta">
                                • {{ $variantInfo['group'] ?? 'Variant' }}:
                                {{ $variantInfo['label'] }}
                                @if ($variantInfo['price'] != 0)
                                    ({{ $variantInfo['price'] > 0 ? '+' : '' }}{{ number_format($variantInfo['price'], 2) }})
                                @endif
                            </div>
                        @endif

                        @if ($crustInfo['name'])
                            <div class="meta">
                                • Crust: {{ $crustInfo['name'] }}
                                @if ($crustInfo['price'] != 0)
                                    ({{ $crustInfo['price'] > 0 ? '+' : '' }}{{ number_format($crustInfo['price'], 2) }})
                                @endif
                            </div>
                        @endif

                        @if ($bunInfo['name'])
                            <div class="meta">
                                • Bun: {{ $bunInfo['name'] }}
                                @if ($bunInfo['price'] != 0)
                                    ({{ $bunInfo['price'] > 0 ? '+' : '' }}{{ number_format($bunInfo['price'], 2) }})
                                @endif
                            </div>
                        @endif

                        @if (!empty($addonInfos))
                            <div class="meta">• Add-ons:</div>
                            @foreach ($addonInfos as $ao)
                                <div class="meta">
                                    - {{ $ao['name'] }}
                                    @if (($ao['price'] ?? 0) != 0)
                                        (+{{ number_format($ao['price'], 2) }})
                                    @endif
                                </div>
                            @endforeach
                        @endif

                        <div class="row">
                            <div class="xs">
                                Qty {{ $qty }} × {{ number_format($unitPrice, 2) }}
                            </div>
                            <div class="xs right bold">
                                {{ number_format($lineTotal, 2) }}
                            </div>
                        </div>
                    </div>
                @endforeach
            @endforeach
        @endforeach
    </div>

    <div class="hr"></div>

    {{-- Totals --}}
    <div class="xs">
        <div class="row">
            <div>Plan Subtotal</div>
            <div class="right">{{ number_format($booking->plan_subtotal ?? $computedSubtotal, 2) }}</div>
        </div>
        <div class="row">
            <div>Shipping</div>
            <div class="right">(+)&nbsp;{{ number_format($booking->shipping_total ?? 0, 2) }}</div>
        </div>
        <div class="hr"></div>
        <div class="row bold">
            <div>Grand Total</div>
            <div class="right">{{ number_format($booking->grand_total ?? (($booking->plan_subtotal ?? $computedSubtotal) + ($booking->shipping_total ?? 0)), 2) }}</div>
        </div>
        <div class="row mt4">
            <div>Paid Now</div>
            <div class="right">{{ number_format($booking->pay_now ?? 0, 2) }}</div>
        </div>
        <div class="row">
            <div>Due Amount</div>
            <div class="right">{{ number_format($booking->due_amount ?? 0, 2) }}</div>
        </div>
    </div>

    <div class="mt8 xs center">
        <div>Payment: {{ strtoupper($booking->payment_method ?? 'COD') }}</div>
        <div>Status: {{ ucfirst($booking->payment_status ?? '-') }}</div>
        <div>Booking: {{ ucfirst($booking->status ?? '-') }}</div>
    </div>

    <div class="mt8 center xs">
        — Thank you —
    </div>
</div>
</body>
</html>
