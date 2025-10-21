<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <title>Orders</title>
    <style>
        body {
            font-family: DejaVu Sans, sans-serif;
            font-size: 12px;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 6px;
        }

        .brand img {
            height: 42px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 6px;
        }

        th {
            background: #f5f5f5;
            text-align: left;
        }

        h1 {
            margin: 0;
            font-size: 18px;
        }

        small {
            color: #555;
        }
    </style>
</head>

<body>

    <div class="brand">
        @if (!empty($logoPath) && file_exists($logoPath))
            <img src="{{ $logoPath }}" alt="Logo">
        @endif
        <h1>{{ $companyName ?? config('app.name', 'CloudBite') }}</h1>
    </div>

    <small>
        Status: {{ $status ?: 'All' }}
        | From: {{ $dateFrom ?: '—' }}
        | To: {{ $dateTo ?: '—' }}
        | Generated: {{ now()->format('Y-m-d H:i') }}
    </small>

    <table style="margin-top:8px;">
        <thead>
            <tr>
                <th>#</th>
                <th>Order ID</th>
                <th>Date</th>
                <th>Customer</th>
                <th>Phone</th>
                <th>Total</th>
                <th>Payment</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $i => $o)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td>{{ $o->order_code }}</td>
                    <td>{{ $o->created_at?->format('Y-m-d H:i') }}</td>
                    <td>{{ $o->contact_name }}</td>
                    <td>{{ $o->phone }}</td>
                    <td>{{ number_format((float) $o->grand_total, 2) }}</td>
                    <td>{{ ucfirst($o->payment_status) }}</td>
                    <td>{{ \Illuminate\Support\Str::headline($o->order_status) }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
