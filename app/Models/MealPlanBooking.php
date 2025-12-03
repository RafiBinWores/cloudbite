<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MealPlanBooking extends Model
{
     protected $fillable = [
        'user_id',
        'booking_code',

        'plan_type',
        'start_date',
        'meal_prefs',
        'days',

        'plan_subtotal',
        'shipping_total',
        'grand_total',

        'payment_option',
        'pay_now',
        'due_amount',

        'payment_method',
        'payment_status',
        'status',

        'contact_name',
        'phone',
        'email',
        'shipping_address',
        'customer_note',
        'meta',
    ];

    protected $casts = [
        'start_date'      => 'date',
        'meal_prefs'      => 'array',
        'days'            => 'array',
        'plan_subtotal'   => 'decimal:2',
        'shipping_total'  => 'decimal:2',
        'grand_total'     => 'decimal:2',
        'pay_now'         => 'decimal:2',
        'due_amount'      => 'decimal:2',
        'shipping_address'=> 'array',
        'meta'            => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeSearch($query, $value)
    {
        $query->where('booking_code', 'like', "%{$value}%")->orWhere('phone', 'like', "%{$value}%")->orWhere('email', 'like', "%{$value}%")->orWhere('contact_name', 'like', "%{$value}%");
    }
}
