<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Coupon extends Model
{
    protected $fillable = [
        'coupon_type',
        'title',
        'coupon_code',
        'same_user_limit',
        'discount_type',
        'discount',
        'start_date',
        'expire_date',
        'minimum_purchase',
        'status',
    ];

    /**
     * Check if coupon is active and valid.
     */
    public function couponIsActive(): bool
    {
        return $this->status === 'active'
            && now()->between($this->start_date, $this->expire_date);
    }

    /**
     * Check if discount type is percent.
     */
    public function isPercent(): bool
    {
        return $this->discount_type === 'percent';
    }

    /**
     * Check if discount type is amount.
     */
    public function isAmount(): bool
    {
        return $this->discount_type === 'amount';
    }

    /**
     * For search.
     */
    public function scopeSearch($query, $value)
    {
        $query->where('title', 'like', "%{$value}%")->orWhere('title', 'like', "%{$value}%")->orWhere('coupon_code', 'like', "%{$value}%")->orWhere('status', 'like', "%{$value}%")->orWhere('created_at', 'like', "%{$value}%");
    }
}
