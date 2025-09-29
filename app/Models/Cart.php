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
        'discount_total',
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
        // sum line totals
        $subtotal = (float) $this->items()->sum('line_total');

        // compute tax from each line using dish VAT%
        $items = $this->items()->with('dish')->get();
        $tax = 0.0;
        foreach ($items as $item) {
            $vatPercent = (float) ($item->dish->vat ?? 0);
            if ($vatPercent <= 0) continue;

            // VAT on whole unit (base + extras). If VAT only on base, use meta['base'] instead.
            $taxableUnit = (float) $item->unit_price;
            $tax += $taxableUnit * ($vatPercent / 100) * (int) $item->qty;
        }

        // keep any previously set coupon discount
        $discount = (float) ($this->discount_total ?? 0);

        $this->subtotal    = round($subtotal, 2);
        $this->tax_total   = round($tax, 2);
        $this->grand_total = round($this->subtotal - $discount + $this->tax_total, 2);

        $this->save();
    }
}
