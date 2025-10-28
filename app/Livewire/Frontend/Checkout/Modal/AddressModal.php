<?php

namespace App\Livewire\Frontend\Checkout\Modal;

use Illuminate\Support\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class AddressModal extends Component
{
        public Collection $addresses;

    public ?int $selectedAddressId = null; // current selection in checkout
    public ?int $tempId = null;            // pick inside modal
    public bool $open = false;             // modal visibility

    public function mount(Collection $addresses, ?int $selectedAddressId = null): void
    {
        $this->addresses = $addresses;
        $this->selectedAddressId = $selectedAddressId;
        $this->tempId = $selectedAddressId;
    }

    #[On('open-address-modal')]
    public function openModal(?int $selectedId = null): void
    {
        $this->tempId = $selectedId ?? $this->selectedAddressId;
        $this->open = true;
    }

    public function closeModal(): void
    {
        $this->open = false;
    }

    public function select(): void
    {
        if (!$this->tempId) return;

        $this->dispatch('address-selected', id: (int) $this->tempId);

        $this->selectedAddressId = (int)$this->tempId;
        $this->open = false;
    }
    
    public function render()
    {
        return view('livewire.frontend.checkout.modal.address-modal');
    }
}
