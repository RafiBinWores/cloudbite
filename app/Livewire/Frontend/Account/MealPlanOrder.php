<?php

namespace App\Livewire\Frontend\Account;

use App\Models\MealPlanBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class MealPlanOrder extends Component
{
    public $bookings;

    public function mount()
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $this->bookings = MealPlanBooking::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->get();
    }

    /**
     * Re-order a completed plan:
     *  - Put its plan data back into session
     *  - Redirect back to Meal Plan builder
     */
    public function reorder(string $code)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $booking = MealPlanBooking::where('user_id', $user->id)
            ->where('booking_code', $code)
            ->firstOrFail();

        if ($booking->status !== 'completed') {
            // silently ignore or flash message if you want
            return;
        }

        session([
            'meal_plan_state' => [
                'planType'    => $booking->plan_type,
                'startDate'   => optional($booking->start_date)->format('Y-m-d')
                    ?? now()->toDateString(),
                'currentWeek' => 1,
                'mealPrefs'   => $booking->meal_prefs ?? [],
                'days'        => $booking->days ?? [],
            ],
        ]);

        return redirect()->route('plans.checkout'); // adjust if your route name is different
    }

    public function render()
    {
        return view(
            'livewire.frontend.account.meal-plan-order',
            [
                'bookings' => $this->bookings,
            ]
        );
    }
}
