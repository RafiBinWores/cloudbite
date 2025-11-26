<?php

namespace App\Livewire\Frontend\Cart;

use App\Repositories\CartRepository;
use Livewire\Attributes\On;
use Livewire\Component;

class CartBadge extends Component
{
    public int $count = 0;

    public function mount(CartRepository $repo, ?int $initial = null): void
    {
        // If you ever pass :initial from Blade, use that; otherwise compute
        $this->count = $initial ?? $this->getCurrentCount($repo);
    }

    #[On('cart-updated')]
    public function refresh(CartRepository $repo): void
    {
        $this->count = $this->getCurrentCount($repo);
    }

    protected function getCurrentCount(CartRepository $repo): int
    {
        $cart = $repo->loadCartNullable(['items']);

        // total quantity (change to ->count() for line count)
        return $cart ? (int) $cart->items->sum('qty') : 0;
    }

    public function render()
    {
        return view('livewire.frontend.cart.cart-badge');
    }
}
