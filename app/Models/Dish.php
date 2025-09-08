<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Dish extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'short_description',
        'description',
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
    ];

    protected $casts = [
        'tags' => 'array',
        'gallery' => 'array',
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
        return $this->belongsToMany(Bun::class, 'bun_dish')->withTimestamps();
    }

    public function crusts()
    {
        return $this->belongsToMany(Crust::class, 'crust_dish')->withTimestamps();
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

    // public function getIsAvailableAttribute(): bool
    // {
    //     if ($this->visibility !== 'Yes') return false;

    //     // If times aren’t set, treat as available
    //     if (!$this->available_from || !$this->available_till) return true;

    //     // Build today’s times (since you store time-of-day)
    //     $from = Carbon::today()->setTimeFromTimeString($this->available_from->format('H:i:s'));
    //     $till = Carbon::today()->setTimeFromTimeString($this->available_till->format('H:i:s'));

    //     return Carbon::now()->between($from, $till);
    // }
}
