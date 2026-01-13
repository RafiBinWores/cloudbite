<?php

namespace Database\Seeders;

use App\Models\CompanyInfo;
use Illuminate\Database\Seeder;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        CompanyInfo::create([
            'logo_dark' => 'logo-dark.png',
            'logo_light' => 'logo-light.png',
            'favicon' => 'favicon.ico',
        ]);
    }
}
