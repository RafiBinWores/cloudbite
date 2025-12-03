<?php

namespace App\Livewire\Admin\Orders;

use App\Exports\MealPlanBookingsExport;
use App\Models\CompanyInfo;
use App\Models\MealPlanBooking as ModelsMealPlanBooking;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;

class MealPlanBooking extends Component
{
    use WithPagination;

    #[Url(history: true)]
    public $search = '';

    #[Url(history: true)]
    public $perPage = 10;

    #[Url(history: true)]
    public ?string $status = null;   // pending | confirmed | ongoing | completed | cancelled

    #[Url(history: true)]
    public ?string $dateFrom = null;

    #[Url(history: true)]
    public ?string $dateTo = null;

    #[On('meal-plan-bookings:refresh')]
    public function refreshList()
    {
        $this->resetPage();
    }

    /* ========= Reset pagination when filters change ========= */

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

    /* ========= Clear filters ========= */

    public function clearFilters(): void
    {
        $this->status   = null;
        $this->dateFrom = null;
        $this->dateTo   = null;
        $this->search   = '';

        $this->resetPage();
    }

    /* ========= Company meta for export (same pattern as Orders) ========= */

    protected function companyExportMeta(): array
    {
        $info = CompanyInfo::query()->first();

        $companyName = $info->name ?? config('app.name', 'CloudBite');
        $logoField   = $info->logo ?? null;

        $logoPath = null;

        if ($logoField) {
            // A) stored on 'public' disk (e.g. uploads/logo.png)
            if (Storage::disk('public')->exists($logoField)) {
                $logoPath = Storage::disk('public')->path($logoField);
            }
            // B) absolute local path
            elseif (is_string($logoField) && file_exists($logoField)) {
                $logoPath = $logoField;
            }
            // C) URL case: you can copy the logic from Orders if you want to support it
        }

        return [$companyName, $logoPath];
    }

    /* ========= Export Excel ========= */

    public function exportExcel()
    {
        [$companyName, $logoPath] = $this->companyExportMeta();

        return Excel::download(
            new MealPlanBookingsExport(
                status: $this->status,
                dateFrom: $this->dateFrom,
                dateTo: $this->dateTo,
                search: $this->search,
                companyName: $companyName,
                logoPath: $logoPath,
            ),
            'meal_plan_bookings_' . now()->format('Y-m-d_His') . '.xlsx'
        );
    }

    /* ========= Export PDF ========= */

    public function exportPdf()
    {
        [$companyName, $logoPath] = $this->companyExportMeta();

        $bookings = \App\Models\MealPlanBooking::query()
            ->when($this->search, function ($q) {
                $s = '%' . $this->search . '%';
                $q->where(function ($sub) use ($s) {
                    $sub->where('booking_code', 'like', $s)
                        ->orWhere('contact_name', 'like', $s)
                        ->orWhere('phone', 'like', $s)
                        ->orWhere('email', 'like', $s);
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'DESC')
            ->limit(5000)
            ->get();

        $pdf = Pdf::loadView('livewire.admin.orders.exports.bookings-pdf', [
            'bookings'    => $bookings,
            'status'      => $this->status,
            'dateFrom'    => $this->dateFrom,
            'dateTo'      => $this->dateTo,
            'companyName' => $companyName,
            'logoPath'    => $logoPath,
        ])->setPaper('a4', 'portrait');

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, 'meal_plan_bookings_' . now()->format('Y-m-d_His') . '.pdf');
    }

    protected function stats(): array
    {
        $base = ModelsMealPlanBooking::query();

        return [
            'pending'   => (clone $base)->where('status', 'pending')->count(),
            'confirmed' => (clone $base)->where('status', 'confirmed')->count(),
            'ongoing'   => (clone $base)->where('status', 'ongoing')->count(),
            'completed' => (clone $base)->where('status', 'completed')->count(),
            'cancelled' => (clone $base)->where('status', 'cancelled')->count(),
        ];
    }

    public function render()
    {
        $bookings = ModelsMealPlanBooking::query()
            ->when($this->search, function ($q) {
                $s = '%' . $this->search . '%';
                $q->where(function ($sub) use ($s) {
                    $sub->where('booking_code', 'like', $s)
                        ->orWhere('contact_name', 'like', $s)
                        ->orWhere('phone', 'like', $s)
                        ->orWhere('email', 'like', $s);
                });
            })
            ->when($this->status, fn($q) => $q->where('status', $this->status))
            ->when($this->dateFrom, fn($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo,   fn($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->orderBy('created_at', 'DESC')
            ->paginate($this->perPage);

        return view('livewire.admin.orders.meal-plan-booking', [
            'bookings' => $bookings,
            'stats'    => $this->stats(),
        ]);
    }
}
