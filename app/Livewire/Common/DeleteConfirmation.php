<?php

namespace App\Livewire\Common;

use Livewire\Attributes\On;
use Livewire\Component;

class DeleteConfirmation extends Component
{
    public $id = null;
    public $dispatchAction = null;
    public $modalName = 'delete-confirmation-modal';
    public $heading = null;
    public $message = null;

    public function render()
    {
        return view('livewire.common.delete-confirmation');
    }

    #[On('confirm-delete')]
    public function deleteConfirm($id, $dispatchAction, $modalName, $heading, $message)
    {
        $this->id = $id;
        $this->dispatchAction = $dispatchAction;
        $this->modalName = $modalName;
        $this->heading = $heading;
        $this->message = $message;
    }

    public function delete()
    {
        $this->dispatch($this->dispatchAction, id: $this->id, modalName: $this->modalName);
    }
}
