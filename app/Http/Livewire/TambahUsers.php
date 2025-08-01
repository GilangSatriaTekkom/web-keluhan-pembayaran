<?php

namespace App\Http\Livewire;

use Livewire\Component;

class TambahUsers extends Component
{
    public $name, $email, $password, $phone, $location, $role;

    protected $rules = [
        'name'     => 'required|string|max:255',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|min:6',
        'phone'    => 'nullable|string|max:20',
        'location' => 'nullable|string|max:255',
        'role'     => 'required|in:admin,pelanggan',
    ];

    public function submit()
    {
        $this->validate();

        User::create([
            'name'       => $this->name,
            'email'      => $this->email,
            'password'   => Hash::make($this->password),
            'phone'      => $this->phone,
            'location'   => $this->location,
            'role'       => $this->role,
            'status'     => 'aktif',
            'tgl_daftar' => now(),
        ]);

        session()->flash('success', 'User berhasil ditambahkan.');

        // Reset input setelah submit
        $this->reset(['name', 'email', 'password', 'phone', 'location', 'role']);

        // Emit event untuk menutup modal dari JS (jika kamu pakai JS)
        $this->dispatchBrowserEvent('user-created');
    }

    public function render()
    {
        return view('livewire.tambah-users');
    }
}
