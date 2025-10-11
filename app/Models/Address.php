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
        'line1',
        'area',
        'city',
        'state',
        'postal_code',
        'country',
        'lat',
        'lng',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
