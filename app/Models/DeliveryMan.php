<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryMan extends Model
{
    protected $table = 'delivery_men';

    protected $fillable = [
        'first_name',
        'last_name',
        'phone_number',
        'identity_type',
        'identity_number',
        'profile_image',
        'identity_images',
        'email',
        'password',
        'status',
    ];

    protected $casts = [
        'identity_images' => 'array',
    ];

    public function scopeSearch($query, $value)
    {
        $query->where('first_name', 'like', "%{$value}%")->orWhere('last_name', 'like', "%{$value}%")->orWhere('phone_number', 'like', "%{$value}%")->orWhere('email', 'like', "%{$value}%")->orWhere('identity_number', 'like', "%{$value}%");
    }
}
