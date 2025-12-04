<?php

namespace App\Livewire\Admin\MealPlanBooking;

use App\Models\Dish;
use App\Models\MealPlanBooking;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Collection;
use Livewire\Component;

class MealPlanBookingDetails extends Component
{
    use WithTcToast;

    public string $code;
    public MealPlanBooking $booking;

    public Collection $dishesById;

    // UI state
    public bool $is_paid = false;
    public string $status = 'pending';

    // Allowed statuses for dropdown
    public array $statuses = [
        'pending',
        'confirmed',
        'on-going',
        'completed',
        'cancelled',
    ];

    public function mount(string $code): void
    {
        $this->booking = MealPlanBooking::where('booking_code', $code)
            ->with('user')
            ->firstOrFail();

        $this->status         = (string) ($this->booking->status ?? 'pending');
        $this->payment_status = (string) ($this->booking->payment_status ?? 'pending');
        $this->is_paid        = strtolower($this->payment_status) === 'paid';

        $this->loadDishes();
    }

    protected function loadDishes(): void
    {
        $days = $this->booking->days ?? [];

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

        $this->dishesById = Dish::query()
            ->whereIn('id', $ids)
            ->with([
                'crusts:id,name,price',
                'buns:id,name',
                'addOns:id,name,price',
            ])
            ->get()
            ->keyBy('id');
    }

    /* ========= Helpers for variant / crust / bun / add-ons (+ price) ========= */

    public function getVariantInfo(Dish $dish, $variantKey): array
    {
        if ($variantKey === null || $variantKey === '') {
            return [
                'label' => null,
                'group' => null,
                'price' => 0.0,
            ];
        }

        $vars = $dish->variations ?? [];

        if (is_string($vars)) {
            $decoded = json_decode($vars, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $vars = $decoded;
            } else {
                $vars = [];
            }
        }

        $candidateOptions = [];

        if (is_array($vars)) {
            if (isset($vars['variants']) && is_array($vars['variants'])) {
                // Case 1: ['variants' => [ ... ]]
                $candidateOptions = $vars['variants'];
            } elseif (isset($vars['options']) && is_array($vars['options'])) {
                // Case 2: ['options' => [ ... ]]
                $candidateOptions = $vars['options'];
            } else {
                // Case 3: [ { name: 'Size', options: [...] }, ... ]
                foreach ($vars as $group) {
                    if (!empty($group['options']) && is_array($group['options'])) {
                        foreach ($group['options'] as $opt) {
                            $opt['_group'] = $group['name'] ?? null;
                            $candidateOptions[] = $opt;
                        }
                    }
                }
            }
        }

        foreach ($candidateOptions as $index => $opt) {
            $optKey = $opt['key'] ?? ($opt['id'] ?? $index);
            if ((string) $optKey === (string) $variantKey) {
                return [
                    'label' => $opt['label'] ?? ($opt['name'] ?? null),
                    'group' => $opt['_group'] ?? ($opt['group'] ?? null),
                    'price' => (float) ($opt['price'] ?? 0),
                ];
            }
        }

        return [
            'label' => null,
            'group' => null,
            'price' => 0.0,
        ];
    }

    public function getCrustInfo(Dish $dish, $crustKey): array
    {
        if (!$crustKey) {
            return ['name' => null, 'price' => 0.0];
        }

        $crust = $dish->crusts->firstWhere('id', (int) $crustKey);

        if (!$crust) {
            return ['name' => null, 'price' => 0.0];
        }

        return [
            'name'  => $crust->name,
            'price' => (float) ($crust->price ?? 0),
        ];
    }

    public function getBunInfo(Dish $dish, $bunKey): array
    {
        if (!$bunKey) {
            return ['name' => null, 'price' => 0.0];
        }

        $bun = $dish->buns->firstWhere('id', (int) $bunKey);

        if (!$bun) {
            return ['name' => null, 'price' => 0.0];
        }

        // If buns have price later, you can add it; for now 0
        return [
            'name'  => $bun->name,
            'price' => 0.0,
        ];
    }

    public function getAddonInfo(Dish $dish, array $addonKeys): array
    {
        $addonIds = array_map('intval', $addonKeys);
        $addons   = [];

        foreach ($dish->addOns as $addon) {
            if (in_array((int) $addon->id, $addonIds, true)) {
                $addons[] = [
                    'name'  => $addon->name,
                    'price' => (float) ($addon->price ?? 0),
                ];
            }
        }

        return $addons;
    }

    public function calculateItemUnitPrice(Dish $dish, array $item): float
    {
        $base  = (float) $dish->price_with_discount;
        $total = $base;

        // Variant
        $variantKey = $item['variant_key'] ?? null;
        if ($variantKey !== null && $variantKey !== '') {
            $variantInfo = $this->getVariantInfo($dish, $variantKey);
            $total += $variantInfo['price'] ?? 0;
        }

        // Crust
        if (!empty($item['crust_key'])) {
            $crustInfo = $this->getCrustInfo($dish, $item['crust_key']);
            $total += $crustInfo['price'] ?? 0;
        }

        // Bun
        if (!empty($item['bun_key'])) {
            $bunInfo = $this->getBunInfo($dish, $item['bun_key']);
            $total += $bunInfo['price'] ?? 0;
        }

        // Add-ons
        if (!empty($item['addon_keys'])) {
            $addons = $this->getAddonInfo($dish, (array) $item['addon_keys']);
            foreach ($addons as $a) {
                $total += $a['price'] ?? 0;
            }
        }

        return $total;
    }

    /* =================== Status / payment actions =================== */

    public function saveStatus(): void
    {
        $this->validate([
            'status' => ['required', 'in:' . implode(',', $this->statuses)],
        ]);

        $this->booking->status = $this->status;
        $this->booking->save();

        $this->dispatch('toast', type: 'success', message: 'Booking status updated.');
    }

    public function savePaymentStatus(): void
    {
        $this->booking->payment_status = $this->is_paid ? 'paid' : 'pending';
        $this->payment_status          = $this->booking->payment_status;
        $this->booking->save();

        $this->dispatch('toast', type: 'success', message: 'Payment status updated.');
    }

    public function render()
    {
        return view('livewire.admin.meal-plan-booking.meal-plan-booking-details', [
            'booking'    => $this->booking,
            'dishesById' => $this->dishesById,
        ]);
    }
}
