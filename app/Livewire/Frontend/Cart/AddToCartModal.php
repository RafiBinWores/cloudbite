<?php

namespace App\Livewire\Frontend\Cart;

use App\Models\Dish;
use App\Repositories\CartRepository;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class AddToCartModal extends Component
{
    use WithTcToast;

    public ?Dish $dish = null;

    public int $qty = 1;
    public ?int $crust_id = null;
    public ?int $bun_id = null;

    public array $addon_ids = [];   // selected addon IDs
    public array $addon_qty = [];   // [addon_id => qty]

    // [groupIndex => optionIndex]
    public array $variation_selection = [];

    public bool $open = false;
    public bool $crust_required = true;
    public bool $bun_required = true;

    public bool $isFavorited = false;

    #[On('open-add-to-cart')]
    public function open(int $dishId): void
    {
        $this->resetValidation();

        $this->qty        = 1;
        $this->crust_id   = null;
        $this->bun_id     = null;
        $this->addon_ids  = [];
        $this->addon_qty  = [];

        $this->dish = Dish::with(['crusts', 'buns', 'addOns'])->findOrFail($dishId);

        // default select first option of each variation group
        $this->variation_selection = [];
        foreach ((array)($this->dish->variations ?? []) as $gIndex => $group) {
            if (!empty($group['options'])) {
                $this->variation_selection[$gIndex] = 0;
            }
        }

        // init addon qty to 1 for each addon (used when selected)
        foreach ($this->dish->addOns as $addon) {
            $this->addon_qty[$addon->id] = $this->addon_qty[$addon->id] ?? 1;
        }

        $this->open = true;

        $this->isFavorited = Auth::check() && Auth::user()->hasFavorited($this->dish->id);
    }

    public function toggleFavorite(): void
    {
        if (!Auth::check()) {
            $this->dispatch('open-login');
            $this->warning(title: 'Please log in to save favorites.', position: 'top-right', showProgress: true);
            return;
        }

        $user   = Auth::user();
        $dishId = $this->dish?->id;

        if (!$dishId) {
            $this->addError('dish', 'Dish not loaded.');
            return;
        }

        if ($user->favorites()->where('dish_id', $dishId)->exists()) {
            $user->favorites()->detach($dishId);
            $this->isFavorited = false;
            $this->info(title: 'Removed from Favorites', position: 'top-right', showProgress: true, showCloseIcon: true);
        } else {
            Dish::query()->findOrFail($dishId);
            $user->favorites()->attach($dishId);
            $this->isFavorited = true;
            $this->success(title: 'Added to Favorites', position: 'top-right', showProgress: true, showCloseIcon: true);
        }
    }

    public function incrementQty(): void
    {
        $this->qty = min(99, $this->qty + 1);
        $this->dispatch('cart-updated');
    }

    public function decrementQty(): void
    {
        $this->qty = max(1, $this->qty - 1);
        $this->dispatch('cart-updated');
    }

    /** ================= Price breakdown ================= */

    public function getBaseItemOriginalPriceProperty(): float
    {
        if (!$this->dish) return 0.0;
        return round((float)($this->dish->price ?? 0), 2);
    }

    public function getBaseItemPriceProperty(): float
    {
        if (!$this->dish) return 0.0;

        $base = (float)($this->dish->price ?? 0);

        if ($this->dish->discount_type && (float)$this->dish->discount > 0) {
            if ($this->dish->discount_type === 'percent') {
                $base = $base * (1 - ((float)$this->dish->discount / 100));
            } elseif ($this->dish->discount_type === 'amount') {
                $base = max(0, $base - (float)$this->dish->discount);
            }
        }

        return round($base, 2);
    }

    public function getHasBaseDiscountProperty(): bool
    {
        return $this->base_item_price < $this->base_item_original_price;
    }

    public function getVariationExtraProperty(): float
    {
        if (!$this->dish) return 0.0;

        $extra = 0.0;
        $vars  = (array)($this->dish->variations ?? []);

        foreach ($vars as $gIndex => $group) {
            $optIndex = $this->variation_selection[$gIndex] ?? null;
            if ($optIndex === null) continue;

            $opt = $group['options'][$optIndex] ?? null;
            if ($opt && isset($opt['price'])) {
                $extra += (float)$opt['price'];
            }
        }

        return round($extra, 2);
    }

    public function getCrustExtraProperty(): float
    {
        if (!$this->dish || !$this->crust_id) return 0.0;
        $c = $this->dish->crusts->firstWhere('id', $this->crust_id);
        return round((float)($c?->price ?? 0), 2);
    }

    public function getBunExtraProperty(): float
    {
        if (!$this->dish || !$this->bun_id) return 0.0;
        $b = $this->dish->buns->firstWhere('id', $this->bun_id);
        return round((float)($b?->price ?? 0), 2);
    }

    /** ✅ Addons extra with per-addon qty */
    public function getAddonsExtraProperty(): float
    {
        if (!$this->dish) return 0.0;

        $sum = 0.0;

        foreach ($this->dish->addOns as $addon) {
            if (in_array($addon->id, $this->addon_ids)) {
                $qtyPerUnit = max(1, (int)($this->addon_qty[$addon->id] ?? 1));
                $sum += (float)($addon->price ?? 0) * $qtyPerUnit;
            }
        }

        return round($sum, 2);
    }

    public function getExtrasPerUnitProperty(): float
    {
        return round(
            $this->variation_extra
            + $this->crust_extra
            + $this->bun_extra
            + $this->addons_extra,
            2
        );
    }

    public function getUnitTotalProperty(): float
    {
        return round($this->base_item_price + $this->extras_per_unit, 2);
    }

    public function getPreviewTotalProperty(): float
    {
        return round($this->unit_total * $this->qty, 2);
    }

    /** Qty control for add-ons */
    public function incrementAddon(int $addonId): void
    {
        if (!isset($this->addon_qty[$addonId])) {
            $this->addon_qty[$addonId] = 1;
        }

        // If user taps +, ensure addon is selected
        if (!in_array($addonId, $this->addon_ids)) {
            $this->addon_ids[] = $addonId;
        }

        $this->addon_qty[$addonId] = min(99, $this->addon_qty[$addonId] + 1);
    }

    public function decrementAddon(int $addonId): void
    {
        if (!isset($this->addon_qty[$addonId])) {
            $this->addon_qty[$addonId] = 1;
        }

        if ($this->addon_qty[$addonId] > 1) {
            $this->addon_qty[$addonId]--;
        } else {
            // going below 1 => unselect addon
            $this->addon_qty[$addonId] = 1;

            $this->addon_ids = collect($this->addon_ids)
                ->reject(fn ($id) => $id == $addonId)
                ->values()
                ->all();
        }
    }

    /** Add to cart */
    public function addToCart(CartRepository $repo): void
    {
        $this->validate([
            'qty'         => 'required|integer|min:1|max:99',
            'crust_id'    => 'nullable|integer',
            'bun_id'      => 'nullable|integer',
            'addon_ids'   => 'nullable|array',
            'addon_ids.*' => 'integer',
            'addon_qty'   => 'nullable|array',
            'addon_qty.*' => 'nullable|integer|min:1|max:99',
        ]);

        // require variation for each group that has options
        $variationRules = [];
        foreach ((array)($this->dish->variations ?? []) as $gIndex => $group) {
            if (!empty($group['options'])) {
                $variationRules["variation_selection.$gIndex"] = 'required|integer|min:0';
            }
        }
        if ($variationRules) {
            $this->validate($variationRules, ['required' => 'Please select a variation option.']);
        }

        $rules = [
            'crust_id' => ($this->crust_required && $this->dish->crusts->count() > 0) ? 'required|integer' : 'nullable',
            'bun_id'   => ($this->bun_required   && $this->dish->buns->count()   > 0) ? 'required|integer' : 'nullable',
        ];

        $messages = [
            'crust_id.required' => 'Please select one crust option.',
            'bun_id.required'   => 'Please select one bun option.',
        ];

        $this->validate($rules, $messages);

        if (!$this->dish) {
            $this->addError('dish', 'Dish not loaded.');
            return;
        }

        $repo->addItem(
            dishId: $this->dish->id,
            qty: $this->qty,
            crustId: $this->crust_id,
            bunId: $this->bun_id,
            addonIds: $this->addon_ids,
            variationsSelected: $this->variation_selection,
            addonQty: $this->addon_qty,
        );

        $this->success(title: 'Added To Cart.', position: 'top-right', showProgress: true, showCloseIcon: true);

        $this->dispatch('cart-updated');
        $this->open = false;

        $this->reset([
            'dish',
            'qty',
            'crust_id',
            'bun_id',
            'addon_ids',
            'addon_qty',
            'variation_selection',
        ]);
    }

    public function render()
    {
        return view('livewire.frontend.cart.add-to-cart-modal');
    }
}
