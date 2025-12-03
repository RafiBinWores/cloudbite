<?php

namespace App\Livewire\Frontend\Checkout;

use App\Models\Address;
use App\Models\Dish;
use App\Models\MealPlanBooking;
use App\Models\ShippingSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;
use Illuminate\Support\Str;
use Carbon\Carbon;

#[Layout('components.layouts.frontend')]
class PlanCheckoutPage extends Component
{
    // Contact & shipping
    public string  $contact_name = '';
    public string  $phone = '';
    public ?string $email = null;

    public string  $address = '';
    public string  $city = '';
    public string  $postcode = '';
    public ?float  $lat = null;
    public ?float  $lng = null;

    public ?string $customer_note = null;

    // Payment
    public string $payment_method  = 'cod';
    public string $payment_option  = 'full'; // full | half

    // Saved addresses
    public $addresses;
    public ?int $selectedAddressId = null;
    public bool $hasExistingAddress = false;

    // Shipping setting
    public ?ShippingSetting $shipSetting = null;

    // Plan state from session
    public string  $planType   = 'weekly'; // weekly | monthly
    public ?string $startDate  = null;
    public array   $mealPrefs  = [];
    public array   $days       = [];

    // Totals
    public float $plan_total     = 0;
    public float $weekly_total   = 0;
    public float $monthly_total  = 0;
    public float $grand_total    = 0;
    public float $payable_amount = 0;
    public float $shipping_total = 0;

