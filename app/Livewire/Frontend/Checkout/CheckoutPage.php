<?php

namespace App\Livewire\Frontend\Checkout;

use App\Mail\OrderPlacedMail;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\ShippingSetting;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Session;
use Livewire\Component;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;

#[Layout('components.layouts.frontend')]
class CheckoutPage extends Component
{
    // Contact & shipping
    public string $contact_name = '';
    public string $phone = '';
    public ?string $email = null;

    public string $address_line1 = '';
    public string $city = '';
    public string $postcode = '';

    public ?string $customer_note = null;

    // Payment
    public string $payment_method = 'cod';

    public ?float $lat = null;
    public ?float $lng = null;
    public ?string $city_from_map = null;
    public ?string $postcode_from_map = null;


    public bool $manual_address_override = false;

    // Totals (read from cart; no re-taxing)
    public float $subtotal = 0;
    public float $discount_total = 0;
    public float $tax_total = 0;
    public float $shipping_total = 0;
    public float $grand_total = 0;

    public ?Cart $cart = null;
    public ?ShippingSetting $shipSetting = null;

    protected function rules(): array
    {
        return [
            'contact_name'   => 'required|string|max:191',
            'phone'          => [
                'required',
                'regex:/^(?:\+?88)?01[3-9]\d{8}$/',
            ],
            'email'          => 'nullable|email',
            'address_line1'  => 'required|string|max:255',
            'city'           => 'required|string|max:120',
            'postcode'       => 'required|string|max:20',
            'customer_note'  => 'nullable|string|max:1000',
            'payment_method' => 'required|in:cod,sslcommerz',
        ];
    }

    public function messages(): array
    {
        return [
            'phone.required'          => 'A phone number is required.',
            'phone.regex'             => 'Enter a valid Bangladeshi mobile number.',
            'email.email'             => 'Please enter a valid email address.',
            'address_line1.required'  => 'The delivery address field is required.',
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

        $this->hydrateTotals();
    }

    public function hydrateTotals(): void
    {
        // Read what the Cart already calculated — DO NOT re-add tax here
        $this->subtotal       = (float) ($this->cart->subtotal ?? 0);
        $this->discount_total = (float) ($this->cart->discount_total ?? 0);
        $this->tax_total      = (float) ($this->cart->tax_total ?? 0);

        // Compute shipping from DB settings
        $base = (float) ($this->shipSetting->base_fee ?? 0);
        $free = (bool) ($this->shipSetting->free_delivery ?? true);
        $min  = (float) ($this->shipSetting->free_minimum ?? 0);

        $this->shipping_total = ($free && $this->subtotal >= $min) ? 0.0 : $base;

        $cartGrand = (float) ($this->cart->grand_total ?? 0);
        $this->grand_total = max(0, $cartGrand + $this->shipping_total);
    }

    public function updated($field): void
    {
        // If you later apply zone-based shipping, re-hydrate on address changes
        if (in_array($field, ['address_line1', 'city', 'postcode'])) {
            $this->hydrateTotals();
        }
    }

    protected function sendOrderPlacedMail(?Order $order): void
    {
        if (! $order || empty($order->email)) {
            return;
        }

        DB::afterCommit(function () use ($order) {
            try {
                Mail::to($order->email)->queue(new OrderPlacedMail($order));
                // If not using queue yet, use ->send(...) instead of ->queue(...)
            } catch (\Throwable $e) {
                logger()->warning('OrderPlacedMail failed', [
                    'order_id' => $order->id ?? null,
                    'error'    => $e->getMessage(),
                ]);
            }
        });
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

            // Current year
            $year = now()->format('Y');

            // Find last order of this year
            $lastOrder = Order::whereYear('created_at', $year)
                ->where('order_code', 'like', "{$year}%")
                ->orderByDesc('id')
                ->first();

            // Extract last 4 digits (sequence number)
            if ($lastOrder && preg_match('/' . $year . '(\d{4})$/', $lastOrder->order_code, $matches)) {
                $nextNumber = (int) $matches[1] + 1;
            } else {
                $nextNumber = 1;
            }

            // Final code format: YYYY0001
            $code = $year . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);

            // Common order payload (buckets from cart; NO placed_at yet)
            $orderPayload = [
                'user_id'        => $user?->id,
                'session_id'     => Session::getId(),
                'order_code'     => $code,

                'subtotal'       => $this->subtotal,
                'discount_total' => $this->discount_total,
                'tax_total'      => $this->tax_total,
                'shipping_total' => $this->shipping_total,
                'grand_total'    => $this->grand_total,

                'coupon_code'    => data_get($this->cart->meta, 'coupon.code'),
                'coupon_value'   => (float) data_get($this->cart->meta, 'coupon.calculated', 0),

                'contact_name'   => $this->contact_name,
                'phone'          => $this->phone,
                'email'          => $this->email,

                'shipping_address' => [
                    'line1'    => $this->address_line1,
                    'city'     => $this->city,
                    'postcode' => $this->postcode,
                    'lat'      => $this->lat,
                    'lng'      => $this->lng,
                ],

                'customer_note'  => $this->customer_note,

                'payment_method' => $this->payment_method,
                'payment_status' => 'unpaid',
                'order_status'   => 'pending',
                'placed_at'      => null,
                'meta'           => ['cart_id' => $this->cart->id],
            ];

            if ($this->payment_method === 'sslcommerz') {
                // 1) Create a shell order for tracking; DO NOT clear the cart yet
                $tranId = 'CB-' . now()->format('YmdHis') . '-' . Str::random(6);
                $order = Order::create(array_merge($orderPayload, [
                    'meta' => array_merge($orderPayload['meta'], ['tran_id' => $tranId]),
                ]));

                // 2) (Optional) copy items now so you have a snapshot even if cart changes
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


                return redirect()->route('ssl.init', ['order' => $order->id]);
            }

            // COD flow — create full order and clear cart now
            $order = Order::create(array_merge($orderPayload, [
                'payment_status' => 'unpaid',
                'order_status'   => 'processing', // or 'pending' if you verify later
                'placed_at'      => now(),
            ]));

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

            // Clear cart only for COD (paid on delivery, but you lock items)
            $this->cart->items()->delete();
            $this->cart->update([
                'subtotal' => 0,
                'discount_total' => 0,
                'tax_total' => 0,
                'grand_total' => 0,
                'meta' => null,
            ]);

            $this->sendOrderPlacedMail($order);
            return redirect()->route('orders.thankyou', ['code' => $order->order_code]);
        });
    }


    public function render()
    {
        return view('livewire.frontend.checkout.checkout-page', [
            'cart' => $this->cart,
            'shipSetting' => $this->shipSetting,
        ])->title('Checkout - CloudBite');
    }
}
