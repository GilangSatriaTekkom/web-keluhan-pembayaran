<?php

namespace App\Http\Livewire\Create;

use Livewire\Component;

class Karyawan extends Component
{
    public $user = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'location' => '',
        'about' => '',
        'password' => '',
        'role' => '',
        'status' => '',
        'tgl_daftar' => '',
    ];

    protected $rules = [
        'user.name' => 'required|string|max:255',
        'user.email' => 'required|email|unique:users,email',
        'user.phone' => 'nullable|string|max:20',
        'user.location' => 'nullable|string|max:255',
        'user.about' => 'nullable|string',
        'user.password' => 'required|string|min:6',
        'user.role' => 'required|string',
        'user.status' => 'required|string',
        'user.tgl_daftar' => 'required|date',
    ];

    public function submit()
    {
        $this->validate();

        User::create([
            'name' => $this->user['name'],
            'email' => $this->user['email'],
            'phone' => $this->user['phone'],
            'location' => $this->user['location'],
            'about' => $this->user['about'],
            'password' => Hash::make($this->user['password']),
            'role' => $this->user['role'],
            'status' => $this->user['status'],
            'tgl_daftar' => $this->user['tgl_daftar'],
        ]);

        session()->flash('status', 'User berhasil ditambahkan.');

        $this->reset('user');
    }

    public function render()
    {

        return view('livewire.create.karyawan');
    }
}
