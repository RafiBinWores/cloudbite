<?php

namespace App\Livewire\Frontend\MealPlan;

use App\Models\Dish;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class MealPlan extends Component
{
    private const SESSION_KEY = 'meal_plan_state';

    public string $planType = 'weekly'; // weekly | monthly
    public ?string $startDate = null;

    // For monthly plan: which week is currently shown (1..4)
    public int $currentWeek = 1;

    // Active category in modal (null = All)
    public ?int $activeDishCategoryId = null;

    public array $mealPrefs = [
        'breakfast' => false,
        'lunch'     => false,
        'tiffin'    => false,
        'dinner'    => false,
    ];

    /**
     * days[index] = [
     *   'name'  => 'SAT 30 NOV',
     *   'slots' => [
     *     'breakfast' => [
     *        'items' => [
     *           [
     *             'dish_id'     => int,
     *             'qty'         => int,
     *             'variant_key' => string|null,
     *             'crust_key'   => string|null,
     *             'bun_key'     => string|null,
     *             'addon_keys'  => array<int|string>,
     *           ],
     *        ],
     *     ],
     *   ],
     * ]
     */
    public array $days = [];

    /** @var \Illuminate\Support\Collection<int,\App\Models\Dish> */
    public Collection $dishes;

    // Slot modal (day + slot)
    public bool $showModal = false;
    public array $tempSelection = [
        'day'    => null,
        'slot'   => null,
        'dishes' => [],
    ];

    // Dish customization modal
    public bool $dishConfigOpen = false;
    public ?int $configDishId = null;

    public const SLOTS = ['breakfast', 'lunch', 'tiffin', 'dinner'];

    public function mount(): void
    {
        // Load dishes (always fresh)
        $this->dishes = Dish::query()
            ->visible()
            ->with([
                'category:id,name',
                'crusts:id,name,price',
                'buns:id,name',
                'addOns:id,name,price',
            ])
            ->orderBy('title')
            ->get([
                'id',
                'category_id',
                'title',
                'short_description',
                'price',
                'discount_type',
                'discount',
                'thumbnail',
                'variations',
            ]);

        // Load saved state if exists
        $saved = session(self::SESSION_KEY, null);

        if (is_array($saved)) {
            $this->planType      = $saved['planType']  ?? $this->planType;
            $this->startDate     = $saved['startDate'] ?? null;
            $this->currentWeek   = (int) ($saved['currentWeek'] ?? 1);
            $this->mealPrefs     = $saved['mealPrefs'] ?? $this->mealPrefs;
            $this->days          = $saved['days'] ?? [];

            // Fallback if saved days are somehow empty
            if (empty($this->days)) {
                $this->startDate = $this->startDate ?: Carbon::today()->toDateString();
                $this->initDays();
                $this->persistState();
            }
        } else {
            // First time visit: build fresh plan
            $this->startDate = $this->startDate ?: Carbon::today()->toDateString();
            $this->initDays();
            $this->persistState();
        }
    }

    private function initDays(): void
    {
        $this->days = [];

        $totalDays = $this->planType === 'monthly' ? 30 : 7;

        $start = $this->startDate
            ? Carbon::parse($this->startDate)->startOfDay()
            : Carbon::today();

        for ($i = 0; $i < $totalDays; $i++) {
            $date = $start->copy()->addDays($i);

            if ($this->planType === 'weekly') {
                $name = strtoupper($date->format('l')) . ' (' . strtoupper($date->format('d M')) . ')';
            } else {
                $name = strtoupper($date->format('d M (D)'));
            }

            $this->days[$i] = [
                'name'  => $name,
                'slots' => [],
            ];

            foreach (self::SLOTS as $slot) {
                $this->days[$i]['slots'][$slot] = [
                    'items' => [],
                ];
            }
        }

        $this->currentWeek = 1;
    }

    private function persistState(): void
    {
        session([
            self::SESSION_KEY => [
                'planType'    => $this->planType,
                'startDate'   => $this->startDate,
                'currentWeek' => $this->currentWeek,
                'mealPrefs'   => $this->mealPrefs,
                'days'        => $this->days,
            ],
        ]);
    }

    /* -------------------------
     * React to changes
     * ------------------------*/

    public function updatedPlanType(): void
    {
        // Change plan type = new calendar, clear items
        $this->showModal        = false;
        $this->dishConfigOpen   = false;
        $this->configDishId     = null;
        $this->currentWeek      = 1;

        $this->initDays();
        $this->persistState();
    }

    public function updatedStartDate(): void
    {
        if ($this->startDate) {
            $this->startDate = Carbon::parse($this->startDate)->toDateString();
        } else {
            $this->startDate = Carbon::today()->toDateString();
        }

        // When user changes start date:
        // - rebuild days with new base date
        // - keep mealPrefs and planType
        // - clear UI modals
        $this->showModal        = false;
        $this->dishConfigOpen   = false;
        $this->configDishId     = null;
        $this->currentWeek      = 1;

        $this->initDays();
        $this->persistState();
    }

    public function updatedMealPrefs(): void
    {
        // Just persist; days themselves stay same
        $this->persistState();
    }

    /* -------------------------
     * UI Helpers / Computed
     * ------------------------*/

    public function getSelectedSlotsProperty(): array
    {
        return array_values(
            array_filter(self::SLOTS, fn ($slot) => !empty($this->mealPrefs[$slot]))
        );
    }

    /**
     * Visible days:
     * - Weekly: all 7 days
     * - Monthly: 7-day slice by currentWeek
     */
    public function getVisibleDaysProperty(): array
    {
        $result = [];

        if (empty($this->days)) {
            return $result;
        }

        if ($this->planType === 'weekly') {
            foreach ($this->days as $idx => $day) {
                $result[] = [
                    'index' => $idx,
                    'day'   => $day,
                ];
            }
            return $result;
        }

        $weekSize   = 7;
        $totalDays  = count($this->days);
        $weekNumber = max(1, min(4, (int) $this->currentWeek));

        $start = ($weekNumber - 1) * $weekSize;
        $end   = min($start + $weekSize, $totalDays);

        for ($i = $start; $i < $end; $i++) {
            $result[] = [
                'index' => $i,
                'day'   => $this->days[$i],
            ];
        }

        return $result;
    }

    /** Unique dish categories for modal filter. */
    public function getDishCategoriesProperty(): Collection
    {
        return $this->dishes
            ->pluck('category')
            ->filter()
            ->unique('id')
            ->values();
    }

    /** Plan total (discounted base price + options). */
    public function getPlanTotalProperty(): float
    {
        $total = 0;

        foreach ($this->days as $day) {
            foreach ($this->selectedSlots as $slot) {
                $items = $day['slots'][$slot]['items'] ?? [];

                foreach ($items as $item) {
                    /** @var \App\Models\Dish|null $dish */
                    $dish = $this->dishes->firstWhere('id', $item['dish_id'] ?? null);
                    if (!$dish) {
                        continue;
                    }

                    $qty  = max(1, (int) ($item['qty'] ?? 1));
                    $unit = $this->calculateItemUnitPrice($dish, $item);

                    $total += $unit * $qty;
                }
            }
        }

        return $total;
    }

    public function getWeeklyTotalProperty(): float
    {
        if ($this->planType === 'weekly') {
            return $this->planTotal;
        }

        return $this->planTotal / 4;
    }

    public function getMonthlyTotalProperty(): float
    {
        if ($this->planType === 'monthly') {
            return $this->planTotal;
        }

        return $this->planTotal * 4;
    }

    /* -------------------------
     * Slot modal workflows
     * ------------------------*/

    public function openSlot(int $dayIndex, string $slot): void
    {
        if (!in_array($slot, self::SLOTS, true)) {
            return;
        }

        if (!isset($this->days[$dayIndex])) {
            return;
        }

        $this->tempSelection = [
            'day'    => $dayIndex,
            'slot'   => $slot,
            'dishes' => [],
        ];

        $this->activeDishCategoryId = null;

        $existingItems = $this->days[$dayIndex]['slots'][$slot]['items'] ?? [];

        // Aggregate existing config per dish
        $existingMap = [];
        foreach ($existingItems as $item) {
            $dishId = (int) ($item['dish_id'] ?? 0);
            if (!$dishId) {
                continue;
            }

            $qty = max(1, (int) ($item['qty'] ?? 1));

            if (!isset($existingMap[$dishId])) {
                $existingMap[$dishId] = [
                    'qty'         => 0,
                    'variant_key' => $item['variant_key'] ?? null,
                    'crust_key'   => $item['crust_key']   ?? null,
                    'bun_key'     => $item['bun_key']     ?? null,
                    'addon_keys'  => $item['addon_keys']  ?? [],
                ];
            }

            $existingMap[$dishId]['qty'] += $qty;
            $existingMap[$dishId]['variant_key'] = $item['variant_key'] ?? $existingMap[$dishId]['variant_key'];
            $existingMap[$dishId]['crust_key']   = $item['crust_key']   ?? $existingMap[$dishId]['crust_key'];
            $existingMap[$dishId]['bun_key']     = $item['bun_key']     ?? $existingMap[$dishId]['bun_key'];
            $existingMap[$dishId]['addon_keys']  = $item['addon_keys']  ?? $existingMap[$dishId]['addon_keys'];
        }

        // Build tempSelection for each dish
        foreach ($this->dishes as $dish) {
            $id     = (int) $dish->id;
            $config = $existingMap[$id] ?? null;

            $this->tempSelection['dishes'][$id] = [
                'selected'    => $config !== null,
                'qty'         => $config['qty']         ?? 1,
                'variant_key' => $config['variant_key'] ?? null,
                'crust_key'   => $config['crust_key']   ?? null,
                'bun_key'     => $config['bun_key']     ?? null,
                'addon_keys'  => $config['addon_keys']  ?? [],
            ];
        }

        $this->resetErrorBag();
        $this->dishConfigOpen = false;
        $this->configDishId   = null;
        $this->showModal      = true;
    }

    public function updatedTempSelection($value, $key): void
    {
        if (!str_starts_with($key, 'tempSelection.dishes.')) {
            return;
        }

        $parts  = explode('.', $key);
        $dishId = isset($parts[2]) ? (int) $parts[2] : 0;

        if (!$dishId || !isset($this->tempSelection['dishes'][$dishId])) {
            return;
        }

        if (str_ends_with($key, '.qty')) {
            $qty = (int) ($this->tempSelection['dishes'][$dishId]['qty'] ?? 1);
            if ($qty < 1) {
                $this->tempSelection['dishes'][$dishId]['qty'] = 1;
            }
        }
    }

    public function incrementDishQty(int $dishId): void
    {
        if (!isset($this->tempSelection['dishes'][$dishId])) {
            return;
        }

        $qty = (int) ($this->tempSelection['dishes'][$dishId]['qty'] ?? 1);
        $this->tempSelection['dishes'][$dishId]['qty'] = max(1, $qty + 1);
    }

    public function decrementDishQty(int $dishId): void
    {
        if (!isset($this->tempSelection['dishes'][$dishId])) {
            return;
        }

        $qty = (int) ($this->tempSelection['dishes'][$dishId]['qty'] ?? 1);
        $qty = max(1, $qty - 1);
        $this->tempSelection['dishes'][$dishId]['qty'] = $qty;
    }

    // Open per-dish customization modal (card click)
    public function openDishConfig(int $dishId): void
    {
        if (!isset($this->tempSelection['day'], $this->tempSelection['slot'])) {
            return;
        }

        if (!isset($this->tempSelection['dishes'][$dishId])) {
            $this->tempSelection['dishes'][$dishId] = [
                'selected'    => false,
                'qty'         => 1,
                'variant_key' => null,
                'crust_key'   => null,
                'bun_key'     => null,
                'addon_keys'  => [],
            ];
        }

        $entry = $this->tempSelection['dishes'][$dishId];

        if (($entry['qty'] ?? 0) < 1) {
            $this->tempSelection['dishes'][$dishId]['qty'] = 1;
        }

        $this->configDishId   = $dishId;
        $this->dishConfigOpen = true;
        $this->resetErrorBag();
    }

    public function closeDishConfig(): void
    {
        $this->dishConfigOpen = false;
        $this->configDishId   = null;
        $this->resetErrorBag();
    }

    /**
     * Save & Continue button: validate required fields and mark dish as selected.
     * We use field-level error keys: config.variant, config.crust, config.bun
     * so Blade can add red borders.
     */
    public function applyDishConfig(int $dishId): void
    {
        if (!isset($this->tempSelection['dishes'][$dishId])) {
            return;
        }

        /** @var Dish|null $dish */
        $dish = $this->dishes->firstWhere('id', $dishId);
        if (!$dish) {
            return;
        }

        $entry = &$this->tempSelection['dishes'][$dishId];

        $this->resetErrorBag();

        // Determine requirement (has options => required)
        $rawVars    = $dish->variations ?? [];
        if (is_string($rawVars)) {
            $decoded = json_decode($rawVars, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $rawVars = $decoded;
            } else {
                $rawVars = [];
            }
        }

        $hasVariant = false;
        if (is_array($rawVars)) {
            if (!empty($rawVars['variants']) && is_array($rawVars['variants'])) {
                $hasVariant = count($rawVars['variants']) > 0;
            } elseif (!empty($rawVars['options']) && is_array($rawVars['options'])) {
                $hasVariant = count($rawVars['options']) > 0;
            } else {
                foreach ($rawVars as $g) {
                    if (!empty($g['options']) && is_array($g['options'])) {
                        $hasVariant = true;
                        break;
                    }
                }
            }
        }

        $hasCrusts = $dish->crusts && $dish->crusts->count() > 0;
        $hasBuns   = $dish->buns   && $dish->buns->count()   > 0;

        $hasErrors = false;

        // Variant
        $variantKey = $entry['variant_key'] ?? null;
        if ($hasVariant && ($variantKey === null || $variantKey === '')) {
            $this->addError('config.variant', 'Variant required.');
            $hasErrors = true;
        }

        // Crust
        $crustKey = $entry['crust_key'] ?? null;
        if ($hasCrusts && ($crustKey === null || $crustKey === '')) {
            $this->addError('config.crust', 'Crust required.');
            $hasErrors = true;
        }

        // Bun
        $bunKey = $entry['bun_key'] ?? null;
        if ($hasBuns && ($bunKey === null || $bunKey === '')) {
            $this->addError('config.bun', 'Bun required.');
            $hasErrors = true;
        }

        if ($hasErrors) {
            // Blade only uses $errors->has(...) to make red borders, no messages shown.
            return;
        }

        $qty = (int) ($entry['qty'] ?? 1);
        if ($qty < 1) {
            $entry['qty'] = 1;
        }

        $entry['selected'] = true;

        $this->resetErrorBag();
        $this->dishConfigOpen = false;
        $this->configDishId   = null;
    }

    // Explicit remove button for a dish in current slot
    public function deselectDish(int $dishId): void
    {
        if (!isset($this->tempSelection['dishes'][$dishId])) {
            return;
        }

        $this->tempSelection['dishes'][$dishId]['selected'] = false;
    }

    public function setActiveDishCategory($categoryId): void
    {
        $this->activeDishCategoryId = $categoryId ? (int) $categoryId : null;
    }

    public function confirmSlotSelection(): void
    {
        $day  = $this->tempSelection['day'] ?? null;
        $slot = $this->tempSelection['slot'] ?? null;

        if ($day === null || $slot === null || !isset($this->days[$day])) {
            return;
        }

        $items = [];

        foreach ($this->tempSelection['dishes'] as $dishId => $data) {
            if (empty($data['selected'])) {
                continue;
            }

            $qty = max(1, (int) ($data['qty'] ?? 1));

            $items[] = [
                'dish_id'     => (int) $dishId,
                'qty'         => $qty,
                'variant_key' => $data['variant_key'] ?? null,
                'crust_key'   => $data['crust_key']   ?? null,
                'bun_key'     => $data['bun_key']     ?? null,
                'addon_keys'  => $data['addon_keys']  ?? [],
            ];
        }

        $this->days[$day]['slots'][$slot]['items'] = $items;

        $this->showModal      = false;
        $this->dishConfigOpen = false;
        $this->configDishId   = null;
        $this->resetErrorBag();

        $this->persistState();
    }

    public function closeModal(): void
    {
        $this->showModal      = false;
        $this->dishConfigOpen = false;
        $this->configDishId   = null;
        $this->resetErrorBag();
    }

    public function removeItem(int $dayIndex, string $slot, int $itemIndex): void
    {
        if (!isset($this->days[$dayIndex]['slots'][$slot]['items'][$itemIndex])) {
            return;
        }

        unset($this->days[$dayIndex]['slots'][$slot]['items'][$itemIndex]);
        $this->days[$dayIndex]['slots'][$slot]['items'] =
            array_values($this->days[$dayIndex]['slots'][$slot]['items']);

        $this->persistState();
    }

    public function resetPlan(): void
    {
        $this->mealPrefs = [
            'breakfast' => false,
            'lunch'     => false,
            'tiffin'    => false,
            'dinner'    => false,
        ];

        $this->startDate            = Carbon::today()->toDateString();
        $this->planType             = 'weekly';
        $this->currentWeek          = 1;
        $this->activeDishCategoryId = null;
        $this->showModal            = false;
        $this->dishConfigOpen       = false;
        $this->configDishId         = null;
        $this->resetErrorBag();

        $this->initDays();

        // Clear and re-save clean state
        session()->forget(self::SESSION_KEY);
        $this->persistState();
    }

    /**
     * Copy Week 1 (days 0–6) => Weeks 2–4.
     */
    public function copyWeekOneToAll(): void
    {
        if ($this->planType !== 'monthly') {
            return;
        }

        $daysCount = count($this->days);
        if ($daysCount < 7) {
            return;
        }

        for ($week = 1; $week < 4; $week++) {
            $destStart = $week * 7;

            for ($offset = 0; $offset < 7; $offset++) {
                $srcIndex  = $offset;
                $destIndex = $destStart + $offset;

                if ($destIndex >= $daysCount) {
                    break;
                }

                $this->days[$destIndex]['slots'] = $this->days[$srcIndex]['slots'];
            }
        }

        $this->persistState();
    }

    /**
     * Calculate unit price using discounted base price + variant/crust/bun/add-ons.
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

            // ensure array (handle JSON string)
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
                // Case 1: ['variants' => [ ... ]]
                if (isset($vars['variants']) && is_array($vars['variants'])) {
                    $variantOptions = $vars['variants'];

                // Case 2: ['options' => [ ... ]]
                } elseif (isset($vars['options']) && is_array($vars['options'])) {
                    $variantOptions = $vars['options'];

                // Case 3: [ { name: 'Size', options: [...] }, ... ]
                } else {
                    foreach ($vars as $g) {
                        if (!empty($g['options']) && is_array($g['options'])) {
                            $variantOptions = array_merge($variantOptions, $g['options']);
                        }
                    }
                }
            }

            foreach ($variantOptions as $index => $opt) {
                // fallback to $index when no explicit id/key
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
                $total += (float) ($bun->price ?? 0);
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

    public function goToPlanCheckout()
{
    // Save current plan state into session
    session([
        'meal_plan_state' => [
            'planType'   => $this->planType,
            'startDate'  => $this->startDate,
            'mealPrefs'  => $this->mealPrefs,
            'days'       => $this->days,
        ],
    ]);

    // Redirect to plan checkout page
    return redirect()->route('plans.checkout');
}


    public function render()
    {
        return view('livewire.frontend.meal-plan.meal-plan');
    }
}