    protected function rules(): array
    {
        return [
            'contact_name'   => 'required|string|max:191',
            'phone'          => ['required', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'email'          => 'nullable|email',
            'address'        => 'required|string|max:255',
            'city'           => 'required|string|max:120',
            'postcode'       => 'required|string|max:20',
            'customer_note'  => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cod,sslcommerz',
            'payment_option' => 'required|in:full,half',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required'   => 'A phone number is required.',
            'phone.regex'      => 'Enter a valid Bangladeshi mobile number.',
            'address.required' => 'Address is required.',
        ];
    }

    public function mount()
    {
        $user  = Auth::user();
        $state = session('meal_plan_state');

        // If no plan in session, abort
        abort_if(! $state || empty($state['days'] ?? []), 404);

        // Load state from session
        $this->planType  = $state['planType']  ?? 'weekly';
        $this->startDate = $state['startDate'] ?? null;
        $this->mealPrefs = $state['mealPrefs'] ?? [];
        $this->days      = $state['days']      ?? [];

        $this->shipSetting = ShippingSetting::latest()->first();

        if ($user) {
            $this->contact_name = $user->name ?? '';
            $this->email        = $user->email ?? null;

            $this->addresses = Address::where('user_id', $user->id)
                ->orderByRaw("FIELD(label,'home','workplace','others')")
                ->latest('id')
                ->get();

            $this->hasExistingAddress = $this->addresses->isNotEmpty();

            if (! $this->hasExistingAddress) {
                redirect()->route('address.create')->send();
                return;
            }

            // Default address
            $default = $this->addresses->firstWhere('is_default', true)
                ?? $this->addresses->firstWhere('label', 'home')
                ?? $this->addresses->first();

            $this->useAddress($default->id);
        }

        $this->calculateTotals();
        $this->updatePayableAmount();
    }

    #[On('address-selected')]
    public function handleAddressSelected(int $id): void
    {
        $this->useAddress($id);
    }

    public function useAddress(int $addressId): void
    {
        $addr = $this->addresses->firstWhere('id', $addressId)
            ?? Address::where('user_id', Auth::id())->find($addressId);

        if (! $addr) return;

        $this->selectedAddressId = $addr->id;

        $this->address  = (string) ($addr->address ?? '');
        $this->city     = (string) ($addr->city ?? '');
        $this->postcode = (string) ($addr->postal_code ?? '');
        $this->lat      = $addr->lat;
        $this->lng      = $addr->lng;

        if ($addr->contact_name)  $this->contact_name = $addr->contact_name;
        if ($addr->contact_phone) $this->phone        = $addr->contact_phone;

        $this->customer_note = (string) ($addr->note ?? '');

        $this->calculateTotals();
        $this->updatePayableAmount();
    }

    public function updated($field): void
    {
        if (in_array($field, ['address', 'city', 'postcode', 'payment_option', 'payment_method'])) {
            $this->calculateTotals();
            $this->updatePayableAmount();
        }
    }

    /**
     * Recalculate plan_total, weekly_total, monthly_total, shipping_total, grand_total
     * from $this->days + $this->mealPrefs
     */
    protected function calculateTotals(): void
    {
        $selectedSlots = array_keys(array_filter($this->mealPrefs ?? []));

        // No slots selected = no cost
        if (empty($selectedSlots) || empty($this->days)) {
            $this->plan_total     = 0;
            $this->weekly_total   = 0;
            $this->monthly_total  = 0;
            $this->shipping_total = 0;
            $this->grand_total    = 0;
            return;
        }

        // Collect all dish IDs used in this plan
        $dishIds = [];
        foreach ($this->days as $day) {
            foreach ($selectedSlots as $slot) {
                $items = $day['slots'][$slot]['items'] ?? [];
                foreach ($items as $item) {
                    if (! empty($item['dish_id'])) {
                        $dishIds[] = (int) $item['dish_id'];
                    }
                }
            }
        }

        $dishIds = array_values(array_unique(array_filter($dishIds)));

        if (empty($dishIds)) {
            $this->plan_total     = 0;
            $this->weekly_total   = 0;
            $this->monthly_total  = 0;
            $this->shipping_total = 0;
            $this->grand_total    = 0;
            return;
        }

        // Load referenced dishes with pricing relations
        $dishes = Dish::query()
            ->with(['crusts:id,name,price', 'buns:id,name', 'addOns:id,name,price'])
            ->whereIn('id', $dishIds)
            ->get()
            ->keyBy('id');

        $total = 0.0;

        foreach ($this->days as $day) {
            foreach ($selectedSlots as $slot) {
                $items = $day['slots'][$slot]['items'] ?? [];

                foreach ($items as $item) {
                    $dishId = (int) ($item['dish_id'] ?? 0);
                    /** @var Dish|null $dish */
                    $dish = $dishes->get($dishId);
                    if (! $dish) {
                        continue;
                    }

                    $qty  = max(1, (int) ($item['qty'] ?? 1));
                    $unit = $this->calculateItemUnitPrice($dish, $item);

                    $total += $unit * $qty;
                }
            }
        }

        $this->plan_total = $total;

        // Weekly / monthly equivalent
        if ($this->planType === 'weekly') {
            $this->weekly_total  = $total;
            $this->monthly_total = $total * 4;
        } else { // monthly plan
            $this->monthly_total = $total;
            $this->weekly_total  = $total / 4;
        }

        // Shipping calculation (similar to normal checkout, but using plan_total)
        if ($this->shipSetting) {
            $base = (float) ($this->shipSetting->base_fee ?? 0);
            $free = (bool)  ($this->shipSetting->free_delivery ?? true);
            $min  = (float) ($this->shipSetting->free_minimum ?? 0);

            $this->shipping_total = ($free && $this->plan_total >= $min) ? 0.0 : $base;
        } else {
            $this->shipping_total = 0;
        }

        $this->grand_total = max(0, $this->plan_total + $this->shipping_total);
    }

    /**
     * Same price logic as MealPlan component.
     */
    protected function calculateItemUnitPrice(Dish $dish, array $item): float
    {
        // base = discounted price_from accessor
        $base  = (float) $dish->price_with_discount;
        $total = $base;

        // =============== VARIANT (optional, but price-affected) ===============
        $variantKey = $item['variant_key'] ?? null;
        if ($variantKey !== null && $variantKey !== '') {
            $vars = $dish->variations ?? [];

            if (is_string($vars)) {
                $decoded = json_decode($vars, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $vars = $decoded;
                } else {
                    $vars = [];
                }
            }

            $variantOptions = [];

            if (is_array($vars)) {
                if (isset($vars['variants']) && is_array($vars['variants'])) {
                    $variantOptions = $vars['variants'];
                } elseif (isset($vars['options']) && is_array($vars['options'])) {
                    $variantOptions = $vars['options'];
                } else {
                    foreach ($vars as $g) {
                        if (!empty($g['options']) && is_array($g['options'])) {
                            $variantOptions = array_merge($variantOptions, $g['options']);
                        }
                    }
                }
            }

            foreach ($variantOptions as $index => $opt) {
                $optKey = $opt['key'] ?? ($opt['id'] ?? $index);

                if ((string) $optKey === (string) $variantKey) {
                    $total += (float) ($opt['price'] ?? 0);
                    break;
                }
            }
        }

        // =============== CRUST (optional but required if exists) ===============
        if (!empty($item['crust_key'])) {
            $crust = $dish->crusts->firstWhere('id', $item['crust_key']);
            if ($crust) {
                $total += (float) ($crust->price ?? 0);
            }
        }

        // =============== BUN (optional but required if exists) ===============
        if (!empty($item['bun_key'])) {
            $bun = $dish->buns->firstWhere('id', $item['bun_key']);
            if ($bun) {
                // buns have no extra price (per your DB), so we *don't* add anything here
                // but you could add price in future if needed
            }
        }

        // =============== ADD-ONS (checkbox list) ===============
        if (!empty($item['addon_keys'])) {
            $addonIds = array_map('intval', (array) $item['addon_keys']);

            foreach ($dish->addOns as $addon) {
                if (in_array((int) $addon->id, $addonIds, true)) {
                    $total += (float) ($addon->price ?? 0);
                }
            }
        }

        return $total;
    }

    protected function updatePayableAmount(): void
    {
        $this->payable_amount = $this->payment_option === 'half'
            ? round($this->grand_total / 2, 2)
            : $this->grand_total;
    }

public function placePlanBooking()
{
    $this->validate();

    if (empty($this->days)) {
        abort(400, 'No plan data found.');
    }

    return DB::transaction(function () {
        $user = Auth::user();
        $year = now()->format('Y');

        // Generate yearly incremental booking code: e.g. 20250001
        $lastBooking = MealPlanBooking::whereYear('created_at', $year)
            ->where('booking_code', 'like', "{$year}%")
            ->orderByDesc('id')
            ->first();

        $nextNumber = ($lastBooking && preg_match('/' . $year . '(\d{4})$/', $lastBooking->booking_code, $m))
            ? ((int) $m[1] + 1)
            : 1;

        $bookingCode = $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

        $grandTotal = (float) $this->grand_total;

        // How much user pays now
        $payNow = $this->payment_option === 'half'
            ? round($grandTotal / 2, 2)
            : $grandTotal;

        // Remaining due
        $dueLater = max(0, $grandTotal - $payNow);

        // Meta (e.g. SSLCommerz tran_id)
        $meta = [];

        if ($this->payment_method === 'sslcommerz') {
            $tranId        = 'MP-' . now()->format('YmdHis') . '-' . Str::random(6);
            $meta['tran_id'] = $tranId;
        }

        $booking = MealPlanBooking::create([
            'user_id'       => $user?->id,
            'booking_code'  => $bookingCode,

            'plan_type'     => $this->planType,
            'start_date'    => $this->startDate,

            'meal_prefs'    => $this->mealPrefs,
            'days'          => $this->days,

            'plan_subtotal'  => (float) $this->plan_total,
            'shipping_total' => (float) $this->shipping_total,
            'grand_total'    => $grandTotal,

            'payment_option' => $this->payment_option,  // full | half
            'payment_method' => $this->payment_method,  // cod | sslcommerz
            'payment_status' => 'pending',
            'status'         => 'pending',

            'contact_name'   => $this->contact_name,
            'phone'          => $this->phone,
            'email'          => $this->email,

            'shipping_address' => [
                'line1'      => $this->address,
                'city'       => $this->city,
                'postcode'   => $this->postcode,
                'lat'        => $this->lat,
                'lng'        => $this->lng,
                'address_id' => $this->selectedAddressId,
            ],

            'customer_note' => $this->customer_note,

            // 🔥 match your DB columns exactly
            'pay_now'    => $payNow,
            'due_amount' => $dueLater,

            'meta' => $meta,
        ]);

        if ($this->payment_method === 'sslcommerz') {
            // Plan-specific SSL route
            return redirect()->route('ssl.plan.init', ['booking' => $booking->id]);
        }

        // COD flow: mark as confirmed immediately
        $booking->update([
            'payment_status' => 'pending',
            'status'         => 'confirmed',
            'confirmed_at'   => now(),
        ]);

        return redirect()->route('meal-plan.thankyou', ['code' => $booking->booking_code]);
    });
}


    public function render()
    {
        return view('livewire.frontend.checkout.plan-checkout-page', [
            'addresses'   => $this->addresses,
            'shipSetting' => $this->shipSetting,
        ]);
    }
}
