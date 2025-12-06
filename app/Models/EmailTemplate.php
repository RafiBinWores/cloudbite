<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
       protected $fillable = [
        'key',
        'logo_path',
        'main_title',
        'header_title',
        'body',
        'button_text',
        'footer_section',
        'copyright',

        'show_privacy_policy',
        'show_refund_policy',
        'show_cancellation_policy',
        'show_contact_us',

        'show_facebook',
        'show_instagram',
        'show_twitter',
        'show_tiktok',
        'show_youtube',
        'show_whatsapp',
    ];
}
