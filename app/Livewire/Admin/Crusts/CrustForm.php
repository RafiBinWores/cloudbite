<?php

namespace App\Livewire\Admin\Crusts;

use App\Models\Crust;
use Developermithu\Tallcraftui\Traits\WithTcToast;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\support\Str;
use Livewire\Attributes\On;

class CrustForm extends Component
{
    use WithPagination;
    use WithTcToast;

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
                $this->warning(
                    title: 'Could not found!',
                    position: 'top-right',
                    showProgress: true,
                    showCloseIcon: true,
                );
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
                $this->warning(
                    title: 'Noting found!',
                    position: 'top-right',
                    showProgress: true,
                    showCloseIcon: true,
                );
                $this->dispatch('crusts:refresh');
                Flux::modal('crust-modal')->close();
                return;
            }

            // Persist
            $crust->fill($payload);
            $crust->save();

            $this->success(
                title: 'Crust updated successfully.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
        } else {
            // CREATE
            Crust::create($payload);

            // Reset form for next entry
            $this->reset(['crustId', 'name']);
            $this->status = 'active';

            $this->success(
                title: 'Crust created successfully.',
                position: 'top-right',
                showProgress: true,
                showCloseIcon: true,
            );
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
