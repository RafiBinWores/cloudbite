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

    public ?string $note = null;
    public bool $save_as_default = true;

    public ?string $contact_name = null;
    public ?string $contact_phone = null;
    public ?string $contact_country = 'BD';

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

        $this->dispatch('address:label-switched', phone: $this->contact_phone, country: $this->contact_country);
    }

    private function loadAddressForLabel(): void
    {
        $existing = Address::where('user_id', Auth::id())
            ->where('label', $this->label)
            ->first();

        if ($existing) {
            $this->addressId       = $existing->id;
            $this->address         = (string)($existing->address ?? '');
            $this->city            = (string)($existing->city ?? 'Dhaka');
            $this->postcode        = (string)($existing->postal_code ?? '');
            $this->lat             = $existing->lat;
            $this->lng             = $existing->lng;
            $this->contact_name    = $existing->contact_name;
            $this->contact_phone   = $existing->contact_phone;
            $this->note            = $existing->note;

            if (property_exists($existing, 'contact_country')) {
                $this->contact_country = $existing->contact_country ?: $this->contact_country;
            }
        } else {
            $this->reset('addressId','address','city','postcode','lat','lng','contact_name','contact_phone','note');
            $this->city = 'Dhaka';
            $this->contact_country = $this->contact_country ?: 'BD';
        }
    }

    protected function rules(): array
    {
        return [
            'label'           => ['required', Rule::in(['home', 'workplace', 'others'])],
            'address'         => ['required', 'string', 'max:255'],
            'city'            => ['required', 'string', 'max:100'],
            'postcode'        => ['nullable', 'string', 'max:20'],
            'lat'             => ['nullable', 'numeric', 'between:-90,90'],
            'lng'             => ['nullable', 'numeric', 'between:-180,180'],
            'contact_name'    => ['required', 'string', 'max:100'],
            'contact_phone'   => ['required', 'string', 'max:30'],
            'contact_country' => ['nullable', 'string', 'size:2'],
            'note'            => ['nullable', 'string', 'max:200'],
            'save_as_default' => ['boolean'],
        ];
    }

    public function save(): void
    {
        $data = $this->validate();

        // Upsert by (user_id, label)
        $row = Address::updateOrCreate(
            ['user_id' => Auth::id(), 'label' => $data['label']],
            [
                'address'         => $data['address'],
                'city'            => $data['city'],
                'postal_code'     => $data['postcode'] ?: null,
                'lat'             => $data['lat'] ?? null,
                'lng'             => $data['lng'] ?? null,
                'contact_name'    => $data['contact_name'] ?? null,
                'contact_phone'   => $data['contact_phone'] ?? null,
                'contact_country' => $data['contact_country'] ?? 'BD',
                'note'            => $data['note'] ?? null,
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
        $existingLabels = Address::where('user_id', Auth::id())->pluck('label')->all();
        $all = ['home', 'workplace', 'others'];
        $available = array_values(array_diff($all, $existingLabels));

        return view('livewire.frontend.account.address-form', [
            'availableLabels' => $available,
            'hasCurrent'      => in_array($this->label, $existingLabels, true),
        ]);
    }
}
