<?php

namespace App\Livewire\Admin\Buns;

use App\Models\Bun;
use Flux\Flux;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Str;
use Livewire\Attributes\On;

class BunForm extends Component
{
    use WithPagination;

    public $bunId = null;
    public $isView = false;
    public $existingImage = null;

    public $name = null, $status = 'active';

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:buns,name,' . $this->bunId,
            'status' => 'required|in:active,disable',
        ];
    }

    public function submit()
    {
        $this->validate();

        $payload = [
            'name'   => trim($this->name),
            'slug'   => Str::slug($this->name),
            'status' => $this->status ?: 'active',
        ];

        if ($this->bunId) {
            // UPDATE
            $bun = Bun::find($this->bunId);
            if (!$bun) {
                $this->dispatch('toast', type: 'error', message: 'bun not found.');
                return;
            }

            // Check if there are any real changes
            $hasChanges = false;
            foreach ($payload as $k => $v) {
                if ($bun->{$k} !== $v) {
                    $hasChanges = true;
                    break;
                }
            }

            if (!$hasChanges) {
                $this->dispatch('toast', type: 'warning', message: 'Nothing to update.');
                $this->dispatch('buns:refresh');
                Flux::modal('bun-modal')->close();
                return;
            }

            // Persist
            $bun->fill($payload);
            $bun->save();

            $this->dispatch('toast', type: 'success', message: 'Bun updated successfully.');
        } else {
            // CREATE
            bun::create($payload);

            // Reset form for next entry
            $this->reset(['bunId', 'name']);
            $this->status = 'active';

            $this->dispatch('toast', type: 'success', message: 'Bun created successfully.');
        }

        $this->dispatch('buns:refresh');
        Flux::modal('bun-modal')->close();
    }

    #[On('open-bun-modal')]
    public function bunDetail($mode, $bun = null)
    {

        $this->isView = $mode === 'view';

        if ($mode === 'create') {

            $this->isView = false;
            $this->reset();
            $this->status = 'active';
        } else {
            // dd($category);
            $this->bunId = $bun['id'];

            $this->name = $bun['name'];
            $this->status = $bun['status'];
        }
    }

    public function render()
    {
        return view('livewire.admin.buns.bun-form');
    }
}
