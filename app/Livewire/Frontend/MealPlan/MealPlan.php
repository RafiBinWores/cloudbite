<?php

namespace App\Livewire\Frontend\MealPlan;

use App\Models\Coupon;
use App\Models\Dish;
use Carbon\Carbon;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Collection;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class MealPlan extends Component
{
    use WithTcToast;

    private const SESSION_KEY = 'meal_plan_state';

    public string $planType = 'weekly'; // weekly | monthly
    public ?string $startDate = null;

    public int $currentWeek = 1;
    public ?int $activeDishCategoryId = null;

    public array $mealPrefs = [
        'breakfast' => false,
        'lunch'     => false,
        'tiffin'    => false,
        'dinner'    => false,
    ];

    public array $days = [];

    /** @var \Illuminate\Support\Collection<int,\App\Models\Dish> */
    public Collection $dishes;

    public bool $showModal = false;
    public array $tempSelection = [
        'day'    => null,
        'slot'   => null,
        'dishes' => [],
    ];

    public bool $dishConfigOpen = false;
    public ?int $configDishId = null;

    // =========================
    // ✅ Coupon fields
    // =========================
    public ?string $couponCode = null;      // user typed
    public bool $couponApplied = false;
    public float $couponDiscount = 0.0;     // final discount amount
    public ?array $couponData = null;       // coupon row snapshot

    public const SLOTS = ['breakfast', 'lunch', 'tiffin', 'dinner'];

    public function mount(?string $plan = null): void
    {
        if (in_array($plan, ['weekly', 'monthly'], true)) {
            $this->planType = $plan;
        }

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

        $saved = session(self::SESSION_KEY, null);

        if (is_array($saved)) {
            if (!in_array($plan, ['weekly', 'monthly'], true)) {
                $this->planType = $saved['planType'] ?? $this->planType;
            }

            $this->startDate   = $saved['startDate'] ?? null;
            $this->currentWeek = (int) ($saved['currentWeek'] ?? 1);
            $this->mealPrefs   = $saved['mealPrefs'] ?? $this->mealPrefs;
            $this->days        = $saved['days'] ?? [];

            // coupon restore
            $this->couponCode     = $saved['couponCode'] ?? null;
            $this->couponApplied  = (bool) ($saved['couponApplied'] ?? false);
            $this->couponDiscount = (float) ($saved['couponDiscount'] ?? 0);
            $this->couponData     = $saved['couponData'] ?? null;
        }

        $this->startDate = $this->startDate ?: Carbon::today()->toDateString();

        $expectedDays = $this->planType === 'monthly' ? 28 : 7;
        if (empty($this->days) || count($this->days) !== $expectedDays) {
            $this->initDays();
        }

        // keep coupon in sync with totals
        $this->syncCoupon(false);
        $this->persistState();
    }

    private function initDays(): void
    {
        $this->days = [];

        $totalDays = $this->planType === 'monthly' ? 28 : 7;

        $start = $this->startDate
            ? Carbon::parse($this->startDate)->startOfDay()
            : Carbon::today();

        for ($i = 0; $i < $totalDays; $i++) {
            $date = $start->copy()->addDays($i);

            $name = strtoupper($date->format('l')) . ' (' . strtoupper($date->format('d M')) . ')';

            $this->days[$i] = [
                'date'  => $date->toDateString(),
                'name'  => $name,
                'slots' => [],
            ];

            foreach (self::SLOTS as $slot) {
                $this->days[$i]['slots'][$slot] = ['items' => []];
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

                'couponCode'     => $this->couponCode,
                'couponApplied'  => $this->couponApplied,
                'couponDiscount' => $this->couponDiscount,
                'couponData'     => $this->couponData,
            ],
        ]);
    }

    public function updatedPlanType(): void
    {
        $this->showModal      = false;
        $this->dishConfigOpen = false;
        $this->configDishId   = null;
        $this->currentWeek    = 1;

        $this->initDays();
        $this->syncCoupon(false);
        $this->persistState();
    }

    public function updatedStartDate(): void
    {
        $this->startDate = $this->startDate
            ? Carbon::parse($this->startDate)->toDateString()
            : Carbon::today()->toDateString();

        $this->showModal      = false;
        $this->dishConfigOpen = false;
        $this->configDishId   = null;
        $this->currentWeek    = 1;

        $this->initDays();
        $this->syncCoupon(false);
        $this->persistState();
    }

    public function updatedMealPrefs(): void
    {
        $this->syncCoupon(false);
        $this->persistState();
    }

    public function getSelectedSlotsProperty(): array
    {
        return array_values(array_filter(self::SLOTS, fn ($slot) => !empty($this->mealPrefs[$slot])));
    }

    public function getVisibleDaysProperty(): array
    {
        $result = [];
        if (empty($this->days)) return $result;

        if ($this->planType === 'weekly') {
            foreach ($this->days as $idx => $day) {
                $result[] = ['index' => $idx, 'day' => $day];
            }
            return $result;
        }

        $weekSize   = 7;
        $totalDays  = count($this->days);
        $weekNumber = max(1, min(4, (int) $this->currentWeek));

        $start = ($weekNumber - 1) * $weekSize;
        $end   = min($start + $weekSize, $totalDays);

        for ($i = $start; $i < $end; $i++) {
            $result[] = ['index' => $i, 'day' => $this->days[$i]];
        }

        return $result;
    }

    public function getDishCategoriesProperty(): Collection
    {
        return $this->dishes->pluck('category')->filter()->unique('id')->values();
    }

    // =========================
    // ✅ Totals
    // =========================

    public function getPlanTotalProperty(): float
    {
        $total = 0;

        foreach ($this->days as $day) {
            foreach ($this->selectedSlots as $slot) {
                $items = $day['slots'][$slot]['items'] ?? [];

                foreach ($items as $item) {
                    $dish = $this->dishes->firstWhere('id', $item['dish_id'] ?? null);
                    if (!$dish) continue;

                    $qty  = max(1, (int) ($item['qty'] ?? 1));
                    $unit = $this->calculateItemUnitPrice($dish, $item);

                    $total += $unit * $qty;
                }
            }
        }

        return (float) $total;
    }

    public function getWeeklyTotalProperty(): float
    {
        return $this->planType === 'weekly' ? $this->planTotal : $this->planTotal / 4;
    }

    public function getMonthlyTotalProperty(): float
    {
        return $this->planType === 'monthly' ? $this->planTotal : $this->planTotal * 4;
    }

    // =========================
    // ✅ Price details like orders
    // =========================

    public function calculateItemUnitPriceOriginal(Dish $dish, array $item): float
    {
        $base  = (float) $dish->price; // original base
        $total = $base;

        $groups = $this->normalizeVariantGroups($dish);

        $variantKeys = $item['variant_keys'] ?? [];
        if (empty($variantKeys) && !empty($item['variant_key'])) {
            $variantKeys = [0 => $item['variant_key']];
        }

        foreach ($groups as $gIndex => $group) {
            $selectedKey = $variantKeys[$gIndex] ?? null;
            if ($selectedKey === null || $selectedKey === '') continue;

            foreach (($group['options'] ?? []) as $idx => $opt) {
                $optKey = $opt['key'] ?? ($opt['id'] ?? $idx);
                if ((string) $optKey === (string) $selectedKey) {
                    $total += (float) ($opt['price'] ?? 0);
                    break;
                }
            }
        }

        if (!empty($item['crust_key'])) {
            $crust = $dish->crusts->firstWhere('id', $item['crust_key']);
            if ($crust) $total += (float) ($crust->price ?? 0);
        }

        if (!empty($item['bun_key'])) {
            $bun = $dish->buns->firstWhere('id', $item['bun_key']);
            if ($bun) $total += (float) ($bun->price ?? 0);
        }

        if (!empty($item['addon_keys'])) {
            $addonIds = array_map('intval', (array) $item['addon_keys']);
            foreach ($dish->addOns as $addon) {
                if (in_array((int) $addon->id, $addonIds, true)) {
                    $total += (float) ($addon->price ?? 0);
                }
            }
        }

        return (float) $total;
    }

    public function getGrossTotalProperty(): float
    {
        $total = 0;

        foreach ($this->days as $day) {
            foreach ($this->selectedSlots as $slot) {
                $items = $day['slots'][$slot]['items'] ?? [];

                foreach ($items as $item) {
                    $dish = $this->dishes->firstWhere('id', $item['dish_id'] ?? null);
                    if (!$dish) continue;

                    $qty  = max(1, (int) ($item['qty'] ?? 1));
                    $unit = $this->calculateItemUnitPriceOriginal($dish, $item);

                    $total += $unit * $qty;
                }
            }
        }

        return (float) $total;
    }

    public function getItemDiscountTotalProperty(): float
    {
        $discount = $this->grossTotal - $this->planTotal;
        return $discount > 0 ? (float) $discount : 0.0;
    }

    public function getCouponDiscountAmountProperty(): float
    {
        $d = (float) $this->couponDiscount;
        if ($d < 0) $d = 0;

        if ($d > $this->planTotal) $d = $this->planTotal;

        return (float) $d;
    }

    public function getGrandTotalProperty(): float
    {
        $grand = $this->planTotal - $this->couponDiscountAmount;
        return $grand > 0 ? (float) $grand : 0.0;
    }

    // =========================
    // ✅ Toast helper
    // =========================
    private function toastError(string $msg): void
    {
        $this->error(
            title: $msg,
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    // =========================
    // ✅ Checkout validation
    // =========================
    public function validateAndCheckout()
    {
        if (!$this->startDate) {
            $this->toastError('Please select start date');
            return null;
        }

        $selectedSlots = $this->selectedSlots;
        if (empty($selectedSlots)) {
            $this->toastError('Please select meal preference');
            return null;
        }

        $slotLabel = [
            'breakfast' => 'Breakfast',
            'lunch'     => 'Lunch',
            'tiffin'    => 'Tiffin',
            'dinner'    => 'Dinner',
        ];

        foreach ($this->days as $dayIndex => $day) {
            $dayName = $day['name'] ?? ('Day ' . ($dayIndex + 1));

            foreach ($selectedSlots as $slot) {
                $items = $day['slots'][$slot]['items'] ?? [];
                if (count($items) === 0) {
                    $this->toastError("{$dayName} → {$slotLabel[$slot]} is required");
                    return null;
                }
            }
        }

        return $this->goToPlanCheckout();
    }

    // =========================
    // ✅ Variants helpers
    // =========================
    public function normalizeVariantGroups(Dish $dish): array
    {
        $raw = $dish->variations ?? [];

        if (is_string($raw)) {
            $decoded = json_decode($raw, true);
            $raw = (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) ? $decoded : [];
        }

        $groups = [];

        if (is_array($raw)) {
            if (!empty($raw['variants']) && is_array($raw['variants'])) {
                $groups[] = ['name' => 'Variant', 'options' => $raw['variants']];
            } elseif (!empty($raw['options']) && is_array($raw['options'])) {
                $groups[] = ['name' => 'Variant', 'options' => $raw['options']];
            } else {
                foreach ($raw as $g) {
                    if (!empty($g['options']) && is_array($g['options'])) {
                        $groups[] = [
                            'name'    => $g['name'] ?? 'Variation',
                            'options' => $g['options'],
                        ];
                    }
                }
            }
        }

        return $groups;
    }

    public function buildMetaParts(Dish $dish, array $item): array
    {
        $meta = [];

        $variantGroups = $this->normalizeVariantGroups($dish);

        $variantKeys = $item['variant_keys'] ?? [];
        if (empty($variantKeys) && !empty($item['variant_key'])) {
            $variantKeys = [0 => $item['variant_key']];
        }

        foreach ($variantGroups as $gIndex => $group) {
            $gName = $group['name'] ?? 'Variant';
            $selectedKey = $variantKeys[$gIndex] ?? null;
            if ($selectedKey === null || $selectedKey === '') continue;

            foreach (($group['options'] ?? []) as $idx => $opt) {
                $okey  = $opt['key'] ?? ($opt['id'] ?? $idx);
                if ((string) $okey === (string) $selectedKey) {
                    $label = $opt['label'] ?? ($opt['name'] ?? $selectedKey);
                    $meta[] = $gName . ': ' . $label;
                    break;
                }
            }
        }

        if (!empty($item['crust_key'])) {
            $c = $dish->crusts->firstWhere('id', $item['crust_key']);
            if ($c) $meta[] = 'Crust: ' . $c->name;
        }

        if (!empty($item['bun_key'])) {
            $b = $dish->buns->firstWhere('id', $item['bun_key']);
            if ($b) $meta[] = 'Bun: ' . $b->name;
        }

        $addonKeys = $item['addon_keys'] ?? [];
        if (!empty($addonKeys)) {
            $addonKeysInt = array_map('intval', (array) $addonKeys);
            $addonNames = [];

            foreach ($dish->addOns as $a) {
                if (in_array((int) $a->id, $addonKeysInt, true)) {
                    $addonNames[] = $a->name;
                }
            }

            if (!empty($addonNames)) {
                $meta[] = 'Add-ons: ' . implode(', ', $addonNames);
            }
        }

        return $meta;
    }

    public function calculateItemUnitPrice(Dish $dish, array $item): float
    {
        $base  = (float) $dish->price_with_discount; // discounted base
        $total = $base;

        $groups = $this->normalizeVariantGroups($dish);

        $variantKeys = $item['variant_keys'] ?? [];
        if (empty($variantKeys) && !empty($item['variant_key'])) {
            $variantKeys = [0 => $item['variant_key']];
        }

        foreach ($groups as $gIndex => $group) {
            $selectedKey = $variantKeys[$gIndex] ?? null;
            if ($selectedKey === null || $selectedKey === '') continue;

            foreach (($group['options'] ?? []) as $idx => $opt) {
                $optKey = $opt['key'] ?? ($opt['id'] ?? $idx);
                if ((string) $optKey === (string) $selectedKey) {
                    $total += (float) ($opt['price'] ?? 0);
                    break;
                }
            }
        }

        if (!empty($item['crust_key'])) {
            $crust = $dish->crusts->firstWhere('id', $item['crust_key']);
            if ($crust) $total += (float) ($crust->price ?? 0);
        }

        if (!empty($item['bun_key'])) {
            $bun = $dish->buns->firstWhere('id', $item['bun_key']);
            if ($bun) $total += (float) ($bun->price ?? 0);
        }

        if (!empty($item['addon_keys'])) {
            $addonIds = array_map('intval', (array) $item['addon_keys']);
            foreach ($dish->addOns as $addon) {
                if (in_array((int) $addon->id, $addonIds, true)) {
                    $total += (float) ($addon->price ?? 0);
                }
            }
        }

        return (float) $total;
    }

    // =========================
    // ✅ Coupon (optimized, 1 sync function)
    // =========================

    public function applyCoupon(): void
    {
        $this->couponCode = trim((string) $this->couponCode);

        if (!$this->couponCode) {
            $this->toastError('Please enter a coupon code');
            return;
        }

        $this->syncCoupon(true);
    }

    public function removeCoupon(): void
    {
        $this->couponApplied  = false;
        $this->couponDiscount = 0;
        $this->couponData     = null;
        $this->couponCode     = null;

        $this->persistState();
    }

    private function syncCoupon(bool $showToast = false): void
    {
        $code = trim((string) $this->couponCode);

        // if no code -> clear coupon
        if ($code === '') {
            $this->couponApplied  = false;
            $this->couponDiscount = 0;
            $this->couponData     = null;
            return;
        }

        // your collation utf8mb4_general_ci is case-insensitive -> normal where is OK
        $coupon = Coupon::query()
            ->whereRaw('BINARY coupon_code = ?', [$code])
            ->where('status', 'active')
            ->first();

        $fail = function (string $message) use ($showToast) {
            $this->couponApplied  = false;
            $this->couponDiscount = 0;
            $this->couponData     = null;
            $this->persistState();

            if ($showToast) $this->toastError($message);
        };

        if (!$coupon) {
            $fail('Invalid coupon code');
            return;
        }

        $today = Carbon::today();

        if (!empty($coupon->start_date) && $today->lt(Carbon::parse($coupon->start_date))) {
            $fail('Coupon not started yet');
            return;
        }

        if (!empty($coupon->expire_date) && $today->gt(Carbon::parse($coupon->expire_date))) {
            $fail('Coupon expired');
            return;
        }

        $payable = (float) $this->planTotal;
        if ($payable <= 0) {
            $fail('Add items before applying coupon');
            return;
        }

        $min = (float) ($coupon->minimum_purchase ?? 0);
        if ($min > 0 && $payable < $min) {
            $fail('Minimum purchase ৳' . number_format($min, 0) . ' required');
            return;
        }

        $value = (float) $coupon->discount;

        $discount = ($coupon->discount_type === 'percent')
            ? ($payable * $value) / 100
            : $value;

        $discount = min($discount, $payable);
        $discount = max(0, round($discount, 2));

        $this->couponApplied  = true;
        $this->couponDiscount = $discount;

        // keep user's typed code in couponCode, store actual row data in couponData
        $this->couponData = [
            'id'              => $coupon->id,
            'title'           => $coupon->title,
            'coupon_code'     => $coupon->coupon_code,
            'coupon_type'     => $coupon->coupon_type,
            'same_user_limit' => (int) ($coupon->same_user_limit ?? 0),
            'minimum_purchase'=> $min,
            'discount_type'   => $coupon->discount_type,
            'discount'        => (float) $coupon->discount,
        ];

        $this->persistState();

        if ($showToast) {
            $this->success(
                title: 'Coupon applied',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true
            );
        }
    }

    // =========================
    // ✅ Slot workflows
    // =========================

    public function openSlot(int $dayIndex, string $slot): void
    {
        if (!in_array($slot, self::SLOTS, true)) return;
        if (!isset($this->days[$dayIndex])) return;

        $this->tempSelection = [
            'day'    => $dayIndex,
            'slot'   => $slot,
            'dishes' => [],
        ];

        $this->activeDishCategoryId = null;

        $existingItems = $this->days[$dayIndex]['slots'][$slot]['items'] ?? [];

        $existingMap = [];
        foreach ($existingItems as $item) {
            $dishId = (int) ($item['dish_id'] ?? 0);
            if (!$dishId) continue;

            $existingMap[$dishId] = [
                'qty'          => max(1, (int) ($item['qty'] ?? 1)),
                'variant_keys' => $item['variant_keys'] ?? [],
                'variant_key'  => $item['variant_key'] ?? null,
                'crust_key'    => $item['crust_key'] ?? null,
                'bun_key'      => $item['bun_key'] ?? null,
                'addon_keys'   => $item['addon_keys'] ?? [],
            ];
        }

        foreach ($this->dishes as $dish) {
            $id = (int) $dish->id;
            $config = $existingMap[$id] ?? null;

            $variantKeys = $config['variant_keys'] ?? [];
            if (empty($variantKeys) && !empty($config['variant_key'])) {
                $variantKeys = [0 => $config['variant_key']];
            }

            $this->tempSelection['dishes'][$id] = [
                'selected'     => $config !== null,
                'qty'          => $config['qty'] ?? 1,
                'variant_keys' => $variantKeys,
                'variant_key'  => $config['variant_key'] ?? null,
                'crust_key'    => $config['crust_key'] ?? null,
                'bun_key'      => $config['bun_key'] ?? null,
                'addon_keys'   => $config['addon_keys'] ?? [],
            ];
        }

        $this->resetErrorBag();
        $this->dishConfigOpen = false;
        $this->configDishId   = null;
        $this->showModal      = true;
    }

    public function updatedTempSelection($value, $key): void
    {
        if (!str_starts_with($key, 'tempSelection.dishes.')) return;

        $parts  = explode('.', $key);
        $dishId = isset($parts[2]) ? (int) $parts[2] : 0;
        if (!$dishId || !isset($this->tempSelection['dishes'][$dishId])) return;

        if (str_ends_with($key, '.qty')) {
            $qty = (int) ($this->tempSelection['dishes'][$dishId]['qty'] ?? 1);
            if ($qty < 1) $this->tempSelection['dishes'][$dishId]['qty'] = 1;
        }
    }

    public function incrementDishQty(int $dishId): void
    {
        if (!isset($this->tempSelection['dishes'][$dishId])) return;
        $qty = (int) ($this->tempSelection['dishes'][$dishId]['qty'] ?? 1);
        $this->tempSelection['dishes'][$dishId]['qty'] = max(1, $qty + 1);
    }

    public function decrementDishQty(int $dishId): void
    {
        if (!isset($this->tempSelection['dishes'][$dishId])) return;
        $qty = (int) ($this->tempSelection['dishes'][$dishId]['qty'] ?? 1);
        $this->tempSelection['dishes'][$dishId]['qty'] = max(1, $qty - 1);
    }

    public function openDishConfig(int $dishId): void
    {
        if (!isset($this->tempSelection['day'], $this->tempSelection['slot'])) return;

        if (!isset($this->tempSelection['dishes'][$dishId])) {
            $this->tempSelection['dishes'][$dishId] = [
                'selected'     => false,
                'qty'          => 1,
                'variant_keys' => [],
                'variant_key'  => null,
                'crust_key'    => null,
                'bun_key'      => null,
                'addon_keys'   => [],
            ];
        }

        if (
            empty($this->tempSelection['dishes'][$dishId]['variant_keys']) &&
            !empty($this->tempSelection['dishes'][$dishId]['variant_key'])
        ) {
            $this->tempSelection['dishes'][$dishId]['variant_keys'] = [0 => $this->tempSelection['dishes'][$dishId]['variant_key']];
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

    public function applyDishConfig(int $dishId): void
    {
        if (!isset($this->tempSelection['dishes'][$dishId])) return;

        $dish = $this->dishes->firstWhere('id', $dishId);
        if (!$dish) return;

        $entry = &$this->tempSelection['dishes'][$dishId];

        $this->resetErrorBag();

        $variantGroups = $this->normalizeVariantGroups($dish);
        $hasCrusts = $dish->crusts && $dish->crusts->count() > 0;
        $hasBuns   = $dish->buns   && $dish->buns->count()   > 0;

        $hasErrors = false;

        $variantKeys = $entry['variant_keys'] ?? [];
        if (empty($variantKeys) && !empty($entry['variant_key'])) {
            $variantKeys = [0 => $entry['variant_key']];
            $entry['variant_keys'] = $variantKeys;
        }

        foreach ($variantGroups as $gIndex => $group) {
            if (empty($group['options'])) continue;

            $selectedKey = $variantKeys[$gIndex] ?? null;
            if ($selectedKey === null || $selectedKey === '') {
                $this->addError('config.variant.' . $gIndex, 'Variant required.');
                $hasErrors = true;
            }
        }

        $crustKey = $entry['crust_key'] ?? null;
        if ($hasCrusts && ($crustKey === null || $crustKey === '')) {
            $this->addError('config.crust', 'Crust required.');
            $hasErrors = true;
        }

        $bunKey = $entry['bun_key'] ?? null;
        if ($hasBuns && ($bunKey === null || $bunKey === '')) {
            $this->addError('config.bun', 'Bun required.');
            $hasErrors = true;
        }

        if ($hasErrors) return;

        $qty = (int) ($entry['qty'] ?? 1);
        if ($qty < 1) $entry['qty'] = 1;

        $entry['selected'] = true;

        $this->resetErrorBag();
        $this->dishConfigOpen = false;
        $this->configDishId   = null;
    }

    public function deselectDish(int $dishId): void
    {
        if (!isset($this->tempSelection['dishes'][$dishId])) return;
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

        if ($day === null || $slot === null || !isset($this->days[$day])) return;

        $items = [];

        foreach ($this->tempSelection['dishes'] as $dishId => $data) {
            if (empty($data['selected'])) continue;

            $qty = max(1, (int) ($data['qty'] ?? 1));

            $variantKeys = $data['variant_keys'] ?? [];
            if (empty($variantKeys) && !empty($data['variant_key'])) {
                $variantKeys = [0 => $data['variant_key']];
            }

            $items[] = [
                'dish_id'      => (int) $dishId,
                'qty'          => $qty,
                'variant_keys' => $variantKeys,
                'variant_key'  => $data['variant_key'] ?? null,
                'crust_key'    => $data['crust_key'] ?? null,
                'bun_key'      => $data['bun_key'] ?? null,
                'addon_keys'   => $data['addon_keys'] ?? [],
            ];
        }

        $this->days[$day]['slots'][$slot]['items'] = $items;

        $this->showModal      = false;
        $this->dishConfigOpen = false;
        $this->configDishId   = null;
        $this->resetErrorBag();

        $this->syncCoupon(false);
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
        if (!isset($this->days[$dayIndex]['slots'][$slot]['items'][$itemIndex])) return;

        unset($this->days[$dayIndex]['slots'][$slot]['items'][$itemIndex]);
        $this->days[$dayIndex]['slots'][$slot]['items'] =
            array_values($this->days[$dayIndex]['slots'][$slot]['items']);

        $this->syncCoupon(false);
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

        // reset coupon
        $this->couponCode = null;
        $this->couponApplied = false;
        $this->couponDiscount = 0;
        $this->couponData = null;

        $this->initDays();

        session()->forget(self::SESSION_KEY);
        $this->persistState();
    }

    public function copyWeekOneToAll(): void
    {
        if ($this->planType !== 'monthly') return;

        $daysCount = count($this->days);
        if ($daysCount < 7) return;

        for ($week = 1; $week < 4; $week++) {
            $destStart = $week * 7;

            for ($offset = 0; $offset < 7; $offset++) {
                $srcIndex  = $offset;
                $destIndex = $destStart + $offset;

                if ($destIndex >= $daysCount) break;

                $this->days[$destIndex]['slots'] = $this->days[$srcIndex]['slots'];
            }
        }

        $this->syncCoupon(false);
        $this->persistState();
    }

    public function goToPlanCheckout()
    {
        // always keep coupon correct before leaving
        $this->syncCoupon(false);
        $this->persistState();

        session([
            'meal_plan_state' => [
                'planType'   => $this->planType,
                'startDate'  => $this->startDate,
                'mealPrefs'  => $this->mealPrefs,
                'days'       => $this->days,

                // coupon snapshot
                'couponCode'     => $this->couponCode,
                'couponApplied'  => $this->couponApplied,
                'couponDiscount' => $this->couponDiscountAmount,
                'couponData'     => $this->couponData,

                // totals snapshot
                'grossTotal'        => $this->grossTotal,
                'itemDiscountTotal' => $this->itemDiscountTotal,
                'planTotal'         => $this->planTotal,
                'grandTotal'        => $this->grandTotal,
            ],
        ]);

        return redirect()->route('plans.checkout');
    }

    public function render()
    {
        return view('livewire.frontend.meal-plan.meal-plan');
    }
}
