<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\HistoriPembayaran;
use App\Models\Langganan;
use Illuminate\Support\Facades\Auth;

class TabelPembayaran extends Component
{
    public function render()
    {
         $userId = Auth::id();
        if (!$userId) {

            return redirect()->route('login');
        }

        return view('livewire.tabel-pembayaran',

        [
            'pembayarans' => HistoriPembayaran::all(),
            'tagihans' => Tagihan::where('user_id', $userId)->get(),
            'langganans' => Langganan::all(),
        ]
    );
    }
}
