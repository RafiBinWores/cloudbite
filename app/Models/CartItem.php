<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'dish_id',
        'qty',
        'crust_id',
        'bun_id',
        'addon_ids',
        'variation_selection',
        'unit_price',
        'line_total',
        'meta',
    ];

    protected $casts = [
        'addon_ids'  => 'array',
        'variation_selection'  => 'array',
        'meta'       => 'array',
        'unit_price' => 'decimal:2',
        'line_total' => 'decimal:2',
    ];


    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    public function dish()
    {
        return $this->belongsTo(Dish::class);
    }

    public function bun()
    {
        return $this->belongsTo(Bun::class);
    }

    public function crust()
    {
        return $this->belongsTo(Crust::class);
    }

    /**
     * Helper: get the selected AddOn models (since we store only IDs in JSON)
     * Usage: $cartItem->selectedAddOns()
     */
    public function selectedAddOns()
    {
        $ids = array_filter((array) $this->addon_ids, fn($v) => is_numeric($v));
        if (empty($ids)) return collect();

        return AddOn::whereIn('id', $ids)->get();
    }
}
