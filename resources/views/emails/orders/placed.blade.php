@php
    $placedAt = optional($order->placed_at ?? $order->created_at)->format('d M Y, h:i A');
    $statusLabel = ucwords(str_replace('_',' ', $order->order_status));
    $paymentLabel = ucfirst($order->payment_status).' ('.strtoupper($order->payment_method).')';
    $address = (array) ($order->shipping_address ?? []);
@endphp

@component('mail::message')
# 🧾 Order Receipt — #{{ $order->order_code }}

Hi {{ $order->contact_name ?? 'there' }},  
Thanks for your order! Here’s your receipt.

@component('mail::panel')
**Order Code:** {{ $order->order_code }}  
**Placed:** {{ $placedAt }}  
**Status:** {{ $statusLabel }}  
**Payment:** {{ $paymentLabel }}
@endcomponent

## Order Summary
@component('mail::table')
| Description | Amount |
|:--|--:|
| Subtotal | ৳{{ number_format($order->subtotal, 2) }} |
| Discount | - ৳{{ number_format($order->discount_total, 2) }} |
| Tax | ৳{{ number_format($order->tax_total, 2) }} |
| Shipping | ৳{{ number_format($order->shipping_total, 2) }} |
| **Grand Total** | **৳{{ number_format($order->grand_total, 2) }}** |
@endcomponent

@if(!empty($order->coupon_code))
**Coupon Applied:** {{ $order->coupon_code }} (৳{{ number_format($order->coupon_value ?? 0, 2) }})
@endif

---

## Items
@component('mail::table')
| Item | Qty | Price | Line Total |
|:--|:--:|--:|--:|
@foreach($order->items as $it)
@php
    $title = $it->dish->title ?? ('Dish #'.$it->dish_id);
    $options = [];
    if ($it->crust?->name) $options[] = 'Crust: '.$it->crust->name;
    if ($it->bun?->name) $options[] = 'Bun: '.$it->bun->name;
    // If you store add-ons names in meta or relation, show them here too
    $subtitle = implode(' • ', $options);
@endphp
| **{{ $title }}**{!! $subtitle ? '<br><span style="color:#6b7280;">'.$subtitle.'</span>' : '' !!} | {{ $it->qty }} | ৳{{ number_format($it->unit_price, 2) }} | ৳{{ number_format($it->line_total, 2) }} |
@endforeach
@endcomponent

---

## Delivery & Contact
@component('mail::panel')
**Shipping Address**  
{{ $address['line1'] ?? '—' }}  
{{ $address['city'] ?? '' }} {{ $address['postcode'] ?? '' }}

**Contact**  
Name: {{ $order->contact_name ?? '—' }}  
Phone: {{ $order->phone ?? '—' }}  
Email: {{ $order->email ?? '—' }}
@endcomponent

@if($order->customer_note)
**Customer Note**  
> {{ $order->customer_note }}
@endif

@component('mail::button', ['url' => route('account.orders.show', $order->order_code)])
View Order Details
@endcomponent

If anything looks off or you need help, just reply to this email.

Thanks,  
**CloudBite**

<hr style="border:none;border-top:1px solid #e5e7eb;margin:16px 0" />

<small>
**CloudBite**  
If you didn’t make this order, please contact support immediately.
</small>
@endcomponent
