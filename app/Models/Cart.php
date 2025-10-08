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
        // Subtotal = sum of pre-discount line totals
        $subtotal = (float) $this->items()->sum('line_total');

        // Compute tax per line using dish VAT%
        $items = $this->items()->with('dish')->get();
        $tax = 0.0;

        foreach ($items as $item) {
            $vatPercent = (float) ($item->dish->vat ?? 0);
            if ($vatPercent <= 0) continue;

            // If VAT should apply only on base (without extras), use ($item->meta['base'] ?? $item->unit_price)
            $taxableUnit = (float) $item->unit_price;
            $tax += $taxableUnit * ($vatPercent / 100) * (int) $item->qty;
        }

        $discount = max(0, (float) ($this->discount_total ?? 0));
        $shipping = (float) ($this->shipping_total ?? 0);
        $tax      = round($tax, 2);
        $subtotal = round($subtotal, 2);

        // Subtotal stays raw (no discount subtracted here)
        $grand = round($subtotal - $discount + $tax + $shipping, 2);
        if ($grand < 0) $grand = 0.00;

        $this->subtotal     = $subtotal;
        $this->tax_total    = $tax;
        $this->grand_total  = $grand;

        $this->save();
    }
}
