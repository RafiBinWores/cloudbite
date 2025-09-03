<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cuisine extends Model
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

    public function scopeSearch($query, $value)
    {
        $query->where('name', 'like', "%{$value}%")->orWhere('status', 'like', "%{$value}%")->orWhere('created_at', 'like', "%{$value}%");
    }
}
