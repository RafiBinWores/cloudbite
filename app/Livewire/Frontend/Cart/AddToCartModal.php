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
    public array $addon_ids = [];

    // ✅ Variations selection: [groupIndex => optionIndex]
    public array $variation_selection = [];

    public bool $open = false;
    public bool $crust_required = true;
    public bool $bun_required = true;

    public bool $isFavorited = false;

    #[On('open-add-to-cart')]
    public function open(int $dishId): void
    {
        $this->resetValidation();

        $this->qty       = 1;
        $this->crust_id  = null;
        $this->bun_id    = null;
        $this->addon_ids = [];

        $this->dish = Dish::with(['crusts', 'buns', 'addOns'])->findOrFail($dishId);

        // ✅ default select first option of each variation group
        $this->variation_selection = [];
        foreach ((array)($this->dish->variations ?? []) as $gIndex => $group) {
            if (!empty($group['options'])) {
                $this->variation_selection[$gIndex] = 0;
            }
        }

        $this->open = true;

        $this->isFavorited = Auth::check() && Auth::user()->hasFavorited($this->dish->id);
    }

    public function toggleFavorite(): void
    {
        if (!Auth::check()) {
            $this->dispatch('open-login');
            $this->warning(
                title: 'Please log in to save favorites.',
                position: 'top-right',
                showProgress: true
            );
            return;
        }

        $user = Auth::user();
        $dishId = $this->dish?->id;

        if (!$dishId) {
            $this->addError('dish', 'Dish not loaded.');
            return;
        }

        if ($user->favorites()->where('dish_id', $dishId)->exists()) {
            $user->favorites()->detach($dishId);
            $this->isFavorited = false;
            $this->info(
                title: 'Removed from Favorites',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        } else {
            Dish::query()->findOrFail($dishId);
            $user->favorites()->attach($dishId);
            $this->isFavorited = true;
            $this->success(
                title: 'Added to Favorites',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        }
    }

    public function incrementQty(): void
    {
        $this->qty = min(99, $this->qty + 1);
    }

    public function decrementQty(): void
    {
        $this->qty = max(1, $this->qty - 1);
    }

    /** ================= Live preview helpers ================= */

    // ✅ Base price is variation price if selected, else dish price_with_discount or price
    public function getBasePriceProperty(): float
    {
        if (!$this->dish) return 0.0;

        $base = (float) ($this->dish->price_with_discount ?? $this->dish->price ?? 0);

        $vars = (array)($this->dish->variations ?? []);
        if (!$vars) return round($base, 2);

        foreach ($vars as $gIndex => $group) {
            $optIndex = $this->variation_selection[$gIndex] ?? null;
            if ($optIndex === null) continue;

            $opt = $group['options'][$optIndex] ?? null;
            if ($opt && isset($opt['price'])) {
                $base = (float)$opt['price'];
            }
        }

        return round($base, 2);
    }

    public function getCrustExtraProperty(): float
    {
        if (!$this->dish || !$this->crust_id) return 0.0;
        $c = $this->dish->crusts->firstWhere('id', $this->crust_id);
        return round((float)($c?->price ?? 0), 2);
    }

    public function getBunExtraProperty(): float
    {
        return 0.0;
    }

    public function getAddonsExtraProperty(): float
    {
        if (!$this->dish) return 0.0;
        $selected = $this->dish->addOns->whereIn('id', $this->addon_ids);
        $sum = 0.0;
        foreach ($selected as $a) {
            $sum += (float)($a->price ?? 0);
        }
        return round($sum, 2);
    }

    public function getUnitTotalProperty(): float
    {
        return round($this->base_price + $this->crust_extra + $this->bun_extra + $this->addons_extra, 2);
    }

    public function getPreviewTotalProperty(): float
    {
        return round($this->unit_total * $this->qty, 2);
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
        ]);

        // ✅ require variation for every group that has options
        $variationRules = [];
        foreach ((array)($this->dish->variations ?? []) as $gIndex => $group) {
            if (!empty($group['options'])) {
                $variationRules["variation_selection.$gIndex"] = 'required|integer|min:0';
            }
        }
        if ($variationRules) {
            $this->validate($variationRules, [
                'required' => 'Please select a variation option.'
            ]);
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
            variationsSelected: $this->variation_selection // ✅ send selected variation
        );

        $this->success(
            title: 'Added To Cart.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );

        $this->dispatch('cart-updated');
        $this->open = false;

        $this->reset(['dish', 'qty', 'crust_id', 'bun_id', 'addon_ids', 'variation_selection']);
    }

    public function render()
    {
        return view('livewire.frontend.cart.add-to-cart-modal');
    }
}
