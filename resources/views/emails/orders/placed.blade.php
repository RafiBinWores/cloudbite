@php
    use Illuminate\Support\Facades\Storage;

    /** @var \App\Models\Order $order */
    /** @var \App\Models\EmailTemplate|null $template */
    /** @var \App\Models\CompanyInfo|null $companyInfo */

    $tpl = $template ?? null;

    // Logo
    $logoUrl = $tpl && $tpl->logo_path
        ? Storage::disk('public')->url($tpl->logo_path)
        : null;

    // Template text (all nullable)
    $mainTitle   = $tpl?->main_title      ?? '';
    $headerTitle = $tpl?->header_title    ?? '';
    $bodyHtml    = $tpl?->body            ?? '';
    $buttonText  = $tpl?->button_text     ?? '';
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

    // Socials from company info
    $companyInfo = $companyInfo ?? null;
    $socials = [
        'facebook'  => $companyInfo?->facebook  ?? null,
        'instagram' => $companyInfo?->instagram ?? null,
        'twitter'   => $companyInfo?->twitter   ?? null,
        'tiktok'    => $companyInfo?->tiktok    ?? null,
        'youtube'   => $companyInfo?->youtube   ?? null,
        'whatsapp'  => $companyInfo?->whatsapp  ?? null,
    ];

    $formatCurrency = function ($amount) {
        $amount = (float) ($amount ?? 0);
        return '৳ ' . number_format($amount, 2);
    };
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>CloudBite Order #{{ $order->order_code }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <style type="text/css">
        a[x-apple-data-detectors] {
            color: inherit !important;
            text-decoration: none !important;
        }

        html, body {
            margin: 0;
            padding: 0;
            width: 100% !important;
            overflow-x: hidden !important;
        }

        body {
            background-color: #f3f4f6;
            font-family: system-ui, -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            -webkit-text-size-adjust: 100%;
            -ms-text-size-adjust: 100%;
        }

        table {
            border-collapse: collapse;
        }

        img {
            max-width: 100% !important;
            height: auto;
            display: block;
        }

        .outer-wrapper {
            width: 100% !important;
            max-width: 100% !important;
        }

        .container {
            max-width: 600px;
            width: 100% !important;
            box-sizing: border-box;
        }

        .stack-column {
            width: 50%;
        }

        @media only screen and (max-width: 600px) {
            .container {
                width: 100% !important;
            }

            .stack-column {
                display: block !important;
                width: 100% !important;
                box-sizing: border-box;
                padding-left: 0 !important;
                padding-right: 0 !important;
                margin-bottom: 8px !important;
            }

            .text-right-sm {
                text-align: left !important;
            }

            .p-x-sm {
                padding-left: 16px !important;
                padding-right: 16px !important;
            }

            .p-y-sm {
                padding-top: 12px !important;
                padding-bottom: 12px !important;
            }
        }
    </style>
</head>
<body>
<table
    class="outer-wrapper"
    width="100%"
    cellpadding="0"
    cellspacing="0"
    role="presentation"
    style="background-color:#f3f4f6;padding:24px 0;max-width:100%;"
