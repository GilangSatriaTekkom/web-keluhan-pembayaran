<?php

namespace App\Http\Livewire\Lihat;

use Livewire\Component;
use App\Models\User;
use App\Models\PaketInternet;
use Illuminate\Support\Facades\Auth;


class Pelanggan extends Component
{
    public $user;
    public $paketInternetOptions;

    public function mount($id)
    {
        $this->user = User::findOrFail($id)->toArray();
        $this->paketInternetOptions = PaketInternet::all(); // Ambil semua paket internet
    }

    public function updatePelanggan()
    {
        User::findOrFail($this->user['id'])->update($this->user);
        session()->flash('status', 'Data pelanggan berhasil diperbarui');
    }
    public function render()
    {
        return view('livewire.lihat.pelanggan');
    }
}
