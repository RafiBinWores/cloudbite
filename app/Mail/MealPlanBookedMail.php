<?php

namespace App\Mail;

use App\Models\CompanyInfo;
use App\Models\Dish;
use App\Models\EmailTemplate;
use App\Models\MealPlanBooking;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class MealPlanBookedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public MealPlanBooking $booking;
    public ?EmailTemplate $template;
    public ?CompanyInfo $companyInfo;
    public string $buttonUrl;

    public function __construct(MealPlanBooking $booking)
    {
        $this->booking = $booking;

        $this->template = EmailTemplate::where('key', 'meal_plan_booking')->first()
            ?? new EmailTemplate();

        $this->companyInfo = CompanyInfo::first()
            ?? new CompanyInfo();

        // ✅ Use your real details route (important)
        // Must match your details page: mount(string $code)
        $this->buttonUrl = route('meal-plan.booking.show', ['code' => $booking->booking_code]);
        // If your route name is different, update it.
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Meal Plan Booking Confirmed — #' . $this->booking->booking_code,
        );
    }

    public function content(): Content
    {
        // ✅ Re-fetch fresh in queued mode
        $booking = $this->booking;

        try {
            if (isset($booking->id)) {
                $booking = MealPlanBooking::find($booking->id) ?? $booking;
            }
        } catch (\Throwable $e) {
            // keep current
        }

        // ✅ Normalize days (in case it is stored as JSON string)
        $days = $booking->days ?? [];
        if (is_string($days)) {
            $decoded = json_decode($days, true);
            $days = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        }
        if (!is_array($days)) {
            $days = [];
        }

        // ✅ Collect dish IDs from days->slots->items
        $ids = [];
        foreach ($days as $day) {
            $slots = $day['slots'] ?? [];
            foreach ($slots as $slotData) {
                foreach (($slotData['items'] ?? []) as $item) {
                    if (!empty($item['dish_id'])) {
                        $ids[] = (int) $item['dish_id'];
                    }
                }
            }
        }
        $ids = array_values(array_unique($ids));

        // ✅ Load dishes exactly like your details page
        $dishesById = Dish::query()
            ->when(!empty($ids), fn ($q) => $q->whereIn('id', $ids))
            ->with([
                'crusts:id,name,price',
                'buns:id,name',
                'addOns:id,name,price',
            ])
            ->get()
            ->keyBy('id');

        return new Content(
            view: 'emails.meal-plans.booked',
            with: [
                'booking'     => $booking,
                'days'        => $days,
                'dishesById'  => $dishesById,
                'template'    => $this->template,
                'companyInfo' => $this->companyInfo,
                'buttonUrl'   => $this->buttonUrl,
            ],
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
