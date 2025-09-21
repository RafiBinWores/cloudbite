<?php

namespace App\Livewire\Admin\AddOns;

use App\Models\AddOn;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class AddOnForm extends Component
{
    use WithPagination;
    use WithTcToast;

    public $addOnId = null;
    public $isView = false;

    public $name = null, $price = null, $status = 'active';

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:add_ons,name,' . $this->addOnId,
            'price' => 'required|string',
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

        if ($this->addOnId) {
            // UPDATE
            $addOn = AddOn::find($this->addOnId);
            if (!$addOn) {
                $this->error(
                    title: 'Add-ons not found!',
                    position: 'top-right',
                    showProgress: true,
                    showCloseIcon: true,
                );
                return;
            }

            // Check if there are any real changes
            $hasChanges = false;
            foreach ($payload as $k => $v) {
                if ($addOn->{$k} !== $v) {
                    $hasChanges = true;
                    break;
                }
            }

            if (!$hasChanges) {
                $this->warning(
                    title: 'Noting to update.',
                    position: 'top-right',
                    showProgress: true,
                    showCloseIcon: true,
                );
                $this->dispatch('addOns:refresh');
                Flux::modal('addOns-modal')->close();
                return;
            }

            // Persist
            $addOn->fill($payload);
            $addOn->save();

            $this->success(
                title: 'Add-ons updated successfully.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        } else {
            // CREATE
            AddOn::create($payload);

            // Reset form for next entry
            $this->reset(['addOnId', 'name']);
            $this->status = 'active';

            $this->success(
                title: 'Add-ons created successfully.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        }

        $this->dispatch('addOns:refresh');
        Flux::modal('addOn-modal')->close();
    }

    #[On('open-addOn-modal')]
    public function addOnsDetail($mode, $addOn = null)
    {

        $this->isView = $mode === 'view';

        if ($mode === 'create') {

            $this->isView = false;
            $this->reset();
            $this->status = 'active';
        } else {
            $this->addOnId = $addOn['id'];

            $this->name = $addOn['name'];
            $this->price = $addOn['price'];
            $this->status = $addOn['status'];
        }
    }

    public function render()
    {
        return view('livewire.admin.add-ons.add-on-form');
    }
}
