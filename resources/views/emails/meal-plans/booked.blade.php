@php
    use Illuminate\Support\Facades\Storage;
    use Carbon\Carbon;

    /** @var \App\Models\MealPlanBooking $booking */
    /** @var \Illuminate\Support\Collection<int,\App\Models\Dish>|\Illuminate\Database\Eloquent\Collection $dishesById */
    /** @var array|string|null $days */
    /** @var \App\Models\EmailTemplate|null $template */
    /** @var \App\Models\CompanyInfo|null $companyInfo */
    /** @var string|null $buttonUrl */

    $tpl = $template ?? null;

    // Logo
    $logoUrl = $tpl && $tpl->logo_path
        ? Storage::disk('public')->url($tpl->logo_path)
        : null;

    // Template text
    $mainTitle   = $tpl?->main_title      ?? 'Meal Plan Booking Confirmed';
    $headerTitle = $tpl?->header_title    ?? 'Booking Info';
    $bodyHtml    = $tpl?->body            ?? '';
    $buttonText  = $tpl?->button_text     ?? 'View Booking';
    $footerText  = $tpl?->footer_section  ?? '';
    $copyright   = $tpl?->copyright       ?? '';

    // Flags
    $showPrivacy = $tpl?->show_privacy_policy      ?? true;
    $showRefund  = $tpl?->show_refund_policy       ?? true;
    $showCancel  = $tpl?->show_cancellation_policy ?? true;
    $showContact = $tpl?->show_contact_us          ?? true;

    $showFacebook  = $tpl?->show_facebook  ?? true;
    $showInstagram = $tpl?->show_instagram ?? true;
    $showTwitter   = $tpl?->show_twitter   ?? true;
    $showTiktok    = $tpl?->show_tiktok    ?? true;
    $showYoutube   = $tpl?->show_youtube   ?? true;
    $showWhatsapp  = $tpl?->show_whatsapp  ?? false;

    // Socials
    $companyInfo = $companyInfo ?? null;
    $socials = [
        'facebook'  => $companyInfo?->facebook  ?? null,
        'instagram' => $companyInfo?->instagram ?? null,
        'twitter'   => $companyInfo?->twitter   ?? null,
        'tiktok'    => $companyInfo?->tiktok    ?? null,
        'youtube'   => $companyInfo?->youtube   ?? null,
        'whatsapp'  => $companyInfo?->whatsapp  ?? null,
    ];

    // Currency
    $formatCurrency = fn($amount) => '৳ ' . number_format((float)($amount ?? 0), 2);

    // Slot labels
    $slotLabel = [
        'breakfast' => 'Breakfast',
        'lunch'     => 'Lunch',
        'tiffin'    => 'Tiffin',
        'dinner'    => 'Dinner',
    ];

    // meal_prefs
    $prefs = (array) ($booking->meal_prefs ?? []);
    $selectedSlots = array_keys(array_filter($prefs));
    $selectedSlotsText = implode(', ', array_map(fn($s) => $slotLabel[$s] ?? $s, $selectedSlots));

    // Address extraction
    $addr = (array) (
        $booking->shipping_address
        ?? $booking->address_json
        ?? $booking->address
        ?? []
    );

    $addrLine1 = $addr['line1'] ?? $addr['address'] ?? $booking->address_line1 ?? $booking->address ?? null;
    $addrCity  = $addr['city'] ?? $booking->city ?? null;
    $addrPost  = $addr['postcode'] ?? $addr['zip'] ?? $booking->postcode ?? null;

    // Any JSON meta
    $details = (array) ($booking->details ?? $booking->meta ?? []);

    // ✅ Normalize $days safely
    $daysNormalized = $days ?? ($booking->days ?? []);
    if (is_string($daysNormalized)) {
        $decoded = json_decode($daysNormalized, true);
        $daysNormalized = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
    }
    if (!is_array($daysNormalized)) $daysNormalized = [];

    /**
     * ✅ Normalize variation groups exactly like your MealPlan component
     */
    $normalizeVariantGroups = function ($dish): array {
        if (!$dish) return [];

        $raw = $dish->variations ?? [];
        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        }

        $groups = [];

        if (is_array($raw)) {
            if (!empty($raw['variants']) && is_array($raw['variants'])) {
                $groups[] = ['name' => 'Variant', 'options' => $raw['variants']];
            } elseif (!empty($raw['options']) && is_array($raw['options'])) {
                $groups[] = ['name' => 'Variant', 'options' => $raw['options']];
            } else {
                foreach ($raw as $g) {
                    if (!empty($g['options']) && is_array($g['options'])) {
                        $groups[] = [
                            'name'    => $g['name'] ?? 'Variation',
                            'options' => $g['options'],
                        ];
                    }
                }
            }
        }

        return $groups;
    };

    /**
     * ✅ Read variant_keys (multi group) with fallback to variant_key
     */
    $extractVariantKeys = function (array $item): array {
        $keys = $item['variant_keys'] ?? [];
        if (empty($keys) && !empty($item['variant_key'])) {
            $keys = [0 => $item['variant_key']];
        }
        return is_array($keys) ? $keys : [];
    };

    /**
     * ✅ Get selected variant meta lines + variant extra total
     */
    $getVariantMeta = function ($dish, array $item) use ($normalizeVariantGroups, $extractVariantKeys) {
        $metaLines = [];
        $extraTotal = 0.0;

        if (!$dish) return [$metaLines, $extraTotal];

        $groups = $normalizeVariantGroups($dish);
        $variantKeys = $extractVariantKeys($item);

        foreach ($groups as $gIndex => $group) {
            $gName = $group['name'] ?? 'Variant';
            $selectedKey = $variantKeys[$gIndex] ?? null;
            if ($selectedKey === null || $selectedKey === '') continue;

            foreach (($group['options'] ?? []) as $idx => $opt) {
                $optKey = $opt['key'] ?? ($opt['id'] ?? $idx);
                if ((string)$optKey === (string)$selectedKey) {
                    $label = $opt['label'] ?? ($opt['name'] ?? $selectedKey);
                    $price = (float)($opt['price'] ?? 0);

                    $line = $gName . ': ' . $label;
                    if ($price > 0) $line .= ' (+' . number_format($price, 2) . ' ৳)';

                    $metaLines[] = $line;
                    $extraTotal += $price;
                    break;
                }
            }
        }

        return [$metaLines, $extraTotal];
    };

    /**
     * ✅ Unit price (MATCH checkout / meal plan): discounted base + variant extras + crust + addons
     * If you want ORIGINAL base instead, change price_with_discount -> price
     */
    $calcUnitPrice = function ($dish, array $item) use ($getVariantMeta) {
        if (!$dish) return 0.0;

        $base = (float)($dish->price_with_discount ?? $dish->price ?? 0);
        $total = $base;

        // Variants (multi)
        [, $variantExtra] = $getVariantMeta($dish, $item);
        $total += $variantExtra;

        // Crust
        if (!empty($item['crust_key'])) {
            $crust = $dish->crusts?->firstWhere('id', (int)$item['crust_key']);
            $total += (float)($crust->price ?? 0);
        }

        // Bun (no price)
        // Add-ons
        if (!empty($item['addon_keys'])) {
            $addonIds = array_map('intval', (array)$item['addon_keys']);
            foreach (($dish->addOns ?? []) as $addon) {
                if (in_array((int)$addon->id, $addonIds, true)) {
                    $total += (float)($addon->price ?? 0);
                }
            }
        }

        return (float)$total;
    };

    // Booking totals (use correct columns from your DB)
    $planSubtotal   = (float)($booking->plan_subtotal ?? 0);
    $shippingTotal  = (float)($booking->shipping_total ?? 0);
    $grandTotal     = (float)($booking->grand_total ?? 0);

    // If you store coupon in booking (recommended)
    $couponCode     = $booking->coupon_code ?? data_get($details, 'couponCode');
    $couponDiscount = (float)($booking->coupon_discount_total ?? data_get($details, 'couponDiscount', 0));

    // Fallback: if coupon not stored, try from meta/ details (optional)
    if ($couponDiscount <= 0 && is_array($booking->meta ?? null)) {
        $couponDiscount = (float) data_get($booking->meta, 'couponDiscount', 0);
    }

    // Pay now / due
    $payNow  = (float)($booking->pay_now ?? 0);
    $dueLater = (float)($booking->due_amount ?? 0);
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meal Plan Booking #{{ $booking->booking_code }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style type="text/css">
        a[x-apple-data-detectors] { color: inherit !important; text-decoration: none !important; }
        html, body { margin: 0; padding: 0; width: 100% !important; overflow-x: hidden !important; }
        body {
            background-color: #f3f4f6;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }
        table { border-collapse: collapse; }
        img { max-width: 100% !important; height: auto; display: block; }
        .outer-wrapper { width: 100% !important; max-width: 100% !important; }
        .container { max-width: 600px; width: 100% !important; box-sizing: border-box; }
        .stack-column { width: 50%; }

        @media only screen and (max-width: 600px) {
            .container { width: 100% !important; }
            .stack-column {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box;
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin-bottom: 8px !important;
            }
            .text-right-sm { text-align: left !important; }
            .p-x-sm { padding-left: 16px !important; padding-right: 16px !important; }
            .p-y-sm { padding-top: 12px !important; padding-bottom: 12px !important; }
        }
    </style>
</head>

<body>
<table class="outer-wrapper" width="100%" cellpadding="0" cellspacing="0" role="presentation"
       style="background-color:#f3f4f6;padding:24px 0;max-width:100%;">
    <tr>
        <td align="center">

            <table class="container" width="600" cellpadding="0" cellspacing="0" role="presentation"
                   style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;">

                {{-- HEADER --}}
                <tr>
                    <td class="p-x-sm" style="padding:16px 24px;border-bottom:1px solid #e5e7eb;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="middle">
                                    @if($logoUrl)
                                        <img src="{{ $logoUrl }}" alt="CloudBite" style="height:32px;width:auto;">
                                    @else
                                        <div style="height:32px;width:32px;border-radius:9999px;background-color:#f97373;color:#ffffff;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;">
                                            CB
                                        </div>
                                    @endif

                                    @if($mainTitle !== '')
                                        <div style="font-size:14px;font-weight:600;color:#111827;margin-top:6px;">
                                            {{ $mainTitle }}
                                        </div>
                                    @endif
                                </td>

                                <td align="right" valign="middle" class="text-right-sm"
                                    style="font-size:11px;color:#6b7280;font-weight:500;">
                                    Booking #{{ $booking->booking_code }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- BOOKING + CUSTOMER/ADDRESS INFO --}}
                <tr>
                    <td class="p-x-sm" style="padding:16px 24px;border-bottom:1px solid #e5e7eb;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>

                                {{-- Booking info --}}
                                <td class="stack-column" valign="top" style="padding-right:8px;">
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="border-radius:8px;border:1px solid #fecaca;background-color:#fef2f2;">
                                        <tr>
                                            <td align="center" style="padding:10px 12px;border-bottom:1px solid #fecaca;">
                                                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#b91c1c;">
                                                    {{ $headerTitle !== '' ? $headerTitle : 'Booking Info' }}
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:10px 12px;font-size:11px;color:#374151;">
                                                <table width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td align="left" style="color:#6b7280;">Plan</td>
                                                        <td align="right" style="font-weight:600;">
                                                            {{ ucfirst($booking->plan_type ?? 'meal') }} Plan
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td align="left" style="color:#6b7280;">Start Date</td>
                                                        <td align="right">
                                                            @if(!empty($booking->start_date))
                                                                {{ Carbon::parse($booking->start_date)->format('d M, Y') }}
                                                            @else
                                                                -
                                                            @endif
                                                        </td>
                                                    </tr>

                                                    <tr>
                                                        <td align="left" style="color:#6b7280;">Meal Slots</td>
                                                        <td align="right">{{ $selectedSlotsText ?: '-' }}</td>
                                                    </tr>

                                                    <tr>
                                                        <td align="left" style="color:#6b7280;">Payment</td>
                                                        <td align="right">{{ ucfirst($booking->payment_method ?? 'cod') }}</td>
                                                    </tr>

                                                    <tr>
                                                        <td align="left" style="color:#6b7280;">Status</td>
                                                        <td align="right">{{ ucfirst($booking->status ?? 'pending') }}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                {{-- Customer / Address --}}
                                <td class="stack-column" valign="top" style="padding-left:8px;">
                                    <table width="100%" cellpadding="0" cellspacing="0"
                                           style="border-radius:8px;border:1px solid #e5e7eb;">
                                        <tr>
                                            <td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;">
                                                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#111827;">
                                                    Customer &amp; Address
                                                </div>
                                            </td>
                                        </tr>

                                        <tr>
                                            <td style="padding:10px 12px;font-size:11px;color:#374151;">
                                                <div style="font-weight:600;">
                                                    {{ $booking->name ?? $booking->contact_name ?? 'Customer' }}
                                                </div>

                                                @if(!empty($booking->phone))
                                                    <div>{{ $booking->phone }}</div>
                                                @endif

                                                @if(!empty($booking->email))
                                                    <div>{{ $booking->email }}</div>
                                                @endif

                                                @if(!empty($addrLine1))
                                                    <div style="margin-top:6px;color:#6b7280;">{{ $addrLine1 }}</div>
                                                @endif

                                                @if(!empty($addrCity) || !empty($addrPost))
                                                    <div style="color:#9ca3af;">
                                                        {{ $addrCity ?? '' }} {{ $addrPost ?? '' }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- MEALS / ITEMS --}}
                <tr>
                    <td class="p-x-sm p-y-sm" style="padding:16px 24px;border-bottom:1px solid #e5e7eb;">
                        <div style="font-size:12px;font-weight:600;color:#111827;margin-bottom:8px;">
                            Selected Meals
                        </div>

                        @php $computedSubtotal = 0.0; @endphp

                        @if(!empty($daysNormalized))
                            <table width="100%" cellpadding="0" cellspacing="0" style="font-size:11px;border-collapse:collapse;">
                                <thead>
                                <tr style="border-bottom:1px solid #e5e7eb;">
                                    <th align="left" style="padding:6px 0;color:#6b7280;font-weight:500;">Day / Slot</th>
                                    <th align="left" style="padding:6px 0;color:#6b7280;font-weight:500;">Item</th>
                                    <th align="right" style="padding:6px 0;color:#6b7280;font-weight:500;width:40px;">Qty</th>
                                    <th align="right" style="padding:6px 0;color:#6b7280;font-weight:500;width:80px;">Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($daysNormalized as $dayIndex => $day)
                                    @php
                                        // Your MealPlan saves: name/date/slots
                                        $dayName = $day['name'] ?? ('Day ' . ($dayIndex + 1));
                                        $slots = (array)($day['slots'] ?? []);
                                    @endphp

                                    @foreach($slots as $slotKey => $slotData)
                                        @php
                                            $slotName = $slotLabel[$slotKey] ?? (is_string($slotKey) ? ucfirst($slotKey) : 'Slot');
                                            $items = (array)($slotData['items'] ?? []);
                                        @endphp

                                        @foreach($items as $item)
                                            @php
                                                $dishId = (int)($item['dish_id'] ?? 0);
                                                $qty = max(1, (int)($item['qty'] ?? 1));
                                                $dish = $dishesById->get($dishId);

                                                // build meta (variants/crust/bun/addons)
                                                [$variantLines, ] = $getVariantMeta($dish, (array)$item);

                                                $metaLines = $variantLines;

                                                if ($dish && !empty($item['crust_key'])) {
                                                    $crust = $dish->crusts?->firstWhere('id', (int)$item['crust_key']);
                                                    if ($crust) {
                                                        $metaLines[] = 'Crust: ' . $crust->name . ((float)$crust->price > 0 ? ' (+' . number_format((float)$crust->price, 2) . ' ৳)' : '');
                                                    }
                                                }

                                                if ($dish && !empty($item['bun_key'])) {
                                                    $bun = $dish->buns?->firstWhere('id', (int)$item['bun_key']);
                                                    if ($bun) $metaLines[] = 'Bun: ' . $bun->name;
                                                }

                                                if ($dish && !empty($item['addon_keys'])) {
                                                    $addonIds = array_map('intval', (array)$item['addon_keys']);
                                                    $addonLines = [];
                                                    foreach (($dish->addOns ?? []) as $addon) {
                                                        if (in_array((int)$addon->id, $addonIds, true)) {
                                                            $addonLines[] = $addon->name . ((float)$addon->price > 0 ? ' (+' . number_format((float)$addon->price, 2) . ' ৳)' : '');
                                                        }
                                                    }
                                                    if (!empty($addonLines)) $metaLines[] = 'Add-ons: ' . implode(', ', $addonLines);
                                                }

                                                $unit = $calcUnitPrice($dish, (array)$item);
                                                $lineTotal = $unit * $qty;
                                                $computedSubtotal += $lineTotal;
                                            @endphp

                                            <tr style="border-bottom:1px solid #f3f4f6;">
                                                <td style="padding:8px 0;vertical-align:top;">
                                                    <div style="font-weight:600;color:#111827;">{{ $dayName }}</div>
                                                    <div style="color:#6b7280;">{{ $slotName }}</div>
                                                </td>

                                                <td style="padding:8px 0;vertical-align:top;">
                                                    <div style="font-weight:600;color:#111827;">
                                                        {{ $dish?->title ?? ('Dish #' . $dishId) }}
                                                    </div>

                                                    @if(!empty($metaLines))
                                                        <div style="color:#6b7280;font-size:10px;line-height:1.45;margin-top:2px;">
                                                            @foreach($metaLines as $m)
                                                                <div>{{ $m }}</div>
                                                            @endforeach
                                                        </div>
                                                    @endif

                                                    <div style="color:#4b5563;font-size:10px;margin-top:3px;">
                                                        Unit: {{ $formatCurrency($unit) }}
                                                    </div>
                                                </td>

                                                <td align="right" style="padding:8px 0;vertical-align:top;">
                                                    {{ $qty }}
                                                </td>

                                                <td align="right" style="padding:8px 0;vertical-align:top;font-weight:600;color:#111827;">
                                                    {{ $formatCurrency($lineTotal) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                @endforeach
                                </tbody>
                            </table>

                            <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:10px;font-size:11px;">
                                <tr>
                                    <td align="left" style="color:#6b7280;">Items Subtotal (computed)</td>
                                    <td align="right" style="font-weight:600;color:#111827;">
                                        {{ $formatCurrency($computedSubtotal) }}
                                    </td>
                                </tr>
                            </table>
                        @else
                            <div style="font-size:11px;color:#6b7280;">
                                No meal items found for this booking.
                            </div>
                        @endif
                    </td>
                </tr>

                {{-- SUMMARY (use booking DB columns) --}}
                <tr>
                    <td class="p-x-sm p-y-sm" style="padding:16px 24px;">
                        <div style="font-size:12px;font-weight:600;color:#111827;margin-bottom:8px;">
                            Plan Summary
                        </div>

                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:8px;font-size:11px;">
                            <tr>
                                <td align="left" style="color:#6b7280;">Plan subtotal</td>
                                <td align="right">{{ $formatCurrency($planSubtotal) }}</td>
                            </tr>

                            @if($couponDiscount > 0)
                                <tr>
                                    <td align="left" style="color:#6b7280;">
                                        Coupon discount
                                        @if(!empty($couponCode))
                                            <span style="color:#9ca3af;">({{ $couponCode }})</span>
                                        @endif
                                    </td>
                                    <td align="right" style="color:#059669;font-weight:600;">
                                        - {{ $formatCurrency($couponDiscount) }}
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td align="left" style="color:#6b7280;">Delivery fee</td>
                                <td align="right">{{ $formatCurrency($shippingTotal) }}</td>
                            </tr>

                            <tr>
                                <td align="left" style="padding-top:6px;border-top:1px solid #e5e7eb;font-weight:600;color:#111827;">
                                    Grand total
                                </td>
                                <td align="right" style="padding-top:6px;border-top:1px solid #e5e7eb;font-weight:600;color:#111827;">
                                    {{ $formatCurrency($grandTotal) }}
                                </td>
                            </tr>

                            @if($payNow > 0 || $dueLater > 0)
                                <tr>
                                    <td align="left" style="padding-top:8px;color:#6b7280;">Pay now</td>
                                    <td align="right" style="padding-top:8px;font-weight:600;color:#b91c1c;">
                                        {{ $formatCurrency($payNow) }}
                                    </td>
                                </tr>
                                <tr>
                                    <td align="left" style="color:#6b7280;">Due later</td>
                                    <td align="right" style="font-weight:600;color:#111827;">
                                        {{ $formatCurrency($dueLater) }}
                                    </td>
                                </tr>
                            @endif
                        </table>

                        @php $note = $booking->customer_note ?? data_get($details, 'note'); @endphp

                        @if(!empty($note))
                            <div style="margin-top:10px;font-size:11px;color:#6b7280;line-height:1.5;">
                                <div style="font-weight:600;color:#111827;margin-bottom:2px;">Note</div>
                                {{ $note }}
                            </div>
                        @endif
                    </td>
                </tr>

                {{-- BODY TEXT FROM TEMPLATE --}}
                @if($bodyHtml !== '')
                    <tr>
                        <td class="p-x-sm" style="padding:8px 24px 0 24px;border-top:1px solid #f3f4f6;">
                            <div style="font-size:13px;color:#374151;line-height:1.5;">
                                {!! $bodyHtml !!}
                            </div>
                        </td>
                    </tr>
                @endif

                {{-- BUTTON --}}
                @if(($buttonText ?? '') !== '' && !empty($buttonUrl))
                    <tr>
                        <td align="center" class="p-x-sm" style="padding:12px 24px 16px 24px;">
                            <a href="{{ $buttonUrl }}"
                               style="display:inline-block;padding:8px 18px;border-radius:9999px;background-color:#f97373;color:#ffffff;font-size:11px;font-weight:500;text-decoration:none;">
                                {{ $buttonText }}
                            </a>
                        </td>
                    </tr>
                @endif

                {{-- FOOTER --}}
                <tr>
                    <td class="p-x-sm p-y-sm" style="padding:16px 24px;border-top:1px solid #e5e7eb;font-size:11px;color:#6b7280;">
                        @if($footerText !== '')
                            <p style="margin:0 0 8px 0;">{{ $footerText }}</p>
                        @endif

                        <p style="margin:0 0 8px 0;">
                            Thanks &amp; Regards,<br>
                            <span style="font-weight:600;color:#111827;">CloudBite</span>
                        </p>

                        <p style="margin:8px 0 0 0;">
                            @if($showPrivacy)
                                <a href="#" style="color:#6b7280;text-decoration:underline;margin-right:8px;">Privacy Policy</a>
                            @endif
                            @if($showRefund)
                                <a href="#" style="color:#6b7280;text-decoration:underline;margin-right:8px;">Refund Policy</a>
                            @endif
                            @if($showCancel)
                                <a href="#" style="color:#6b7280;text-decoration:underline;margin-right:8px;">Cancellation Policy</a>
                            @endif
                            @if($showContact)
                                <a href="#" style="color:#6b7280;text-decoration:underline;margin-right:8px;">Contact us</a>
                            @endif
                        </p>

                        @php
                            $socialLinks = [];
                            if ($showFacebook  && !empty($socials['facebook']))  $socialLinks[] = ['label' => 'Facebook',  'url' => $socials['facebook']];
                            if ($showInstagram && !empty($socials['instagram'])) $socialLinks[] = ['label' => 'Instagram', 'url' => $socials['instagram']];
                            if ($showTwitter   && !empty($socials['twitter']))   $socialLinks[] = ['label' => 'Twitter',   'url' => $socials['twitter']];
                            if ($showTiktok    && !empty($socials['tiktok']))    $socialLinks[] = ['label' => 'TikTok',    'url' => $socials['tiktok']];
                            if ($showYoutube   && !empty($socials['youtube']))   $socialLinks[] = ['label' => 'YouTube',   'url' => $socials['youtube']];
                            if ($showWhatsapp  && !empty($socials['whatsapp']))  $socialLinks[] = ['label' => 'WhatsApp',  'url' => $socials['whatsapp']];
                        @endphp

                        @if(count($socialLinks))
                            <p style="margin:8px 0 0 0;">
                                Social:
                                @foreach($socialLinks as $s)
                                    @php
                                        $url = $s['url'];
                                        $href = str_starts_with($url, 'http') ? $url : ('https://' . $url);
                                    @endphp
                                    <a href="{{ $href }}" style="color:#6b7280;text-decoration:underline;margin-left:4px;" target="_blank">
                                        {{ $s['label'] }}
                                    </a>
                                @endforeach
                            </p>
                        @endif

                        @if($copyright !== '')
                            <p style="margin:8px 0 0 0;font-size:10px;color:#9ca3af;">
                                {{ $copyright }}
                            </p>
                        @endif
                    </td>
                </tr>

            </table>

        </td>
    </tr>
</table>
</body>
</html>
