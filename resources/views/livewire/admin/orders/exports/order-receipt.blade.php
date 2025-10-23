<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Receipt #{{ $order->order_code }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
  /* ===== Thermal 80mm preset ===== */
  @page {
    /* Most 80mm printers are ~72–80mm effective printable width. */
    size: 80mm auto;
    margin: 0;                /* absolutely no browser margins */
  }

  html, body {
    padding: 0;
    margin: 0;
    background: #fff;
  }

  /* Fixed content width to the paper */
  .receipt {
    width: 80mm;
    padding: 6mm 4mm;         /* small safe padding */
    box-sizing: border-box;
    font-family: ui-monospace, SFMono-Regular, Menlo, Consolas, "Liberation Mono", monospace;
    font-size: 12px;          /* good for readability on thermal */
    line-height: 1.3;
    color: #000;
  }

  .center { text-align: center; }
  .right  { text-align: right; }
  .bold   { font-weight: 700; }
  .muted  { color: #000; opacity: .85; }
  .hr     { border-top: 1px dashed #000; margin: 6px 0; }

  .logo {
    display: block;
    margin: 0 auto 4px auto;
    max-width: 38mm;          /* fit inside 80mm with margins */
  }

  .title { font-size: 13px; font-weight: 700; margin-bottom: 2px; }
  .sm    { font-size: 11px; }
  .xs    { font-size: 10px; }

  .row { display: flex; justify-content: space-between; gap: 6px; }
  .mt4 { margin-top: 4px; }
  .mt6 { margin-top: 6px; }
  .mt8 { margin-top: 8px; }

  /* Items layout */
  .item { margin: 4px 0; }
  .item .name { display: block; }
  .item .meta { margin-left: 8px; }

  /* Ensure no page header/footer from browser */
  @media print {
    body { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>

<script>
  // Auto-print on load, then close the tab/window after printing
  window.addEventListener('load', () => {
    window.print();
  });
  window.addEventListener('afterprint', () => {
    // Give a short delay in case some browsers need it
    setTimeout(() => window.close(), 50);
  });
</script>
</head>
<body>
  <div class="receipt">

    {{-- Brand header --}}
    <div class="center">
      @if(!empty($businessSetting?->logo))
        <img src="{{ asset($businessSetting->logo) }}" class="logo" alt="Logo">
      @endif
      <div class="title">{{ $businessSetting->company_name ?? 'Company Name' }}</div>
      <div class="xs muted">{{ $businessSetting->address ?? '' }}</div>
      <div class="xs muted">{{ $businessSetting->phone ?? '' }}</div>
    </div>

    <div class="hr"></div>

    {{-- Invoice meta --}}
    <div class="row xs">
      <div><span class="bold">Invoice:</span> {{ $order->order_code }}</div>
      <div class="right">{{ optional($order->created_at)->format('d M Y, h:i A') }}</div>
    </div>

    {{-- Customer --}}
    <div class="mt4 xs">
      <div><span class="bold">Name:</span> {{ $order->contact_name ?? 'Guest' }}</div>
      <div><span class="bold">Phone:</span> {{ $order->phone ?? '-' }}</div>
      @php
        $addr = $order->shipping_address ?? null;
        if (is_string($addr)) {
            $decoded = json_decode($addr, true);
            $addr = json_last_error() === JSON_ERROR_NONE ? $decoded : $addr;
        }
        $addressLine = '-';
        if (is_array($addr)) {
            $addressLine = implode(', ', array_filter([$addr['line1'] ?? null, $addr['city'] ?? null, $addr['postcode'] ?? null]));
            $addressLine = $addressLine === '' ? '-' : $addressLine;
        } elseif (is_string($addr) && trim($addr) !== '') {
            $addressLine = $addr;
        }
      @endphp
      <div><span class="bold">Address:</span> {{ $addressLine }}</div>
    </div>

    <div class="hr"></div>

    {{-- Items --}}
    <div class="xs">
      @php
        $sub = 0.0;
      @endphp

      @foreach ($order->items as $item)
        @php
          $qty  = (int)($item->qty ?? 1);
          $unit = (float)($item->unit_price ?? 0);
          $ids  = is_array($item->addon_ids ?? null) ? $item->addon_ids : [];
          $itemAddons = collect($ids)->map(fn($id) => $addons[$id] ?? null)->filter();
          $metaAddons = [];
          if (is_array($item->meta ?? null) && isset($item->meta['addons']) && is_array($item->meta['addons'])) {
              $metaAddons = $item->meta['addons']; // id => [price, quantity]
          }
          $addonsTotal = 0.0;
        @endphp

        <div class="item">
          <span class="name bold">{{ $item->dish->title ?? ($item->dish->name ?? 'Item') }}</span>
          @if ($item->crust)
            <div class="meta">• Crust: {{ $item->crust->name }}</div>
          @endif
          @if ($item->bun)
            <div class="meta">• Bun: {{ $item->bun->name }}</div>
          @endif
          @if ($itemAddons->isNotEmpty())
            <div class="meta">• Add-ons:</div>
            @foreach ($itemAddons as $ao)
              @php
                $override = $metaAddons[$ao->id] ?? null;
                $aoName = $ao->name ?? ($ao->title ?? 'Add-on');
                $aoPrice = (float)($override['price'] ?? ($ao->price ?? 0));
                $aoQty = (int)($override['quantity'] ?? ($override['qty'] ?? 1));
                $line = $aoPrice * $aoQty;
                $addonsTotal += $line;
              @endphp
              <div class="meta">   - {{ $aoName }} × {{ $aoQty }} = {{ number_format($line, 2) }}</div>
            @endforeach
          @endif

          @php
            $computedLine = $unit * $qty + $addonsTotal;
            $lineTotal = isset($item->line_total) ? (float)$item->line_total : $computedLine;
            $sub += $lineTotal;
          @endphp

          <div class="row">
            <div class="xs">Qty {{ $qty }} × {{ number_format($unit, 2) }}</div>
            <div class="xs right bold">{{ number_format($lineTotal, 2) }}</div>
          </div>
        </div>
      @endforeach
    </div>

    <div class="hr"></div>

    {{-- Totals --}}
    <div class="xs">
      <div class="row"><div>Subtotal</div><div class="right">{{ number_format($order->subtotal ?? $sub, 2) }}</div></div>
      <div class="row"><div>Delivery</div><div class="right">(+)&nbsp;{{ number_format($order->shipping_total ?? 0, 2) }}</div></div>
      <div class="row"><div>Discount</div><div class="right">(-)&nbsp;{{ number_format($order->discount_total ?? 0, 2) }}</div></div>
      <div class="hr"></div>
      <div class="row bold"><div>Total</div><div class="right">{{ number_format($order->grand_total ?? (($order->subtotal ?? $sub) + ($order->shipping_total ?? 0) - ($order->discount_total ?? 0)), 2) }}</div></div>
    </div>

    <div class="mt8 xs center">
      <div>Payment: {{ strtoupper($order->payment_method ?? 'COD') }}</div>
      <div>Status: {{ ucfirst(str_replace('_',' ',$order->payment_status ?? '-')) }}</div>
    </div>

    <div class="mt8 center xs">
      — Thank you for your purchase —
    </div>

  </div>
</body>
</html>
