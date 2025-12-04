<?php

namespace App\Http\Controllers;

use App\Models\CompanyInfo;
use App\Models\MealPlanBooking;
use Illuminate\Http\Request;

class MealPlanBookingPrintController extends Controller
{
    public function thermal(string $code)
    {
        $booking = MealPlanBooking::where('booking_code', $code)->firstOrFail();

        // Load company / business info
        $businessSetting = CompanyInfo::query()->first();

        return view('livewire.admin.meal-plan-booking.exports.meal-plan-receipt', [
            'booking'         => $booking,
            'businessSetting' => $businessSetting,
        ]);
    }
}
