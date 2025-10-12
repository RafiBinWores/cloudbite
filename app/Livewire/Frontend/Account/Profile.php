<?php

namespace App\Livewire\Frontend\Account;

use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.frontend')]
class Profile extends Component
{
    public function render()
    {
        return view('livewire.frontend.account.profile');
    }
}
