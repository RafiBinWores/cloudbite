<?php

namespace App\Repositories;

use App\Models\{Cart, CartItem, Dish};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Models\{Coupon, CouponUsage};
use Carbon\Carbon;

class CartRepository
{
    public function forCurrentUser(): Cart
    {
        $query = Cart::query();

        if (Auth::check()) {
            if ($cart = $query->where('user_id', Auth::id())->first()) {
                return $cart;
            }
        }

        $sessionId = session()->getId();

        return $query->where('session_id', $sessionId)->first()
            ?? Cart::create(['user_id' => Auth::id(), 'session_id' => $sessionId]);
    }

    /**
     * Add/update a cart line with same selections.
     * Prices are taken from MODEL columns (no pivot extras).
     */
    public function addItem(
        int $dishId,
        int $qty,
        ?int $crustId = null,
        ?int $bunId = null,
        array $addonIds = []
    ): Cart {
        return DB::transaction(function () use ($dishId, $qty, $crustId, $bunId, $addonIds) {
            $cart = $this->forCurrentUser();

            $dish = Dish::with(['crusts', 'buns', 'addOns'])->findOrFail($dishId);

            $base = (float) ($dish->price_with_discount ?? $dish->price ?? 0);

            $crustExtra = 0.0;
            if ($crustId) {
                $c = $dish->crusts->firstWhere('id', $crustId);
                $crustExtra = (float) ($c?->price ?? 0);
            }

            $bunExtra = 0.0; // if buns become paid later, read $b->price

            // normalize add-ons
            $addonIdsSorted = array_values(array_unique(array_map('intval', $addonIds)));
            sort($addonIdsSorted);

            $addonsExtra = 0.0;
            if (!empty($addonIdsSorted)) {
                $selected = $dish->addOns->whereIn('id', $addonIdsSorted);
                foreach ($selected as $a) {
                    $addonsExtra += (float) ($a->price ?? 0);
                }
            }

            $unit = round($base + $crustExtra + $bunExtra + $addonsExtra, 2);

            // Merge line if exact same configuration exists (compare normalized JSON)
            $existing = $cart->items()
                ->where('dish_id', $dishId)
                ->where('crust_id', $crustId)
                ->where('bun_id', $bunId)
                ->where('addon_ids', json_encode($addonIdsSorted)) // relies on consistent encoding/casts
                ->first();

            if ($existing) {
                $existing->qty += $qty;
                $existing->unit_price = $unit;
                $existing->line_total = $existing->qty * $unit;
                $existing->meta = [
                    'base'         => $base,
                    'crust_extra'  => $crustExtra,
                    'bun_extra'    => $bunExtra,
                    'addons_extra' => $addonsExtra,
                ];
                $existing->save();
            } else {
                $cart->items()->create([
                    'dish_id'    => $dishId,
                    'qty'        => $qty,
                    'crust_id'   => $crustId,
                    'bun_id'     => $bunId,
                    'addon_ids'  => $addonIdsSorted,   // stored as JSON array (casts)
                    'unit_price' => $unit,
                    'line_total' => $unit * $qty,
                    'meta'       => [
                        'base'         => $base,
                        'crust_extra'  => $crustExtra,
                        'bun_extra'    => $bunExtra,
                        'addons_extra' => $addonsExtra,
                    ],
                ]);
            }

            // >>> IMPORTANT: persist totals (subtotal, tax from dish->vat, grand_total)
            $cart->refreshTotals();

            // Return fresh with relations for UI
            return $cart->fresh(['items.dish', 'items.crust', 'items.bun']);
        });
    }

    public function loadCart(array $with = []): Cart
    {
        $cart = $this->forCurrentUser();
        if (!empty($with)) {
            $cart->load($with);
        }
        return $cart;
    }

    /** +/- delta on qty (clamped 1..99) */
    public function bumpItemQty(int $itemId, int $delta): void
    {
        $cart = $this->forCurrentUser();
        $item = $cart->items()->where('id', $itemId)->firstOrFail();

        $item->qty = max(1, min(99, $item->qty + $delta));
        $item->line_total = $item->qty * $item->unit_price;
        $item->save();

        $cart->refreshTotals();
    }

    /** Set qty directly (clamped 1..99) */
    public function setItemQty(int $itemId, int $qty): void
    {
        $cart = $this->forCurrentUser();
        $item = $cart->items()->where('id', $itemId)->firstOrFail();

        $item->qty = max(1, min(99, $qty));
        $item->line_total = $item->qty * $item->unit_price;
        $item->save();

        $cart->refreshTotals();
    }

    public function removeItem(int $itemId): void
    {
        $cart = $this->forCurrentUser();
        $item = $cart->items()->where('id', $itemId)->firstOrFail();
        $item->delete();

        $cart->refreshTotals();
    }

    public function clear(): void
    {
        $cart = $this->forCurrentUser();
        $cart->items()->delete();
        $cart->refreshTotals();
    }

