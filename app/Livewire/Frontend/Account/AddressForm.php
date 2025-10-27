<?php

namespace App\Livewire\Frontend\Account;

use App\Models\Address;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class AddressForm extends Component
{
    use WithTcToast;

    public string $label = 'home';

    public ?float $lat = null;
    public ?float $lng = null;
    public string $address = '';
    public string $city = 'Dhaka';
    public string $postcode = '';

    public ?string $customer_note = null;
    public bool $save_as_default = true;

    public ?string $contact_name = null;
    public ?string $contact_phone = null;

    public ?int $addressId = null;

    public function mount(?string $label = null): void
    {
        if ($label) $this->label = $label;
        $this->loadAddressForLabel();
    }

    public function updatedLabel(): void
    {
        $this->loadAddressForLabel();
        $this->resetValidation();
        // Optional: tell Alpine you switched labels
        $this->dispatch('address:label-switched');
    }

    private function loadAddressForLabel(): void
{
    $existing = Address::where('user_id', Auth::id())
        ->where('label', $this->label)
        ->first();

    if ($existing) {
        $this->addressId     = $existing->id;
        $this->address       = (string)($existing->address ?? '');
        $this->city          = (string)($existing->city ?? 'Dhaka');
        $this->postcode      = (string)($existing->postal_code ?? '');
        $this->lat           = $existing->lat;
        $this->lng           = $existing->lng;
        $this->contact_name  = $existing->contact_name;
        $this->contact_phone = $existing->contact_phone;
    } else {
        $this->reset(
            'addressId',
            'address',
            'city',
            'postcode',
            'lat',
            'lng',
            'contact_name',
            'contact_phone',
            'customer_note'
        );
        $this->city = 'Dhaka'; // keep your default
    }
}


    public function save(): void
    {
        $data = $this->validate([
            'label'         => ['required', Rule::in(['home','workplace','others'])],
            'address'       => ['required', 'string', 'max:255'],
            'city'          => ['required', 'string', 'max:100'],
            'postcode'      => ['nullable', 'string', 'max:20'],
            'lat'           => ['nullable', 'numeric', 'between:-90,90'],
            'lng'           => ['nullable', 'numeric', 'between:-180,180'],
            'contact_name'  => ['nullable', 'string', 'max:100'],
            'contact_phone' => ['nullable', 'string', 'max:30'],
            'customer_note' => ['nullable', 'string', 'max:200'],
            'save_as_default' => ['boolean'],
        ]);

        // Upsert by (user_id, label) => one row per label for each user
        $row = Address::updateOrCreate(
            ['user_id' => Auth::id(), 'label' => $data['label']],
            [
                'address'      => $data['address'],
                'city'         => $data['city'],
                'postal_code'  => $data['postcode'] ?: null,
                'lat'          => $data['lat'] ?? null,
                'lng'          => $data['lng'] ?? null,
                'contact_name' => $data['contact_name'] ?? null,
                'contact_phone'=> $data['contact_phone'] ?? null,
                // keep defaults like country/state/area if you want to extend later
            ]
        );

        $this->addressId = $row->id;

        $this->success(
            title: 'Address saved successfully',
            position: 'top-right',
            showProgress: true,
            showCloseIcon: true,
        );
    }

    public function render()
    {
        $existing = Address::where('user_id', Auth::id())->pluck('label')->all();
        $all = ['home','workplace','others'];
        $available = array_values(array_diff($all, $existing));

        return view('livewire.frontend.account.address-form', [
            'availableLabels' => $available,
            'hasCurrent'      => in_array($this->label, $existing, true),
        ]);
    }
}
