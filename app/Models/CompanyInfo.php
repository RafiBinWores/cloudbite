<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CompanyInfo extends Model
{
    protected $fillable = [
        'company_name',
        'phone',
        'email',
        'address',
        'logo_dark',
        'logo_light',
        'favicon',
        'facebook',
        'instagram',
        'twitter',
        'tiktok',
        'youtube',
        'whatsapp',
    ];
}
