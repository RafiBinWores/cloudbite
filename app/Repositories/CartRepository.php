<?php

namespace App\Repositories;

use App\Models\{Cart, CartItem, Dish, Coupon, CouponUsage, Order};
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class CartRepository
{
    /** READ: current cart or null (NEVER creates) */
    public function forCurrentUser(): ?Cart
    {
        $q = Cart::query();

        if (Auth::check()) {
            return $q->where('user_id', Auth::id())->first();
        }
        return $q->where('session_id', session()->getId())->first();
    }

    /** READ: load relations, non-creating */
    public function loadCartNullable(array $with = []): ?Cart
    {
        $cart = $this->forCurrentUser();
        if ($cart && $with) $cart->load($with);
        return $cart;
    }

    /** MUTATION: ensure a cart exists (ONLY place that creates) */
    public function ensureCart(): Cart
    {
        if ($existing = $this->forCurrentUser()) {
            return $existing;
        }
        return Cart::create([
            'user_id'    => Auth::id(),
            'session_id' => session()->getId(),
            'meta' => [
                'prices_include_tax' => false, // NET prices by default
            ],
        ]);
    }

    /** (Deprecated) Kept only if some old code still calls it — now non-creating */
    public function loadCart(array $with = []): ?Cart
    {
        $cart = $this->forCurrentUser();
        if ($cart && $with) $cart->load($with);
        return $cart;
    }

    /** Add/update a cart line (CREATES cart if missing) */
    public function addItem(
        int $dishId,
        int $qty,
        ?int $crustId = null,
        ?int $bunId = null,
        array $addonIds = [],
        array $variationsSelected = []
    ): Cart {
        return DB::transaction(function () use (
            $dishId,
            $qty,
            $crustId,
            $bunId,
            $addonIds,
            $variationsSelected
        ) {
            $cart = $this->ensureCart();

            $dish = Dish::with(['crusts', 'buns', 'addOns'])->findOrFail($dishId);

            /** ----------------------------
             *  1) BASE PRICE (variation aware)
             *  ---------------------------- */
            $baseOriginal = (float) ($dish->price ?? 0);

            $vars = (array) ($dish->variations ?? []);
            foreach ($vars as $gIndex => $group) {
                $optIndex = $variationsSelected[$gIndex] ?? null;
                if ($optIndex === null) continue;

                $opt = $group['options'][$optIndex] ?? null;
                if ($opt && isset($opt['price'])) {
                    $baseOriginal = (float) $opt['price'];
                }
            }

            /** ----------------------------
             *  2) APPLY DISH DISCOUNT on chosen base
             *  ---------------------------- */
            $baseAfterDiscount = $baseOriginal;

            if ($dish->discount_type && (float) $dish->discount > 0) {
                if ($dish->discount_type === 'percent') {
                    $baseAfterDiscount = $baseOriginal * (1 - ((float) $dish->discount / 100));
                } elseif ($dish->discount_type === 'amount') {
                    $baseAfterDiscount = max(0, $baseOriginal - (float) $dish->discount);
                }
            }

            $baseAfterDiscount = round($baseAfterDiscount, 2);

            /** ----------------------------
             *  3) EXTRAS
             *  ---------------------------- */
            $crustExtra = 0.0;
            if ($crustId) {
                $c = $dish->crusts->firstWhere('id', $crustId);
                $crustExtra = (float) ($c?->price ?? 0);
            }

            $bunExtra = 0.0;

            $addonIdsSorted = array_values(array_unique(array_map('intval', $addonIds)));
            sort($addonIdsSorted);

            $addonsExtra = 0.0;
            if ($addonIdsSorted) {
                $selected = $dish->addOns->whereIn('id', $addonIdsSorted);
                foreach ($selected as $a) {
                    $addonsExtra += (float) ($a->price ?? 0);
                }
            }

            /** ----------------------------
             *  4) FINAL UNIT (NET, VAT excluded)
             *  ---------------------------- */
            $unit = round($baseAfterDiscount + $crustExtra + $bunExtra + $addonsExtra, 2);

            /** ----------------------------
             *  5) UNIQUE LINE KEY (addon + variation)
             *  ---------------------------- */
            $variationsSelectedSorted = (array) $variationsSelected;
            ksort($variationsSelectedSorted);
            $variationKey = json_encode($variationsSelectedSorted);

            /** ----------------------------
             *  6) VAT percent saved for cart calc
             *  ---------------------------- */
            $vatPercent = (float) ($dish->vat ?? 0);

            $existing = $cart->items()
                ->where('dish_id', $dishId)
                ->where('crust_id', $crustId)
                ->where('bun_id', $bunId)
                ->where('addon_ids', json_encode($addonIdsSorted))
                ->where('variation_selection', $variationKey)
                ->first();

            $metaPayload = [
                'base_original'       => $baseOriginal,       // ✅ needed to compute discount later
                'base_after_discount' => $baseAfterDiscount,  // ✅ discounted base stored
                'crust_extra'         => $crustExtra,
                'bun_extra'           => $bunExtra,
                'addons_extra'        => $addonsExtra,
                'variation_selection' => $variationsSelectedSorted,
                'vat_percent'         => $vatPercent,         // ✅ for cart VAT calc
            ];

            if ($existing) {
                $existing->qty += $qty;
                $existing->unit_price = $unit;
                $existing->line_total = $existing->qty * $unit;
                $existing->variation_selection = $variationsSelectedSorted;
                $existing->meta = $metaPayload;
                $existing->save();
            } else {
                $cart->items()->create([
                    'dish_id'    => $dishId,
                    'qty'        => $qty,
                    'crust_id'   => $crustId,
                    'bun_id'     => $bunId,
                    'addon_ids'  => $addonIdsSorted,
                    'variation_selection' => $variationsSelectedSorted,
                    'unit_price' => $unit,
                    'line_total' => $unit * $qty,
                    'meta'       => $metaPayload,
                ]);
            }

            $cart->refreshTotals();

            return $cart->fresh(['items.dish', 'items.crust', 'items.bun']);
        });
    }

    /** Mutations below: NEVER create cart if missing */

    public function bumpItemQty(int $itemId, int $delta): void
    {
        $cart = $this->forCurrentUser();
        if (!$cart) return;

        $item = $cart->items()->where('id', $itemId)->first();
        if (!$item) return;

        $item->qty = max(1, min(99, $item->qty + $delta));
        $item->line_total = $item->qty * $item->unit_price;
        $item->save();

        $this->deleteCartIfEmptyOrRefresh($cart);
    }

    public function setItemQty(int $itemId, int $qty): void
    {
        $cart = $this->forCurrentUser();
        if (!$cart) return;

        $item = $cart->items()->where('id', $itemId)->first();
        if (!$item) return;

        $item->qty = max(1, min(99, $qty));
        $item->line_total = $item->qty * $item->unit_price;
        $item->save();

        $this->deleteCartIfEmptyOrRefresh($cart);
    }

    public function removeItem(int $itemId): void
    {
        $cart = $this->forCurrentUser();
        if (!$cart) return;

        $item = $cart->items()->where('id', $itemId)->first();
        if (!$item) return;

        $item->delete();
        $this->deleteCartIfEmptyOrRefresh($cart);
    }

    public function clear(): void
    {
        $cart = $this->forCurrentUser();
        if (!$cart) return;

        $cart->items()->delete();
        $cart->delete();
    }

    public function setCouponDiscount(float $amount): void
    {
        $cart = $this->forCurrentUser();
        if (!$cart) return;

        $cart->discount_total = max(0, round($amount, 2));
        $this->deleteCartIfEmptyOrRefresh($cart);
    }

    public function applyCoupon(string $rawCode): array
    {
        $code = Str::upper(trim($rawCode));
        if ($code === '') return ['ok' => false, 'message' => 'Enter a coupon code.'];

        $cart = $this->forCurrentUser();
        if (!$cart) return ['ok' => false, 'message' => 'Your cart is empty.'];
        $cart->load('items.dish');

        if ($cart->items->isEmpty()) return ['ok' => false, 'message' => 'Your cart is empty.'];

        $coupon = Coupon::whereRaw('UPPER(coupon_code) = ?', [$code])->first();
        if (!$coupon) return ['ok' => false, 'message' => 'Invalid coupon code.'];

        if ($coupon->status !== 'active') return ['ok' => false, 'message' => 'This coupon is not active.'];

        $today = Carbon::today();
        if ($today->lt(Carbon::parse($coupon->start_date)) || $today->gt(Carbon::parse($coupon->expire_date))) {
            return ['ok' => false, 'message' => 'This coupon is not valid today.'];
        }

        $subtotal = (float) $cart->items->sum('line_total');

        $minPurchase = (float) ($coupon->minimum_purchase ?? 0);
        if ($minPurchase > 0 && $subtotal < $minPurchase) {
            return ['ok' => false, 'message' => 'Minimum purchase not met for this coupon.'];
        }

        $userId    = Auth::id();
        $sessionId = session()->getId();

        $usageQuery = CouponUsage::query()->where('coupon_id', $coupon->id);
        if ($userId) $usageQuery->where('user_id', $userId);
        else $usageQuery->where('session_id', $sessionId);

        $usageCount = (int) $usageQuery->count();
        if ($coupon->same_user_limit !== null && $usageCount >= (int) $coupon->same_user_limit) {
            return ['ok' => false, 'message' => 'You have reached the usage limit for this coupon.'];
        }

        if ($coupon->coupon_type === 'first_order') {
            $hasAnyOrder = false;
            if ($userId && class_exists(Order::class)) {
                $hasAnyOrder = Order::where('user_id', $userId)->exists();
            } else {
                $hasAnyOrder = CouponUsage::when($userId, fn($q) => $q->where('user_id', $userId))
                                          ->when(!$userId, fn($q) => $q->where('session_id', $sessionId))
                                          ->exists();
            }
            if ($hasAnyOrder) return ['ok' => false, 'message' => 'This coupon is only for your first order.'];
        }

        $discount = $coupon->discount_type === 'percent'
            ? round($subtotal * ((float) $coupon->discount / 100), 2)
            : round(min((float) $coupon->discount, $subtotal), 2);

        $meta = (array) ($cart->meta ?? []);
        $meta['coupon'] = [
            'id'             => $coupon->id,
            'code'           => $coupon->coupon_code,
            'discount_type'  => $coupon->discount_type,
            'discount_value' => (float) $coupon->discount,
            'calculated'     => $discount,
            'applied_at'     => now()->toISOString(),
        ];

        $cart->discount_total = $discount; // coupon only
        $cart->meta = $meta;
        $cart->refreshTotals();

        return ['ok' => true, 'message' => 'Coupon applied.'];
    }

    public function removeCoupon(): void
    {
        $cart = $this->forCurrentUser();
        if (!$cart) return;

        $meta = (array) ($cart->meta ?? []);
        unset($meta['coupon']);

        $cart->discount_total = 0;
        $cart->meta = $meta;
        $this->deleteCartIfEmptyOrRefresh($cart);
    }

    protected function deleteCartIfEmptyOrRefresh(Cart $cart): void
    {
        if (!$cart->items()->exists()) {
            $cart->delete();
        } else {
            $cart->refreshTotals();
        }
    }

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
