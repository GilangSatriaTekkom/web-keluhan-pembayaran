<?php

namespace App\Http\Livewire\Lihat;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class Karyawan extends Component
{
    public $user;

    public function mount($id)
    {
        $this->user = User::findOrFail($id)->toArray();
    }

    public function updateUser()
    {
        User::findOrFail($this->user['id'])->update($this->user);
        session()->flash('status', 'Data karyawan berhasil diperbarui');
    }
    public function render()
    {
        return view('livewire.lihat.karyawan');
    }
}
