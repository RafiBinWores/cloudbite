<?php

namespace App\Exports;

use App\Models\MealPlanBooking;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class MealPlanBookingsExport implements
    FromQuery,
    WithHeadings,
    WithMapping,
    WithCustomStartCell,
    WithStyles,
    WithEvents,
    WithColumnWidths,
    WithDrawings
{
    public function __construct(
        public ?string $status      = null,
        public ?string $dateFrom    = null,
        public ?string $dateTo      = null,
        public ?string $search      = '',
        public ?string $companyName = null,
        public ?string $logoPath    = null
    ) {}

    /** Row counter for SL column */
    private int $rowNum = 0;

    /** Start table below the title area */
    public function startCell(): string
    {
        return 'A6';
    }

    public function query()
    {
        // If you don't have a `search()` scope on MealPlanBooking,
        // replace `MealPlanBooking::search($this->search)` with MealPlanBooking::query()
        return MealPlanBooking::search($this->search)
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest('id');
    }

    public function headings(): array
    {
        return [
            'SL',
            'Booking Code',
            'Plan Type',
            'Start Date',
            'Customer',
            'Phone',
            'Plan Subtotal',
            'Shipping',
            'Grand Total',
            'Paid Now',
            'Due Amount',
            'Payment Option',
            'Payment Method',
            'Payment Status',
            'Booking Status',
            'Created At',
        ];
    }

    public function map($b): array
    {
        return [
            ++$this->rowNum,                                     // SL
            $b->booking_code,
            ucfirst($b->plan_type),
            optional($b->start_date)?->format('Y-m-d'),
            $b->contact_name,
            " " . (string) $b->phone,
            (float) $b->plan_subtotal,
            (float) $b->shipping_total,
            (float) $b->grand_total,
            (float) $b->pay_now,       // 🔹 Paid Now
            (float) $b->due_amount,    // 🔹 Due Amount
            $b->payment_option === 'half'
                ? '50% now, 50% later'
                : 'Full payment',
            strtoupper($b->payment_method),
            ucfirst($b->payment_status),
            ucfirst($b->status),
            optional($b->created_at)?->format('Y-m-d H:i'),
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // SL
            'B' => 18,  // Booking Code
            'C' => 12,  // Plan Type
            'D' => 14,  // Start Date
            'E' => 26,  // Customer
            'F' => 16,  // Phone
            'G' => 14,  // Plan Subtotal
            'H' => 14,  // Shipping
            'I' => 14,  // Grand Total
            'J' => 14,  // Paid Now
            'K' => 14,  // Due Amount
            'L' => 18,  // Payment Option
            'M' => 16,  // Payment Method
            'N' => 16,  // Payment Status
            'O' => 16,  // Booking Status
            'P' => 20,  // Created At
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row styling
        $sheet->getStyle('A6:P6')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1F2937']],
            'fill' => [
                'fillType'   => 'solid',
                'startColor' => ['argb' => 'FFE5E7EB'],
            ],
            'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
            'borders' => [
                'bottom' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'CBD5E1']],
            ],
        ]);

        // Title area rows height
        $sheet->getRowDimension(1)->setRowHeight(36);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(18);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // ===== Big title (company name) =====
                $company = $this->companyName ?: config('app.name', 'CloudBite');
                $sheet->mergeCells('A1:P1');
                $sheet->setCellValue('A1', $company);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '111827']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // ===== Subtitle =====
                $sheet->mergeCells('A2:P2');
                $sheet->setCellValue('A2', 'Meal Plan Bookings Export');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // ===== Filter meta info =====
                $meta = sprintf(
                    'Status: %s   |   From: %s   |   To: %s   |   Generated: %s',
                    $this->status ?: 'All',
                    $this->dateFrom ?: '—',
                    $this->dateTo ?: '—',
                    now()->format('Y-m-d H:i')
                );
                $sheet->mergeCells('A3:P3');
                $sheet->setCellValue('A3', $meta);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Freeze header (row 6 is the header, so freeze pane A7)
                $sheet->freezePane('A7');

                // ===== Borders, auto-filter, and number formats =====
                $highestRow = $sheet->getHighestRow(); // header + data

                if ($highestRow >= 6) {
                    // Thin border around the table (header + data)
                    $sheet->getStyle("A6:P{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => [
                                'borderStyle' => 'thin',
                                'color' => ['rgb' => 'E5E7EB'],
                            ],
                        ],
                    ]);

                    if ($highestRow >= 7) {
                        // Date formats
                        $sheet->getStyle("D7:D{$highestRow}")
                            ->getNumberFormat()
                            ->setFormatCode('yyyy-mm-dd');

                        $sheet->getStyle("P7:P{$highestRow}")
                            ->getNumberFormat()
                            ->setFormatCode('yyyy-mm-dd hh:mm');

                        // Money formats (Plan Subtotal, Shipping, Grand, Paid Now, Due Amount)
                        $sheet->getStyle("G7:K{$highestRow}")
                            ->getNumberFormat()
                            ->setFormatCode('#,##0.00');
                    }

                    // AutoFilter on header row
                    $sheet->setAutoFilter("A6:P6");

                    // Totals row (SUM of Grand Total, Paid Now, Due Amount)
                    $totalsRow = $highestRow + 1;

                    // Label
                    $sheet->setCellValue("F{$totalsRow}", 'Totals:');

                    // Grand Total SUM
                    $sheet->setCellValue("I{$totalsRow}", "=SUM(I7:I{$highestRow})");
                    // Paid Now SUM
                    $sheet->setCellValue("J{$totalsRow}", "=SUM(J7:J{$highestRow})");
                    // Due Amount SUM
                    $sheet->setCellValue("K{$totalsRow}", "=SUM(K7:K{$highestRow})");

                    $sheet->getStyle("F{$totalsRow}:K{$totalsRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => [
                            'top' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'CBD5E1']],
                        ],
                    ]);

                    $sheet->getStyle("I{$totalsRow}:K{$totalsRow}")
                        ->getNumberFormat()
                        ->setFormatCode('#,##0.00');
                }
            },
        ];
    }

    public function drawings()
    {
        if (!is_string($this->logoPath) || !file_exists($this->logoPath)) {
            return [];
        }

        $drawing = new Drawing();
        $drawing->setName('Logo');
        $drawing->setDescription('Company Logo');
        $drawing->setPath($this->logoPath);
        $drawing->setHeight(48);
        $drawing->setCoordinates('A1');
        $drawing->setOffsetX(4);
        $drawing->setOffsetY(4);

        return [$drawing];
    }
}
