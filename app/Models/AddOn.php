<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AddOn extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'price',
        'status'
    ];

    public function scopeSearch($query, $value)
    {
        $query->where('name', 'like', "%{$value}%")->orWhere('status', 'like', "%{$value}%")->orWhere('created_at', 'like', "%{$value}%");
    }
}
