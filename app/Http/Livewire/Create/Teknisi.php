<?php

namespace App\Http\Livewire\Create;

use Livewire\Component;
use App\Models\User;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Teknisi extends Component
{

    public $user = [
        'name' => '',
        'email' => '',
        'phone' => '',
        'location' => '',
        'password' => '',
        'role' => 'teknisi',
        'status' => 'aktif',
    ];

    protected $rules = [
        'user.name' => 'required|string|max:255',
        'user.email' => 'required|email|unique:users,email',
        'user.phone' => 'nullable|string|max:20',
        'user.location' => 'nullable|string|max:255',
        'user.password' => 'required|string|min:6',
        'user.role' => 'required|string',
        'user.status' => 'required|string',
    ];

    public function confirmSubmit()
    {

        LivewireAlert::title('Konfirmasi')
            ->text('Yakin buat Teknisi?')
            ->question()
            ->asConfirm()
            ->onConfirm('submit')
            ->onDeny('onDenyHandler')
            ->show();
    }

    public function onDenyHandler()
    {
        LivewireAlert::title('Informasi')
            ->text('Pembuatan Teknisi dibatalkan')
            ->info()
            ->show();
    }

    public function submit()
    {
        try {
            $this->validate();

       $user = User::create([
            'name' => $this->user['name'],
            'email' => $this->user['email'],
            'phone' => $this->user['phone'],
            'location' => $this->user['location'],
            'password' => $this->user['password'],
            'role' => $this->user['role'],
            'status' => $this->user['status'],
        ]);

            LivewireAlert::title('Sukses')
                ->text('Teknisi berhasil dibuat!')
                ->success()
                ->withConfirmButton('OK')
                ->onConfirm('returnToList')
                ->show();

            $this->reset('user');
        } catch (\Exception $e) {
            LivewireAlert::title('Error')
                ->text('Terjadi kesalahan: ' . $e->getMessage())
                ->error()
                ->show();
        }
    }

     public function returnToList()
    {
        return redirect()->route('teknisi');
    }


    public function render()
    {
        return view('livewire.create.teknisi');
    }
}
