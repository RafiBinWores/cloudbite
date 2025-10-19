<?php

namespace Database\Seeders;

use App\Models\ShippingSetting;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ShippingSettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        ShippingSetting::query()->firstOrCreate([], [
            'base_fee' => 60.00,
            'free_delivery' => false,
            'free_minimum' => 500.00,
        ]);
    }
}
