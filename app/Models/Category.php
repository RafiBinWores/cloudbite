<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'image',
        'meta_title',
        'meta_description',
        'meta_keywords',
        'status',
    ];

    public function dishes()
    {
        return $this->hasMany(Dish::class);
    }

    public function scopeActive($q)
    {
        $q->where(function ($qq) {
            $qq->where('status', 1)
                ->orWhere('status', true)
                ->orWhere('status', 'active');
        });
    }

    public function scopeSearch($query, $value)
    {
        $query->where('name', 'like', "%{$value}%")->orWhere('status', 'like', "%{$value}%")->orWhere('created_at', 'like', "%{$value}%");
    }
}
