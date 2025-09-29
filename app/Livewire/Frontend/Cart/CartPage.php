<?php

namespace App\Livewire\Frontend\Cart;

use App\Models\Cart;
use App\Repositories\CartRepository;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Livewire\Attributes\On;
use Livewire\Component;

class CartPage extends Component
{
    use WithTcToast;

    public ?Cart $cart = null;

    public string $coupon_code = '';
    public ?string $coupon_feedback = null;

    // public function mount(CartRepository $repo): void
    // {
    //     $this->loadCart($repo);
    // }

    // #[On('cart-updated')]
    // public function refreshCart(CartRepository $repo): void
    // {
    //     $this->loadCart($repo);
    // }

    // protected function loadCart(CartRepository $repo): void
    // {
    //     $this->cart = $repo->loadCart(['items.dish', 'items.crust', 'items.bun']);
    // }

    public function mount(CartRepository $repo): void
    {
        $this->loadCart($repo);
        $this->coupon_code = (string) data_get($this->cart?->meta, 'coupon.code', '');
    }

    #[On('cart-updated')]
    public function refreshCart(CartRepository $repo): void
    {
        $this->loadCart($repo);
    }

    protected function loadCart(CartRepository $repo): void
    {
        $this->cart = $repo->loadCart(['items.dish', 'items.crust', 'items.bun']);
    }

    public function applyCoupon(CartRepository $repo): void
    {
        $this->validate([
            'coupon_code' => 'required|string|max:50',
        ]);

        $res = $repo->applyCoupon($this->coupon_code);
        $this->coupon_feedback = $res['message'];
        $this->loadCart($repo);

        // Keep input synced with applied one or clear on failure
        if (!$res['ok']) {
            // do not clear so user can fix typo
        } else {
            $this->coupon_code = (string) data_get($this->cart?->meta, 'coupon.code', $this->coupon_code);
        }
    }

    public function removeCoupon(CartRepository $repo): void
    {
        $repo->removeCoupon();
        $this->coupon_code = '';
        // $this->coupon_feedback = 'Coupon removed.';
        $this->success(
            title: 'Coupon removed.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
        $this->loadCart($repo);
    }

    public function incrementQty(CartRepository $repo, int $itemId): void
    {
        $repo->bumpItemQty($itemId, +1);
        $this->loadCart($repo);
        $this->success(
            title: 'Quantity updated.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    public function decrementQty(CartRepository $repo, int $itemId): void
    {
        $repo->bumpItemQty($itemId, -1);
        $this->loadCart($repo);
        $this->success(
            title: 'Quantity updated.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    public function changeQty(CartRepository $repo, int $itemId, int $qty): void
    {
        $repo->setItemQty($itemId, max(1, min(99, $qty)));
        $this->loadCart($repo);
        $this->success(
            title: 'Quantity updated.',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    public function removeItem(CartRepository $repo, int $itemId): void
    {
        $repo->removeItem($itemId);
        $this->loadCart($repo);
        $this->dispatch('toast', type: 'info', message: 'Item removed');
    }

    public function clearCart(CartRepository $repo): void
    {
        $repo->clear();
        $this->loadCart($repo);
        $this->dispatch('toast', type: 'info', message: 'Cart cleared');
    }

    /** ================== Summary breakdown ================== */

    /** Sum of base product prices (discounted unit base), excludes crust/add-ons */
    public function getProductPriceSubtotalProperty(): float
    {
        if (!$this->cart) return 0.0;

        $sum = 0.0;
        foreach ($this->cart->items as $item) {
            $crustExtra  = (float) data_get($item->meta, 'crust_extra', 0);
            $addonsExtra = (float) data_get($item->meta, 'addons_extra', 0);

            // Prefer saved 'base' (discounted unit base). If missing, approximate:
            $base = data_get($item->meta, 'base');
            if ($base === null) {
                $base = (float) $item->unit_price - $crustExtra - $addonsExtra; // bun_extra is 0 for now
            }

            $sum += ((float) $base) * (int) $item->qty;
        }
        return round($sum, 2);
    }

    /** Sum of per-product discounts (original dish price - discounted base) */
    public function getProductDiscountSubtotalProperty(): float
    {
        if (!$this->cart) return 0.0;

        $sum = 0.0;
        foreach ($this->cart->items as $item) {
            $base = (float) (data_get($item->meta, 'base', $item->unit_price));
            // Original/current list price from the Dish model
            $original = (float) ($item->dish->price ?? $base);
            $discountPerUnit = max(0.0, $original - $base);
            $sum += $discountPerUnit * (int) $item->qty;
        }
        return round($sum, 2);
    }

    /** Add-ons bucket = crust extra + add-on extras (all options) */
    public function getAddonsSubtotalProperty(): float
    {
        if (!$this->cart) return 0.0;

        $sum = 0.0;
        foreach ($this->cart->items as $item) {
            $crustExtra  = (float) data_get($item->meta, 'crust_extra', 0);
            $addonsExtra = (float) data_get($item->meta, 'addons_extra', 0);
            $sum += ($crustExtra + $addonsExtra) * (int) $item->qty;
        }
        return round($sum, 2);
    }

    /** Coupon discount at cart level (use carts.discount_total) */
    public function getCouponDiscountTotalProperty(): float
    {
        return (float) ($this->cart->discount_total ?? 0);
    }

    /** Tax at cart level (use carts.tax_total) */
    public function getTaxTotalProperty(): float
    {
        return (float) ($this->cart->tax_total ?? 0);
    }

    /** Grand total = product + addons + tax - product discount - coupon discount */
    public function getGrandTotalProperty(): float
    {
        return round(
            $this->product_price_subtotal
                + $this->addons_subtotal
                + $this->tax_total
                - $this->product_discount_subtotal
                - $this->coupon_discount_total,
            2
        );
    }

    public function render()
    {
        return view('livewire.frontend.cart.cart-page')
            ->layout('components.layouts.frontend', ['title' => 'Home | Cart']);
    }
}
