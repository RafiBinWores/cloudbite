<?php

namespace App\Livewire\Frontend\Checkout;

use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class CheckoutPage extends Component
{

    // Contact & shipping
    public string $contact_name = '';
    public string $phone = '';
    public ?string $email = null;

    public string $address_line1 = '';
    public ?string $address_line2 = null;
    public string $city = '';
    public string $postcode = '';

    public ?string $customer_note = null;

    // Payment
    public string $payment_method = 'cod'; // cod|sslcommerz

    // Add these public properties at the top with your other fields:
    public ?float $lat = null;
    public ?float $lng = null;
    public ?string $city_from_map = null;
    public ?string $postcode_from_map = null;

    // (Optional) helper: allow overriding address if user wants to type
    public bool $manual_address_override = false;

    // Totals (read from cart; no re-taxing)
    public float $subtotal = 0;
    public float $discount_total = 0;
    public float $tax_total = 0;
    public float $shipping_total = 0;
    public float $grand_total = 0; // = cart.grand_total + shipping_total (to avoid double tax)

    public ?Cart $cart = null;
    public ?ShippingSetting $shipSetting = null;

    protected function rules(): array
    {
        return [
            'contact_name'   => 'required|string|max:191',
            'phone'          => 'required|string|max:50',
            'email'          => 'nullable|email',
            'address_line1'  => 'required|string|max:255',
            'address_line2'  => 'nullable|string|max:255',
            'city'           => 'required|string|max:120',
            'postcode'       => 'required|string|max:20',
            'customer_note'  => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cod,sslcommerz',
        ];
    }

    public function mount()
    {
        $user = Auth::user();
        $sessionId = Session::getId();

        $this->cart = Cart::with(['items.dish', 'items.crust', 'items.bun'])
            ->when($user, fn($q) => $q->where('user_id', $user->id))
            ->when(!$user, fn($q) => $q->where('session_id', $sessionId))
            ->latest('id')
            ->first();

        abort_if(!$this->cart || $this->cart->items->isEmpty(), 404, 'Your cart is empty.');

        $this->shipSetting = ShippingSetting::query()->latest('id')->first();

        // Prefill
        if ($user) {
            $this->contact_name = $user->name ?? '';
            $this->email = $user->email ?? null;
        }

        $this->hydrateTotals(); // reads cart buckets, then adds shipping
    }

    public function hydrateTotals(): void
    {
        // Read what the Cart already calculated — DO NOT re-add tax here
        $this->subtotal       = (float) ($this->cart->subtotal ?? 0);
        $this->discount_total = (float) ($this->cart->discount_total ?? 0);
        $this->tax_total      = (float) ($this->cart->tax_total ?? 0);

        // Compute shipping from DB settings
        $base = (float) ($this->shipSetting->base_fee ?? 0);
        $free = (bool) ($this->shipSetting->free_delivery ?? false);
        $min  = (float) ($this->shipSetting->free_minimum ?? 0);

        $this->shipping_total = ($free && $this->subtotal >= $min) ? 0.0 : $base;

        // IMPORTANT: avoid double-tax — assume cart.grand_total ALREADY includes subtotal - discount + tax
        // If your cart.grand_total already included shipping, then just set grand_total = cart.grand_total.
        $cartGrand = (float) ($this->cart->grand_total ?? 0);
        $this->grand_total = max(0, $cartGrand + $this->shipping_total);
    }

    public function updated($field): void
    {
        // If you later apply zone-based shipping, re-hydrate on address changes
        if (in_array($field, ['address_line1', 'address_line2', 'city', 'postcode'])) {
            $this->hydrateTotals();
        }
    }

    #[On('shipping-settings-updated')]
    public function refreshShipping(): void
    {
        $this->shipSetting = ShippingSetting::query()->latest('id')->first();
        $this->hydrateTotals();
    }

    public function placeOrder()
    {
        $this->validate();
        abort_if(!$this->cart || $this->cart->items->isEmpty(), 400, 'Cart empty');

        return DB::transaction(function () {
            $user = Auth::user();

            $code = 'ORD-' . now()->format('Ymd') . '-' . Str::upper(Str::random(6));

            $order = Order::create([
                'user_id'        => $user?->id,
                'session_id'     => Session::getId(),
                'order_code'     => $code,

                // Persist the exact buckets (no recomputation to avoid drift / double tax)
                'subtotal'       => $this->subtotal,
                'discount_total' => $this->discount_total,
                'tax_total'      => $this->tax_total,
                'shipping_total' => $this->shipping_total,
                'grand_total'    => $this->grand_total, // cart grand + shipping

                'coupon_code'    => data_get($this->cart->meta, 'coupon.code'),
                'coupon_value'   => (float) data_get($this->cart->meta, 'coupon.calculated', 0),

                'contact_name'   => $this->contact_name,
                'phone'          => $this->phone,
                'email'          => $this->email,

                'shipping_address' => [
                    'line1'    => $this->address_line1,
                    'line2'    => $this->address_line2,
                    'city'     => $this->city,
                    'postcode' => $this->postcode,
                ],

                'customer_note'  => $this->customer_note,

                'payment_method' => $this->payment_method,
                'payment_status' => 'unpaid',
                'order_status'   => 'pending',
                'placed_at'      => now(),

                'meta'           => [
                    'cart_id' => $this->cart->id,
                ],
            ]);

            // Copy cart items → order_items
            foreach ($this->cart->items as $ci) {
                OrderItem::create([
                    'order_id'   => $order->id,
                    'dish_id'    => $ci->dish_id,
                    'qty'        => $ci->qty,
                    'crust_id'   => $ci->crust_id,
                    'bun_id'     => $ci->bun_id,
                    'addon_ids'  => $ci->addon_ids,
                    'unit_price' => $ci->unit_price,
                    'line_total' => $ci->line_total,
                    'meta'       => $ci->meta,
                ]);
            }

            // Clear cart (soft reset)
            $this->cart->items()->delete();
            $this->cart->update([
                'subtotal' => 0,
                'discount_total' => 0,
                'tax_total' => 0,
                'grand_total' => 0,
                'meta' => null,
            ]);


            if ($this->payment_method === 'sslcommerz') {
                // TODO: integrate SSLCommerz
                return redirect()->route('orders.thankyou', ['code' => $order->order_code]);
            }

            // COD
            return redirect()->route('orders.thankyou', ['code' => $order->order_code]);
        });
    }

    public function render()
    {
        return view('livewire.frontend.checkout.checkout-page', [
            'cart' => $this->cart,
            'shipSetting' => $this->shipSetting,
        ])->layout('components.layouts.frontend', ['title' => 'Checkout - CloudBite']);
    }
}
