<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class LoginModal extends Component
{
    public $name;
    public $email;
    public $password;
    public $password_confirmation;
    public $role = 'pelanggan';
    public $status = 'aktif';

    public function submitAkun()
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Simpan user baru
        User::create([
            'name' => $this->name,
            'email' => $this->email,
            'password' => $this->password,
            'role' => $this->role,
            'status' => $this->status,
        ]);

        // Reset form
        $this->reset(['name', 'email', 'password', 'password_confirmation']);

        // Tutup modal via event Livewire
        $this->dispatch('buatAkun', ['id' => 'buatAkun']);
    }

    public function render()
    {
        return view('livewire.components.login-modal');
    }
}
