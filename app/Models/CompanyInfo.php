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
        'logo',
        'favicon',
    ];
}
