<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dish extends Model
{
    protected $fillable = [
        'title',
        'short_description',
        'description',
        'category_id',
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
}
