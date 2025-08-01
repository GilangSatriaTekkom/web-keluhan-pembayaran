<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\HistoriPembayaran;
use App\Models\Tiket;
use App\Models\Langganan;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Dashboard extends Component
{
    public function render()
    {

        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }




        $totalTagihan = Tagihan::where('user_id', $userId)
            ->sum('jumlah_tagihan');

        $keluhanDirespon = Tiket::where('user_id', $userId)
            ->where('status', 'Selesai')
            ->whereMonth('created_at', now()->month)
            ->count();

        return view('livewire.dashboard',
        [
            'users' => User::all(),
            'jumlahTagihans' => $totalTagihan ? formatRupiah($totalTagihan) : null,
            'tagihanBelumLunas' => Tagihan::where('user_id', $userId)->get(),
            'keluhanBulanIni' => Tiket::where('user_id', $userId)->whereMonth('created_at', now()->month)->get(),
            'pembayarans' => HistoriPembayaran::all(),
            'tiket' => Tiket::all(),
            'keluhanDirespon' => $keluhanDirespon,
            'langganans' => Langganan::all()
        ]
    );
    }
}
