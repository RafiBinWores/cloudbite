<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'category_id',
        'cuisine_id',
        'price',
        'tags',
        'discount_type',
        'discount',
        'vat',
        'sku',
        'track_stock',
        'daily_stock',
        'available_from',
        'available_till',
        'visibility',
        'thumbnail',
        'gallery',
        'meta_title',
        'meta_description',
        'meta_keyword',
        'variations',
    ];

    protected $casts = [
        'available_from' => 'string',
        'available_till' => 'string',
        'tags' => 'array',
        'gallery' => 'array',
        'variations' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function cuisine()
    {
        return $this->belongsTo(Cuisine::class, 'cuisine_id');
    }

    public function buns()
    {
        return $this->belongsToMany(Bun::class, 'bun_dish')
            ->withTimestamps();
    }

    public function crusts()
    {
        return $this->belongsToMany(Crust::class, 'crust_dish')
            ->withTimestamps();
    }

    public function addOns()
    {
        return $this->belongsToMany(AddOn::class, 'add_on_dish')
            ->withTimestamps();
    }

    public function relatedDishes()
    {
        return $this->belongsToMany(Dish::class, 'dish_related', 'dish_id', 'related_dish_id')
            ->withTimestamps();
    }

    public function scopeSearch($query, $value)
    {
        $query->where('title', 'like', "%{$value}%")->orWhere('visibility', 'like', "%{$value}%")->orWhere('created_at', 'like', "%{$value}%");
    }

    public function getPriceWithDiscountAttribute(): string
    {
        $value = $this->price;
        if ($this->discount && $this->discount_type) {
            if ($this->discount_type === 'percent') {
                $value = $this->price * (1 - ($this->discount / 100));
            } elseif ($this->discount_type === 'amount') {
                $value = max(0, $this->price - $this->discount);
            }
        }
        return $this->formatPrice($value);
    }

    public function getDisplayPriceAttribute(): string
    {
        return $this->formatPrice($this->price);
    }

    protected function formatPrice($value): string
    {
        return (string) round($value);
    }

    public function favoritedBy()
    {
        return $this->belongsToMany(User::class, 'favorites')->withTimestamps();
    }

    public function scopeVisible($q)
    {
        return $q->where('visibility', 'Yes');
    }
    public function scopeAvailableNow($q, $now = null)
    {
        $now = $now ?: now();
        return $q->where('available_from', '<=', $now)->where('available_till', '>=', $now);
    }
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }
}
