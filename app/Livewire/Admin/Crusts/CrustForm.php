<?php

namespace App\Livewire\Admin\Crusts;

use App\Models\Crust;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\support\Str;
use Livewire\Attributes\On;

class CrustForm extends Component
{
    use WithPagination;

    public $crustId = null;
    public $isView = false;
    public $existingImage = null;

    public $name = null, $price = null, $status = 'active';

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:crusts,name,' . $this->crustId,
            'price' => 'nullable|string',
            'status' => 'required|in:active,disable',
        ];
    }

    public function submit()
    {
        $this->validate();

        $payload = [
            'name'   => trim($this->name),
            'slug'   => Str::slug($this->name),
            'price'  => $this->price,
            'status' => $this->status ?: 'active',
        ];

        if ($this->crustId) {
            // UPDATE
            $crust = Crust::find($this->crustId);
            if (!$crust) {
                $this->dispatch('toast', type: 'error', message: 'Crust not found.');
                return;
            }

            // Check if there are any real changes
            $hasChanges = false;
            foreach ($payload as $k => $v) {
                if ($crust->{$k} !== $v) {
                    $hasChanges = true;
                    break;
                }
            }

            if (!$hasChanges) {
                $this->dispatch('toast', type: 'warning', message: 'Nothing to update.');
                $this->dispatch('crusts:refresh'); // was categories:refresh
                Flux::modal('crust-modal')->close();
                return;
            }

            // Persist
            $crust->fill($payload);
            $crust->save();

            $this->dispatch('toast', type: 'success', message: 'Crust updated successfully.');
        } else {
            // CREATE
            Crust::create($payload);

            // Reset form for next entry
            $this->reset(['crustId', 'name']);
            $this->status = 'active';

            $this->dispatch('toast', type: 'success', message: 'Crust created successfully.');
        }

        $this->dispatch('crusts:refresh');
        Flux::modal('crust-modal')->close();
    }

    #[On('open-crust-modal')]
    public function crustDetail($mode, $crust = null)
    {

        $this->isView = $mode === 'view';

        if ($mode === 'create') {

            $this->isView = false;
            $this->reset();
            $this->status = 'active';
        } else {
            // dd($category);
            $this->crustId = $crust['id'];

            $this->name = $crust['name'];
            $this->price = $crust['price'];
            $this->status = $crust['status'];
        }
    }

    public function render()
    {
        return view('livewire.admin.crusts.crust-form');
    }
}
