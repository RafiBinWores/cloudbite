<?php

namespace Database\Seeders;

use App\Models\CompanyInfo;
use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingsSeeder extends Seeder
{
    public function run()
    {
        CompanyInfo::create([
            'logo_dark' => 'logo-dark.png',
            'logo_light' => 'logo-light.png',
            'favicon' => 'favicon.ico',
        ]);

        $this->command->info('Default settings created!');
    }
}
