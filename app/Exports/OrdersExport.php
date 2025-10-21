<?php

namespace App\Exports;

use App\Models\Order;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithDrawings;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;

class OrdersExport implements
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
        public ?string $companyName = null,   // passed from controller (CompanyInfo)
        public ?string $logoPath    = null    // local file path resolved in controller
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
        return Order::search($this->search)
            ->when($this->status, fn($q) => $q->where('order_status', $this->status))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest('id');
    }

    public function headings(): array
    {
        return [
            'SL',
            'Order ID',
            'Order Date',
            'Customer',
            'Phone',
            'Total',
            'Payment',
            'Status',
        ];
    }

    public function map($o): array
    {
        return [
            ++$this->rowNum,                                 // SL
            $o->order_code,
            optional($o->created_at)?->format('Y-m-d H:i'),  // will also style format in AfterSheet
            $o->contact_name,
            $o->phone,
            (float) $o->grand_total,                         // keep numeric for SUM and number format
            ucfirst($o->payment_status),
            $o->order_status,
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,   // SL
            'B' => 18,  // Order ID
            'C' => 20,  // Date
            'D' => 28,  // Customer
            'E' => 18,  // Phone
            'F' => 14,  // Total
            'G' => 14,  // Payment
            'H' => 20,  // Status
        ];
    }

    public function styles(Worksheet $sheet)
    {
        // Header row
        $sheet->getStyle('A6:H6')->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => '1F2937']],
            'fill' => [
                'fillType' => 'solid',
                'startColor' => ['argb' => 'FFE5E7EB'],
            ],
            'alignment' => ['horizontal' => 'left', 'vertical' => 'center'],
            'borders' => [
                'bottom' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'CBD5E1']]
            ],
        ]);

        // Title rows size
        $sheet->getRowDimension(1)->setRowHeight(36);
        $sheet->getRowDimension(2)->setRowHeight(22);
        $sheet->getRowDimension(3)->setRowHeight(18);
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                // Big title (company name)
                $company = $this->companyName ?: config('app.name', 'CloudBite');
                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', $company);
                $sheet->getStyle('A1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 18, 'color' => ['rgb' => '111827']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Subtitle
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', 'Orders Export');
                $sheet->getStyle('A2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 12],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Filter info
                $meta = sprintf(
                    'Status: %s   |   From: %s   |   To: %s   |   Generated: %s',
                    $this->status ?: 'All',
                    $this->dateFrom ?: '—',
                    $this->dateTo ?: '—',
                    now()->format('Y-m-d H:i')
                );
                $sheet->mergeCells('A3:H3');
                $sheet->setCellValue('A3', $meta);
                $sheet->getStyle('A3')->applyFromArray([
                    'font' => ['size' => 10, 'color' => ['rgb' => '6B7280']],
                    'alignment' => ['horizontal' => 'center', 'vertical' => 'center'],
                ]);

                // Freeze header (row 6 is the header, so freeze pane A7)
                $sheet->freezePane('A7');

                // Apply borders to data area and formats
                $highestRow = $sheet->getHighestRow(); // last row with data (includes header)
                if ($highestRow >= 6) {
                    // Borders all around
                    $sheet->getStyle("A6:H{$highestRow}")->applyFromArray([
                        'borders' => [
                            'allBorders' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'E5E7EB']]
                        ],
                    ]);

                    // Date format for column C (Order Date) => rows 7..highest
                    if ($highestRow >= 7) {
                        $sheet->getStyle("C7:C{$highestRow}")
                              ->getNumberFormat()
                              ->setFormatCode('yyyy-mm-dd hh:mm');
                        // Total format for column F
                        $sheet->getStyle("F7:F{$highestRow}")
                              ->getNumberFormat()
                              ->setFormatCode('#,##0.00');
                    }

                    // AutoFilter on header row
                    $sheet->setAutoFilter("A6:H6");

                    // Totals row (SUM of Total in column F)
                    $totalsRow = $highestRow + 1;
                    $sheet->setCellValue("E{$totalsRow}", 'Total:');
                    $sheet->setCellValue("F{$totalsRow}", "=SUM(F7:F{$highestRow})");
                    $sheet->getStyle("E{$totalsRow}:F{$totalsRow}")->applyFromArray([
                        'font' => ['bold' => true],
                        'borders' => [
                            'top' => ['borderStyle' => 'thin', 'color' => ['rgb' => 'CBD5E1']],
                        ],
                    ]);
                    $sheet->getStyle("F{$totalsRow}")
                          ->getNumberFormat()
                          ->setFormatCode('#,##0.00');
                }
            }
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
