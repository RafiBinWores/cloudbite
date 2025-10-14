<?php

namespace App\Http\Controllers;

use App\Mail\OrderPlacedMail;
use App\Models\Cart;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;

class SslCommerzController extends Controller
{
    protected function endpoints(): array
    {
        $sandbox = filter_var(config('services.sslcommerz.sandbox', true), FILTER_VALIDATE_BOOL);

        return [
            'init'   => $sandbox
                ? 'https://sandbox.sslcommerz.com/gwprocess/v4/api.php'
                : 'https://seamless-epay.sslcommerz.com/gwprocess/v4/api.php',
            'verify' => $sandbox
                ? 'https://sandbox.sslcommerz.com/validator/api/validationserverAPI.php'
                : 'https://validation.sslcommerz.com/validator/api/validationserverAPI.php',
        ];
    }

    public function init(Order $order, Request $request)
    {
        abort_if($order->payment_status !== 'unpaid' || $order->order_status !== 'pending', 409, 'Order not payable');

        $tranId = data_get($order->meta, 'tran_id') ?? ('CB-' . now()->format('YmdHis'));
        $storeId = config('services.sslcommerz.store_id');
        $storePass = config('services.sslcommerz.store_password');

        $payload = [
            'store_id'      => $storeId,
            'store_passwd'  => $storePass,
            'total_amount'  => (float) $order->grand_total,
            'currency'      => 'BDT',
            'tran_id'       => $tranId,

            'success_url'   => route('ssl.success'),
            'fail_url'      => route('ssl.fail'),
            'cancel_url'    => route('ssl.cancel'),
            'ipn_url'       => route('ssl.ipn'),

            'cus_name'      => $order->contact_name,
            'cus_email'     => $order->email ?: 'no-reply@example.com',
            'cus_add1'      => data_get($order->shipping_address, 'line1', ''),
            'cus_city'      => data_get($order->shipping_address, 'city', ''),
            'cus_postcode'  => data_get($order->shipping_address, 'postcode', ''),
            'cus_country'   => 'Bangladesh',
            'cus_phone'     => $order->phone,

            'shipping_method'   => 'NO',
            'product_name'      => 'Order ' . $order->order_code,
            'product_category'  => 'Food',
            'product_profile'   => 'general',

            // optional
            'cus_add2'      => data_get($order->shipping_address, 'line2', ''),
            'value_a'       => (string) $order->id, // pass order id back
        ];

        $endpoint = $this->endpoints()['init'];

        $res = Http::asForm()->post($endpoint, $payload);
        abort_unless($res->ok(), 502, 'SSLCommerz init failed');

        $data = $res->json();

        if (($data['status'] ?? '') === 'SUCCESS' && !empty($data['GatewayPageURL'])) {
            // persist tran_id in case you created it just now
            if (!data_get($order->meta, 'tran_id')) {
                $order->update(['meta' => array_merge($order->meta ?? [], ['tran_id' => $tranId])]);
            }
            return redirect()->away($data['GatewayPageURL']);
        }

        // Debug details in logs
        logger()->error('SSL Init Error', ['response' => $data]);
        return redirect()
            ->route('orders.thankyou', ['code' => $order->order_code])
            ->with('payment_error', 'Could not start payment. Please try again.');
    }

    public function success(Request $request)
    {
        // SSLCommerz posts back these params
        $tranId = $request->input('tran_id');
        $valId  = $request->input('val_id');
        $amount = (float) $request->input('amount');

        $order = Order::where('meta->tran_id', $tranId)->first();
        if (!$order) {
            abort(404, 'Order not found');
        }

        // Verify with validation API to prevent tampering
        $verifyUrl = $this->endpoints()['verify'];
        $res = Http::get($verifyUrl, [
            'val_id'       => $valId,
            'store_id'     => config('services.sslcommerz.store_id'),
            'store_passwd' => config('services.sslcommerz.store_password'),
            'format'       => 'json',
        ]);

        if (!$res->ok()) {
            return $this->paymentFailed($order, 'Validation API error');
        }

        $data = $res->json();
        $status = $data['status'] ?? '';
        $amountVerified = (float) ($data['amount'] ?? 0);
        $currency = $data['currency'] ?? 'BDT';

        if (
            in_array($status, ['VALID', 'VALIDATED'], true)
            && abs($amountVerified - (float)$order->grand_total) < 0.01
            && $currency === 'BDT'
        ) {

            $order->update([
                'payment_status' => 'paid',
                'order_status'   => 'processing',
                'placed_at'      => now(),
                'meta' => array_merge($order->meta ?? [], [
                    'ssl' => [ /* ... */],
                ]),
            ]);

            // Now it's safe to clear the cart (if you didn't clear at init)
            if (isset($order->meta['cart_id'])) {
                Cart::where('id', $order->meta['cart_id'])->first()?->items()->delete();
                Cart::where('id', $order->meta['cart_id'])->update([
                    'subtotal' => 0,
                    'discount_total' => 0,
                    'tax_total' => 0,
                    'grand_total' => 0,
                    'meta' => null,
                ]);
            }

            DB::afterCommit(function () use ($order) {
                Mail::to($order->email)->queue(new OrderPlacedMail($order));
            });

            return redirect()->route('orders.thankyou', ['code' => $order->order_code]);
        }

        return $this->paymentFailed($order, 'Validation failed or amount mismatch');
    }

    public function fail(Request $request)
    {
        $tranId = $request->input('tran_id');
        $order = Order::where('meta->tran_id', $tranId)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'unpaid',
                'order_status'   => 'pending',
                'meta' => array_merge($order->meta ?? [], [
                    'ssl' => [
                        'status' => 'FAILED',
                        'reason' => $request->input('error') ?? 'Gateway reported failure',
                    ],
                ]),
            ]);
            return redirect()
                ->route('orders.thankyou', ['code' => $order->order_code])
                ->with('payment_error', 'Payment failed. You can try again from your orders page.');
        }

        abort(404, 'Order not found');
    }

    public function cancel(Request $request)
    {
        $tranId = $request->input('tran_id');
        $order = Order::where('meta->tran_id', $tranId)->first();

        if ($order) {
            $order->update([
                'payment_status' => 'unpaid',
                'order_status'   => 'pending',
                'meta' => array_merge($order->meta ?? [], [
                    'ssl' => [
                        'status' => 'CANCELLED',
                    ],
                ]),
            ]);
            return redirect()
                ->route('orders.thankyou', ['code' => $order->order_code])
                ->with('payment_error', 'Payment cancelled.');
        }

        abort(404, 'Order not found');
    }

    public function ipn(Request $request)
    {
        // Optional: you can re-verify here similarly to success(), using val_id or tran_id,
        // and correct order state if needed.
        return response('OK');
    }

    protected function paymentFailed(Order $order, string $why)
    {
        $order->update([
            'payment_status' => 'unpaid',
            'order_status'   => 'pending',
            'meta' => array_merge($order->meta ?? [], [
                'ssl' => [
                    'status' => 'VALIDATION_FAILED',
                    'reason' => $why,
                ],
            ]),
        ]);

        return redirect()
            ->route('orders.thankyou', ['code' => $order->order_code])
            ->with('payment_error', 'Payment could not be verified.');
    }
}
