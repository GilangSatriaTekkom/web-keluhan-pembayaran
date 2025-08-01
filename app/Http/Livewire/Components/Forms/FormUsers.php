<?php

namespace App\Http\Livewire\Components\Forms;

use Livewire\Component;

class FormUsers extends Component
{
    public string $role = 'pelanggan';

    public function render()
    {
        return view('livewire.components.forms.form-users');
    }
}
