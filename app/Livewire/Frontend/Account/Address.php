<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Address as ModelsAddress;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class Address extends Component
{
    public $addresses;

    public function mount(): void
    {
        $this->loadAddresses();
    }

    private function loadAddresses(): void
    {
        $this->addresses = ModelsAddress::query()
            ->where('user_id', Auth::id())
            ->latest('id')
            ->get();
    }

    public function deleteAddress(int $id): void
    {
        $addr = ModelsAddress::where('user_id', Auth::id())->find($id);
        if (! $addr) return;

        // optional guard – remove if you want to allow deleting the last one
        // if (ModelsAddress::where('user_id', Auth::id())->count() <= 1) {
        //     $this->addError('address', 'You must keep at least one address.');
        //     return;
        // }

        $addr->delete();
        $this->loadAddresses();
    }


    public function render()
    {
        return view('livewire.frontend.account.address', [
            'addresses' => $this->addresses,
        ]);
    }
}
