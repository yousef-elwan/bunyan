<?php

namespace App\Livewire;

use Livewire\Component;

class UserAvatarDropdown extends Component
{
    protected $listeners = ['userLoggedIn' => '$refresh', 'userLoggedOut' => '$refresh'];

    public function render()
    {
        return view('livewire.user-avatar-dropdown');
    }
}
