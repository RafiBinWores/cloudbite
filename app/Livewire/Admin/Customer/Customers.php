<?php

namespace App\Livewire\Admin\Customer;

use App\Enums\UserRole;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class Customers extends Component
{
    use WithPagination;

    public string $search = '';
    public int $perPage = 15;

    public ?array $orderStatuses = null;

    protected $queryString = [
        'search'  => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }
    public function updatingPerPage(): void
    {
        $this->resetPage();
    }

    public function render()
    {
        $includeStatuses = $this->orderStatuses ?: [
            'pending',
            'processing',
            'confirmed',
            'preparing',
            'out_for_delivery',
            'delivered',
        ];

        $customers = User::query()
            ->where('role', UserRole::User)
            ->when($this->search, function ($q) {
                $q->where(function ($q) {
                    $q->where('name', 'like', "%{$this->search}%")
                        ->orWhere('email', 'like', "%{$this->search}%")
                        ->orWhere('phone', 'like', "%{$this->search}%");
                });
            })
            ->withCount([
                'orders as orders_count' => fn($q) => $q->whereIn('order_status', $includeStatuses),
            ])
            ->withSum([
                'orders as orders_amount' => fn($q) => $q->whereIn('order_status', $includeStatuses),
            ], 'grand_total')
            ->orderByDesc('orders_amount')
            ->orderBy('name')

            // paginate!
            ->paginate($this->perPage);

        return view('livewire.admin.customer.customers', compact('customers'));
    }
}
