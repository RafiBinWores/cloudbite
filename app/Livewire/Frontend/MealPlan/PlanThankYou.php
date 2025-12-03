<?php

namespace App\Livewire\Frontend\MealPlan;

use App\Models\MealPlanBooking;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class PlanThankYou extends Component
{
    public ?MealPlanBooking $booking = null;

    public function mount($code)
    {
        $this->booking = MealPlanBooking::where('booking_code', $code)->firstOrFail();
    }

    public function render()
    {
        return view('livewire.frontend.meal-plan.plan-thank-you');
    }
}
