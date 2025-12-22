<?php

namespace App\Livewire\Frontend\Account;

use App\Models\MealPlanBooking;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.frontend')]
class MealPlanOrder extends Component
{
    use WithPagination;

    public function mount()
    {
        abort_unless(Auth::check(), 403);
    }

    public function reorder(string $code)
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $booking = MealPlanBooking::where('user_id', $user->id)
            ->where('booking_code', $code)
            ->firstOrFail();

        if ($booking->status !== 'completed') {
            return;
        }

        session([
            'meal_plan_state' => [
                'planType'    => $booking->plan_type,
                'startDate'   => optional($booking->start_date)->format('Y-m-d') ?? now()->toDateString(),
                'currentWeek' => 1,
                'mealPrefs'   => $booking->meal_prefs ?? [],
                'days'        => $booking->days ?? [],
            ],
        ]);

        return redirect()->route('plans.checkout');
    }

    public function render()
    {
        $user = Auth::user();

        $bookings = MealPlanBooking::query()
            ->where('user_id', $user->id)
            ->latest('id')
            ->paginate(10);

        return view('livewire.frontend.account.meal-plan-order', [
            'bookings' => $bookings,
        ]);
    }
}