    /**
     * Optional: set coupon discount on the cart, then recompute grand total.
     * Pass a positive amount (BDT).
     */
    public function setCouponDiscount(float $amount): void
    {
        $cart = $this->forCurrentUser();
        $cart->discount_total = max(0, round($amount, 2));
        $cart->refreshTotals();
    }


     public function applyCoupon(string $rawCode): array
    {
        $code = Str::upper(trim($rawCode));
        if ($code === '') {
            return ['ok' => false, 'message' => 'Enter a coupon code.'];
        }

        $cart = $this->forCurrentUser()->load('items.dish');
        if ($cart->items->isEmpty()) {
            return ['ok' => false, 'message' => 'Your cart is empty.'];
        }

        // Already applied?
        $existing = data_get($cart->meta, 'coupon.code');
        if ($existing && $existing === $code) {
            return ['ok' => true, 'message' => 'This coupon is already applied.'];
        }

        $coupon = Coupon::whereRaw('UPPER(coupon_code) = ?', [$code])->first();
        if (!$coupon) {
            return ['ok' => false, 'message' => 'Invalid coupon code.'];
        }

        // Basic checks
        if ($coupon->status !== 'active') {
            return ['ok' => false, 'message' => 'This coupon is not active.'];
        }
        $today = Carbon::today();
        if ($today->lt(Carbon::parse($coupon->start_date)) || $today->gt(Carbon::parse($coupon->expire_date))) {
            return ['ok' => false, 'message' => 'This coupon is not valid today.'];
        }

        // Subtotal (line_total sum: base + extras), before coupon
        $subtotal = (float) $cart->items->sum('line_total');

        // Minimum purchase
        $minPurchase = (float) ($coupon->minimum_purchase ?? 0);
        if ($minPurchase > 0 && $subtotal < $minPurchase) {
            return ['ok' => false, 'message' => 'Minimum purchase not met for this coupon.'];
        }

        // Same user limit
        $userId    = Auth::id();
        $sessionId = session()->getId();

        $usageQuery = CouponUsage::query()->where('coupon_id', $coupon->id);
        if ($userId) {
            $usageQuery->where('user_id', $userId);
        } else {
            $usageQuery->where('session_id', $sessionId);
        }
        $usageCount = (int) $usageQuery->count();

        if ($coupon->same_user_limit !== null && $usageCount >= (int) $coupon->same_user_limit) {
            return ['ok' => false, 'message' => 'You have reached the usage limit for this coupon.'];
        }

        // First order rule: allow only if the user has no previous orders/usage
        // if ($coupon->coupon_type === 'first_order') {
        //     $hasAnyOrder = false;
        //     if ($userId && class_exists(\App\Models\Order::class)) {
        //         $hasAnyOrder = \App\Models\Order::where('user_id', $userId)->exists();
        //     } else {
        //         // Fallback: consider any prior usage (across all coupons) as not first order
        //         $hasAnyOrder = CouponUsage::when($userId, fn($q) => $q->where('user_id', $userId))
        //                                   ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
        //                                   ->exists();
        //     }
        //     if ($hasAnyOrder) {
        //         return ['ok' => false, 'message' => 'This coupon is only for your first order.'];
        //     }
        // }

        // Calculate discount
        $discount = 0.0;
        if ($coupon->discount_type === 'percent') {
            $discount = round($subtotal * ((float)$coupon->discount / 100), 2);
        } else { // 'amount'
            $discount = round(min((float)$coupon->discount, $subtotal), 2);
        }

        // Persist on cart
        $meta = (array) ($cart->meta ?? []);
        $meta['coupon'] = [
            'id'             => $coupon->id,
            'code'           => $coupon->coupon_code,
            'discount_type'  => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount,
            'calculated'     => $discount,
            'applied_at'     => now()->toISOString(),
        ];

        $cart->discount_total = $discount;
        $cart->meta = $meta;

        // Recalc tax + grand_total based on your refreshTotals logic
        $cart->refreshTotals();

        return ['ok' => true, 'message' => 'Coupon applied.'];
    }

    /**
     * Remove currently applied coupon from the cart.
     */
    public function removeCoupon(): void
    {
        $cart = $this->forCurrentUser();
        $meta = (array) ($cart->meta ?? []);
        unset($meta['coupon']);

        $cart->discount_total = 0;
        $cart->meta = $meta;
        $cart->refreshTotals();
    }

    /**
     * (Call this AFTER order placement) Record that a coupon was actually used.
     * Pass the order_id so you can audit later.
     */
    public function recordCouponUsageAfterOrder(int $couponId, ?int $orderId = null): void
    {
        $userId    = Auth::id();
        $sessionId = session()->getId();

        CouponUsage::create([
            'coupon_id' => $couponId,
            'user_id'   => $userId,
            'session_id'=> $userId ? null : $sessionId,
            'order_id'  => $orderId,
            'used_at'   => now(),
            'meta'      => null,
        ]);
    }
}
