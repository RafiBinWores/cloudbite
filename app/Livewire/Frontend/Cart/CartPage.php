<?php

namespace App\Livewire\Frontend\Cart;

use App\Models\Cart;
use App\Repositories\CartRepository;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class CartPage extends Component
{
    use WithTcToast;

    public ?Cart $cart = null;

    public string $coupon_code = '';
    public ?string $coupon_feedback = null;

    public function mount(CartRepository $repo): void
    {
        $this->loadCart($repo);
        $this->coupon_code = (string) data_get($this->cart?->meta, 'coupon.code', '');
    }

    #[On('cart-updated')]
    public function refreshCart(CartRepository $repo): void
    {
        $this->loadCart($repo);
        $this->dispatch('cart-updated');
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

        if ($res['ok']) {
            $this->coupon_code = (string) data_get($this->cart?->meta, 'coupon.code', $this->coupon_code);
        }
    }

    public function removeCoupon(CartRepository $repo): void
    {
        $repo->removeCoupon();
        $this->coupon_code = '';

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
        $this->success(title: 'Quantity updated.', position: 'top-right', showProgress: true, showCloseIcon: true);
        $this->dispatch('cart-updated');
    }

    public function decrementQty(CartRepository $repo, int $itemId): void
    {
        $repo->bumpItemQty($itemId, -1);
        $this->loadCart($repo);
        $this->success(title: 'Quantity updated.', position: 'top-right', showProgress: true, showCloseIcon: true);
        $this->dispatch('cart-updated');
    }

    public function changeQty(CartRepository $repo, int $itemId, int $qty): void
    {
        $repo->setItemQty($itemId, max(1, min(99, $qty)));
        $this->loadCart($repo);
        $this->success(title: 'Quantity updated.', position: 'top-right', showProgress: true, showCloseIcon: true);
        $this->dispatch('cart-updated');
    }

    public function removeItem(CartRepository $repo, int $itemId): void
    {
        $repo->removeItem($itemId);
        $this->loadCart($repo);
        $this->success(title: 'Item removed.', position: 'top-right', showProgress: true, showCloseIcon: true);
        $this->dispatch('cart-updated');
    }

    public function clearCart(CartRepository $repo): void
    {
        $repo->clear();
        $this->loadCart($repo);
        $this->success(title: 'Cart cleared.', position: 'top-right', showProgress: true, showCloseIcon: true);
        $this->dispatch('cart-updated');
    }

    /** Item price WITHOUT discount (variation-aware) */
    public function getProductPriceSubtotalProperty(): float
    {
        if (!$this->cart) return 0.0;

        $sum = 0.0;
        foreach ($this->cart->items as $item) {
            $baseOriginal = (float) data_get(
                $item->meta,
                'base_original',
                data_get($item->meta, 'base', $item->unit_price)
            );

            $sum += $baseOriginal * (int) $item->qty;
        }
        return round($sum, 2);
    }

    /** Only product discount amount */
    public function getProductDiscountSubtotalProperty(): float
    {
        if (!$this->cart) return 0.0;

        $sum = 0.0;
        foreach ($this->cart->items as $item) {
            $baseOriginal = (float) data_get(
                $item->meta,
                'base_original',
                data_get($item->meta, 'base', $item->unit_price)
            );

            $baseAfterDiscount = (float) data_get(
                $item->meta,
                'base_after_discount',
                data_get($item->meta, 'display_price_with_discount', $baseOriginal)
            );

            $perUnitDiscount = max(0.0, $baseOriginal - $baseAfterDiscount);
            $sum += $perUnitDiscount * (int) $item->qty;
        }
        return round($sum, 2);
    }

    /** Price after discount */
    public function getProductAfterDiscountSubtotalProperty(): float
    {
        return round($this->product_price_subtotal - $this->product_discount_subtotal, 2);
    }

    /** Add-ons bucket = crust extra + bun extra + add-on extras */
    public function getAddonsSubtotalProperty(): float
    {
        if (!$this->cart) return 0.0;

        $sum = 0.0;
        foreach ($this->cart->items as $item) {
            $crustExtra  = (float) data_get($item->meta, 'crust_extra', 0);
            $bunExtra    = (float) data_get($item->meta, 'bun_extra', 0);
            $addonsExtra = (float) data_get($item->meta, 'addons_extra', 0);

            $sum += ($crustExtra + $bunExtra + $addonsExtra) * (int) $item->qty;
        }
        return round($sum, 2);
    }

    /** Subtotal after product discount + addons */
    public function getSubTotalAfterDiscountProperty(): float
    {
        return round($this->product_after_discount_subtotal + $this->addons_subtotal, 2);
    }

    /** Coupon discount at cart level */
    public function getCouponDiscountTotalProperty(): float
    {
        return (float) ($this->cart->discount_total ?? 0);
    }

    /** Tax at cart level */
    public function getTaxTotalProperty(): float
    {
        return (float) ($this->cart->tax_total ?? 0);
    }

    /** Grand total = base after discount + addons + tax - coupon */
    public function getGrandTotalProperty(): float
    {
        return round(
            $this->sub_total_after_discount
                + $this->tax_total
                - $this->coupon_discount_total,
            2
        );
    }

    public function render()
    {
        return view('livewire.frontend.cart.cart-page');
    }
}
