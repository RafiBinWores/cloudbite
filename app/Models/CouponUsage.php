<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CouponUsage extends Model
{
    protected $fillable = [
        'coupon_id',
        'user_id',
        'session_id',
        'order_id',
        'used_at',
        'meta',
    ];

    protected $casts = [
        'used_at' => 'datetime',
        'meta'    => 'array',
    ];

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }
}