>
    <tr>
        <td align="center">
            <table
                class="container"
                width="600"
                cellpadding="0"
                cellspacing="0"
                role="presentation"
                style="max-width:600px;width:100%;background-color:#ffffff;border-radius:8px;overflow:hidden;border:1px solid #e5e7eb;"
            >

                {{-- HEADER --}}
                <tr>
                    <td class="p-x-sm" style="padding:16px 24px;border-bottom:1px solid #e5e7eb;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                <td valign="middle">
                                    <table cellpadding="0" cellspacing="0">
                                        <tr>
                                            <td style="padding-right:12px;">
                                                @if($logoUrl)
                                                    <img src="{{ $logoUrl }}" alt="CloudBite" style="height:32px;width:auto;">
                                                @else
                                                    <div style="height:32px;width:32px;border-radius:9999px;background-color:#f97373;color:#ffffff;font-weight:700;font-size:13px;display:flex;align-items:center;justify-content:center;">
                                                        CB
                                                    </div>
                                                @endif

                                                @if($mainTitle !== '')
                                                    <div style="font-size:14px;font-weight:600;color:#111827;margin-top:2px;">
                                                        {{ $mainTitle }}
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    </table>
                                </td>
                                <td align="right" valign="middle" class="text-right-sm" style="font-size:11px;color:#6b7280; font-weight:500;">
                                    Order #{{ $order->order_code }}
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>

                {{-- ORDER + ADDRESS INFO --}}
                <tr>
                    <td class="p-x-sm" style="padding:16px 24px;border-bottom:1px solid #e5e7eb;">
                        <table width="100%" cellpadding="0" cellspacing="0">
                            <tr>
                                {{-- Order info --}}
                                <td class="stack-column" valign="top" style="padding-right:8px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" style="border-radius:8px;border:1px solid #fecaca;background-color:#fef2f2;">
                                        <tr>
                                            <td align="center" style="padding:10px 12px;border-bottom:1px solid #fecaca;">
                                                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#b91c1c;">
                                                    {{ $headerTitle !== '' ? $headerTitle : 'Order Info' }}
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td style="padding:10px 12px;font-size:11px;color:#374151;">
                                                <table width="100%" cellpadding="0" cellspacing="0">
                                                    <tr>
                                                        <td align="left" style="color:#6b7280;">Date</td>
                                                        <td align="right">{{ $order->created_at?->format('d M, Y') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="color:#6b7280;">Time</td>
                                                        <td align="right">{{ $order->created_at?->format('h:i A') }}</td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="color:#6b7280;">Payment</td>
                                                        <td align="right">
                                                            {{ $order->payment_method === 'sslcommerz' ? 'Online Payment' : 'Cash on Delivery' }}
                                                        </td>
                                                    </tr>
                                                    <tr>
                                                        <td align="left" style="color:#6b7280;">Order Status</td>
                                                        <td align="right">{{ ucfirst($order->order_status ?? 'pending') }}</td>
                                                    </tr>
                                                </table>
                                            </td>
                                        </tr>
                                    </table>
                                </td>

                                {{-- Shipping address --}}
                                <td class="stack-column" valign="top" style="padding-left:8px;">
                                    <table width="100%" cellpadding="0" cellspacing="0" style="border-radius:8px;border:1px solid #e5e7eb;">
                                        <tr>
                                            <td style="padding:10px 12px;border-bottom:1px solid #e5e7eb;">
                                                <div style="font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:.06em;color:#111827;">
                                                    Delivery Address
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            @php
                                                $addr = $order->shipping_address ?? [];
                                            @endphp
                                            <td style="padding:10px 12px;font-size:11px;color:#374151;">
                                                <div style="font-weight:600;">
                                                    {{ $order->contact_name }}
                                                </div>
                                                <div>{{ $order->phone }}</div>
                                                @if(!empty($addr['line1']))
                                                    <div style="color:#6b7280;">{{ $addr['line1'] }}</div>
                                                @endif
                                                @if(!empty($addr['city']) || !empty($addr['postcode']))
                                                    <div style="color:#9ca3af;">
                                                        {{ $addr['city'] ?? '' }} {{ $addr['postcode'] ?? '' }}
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

                {{-- ITEMS (LIKE CART) --}}
                <tr>
                    <td class="p-x-sm p-y-sm" style="padding:16px 24px;">
                        <div style="font-size:12px;font-weight:600;color:#111827;margin-bottom:8px;">
                            Order Summary
                        </div>

                        {{-- running totals for all lines --}}
                        @php
                            $orderItemsBaseTotal = 0.0;     // sum of base_after_discount * qty
                            $orderExtrasTotal    = 0.0;     // sum of (crust + bun + addons) for all lines
                        @endphp

                        <table width="100%" cellpadding="0" cellspacing="0" style="font-size:11px;border-collapse:collapse;">
                            <thead>
                                <tr style="border-bottom:1px solid #e5e7eb;">
                                    <th align="left" style="padding:4px 0;color:#6b7280;font-weight:500;">Product</th>
                                    <th align="right" style="padding:4px 0;color:#6b7280;font-weight:500;width:40px;">Qty</th>
                                    <th align="right" style="padding:4px 0;color:#6b7280;font-weight:500;width:80px;">Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->items as $item)
                                    @php
                                        $meta = $item->meta ?? [];
                                        $qty  = (int) $item->qty;

                                        // ========= BASE PRICES =========
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

                                        // ========= VARIATIONS FROM meta.variation_details =========
                                        $variationDetails = (array) data_get($meta, 'variation_details', []);

                                        $variationLines = [];

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
                                            // fallback for legacy orders
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

                                        // ========= CRUST =========
                                        $crustLabel = $item->crust?->name
                                            ?? $item->crust?->title
                                            ?? data_get($meta, 'crust')
                                            ?? data_get($meta, 'crust_label')
                                            ?? data_get($meta, 'crust.name')
                                            ?? data_get($meta, 'crust_title')
                                            ?? null;

                                        // ========= BUN =========
                                        $bunLabel = $item->bun?->name
                                            ?? $item->bun?->title
                                            ?? data_get($meta, 'bun')
                                            ?? data_get($meta, 'bun_label')
                                            ?? data_get($meta, 'bun.name')
                                            ?? data_get($meta, 'bun_title')
                                            ?? null;

                                        // ========= EXTRAS TOTALS =========
                                        $crustExtra  = (float) data_get($meta, 'crust_extra', 0);
                                        $bunExtra    = (float) data_get($meta, 'bun_extra', 0);
                                        $addonsExtra = (float) data_get($meta, 'addons_extra', 0);

                                        $extrasPerUnit = $crustExtra + $bunExtra + $addonsExtra;
                                        $extrasTotal   = $extrasPerUnit * $qty;

                                        // accumulate order-level totals
                                        $orderItemsBaseTotal += $baseTotal;
                                        $orderExtrasTotal    += $extrasTotal;

                                        // ========= ADD-ONS DETAILS =========
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

                                    <tr style="border-bottom:1px solid #f3f4f6;">
                                        {{-- LEFT: product info --}}
                                        <td style="padding:6px 0;">
                                            {{-- Dish title --}}
                                            <div style="font-weight:600;color:#111827;font-size:12px;">
                                                {{ $item->dish?->title ?? 'Dish #'.$item->dish_id }}
                                            </div>

                                            {{-- Variation / bun / crust / add-ons --}}
                                            <div style="color:#6b7280;font-size:10px;line-height:1.5;margin-top:2px;">
                                                @if(!empty($variationLines))
                                                    <div>
                                                        @foreach($variationLines as $vline)
                                                            <div>{{ $vline }}</div>
                                                        @endforeach
                                                    </div>
                                                @endif

                                                @if($bunLabel)
                                                    <div>Bun: {{ $bunLabel }}</div>
                                                @endif

                                                @if($crustLabel)
                                                    <div>Crust: {{ $crustLabel }}</div>
                                                @endif

                                                @if(!empty($addonLines))
                                                    <div>
                                                        Add-ons:
                                                        @foreach($addonLines as $line)
                                                            <div style="margin-left:8px;">
                                                                {{ $line['name'] }}
                                                                × {{ $line['qty'] }}
                                                                @if($line['total'] > 0)
                                                                    = {{ $formatCurrency($line['total']) }}
                                                                @endif
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>

                                            {{-- Price breakdown --}}
                                            <div style="margin-top:4px;font-size:10px;color:#4b5563;">
                                                <div>
                                                    Item price:
                                                    {{ $formatCurrency($baseAfterDiscount) }}
                                                    @if($perUnitDiscount > 0)
                                                        <span style="text-decoration:line-through;color:#9ca3af;margin-left:4px;">
                                                            {{ $formatCurrency($baseOriginal) }}
                                                        </span>
                                                    @endif
                                                    <span style="color:#6b7280;">
                                                        × {{ $qty }} = {{ $formatCurrency($baseTotal) }}
                                                    </span>
                                                </div>

                                                {{-- ⛔️ removed per-item extras line --}}
                                                {{-- We now show Extras Total once below in the summary --}}
                                            </div>
                                        </td>

                                        {{-- Qty --}}
                                        <td align="right" style="padding:6px 0;vertical-align:top;">
                                            {{ $qty }}
                                        </td>

                                        {{-- Line total --}}
                                        <td align="right" style="padding:6px 0;vertical-align:top;font-weight:600;color:#111827;">
                                            {{ $formatCurrency($lineTotal) }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>

                        {{-- CART-LEVEL TOTALS --}}
                        <table width="100%" cellpadding="0" cellspacing="0" style="margin-top:8px;font-size:11px;">
                            <tr>
                                <td align="left" style="color:#6b7280;">Item Price</td>
                                <td align="right">
                                    {{ $formatCurrency($orderItemsBaseTotal) }}
                                </td>
                            </tr>

                            @if($orderExtrasTotal > 0)
                                <tr>
                                    <td align="left" style="color:#6b7280;">
                                        Extras Total
                                    </td>
                                    <td align="right">
                                        {{ $formatCurrency($orderExtrasTotal) }}
                                    </td>
                                </tr>
                            @endif

                            @if(($order->discount_total ?? 0) > 0)
                                <tr>
                                    <td align="left" style="color:#6b7280;">Discount</td>
                                    <td align="right" style="color:#b91c1c;">
                                        - {{ $formatCurrency($order->discount_total) }}
                                    </td>
                                </tr>
                            @endif

                            <tr>
                                <td align="left" style="color:#6b7280;">Delivery Charge</td>
                                <td align="right">{{ $formatCurrency($order->shipping_total) }}</td>
                            </tr>

                            <tr>
                                <td align="left" style="color:#6b7280;">VAT / Tax</td>
                                <td align="right">{{ $formatCurrency($order->tax_total) }}</td>
                            </tr>

                            <tr>
                                <td align="left" style="padding-top:6px;border-top:1px solid #e5e7eb;font-weight:600;color:#111827;">
                                    Total
                                </td>
                                <td align="right" style="padding-top:6px;border-top:1px solid #e5e7eb;font-weight:600;color:#111827;">
                                    {{ $formatCurrency($order->grand_total) }}
                                </td>
                            </tr>
                        </table>
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
                @if($buttonText !== '')
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
                            <p style="margin:0 0 8px 0;">
                                {{ $footerText }}
                            </p>
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
