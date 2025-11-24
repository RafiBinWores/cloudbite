<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'currency',
        'subtotal',
        'discount_total', // coupon only
        'tax_total',
        'grand_total',
        'meta',
    ];

    protected $casts = [
        'meta'           => 'array',
        'subtotal'       => 'decimal:2',
        'discount_total' => 'decimal:2',
        'tax_total'      => 'decimal:2',
        'grand_total'    => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    public function refreshTotals(): void
    {
        $items = $this->items()->with('dish')->get();

        /**
         * ✅ IMPORTANT:
         * Your cart item prices are NET (VAT not added in repo),
         * so prices_include_tax MUST default to FALSE.
         */
        $pricesIncludeTax = (bool) data_get($this->meta, 'prices_include_tax', false);

        $rawSubtotalNet = 0.0;        // Net subtotal (already discounted product base + extras)
        $displayTax     = 0.0;        // VAT computed on net subtotal (or extracted if gross)
        $couponDiscount = max(0.0, (float) ($this->discount_total ?? 0.0)); // coupon only

        // extra buckets for UI / reporting
        $originalProductSubtotal = 0.0;  // base_original * qty
        $discountedBaseSubtotal  = 0.0;  // base_after_discount * qty
        $extrasSubtotal          = 0.0;  // extras * qty

        foreach ($items as $item) {
            $qty = (int) ($item->qty ?? 1);

            $lineTotal = (float) ($item->line_total ?? 0);

            $vat = (float) data_get($item->meta, 'vat_percent', optional($item->dish)->vat ?? 0.0);

            // for UI buckets
            $baseOriginal = (float) data_get($item->meta, 'base_original', 0);
            $baseAfter    = (float) data_get($item->meta, 'base_after_discount', $baseOriginal);

            $crustExtra   = (float) data_get($item->meta, 'crust_extra', 0);
            $addonsExtra  = (float) data_get($item->meta, 'addons_extra', 0);
            $extrasPerUnit= $crustExtra + $addonsExtra;

            $originalProductSubtotal += $baseOriginal * $qty;
            $discountedBaseSubtotal  += $baseAfter * $qty;
            $extrasSubtotal          += $extrasPerUnit * $qty;

            // cart subtotal net
            $rawSubtotalNet += $lineTotal;

            // tax display/compute
            if ($vat > 0) {
                if ($pricesIncludeTax) {
                    // Extract VAT from gross line
                    $rate      = 1 + ($vat / 100);
                    $baseExVat = $lineTotal / $rate;
                    $displayTax += ($lineTotal - $baseExVat);
                } else {
                    // Add VAT on net line
                    $displayTax += $lineTotal * ($vat / 100);
                }
            }
        }

        $subtotalNet = round($rawSubtotalNet, 2);
        $displayTax  = round($displayTax, 2);

        // Clamp coupon discount
        if ($couponDiscount > $subtotalNet) {
            $couponDiscount = $subtotalNet;
        }
        $couponDiscount = round($couponDiscount, 2);

        // ✅ Grand total
        if ($pricesIncludeTax) {
            $grand = round($subtotalNet - $couponDiscount, 2);
        } else {
            $grand = round($subtotalNet + $displayTax - $couponDiscount, 2);
        }
        if ($grand < 0) $grand = 0.00;

        // ✅ Save core columns
        $this->subtotal       = $subtotalNet;
        $this->discount_total = $couponDiscount; // coupon only
        $this->tax_total      = $displayTax;
        $this->grand_total    = $grand;

        // ✅ Save product discount breakdown into meta for UI
        $meta = (array) ($this->meta ?? []);
        $meta['breakdown'] = [
            'product_original_subtotal' => round($originalProductSubtotal, 2),
            'product_discount_subtotal' => round(max(0, $originalProductSubtotal - $discountedBaseSubtotal), 2),
            'addons_subtotal'           => round($extrasSubtotal, 2),
        ];
        $meta['prices_include_tax'] = $pricesIncludeTax;

        $this->meta = $meta;

        $this->save();
    }
}
