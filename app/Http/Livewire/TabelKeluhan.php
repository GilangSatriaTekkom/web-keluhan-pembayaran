<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\HistoriPembayaran;
use App\Models\Tiket;
use App\Models\Langganan;
use Illuminate\Support\Facades\Auth;

class TabelKeluhan extends Component
{
    public function render()
    {
        $userId = Auth::id();
        if (!$userId) {

            return redirect()->route('login');
        }

        $user = User::find($userId);
        $tiketPelanggan = Tiket::where('user_id', $userId)->get();
        $tiketAll = Tiket::all();

        return view('livewire.tabel-keluhan',
        [
            'tiket' =>   $user->role === 'pelanggan' ? $tiketPelanggan : $tiketAll,
            'users' => $user,
            'langganans' => Langganan::all(),
            'pembayarans' => HistoriPembayaran::all(),
            'tagihans' => Tagihan::all(),
        ]);
    }
}
