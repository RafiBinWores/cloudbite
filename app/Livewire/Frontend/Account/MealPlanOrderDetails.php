<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Dish;
use App\Models\MealPlanBooking;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class MealPlanOrderDetails extends Component
{
    public MealPlanBooking $booking;

    /** @var \Illuminate\Support\Collection<int,\App\Models\Dish> */
    public Collection $dishesById;

    public function mount(string $code): void
    {
        $user = Auth::user();
        abort_unless($user, 403);

        $this->booking = MealPlanBooking::where('user_id', $user->id)
            ->where('booking_code', $code)
            ->firstOrFail();

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
                'buns:id,name',           // buns: no price yet
                'addOns:id,name,price',
            ])
            ->get()
            ->keyBy('id');
    }

    /**
     * Human readable meta (kept in case you still want short text anywhere).
     */
    public function buildItemMeta(?Dish $dish, array $item): string
    {
        if (!$dish) {
            return '';
        }

        $parts = [];

        // ===== VARIANT =====
        $variantKey = $item['variant_key'] ?? null;
        if ($variantKey !== null && $variantKey !== '') {
            $raw = $dish->variations ?? [];

            if (is_string($raw)) {
                $decoded = json_decode($raw, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $raw = $decoded;
                } else {
                    $raw = [];
                }
            }

            $label = null;
            $groupName = 'Variant';

            if (is_array($raw)) {
                $found = false;

                // Case 3: [ { name: 'Size', options: [...] }, ... ]
                foreach ($raw as $group) {
                    if (!isset($group['options']) || !is_array($group['options'])) {
                        continue;
                    }

                    foreach ($group['options'] as $idx => $opt) {
                        $optKey = $opt['key'] ?? ($opt['id'] ?? $idx);

                        if ((string) $optKey === (string) $variantKey) {
                            $label     = $opt['label'] ?? $opt['name'] ?? null;
                            $groupName = $group['name'] ?? 'Variant';
                            $found     = true;
                            break 2;
                        }
                    }
                }

                // Case 1/2: flat structure
                if (!$found) {
                    $opts = $raw['variants'] ?? ($raw['options'] ?? []);
                    if (is_array($opts)) {
                        foreach ($opts as $idx => $opt) {
                            $optKey = $opt['key'] ?? ($opt['id'] ?? $idx);
                            if ((string) $optKey === (string) $variantKey) {
                                $label = $opt['label'] ?? $opt['name'] ?? null;
                                break;
                            }
                        }
                    }
                }
            }

            if ($label) {
                $parts[] = "{$groupName}: {$label}";
            }
        }

        // ===== CRUST =====
        if (!empty($item['crust_key'])) {
            $crust = $dish->crusts->firstWhere('id', $item['crust_key']);
            if ($crust) {
                $parts[] = 'Crust: ' . $crust->name;
            }
        }

        // ===== BUN =====
        if (!empty($item['bun_key'])) {
            $bun = $dish->buns->firstWhere('id', $item['bun_key']);
            if ($bun) {
                $parts[] = 'Bun: ' . $bun->name;
            }
        }

        // ===== ADD-ONS =====
        if (!empty($item['addon_keys'])) {
            $addonIds = array_map('intval', (array) $item['addon_keys']);
            $names = [];

            foreach ($dish->addOns as $addon) {
                if (in_array((int) $addon->id, $addonIds, true)) {
                    $names[] = $addon->name;
                }
            }

            if (!empty($names)) {
                $parts[] = 'Add-ons: ' . implode(', ', $names);
            }
        }

        return implode(' • ', $parts);
    }

    /* ========= NEW: helpers to compute name + price for each part ========= */

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
            // Case 1: ['variants' => [ ... ]]
            if (isset($vars['variants']) && is_array($vars['variants'])) {
                $candidateOptions = $vars['variants'];

            // Case 2: ['options' => [ ... ]]
            } elseif (isset($vars['options']) && is_array($vars['options'])) {
                $candidateOptions = $vars['options'];

            // Case 3: [ { name: 'Size', options: [...] }, ... ]
            } else {
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

        // currently bun has no price column, so 0
        return [
            'name'  => $bun->name,
            'price' => 0.0,
        ];
    }

    public function getAddonInfo(Dish $dish, array $addonKeys): array
    {
        $addonIds = array_map('intval', $addonKeys);
        $addons = [];

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

        // Addons
        if (!empty($item['addon_keys'])) {
            $addons = $this->getAddonInfo($dish, (array) $item['addon_keys']);
            foreach ($addons as $a) {
                $total += $a['price'] ?? 0;
            }
        }

        return $total;
    }

    /**
     * Re-order from details page.
     */
    public function reorder(): void
    {
        $booking = $this->booking;

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

        redirect()->route('meal.plans');
    }

    public function render()
    {
        return view('livewire.frontend.account.meal-plan-order-details', [
            'booking'    => $this->booking,
            'dishesById' => $this->dishesById,
        ]);
    }
}
