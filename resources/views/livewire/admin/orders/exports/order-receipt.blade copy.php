<!doctype html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Receipt #{{ $order->order_code }}</title>
<meta name="viewport" content="width=device-width, initial-scale=1">

<style>
  /* ===== Thermal paper preset =====
     Switch 80mm <-> 58mm by changing both @page size and .receipt width
  */
  @page { size: 80mm auto; margin: 0; }

  :root{
    --ink:#000;
    --muted: #000000A8;
    --accent:#000;          /* keep pure black for thermal */
    --gap:6px;
    --pad:6mm;
    --radius:6px;           /* visual corners (some printers ignore) */
    --fs-xs:10px;
    --fs-sm:11px;
    --fs-md:12px;
    --fs-lg:13px;
  }

  * { -webkit-print-color-adjust: exact; print-color-adjust: exact; box-sizing: border-box; }
  html, body { margin:0; padding:0; background:#fff; color:var(--ink); }
  body { font: var(--fs-md) / 1.35 ui-sans-serif, system-ui, -apple-system, Segoe UI, Roboto, "Helvetica Neue", Arial, "Noto Sans", "Liberation Sans", ui-monospace, monospace; }

  .receipt{ width:80mm; padding: var(--pad); }
  .center{ text-align:center; }
  .right{ text-align:right; }
  .bold{ font-weight:700; }
  .muted{ color:var(--muted); }
  .caps{ text-transform: uppercase; letter-spacing:.02em; }
  .hr{ border-top: 1px dashed var(--ink); margin: 8px 0; }
  .hr-solid{ border-top: 1px solid var(--ink); margin: 8px 0; }
  .sp{ height:4px; }

  .brand{
    text-align:center;
    margin-bottom: 6px;
  }
  .brand .logo{
    display:block; margin:0 auto 4px auto;
    max-width: 40mm; max-height: 16mm; object-fit: contain;
  }
  .brand .name{ font-size: var(--fs-lg); font-weight: 800; }
  .brand .meta{ font-size: var(--fs-xs); }

  .kv{ display:flex; justify-content:space-between; gap:8px; }
  .kv .k{ color:var(--muted); }
  .kv + .kv{ margin-top: 3px; }

  /* Section headings with lines */
  .section-title{
    display:flex; align-items:center; gap:6px; margin: 8px 0 4px;
    font-weight:700; font-size: var(--fs-sm);
  }
  .section-title:before,.section-title:after{
    content:""; flex:1; border-top: 1px dashed var(--ink);
  }

  /* Badge (status) */
  .badge{
    display:inline-block; border:1px solid var(--ink);
    padding: 2px 6px; border-radius: 999px; font-size: var(--fs-xs); font-weight:700;
  }

  /* Items */
  .item{ padding:6px 0; }
  .item .title{ font-weight:700; }
  .item .meta{ margin-top:2px; margin-left:8px; font-size: var(--fs-xs); }
  .item .row{ display:flex; justify-content:space-between; margin-top:2px; }

  /* Totals card */
  .totals{
    margin-top: 8px;
    border: 1px solid var(--ink);
    border-radius: var(--radius);
    padding: 8px;
  }
  .totals .line{ display:flex; justify-content:space-between; margin:2px 0; }
  .totals .line.total{ border-top:1px solid var(--ink); padding-top:6px; margin-top:6px; font-weight:800; }

  /* Footer */
  .note{ margin-top:8px; font-size: var(--fs-xs); text-align:center; }
  .thankyou{ margin-top:8px; text-align:center; font-weight:800; }

  /* Optional tiny grid helpers */
  .grid-2{ display:grid; grid-template-columns: 1fr auto; gap: 2px 8px; }

  @media print{
    .no-print{ display:none !important; }
  }
</style>

<script>
  window.addEventListener('load', () => { window.print(); });
  window.addEventListener('afterprint', () => { setTimeout(() => window.close(), 80); });
</script>
</head>
<body>
  <div class="receipt">

    {{-- Brand header --}}
    <div class="brand">
      @if(!empty($businessSetting?->logo))
        <img src="{{ asset($businessSetting->logo) }}" class="logo" alt="Logo">
      @endif
      <div class="name">{{ $businessSetting->company_name ?? 'Company Name' }}</div>
      @if(!empty($businessSetting?->tagline))
        <div class="muted" style="font-size:var(--fs-sm)">{{ $businessSetting->tagline }}</div>
      @endif
      <div class="meta muted">
        {{ $businessSetting->address ?? '' }} @if(!empty($businessSetting?->phone)) • {{ $businessSetting->phone }} @endif
      </div>
    </div>

    <div class="hr"></div>

    {{-- Header meta --}}
    <div class="kv" style="font-size:var(--fs-sm)">
      <div><span class="k">Invoice</span>: <span class="bold">#{{ $order->order_code }}</span></div>
      <div class="right">{{ optional($order->created_at)->format('d M Y, h:i A') }}</div>
    </div>

    {{-- Status / Payment --}}
    @php
      $status = (string)($order->order_status ?? 'pending');
      $statusLabel = ucfirst(str_replace('_',' ', $status));
      $payMethod = strtoupper($order->payment_method ?? 'COD');
      $payStatus = ucfirst(str_replace('_',' ', $order->payment_status ?? '-'));
    @endphp
    <div class="kv" style="margin-top:4px">
      <div><span class="k">Status</span>: <span class="badge">{{ $statusLabel }}</span></div>
      <div class="right"><span class="k">Payment</span>: <span class="bold">{{ $payMethod }}</span> / {{ $payStatus }}</div>
    </div>

    {{-- Customer / Address --}}
    <div class="section-title"><span>Customer</span></div>
    @php
      $addr = $order->shipping_address ?? null;
      if (is_string($addr)) { $dec = json_decode($addr, true); $addr = json_last_error()===JSON_ERROR_NONE ? $dec : $addr; }
      $addressLine = '-';
      if (is_array($addr)) {
        $addressLine = implode(', ', array_filter([$addr['line1'] ?? null, $addr['city'] ?? null, $addr['postcode'] ?? null]));
        $addressLine = $addressLine === '' ? '-' : $addressLine;
      } elseif (is_string($addr) && trim($addr) !== '') { $addressLine = $addr; }
    @endphp
    <div class="grid-2" style="font-size:var(--fs-sm); margin-top:2px">
      <div class="muted">Name</div><div class="right bold">{{ $order->contact_name ?? 'Guest' }}</div>
      <div class="muted">Phone</div><div class="right">{{ $order->phone ?? '-' }}</div>
      <div class="muted">Address</div><div class="right" style="max-width:54mm">{{ $addressLine }}</div>
    </div>

    <div class="section-title"><span>Items</span></div>

    {{-- Items --}}
    @php $sub = 0.0; @endphp
    @foreach ($order->items as $item)
      @php
        $qty  = (int)($item->qty ?? 1);
        $unit = (float)($item->unit_price ?? 0);

        $ids  = is_array($item->addon_ids ?? null) ? $item->addon_ids : [];
        $itemAddons = collect($ids)->map(fn($id) => $addons[$id] ?? null)->filter();
        $metaAddons = (is_array($item->meta ?? null) && is_array(($item->meta['addons'] ?? null))) ? $item->meta['addons'] : [];
        $addonsTotal = 0.0;
      @endphp

      <div class="item">
        <div class="title">{{ $item->dish->title ?? ($item->dish->name ?? 'Item') }}</div>
        @if ($item->crust || $item->bun || $itemAddons->isNotEmpty())
          <div class="meta">
            @if ($item->crust)  • Crust: {{ $item->crust->name }}<br>@endif
            @if ($item->bun)    • Bun: {{ $item->bun->name }}<br>@endif
            @if ($itemAddons->isNotEmpty())
              • Add-ons:<br>
              @foreach ($itemAddons as $ao)
                @php
                  $override = $metaAddons[$ao->id] ?? null;
                  $aoName = $ao->name ?? ($ao->title ?? 'Add-on');
                  $aoPrice = (float)($override['price'] ?? ($ao->price ?? 0));
                  $aoQty = (int)($override['quantity'] ?? ($override['qty'] ?? 1));
                  $line = $aoPrice * $aoQty;
                  $addonsTotal += $line;
                @endphp
                &nbsp;&nbsp;– {{ $aoName }} × {{ $aoQty }} = {{ number_format($line, 2) }}<br>
              @endforeach
            @endif
          </div>
        @endif

        @php
          $computedLine = $unit * $qty + $addonsTotal;
          $lineTotal = isset($item->line_total) ? (float)$item->line_total : $computedLine;
          $sub += $lineTotal;
        @endphp

        <div class="row">
          <div class="muted">Qty {{ $qty }} × {{ number_format($unit, 2) }}</div>
          <div class="bold">{{ number_format($lineTotal, 2) }}</div>
        </div>
        <div class="hr"></div>
      </div>
    @endforeach

    {{-- Totals --}}
    <div class="totals">
      <div class="line"><div>Subtotal</div><div class="right">{{ number_format($order->subtotal ?? $sub, 2) }}</div></div>
      <div class="line"><div>Delivery</div><div class="right">(+)&nbsp;{{ number_format($order->shipping_total ?? 0, 2) }}</div></div>
      <div class="line"><div>Discount</div><div class="right">(-)&nbsp;{{ number_format($order->discount_total ?? 0, 2) }}</div></div>
      <div class="line total"><div>Total</div><div class="right">{{ number_format($order->grand_total ?? (($order->subtotal ?? $sub) + ($order->shipping_total ?? 0) - ($order->discount_total ?? 0)), 2) }}</div></div>
    </div>

    {{-- Optional footer notes --}}
    @if(!empty($businessSetting?->footnote))
      <div class="note muted">{{ $businessSetting->footnote }}</div>
    @endif

    <div class="thankyou">— Thank you —</div>
    <div class="note muted">Order: #{{ $order->order_code }}</div>
  </div>
</body>
</html>
