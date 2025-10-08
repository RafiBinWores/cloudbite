<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'order_code',
        'subtotal',
        'discount_total',
        'tax_total',
        'shipping_total',
        'grand_total',
        'coupon_code',
        'coupon_value',
        'contact_name',
        'phone',
        'email',
        'shipping_address',
        'customer_note',
        'payment_method',
        'payment_status',
        'order_status',
        'placed_at',
        'meta'
    ];


    protected $casts = [
        'shipping_address' => 'array',
        'meta' => 'array',
        'placed_at' => 'datetime',
    ];


    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }
}
