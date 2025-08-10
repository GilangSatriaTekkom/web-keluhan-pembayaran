<?php

namespace App\Http\Livewire\Lihat;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;


class Karyawan extends Component
{
    public $user;
    public $name;
    public $email;
    public $alamat;
    public $user_id;
    public $phone;
    public $status;

    public function updateEdit() {
         LivewireAlert::title("Perhatian")
            ->text("Apakah Anda yakin ingin memperbarui data karyawan ini?")
            ->question()
            ->asConfirm()
            ->onConfirm("updateUser")
            ->show();
    }

    public function updateUser()
    {
        User::findOrFail($this->user_id)->update([
            'name'   => $this->name,
            'email'  => $this->email,
            'alamat' => $this->alamat,
            'phone'  => $this->phone,
            'status' => $this->status,
        ]);
        LivewireAlert::title("Data Berhasil diperbarui")
            ->success()
            ->onConfirm("redirectToLihatKaryawan")
            ->withConfirmButton('Ok!')
            ->show();
    }

    public function redirectToLihatKaryawan()
    {
        return redirect()->route('karyawan.lihat', ['id' => $this->user_id]);
    }

    public function mount($id)
    {
        $user = User::findOrFail($id);
        $this->user_id = $user->id;
        $this->name    = $user->name;
        $this->email   = $user->email;
        $this->alamat  = $user->location;
        $this->phone   = $user->phone;
        $this->status  = $user->status;
        $this->user    = $user;
    }

    public function render()
    {
        return view('livewire.lihat.karyawan');
    }
}
