@php
    use Illuminate\Support\Str;

    $statusLabel = function (string $status): string {
        return Str::headline($status);
    };
@endphp

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Meal Plan Bookings Export</title>

    <style>
        /* ===== PAGE SETUP: A4 LANDSCAPE ===== */
        @page {
            size: A4 landscape;
            margin: 10mm 8mm 12mm 8mm;
        }

        * {
            box-sizing: border-box;
            font-family: DejaVu Sans, sans-serif;
        }

        body {
            font-size: 10px;
            color: #0f172a;
            margin: 0;
            padding: 0;
        }

        .header {
            margin: 0 8px 8px 8px;
            padding-bottom: 6px;
            border-bottom: 1px solid #e2e8f0;
        }
        .header-title {
            font-size: 16px;
            font-weight: bold;
        }
        .header-meta {
            font-size: 9px;
            color: #6b7280;
            margin-top: 2px;
        }
        .filters {
            font-size: 9px;
            color: #4b5563;
            margin-top: 2px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed; /* 👈 force columns to fit */
            margin: 6px 8px 0 8px;
        }

        th, td {
            border: 1px solid #e5e7eb;
            padding: 4px 3px;
            text-align: left;
            word-wrap: break-word;
            word-break: break-word;
        }

        th {
            background-color: #f3f4f6;
            font-size: 9px;
            font-weight: 600;
        }

        td {
            font-size: 9px;
        }

        .text-right  { text-align: right; }
        .text-center { text-align: center; }
        .muted       { color: #6b7280; }

        /* Column widths (tuned for landscape) */
        th:nth-child(1),  td:nth-child(1)  { width: 10px;  }  /* # */
        th:nth-child(2),  td:nth-child(2)  { width: 70px;  }  /* Booking Code */
        th:nth-child(3),  td:nth-child(3)  { width: 55px;  }  /* Plan Type */
        th:nth-child(4),  td:nth-child(4)  { width: 55px;  }  /* Start Date */
        th:nth-child(5),  td:nth-child(5)  { width: 90px;  }  /* Customer */
        th:nth-child(6),  td:nth-child(6)  { width: 80px;  }  /* Phone */
        th:nth-child(7),  td:nth-child(7)  { width: 90px;  }  /* Email */
        th:nth-child(8),  td:nth-child(8)  { width: 55px;  }  /* Subtotal */
        th:nth-child(9),  td:nth-child(9)  { width: 55px;  }  /* Shipping */
        th:nth-child(10), td:nth-child(10) { width: 60px;  }  /* Grand Total */
        th:nth-child(11), td:nth-child(11) { width: 70px;  }  /* Pay Method */
        th:nth-child(12), td:nth-child(12) { width: 70px;  }  /* Pay Status */
        th:nth-child(13), td:nth-child(13) { width: 70px;  }  /* Status */
        th:nth-child(14), td:nth-child(14) { width: 75px;  }  /* Created At */
    </style>
</head>
<body>

    <div class="header">
        <div class="header-title">
            {{ $companyName ?? config('app.name', 'CloudBite') }}
        </div>

        <div class="header-meta">
            Meal Plan Bookings Export ·
            Generated at {{ now()->format('Y-m-d H:i:s') }}
        </div>

        <div class="filters">
            @if(!empty($status))
                Status: <strong>{{ \Illuminate\Support\Str::headline($status) }}</strong> ·
            @endif

            @if(!empty($dateFrom))
                From: <strong>{{ $dateFrom }}</strong>
            @endif

            @if(!empty($dateTo))
                To: <strong>{{ $dateTo }}</strong>
            @endif
        </div>
    </div>

    <table>
        <thead>
        <tr>
            <th class="text-center">#</th>
            <th>Booking Code</th>
            <th>Plan Type</th>
            <th>Start Date</th>
            <th>Customer</th>
            <th>Phone</th>
            <th>Email</th>
            <th class="text-right">Subtotal</th>
            <th class="text-right">Shipping</th>
            <th class="text-right">Grand Total</th>
            <th>Pay Method</th>
            <th>Pay Status</th>
            <th>Status</th>
            <th>Created At</th>
        </tr>
        </thead>

        <tbody>
        @forelse($bookings as $index => $booking)
            <tr>
                <td class="text-center">
                    {{ $index + 1 }}
                </td>
                <td>
                    #{{ $booking->booking_code }}
                </td>
                <td>
                    {{ ucfirst($booking->plan_type) }}
                </td>
                <td>
                    {{ optional($booking->start_date)->format('Y-m-d') }}
                </td>
                <td>
                    {{ $booking->contact_name }}
                </td>
                <td>
                    {{ $booking->phone }}
                </td>
                <td>
                    {{ $booking->email }}
                </td>
                <td class="text-right">
                    {{ number_format((float) $booking->plan_subtotal, 2) }}
                </td>
                <td class="text-right">
                    {{ number_format((float) $booking->shipping_total, 2) }}
                </td>
                <td class="text-right">
                    {{ number_format((float) $booking->grand_total, 2) }}
                </td>
                <td>
                    {{ strtoupper($booking->payment_method) === 'COD' ? 'Cash on Delivery' : 'SSLCommerz' }}
                </td>
                <td>
                    {{ ucfirst($booking->payment_status) }}
                </td>
                <td>
                    {{ $statusLabel($booking->status) }}
                </td>
                <td>
                    {{ $booking->created_at?->format('Y-m-d H:i') }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="14" class="text-center muted">
                    No meal plan bookings found for the selected filters.
                </td>
            </tr>
        @endforelse
        </tbody>
    </table>

</body>
</html>
