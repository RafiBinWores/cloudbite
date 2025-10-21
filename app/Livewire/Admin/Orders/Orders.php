<?php

namespace App\Livewire\Admin\Orders;

use App\Exports\OrdersExport;
use App\Models\CompanyInfo;
use App\Models\Order;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf as DomPDF;
use Illuminate\Support\Facades\Storage;

class Orders extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    // ADD: new filters
    #[Url(history: true)]
    public ?string $status = null;

    #[Url(history: true)]
    public ?string $dateFrom = null;

    #[Url(history: true)]
    public ?string $dateTo = null;


    #[On('orders:refresh')]
    public function refreshList()
    {
        $this->resetPage();
    }

    // Reset pagination when any of these change
    public function updatedSearch()
    {
        $this->resetPage();
    }
    public function updatedPerPage()
    {
        $this->resetPage();
    }

    public function updatedStatus()
    {
        $this->resetPage();
    }
    public function updatedDateFrom()
    {
        $this->resetPage();
    }
    public function updatedDateTo()
    {
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->status   = null;
        $this->dateFrom = null;
        $this->dateTo   = null;

        $this->search = '';

        $this->resetPage();
    }


    protected function companyExportMeta(): array
    {
        $info = CompanyInfo::query()->first();

        $companyName = $info->name ?? config('app.name', 'CloudBite');
        $logoField   = $info->logo ?? null;

        $logoPath = null;

        if ($logoField) {
            // CASE A: stored as 'public' disk path like 'uploads/logo.png'
            if (Storage::disk('public')->exists($logoField)) {
                $logoPath = Storage::disk('public')->path($logoField);
            }
            // CASE B: stored as absolute local path (rare, but just in case)
            elseif (is_string($logoField) && file_exists($logoField)) {
                $logoPath = $logoField;
            }
            // CASE C: stored as full URL => (optional) try to copy to temp for Excel/PDF
            // Uncomment if you want to support external URLs directly:
            /*
        elseif (filter_var($logoField, FILTER_VALIDATE_URL)) {
            try {
                $bytes = file_get_contents($logoField); // or Http::get($logoField)->body();
                if ($bytes !== false) {
                    $tmp = storage_path('app/tmp');
                    if (!is_dir($tmp)) @mkdir($tmp, 0775, true);
                    $tmpPath = $tmp.'/company_logo_'.uniqid().'.png';
                    file_put_contents($tmpPath, $bytes);
                    $logoPath = $tmpPath;
                }
            } catch (\Throwable $e) {}
        }
        */
        }

        return [$companyName, $logoPath];
    }

    public function exportExcel()
    {
        [$companyName, $logoPath] = $this->companyExportMeta();

        return Excel::download(
            new OrdersExport(
                status: $this->status,
                dateFrom: $this->dateFrom,
                dateTo: $this->dateTo,
                search: $this->search,
                companyName: $companyName,
                logoPath: $logoPath,
            ),
            'orders_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    public function exportPdf()
    {
        [$companyName, $logoPath] = $this->companyExportMeta();

        $orders = \App\Models\Order::search($this->search)
            ->when($this->status, fn($q) => $q->where('order_status', $this->status))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'DESC')
            ->limit(5000)
            ->get();

        $pdf = DomPDF::loadView('livewire.admin.orders.exports.orders-pdf', [
            'orders'     => $orders,
            'status'     => $this->status,
            'dateFrom'   => $this->dateFrom,
            'dateTo'     => $this->dateTo,
            'companyName' => $companyName,
            'logoPath'   => $logoPath,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'orders_' . now()->format('Y-m-d_His') . '.pdf');
    }


    public function render()
    {
        $orders = Order::search($this->search)
            ->when($this->status, fn($q) => $q->where('order_status', $this->status))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'DESC')
            ->paginate($this->perPage);

        return view('livewire.admin.orders.orders', compact('orders'));
    }
}
