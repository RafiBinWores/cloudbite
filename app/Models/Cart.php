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
        $items = $this->items()->with('dish')->get();

        // Default TRUE because you mentioned you've already added VAT in cart lines.
        $pricesIncludeTax = (bool) data_get($this->meta, 'prices_include_tax', true);

        $rawSubtotal = 0.0;
        $displayTax  = 0.0;  // always for UI/reporting
        $discount    = max(0.0, (float) ($this->discount_total ?? 0.0));

        // First pass: subtotal & (preliminary) tax for display
        foreach ($items as $item) {
            $lineTotal = (float) $item->line_total;                    // what you already store
            $vat       = (float) (optional($item->dish)->vat ?? 0.0);  // per-item VAT %

            $rawSubtotal += $lineTotal;

            if ($vat > 0) {
                if ($pricesIncludeTax) {
                    // Extract included VAT from a gross line:
                    // tax = gross - gross/(1+r)
                    $rate      = 1 + ($vat / 100);
                    $baseExVat = $lineTotal / $rate;
                    $displayTax += ($lineTotal - $baseExVat);
                } else {
                    // Compute VAT from a net line:
                    $displayTax += $lineTotal * ($vat / 100);
                }
            }
        }

        $subtotal   = round($rawSubtotal, 2);
        $displayTax = round($displayTax);

        // Clamp discount
        if ($discount > $subtotal) {
            $discount = $subtotal;
        }
        $discount = round($discount, 2);

        // GRAND TOTAL (no shipping)
        if ($pricesIncludeTax) {
            // VAT is already inside subtotal → don't add again
            $grand = round($subtotal - $discount, 2);
        } else {
            // VAT not in subtotal → add it
            // (Optional improvement: recompute tax after discount by prorating discount per line)
            $grand = round($subtotal - $discount + $displayTax, 2);
        }

        if ($grand < 0) {
            $grand = 0.00;
        }

        // Persist
        $this->subtotal       = $subtotal;
        $this->discount_total = $discount;
        $this->tax_total      = $displayTax;   // display/report only if prices include tax
        $this->grand_total    = $grand;

        $this->save();
    }
}
