<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'dish_id',
        'qty',
        'crust_id',
        'bun_id',
        'addon_ids',
        'unit_price',
        'line_total',
        'meta'
    ];


    protected $casts = [
        'addon_ids' => 'array',
        'meta' => 'array',
    ];

     public function dish()
    {
        return $this->belongsTo(Dish::class);
    }
}
