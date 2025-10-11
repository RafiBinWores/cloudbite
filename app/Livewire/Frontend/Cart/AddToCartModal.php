<?php

namespace App\Livewire\Frontend\Cart;

use App\Models\Dish;
use App\Repositories\CartRepository;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class AddToCartModal extends Component
{
    use WithTcToast;

    public ?Dish $dish = null;

    public int $qty = 1;
    public ?int $crust_id = null;     // single
    public ?int $bun_id = null;       // optional, free for now
    public array $addon_ids = [];     // multiple

    public bool $open = false;
    public bool $crust_required = true; // force a crust if dish has any
    public bool $bun_required = true; // force a bun if dish has any

    #[On('open-add-to-cart')] // emit('open-add-to-cart', dishId)
    public function open(int $dishId): void
    {
        $this->resetValidation();

        $this->qty       = 1;
        $this->crust_id  = null;
        $this->bun_id    = null;
        $this->addon_ids = [];

        $this->dish = Dish::with(['crusts', 'buns', 'addOns'])->findOrFail($dishId);
        $this->open = true;
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

    public function getBasePriceProperty(): float
    {
        if (!$this->dish) return 0.0;
        // If you have an accessor (price_with_discount), it'll be used; otherwise fallback to price.
        return (float) ($this->dish->price_with_discount ?? $this->dish->price ?? 0);
    }

    // Crust extra from Crust model's own price (NOT pivot)
    public function getCrustExtraProperty(): float
    {
        if (!$this->dish || !$this->crust_id) return 0.0;
        $c = $this->dish->crusts->firstWhere('id', $this->crust_id);
        return round((float)($c?->price ?? 0), 2);
    }

    // Bun extra (free for now)
    public function getBunExtraProperty(): float
    {
        return 0.0;
    }

    // Add-ons total from AddOn model's own price (NOT pivot)
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
            addonIds: $this->addon_ids
        );

        $this->success(
            title: 'Added To Cart.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
        $this->dispatch('cart-updated');
        $this->open = false;

        $this->reset(['dish', 'qty', 'crust_id', 'bun_id', 'addon_ids']);
    }

    public function render()
    {
        return view('livewire.frontend.cart.add-to-cart-modal');
    }
}
