<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ShippingSetting extends Model
{
    protected $fillable = ['base_fee','free_delivery','free_minimum'];
}
