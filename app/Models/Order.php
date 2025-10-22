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
        'cooking_time_min',
        'cooking_end_at',
        'placed_at',
        'cancelled_at',
        'cancelled_reason',
        'meta'
    ];


    protected $casts = [
        'shipping_address' => 'array',
        'meta' => 'array',
        'placed_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'cooking_end_at' => 'datetime',
    ];


    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeOngoing($q)
    {
        return $q->whereIn('order_status', ['pending', 'processing', 'confirmed', 'out_for_delivery']);
    }

    public function scopeDelivered($q)
    {
        return $q->where('order_status', 'delivered');
    }

        public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function scopeSearch($query, $value)
    {
        $query->where('order_code', 'like', "%{$value}%")->orWhere('phone', 'like', "%{$value}%")->orWhere('email', 'like', "%{$value}%")->orWhere('contact_name', 'like', "%{$value}%");
    }
}
