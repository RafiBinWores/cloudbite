<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    protected $fillable = [
        'user_id',
        'label',
        'contact_name',
        'contact_phone',
        'address',
        'area',
        'city',
        'state',
        'postal_code',
        'country',
        'note',
        'lat',
        'lng',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
