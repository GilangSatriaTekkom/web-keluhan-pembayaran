<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Langganan;
use App\Models\PaketInternet;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;

class Pelanggan extends Component
{


    use WithPagination;

    public $searchAktif = '';
    public $tanggalAktif = '';

    protected $paginationTheme = 'bootstrap';

    // Reset pagination jika filter berubah
    public function updatingSearchAktif() { $this->resetPage('aktifPage'); }
    public function updatingTanggalAktif() { $this->resetPage('aktifPage'); }



    public function render()
    {

        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $baseQuery = User::with(['langganans.paket'])
                ->where('role', 'pelanggan');

        // Query untuk tiket aktif
        $pelanggans = (clone $baseQuery)
            ->where(function($query) {
                $query->where('name', 'like', "%{$this->searchAktif}%")
                      ->orwhere('phone', 'like', "%{$this->searchAktif}%")
                      ->orwhere('location', 'like', "%{$this->searchAktif}%")
                      ->orWhere('email', 'like', "%{$this->searchAktif}%");
            })
            ->when($this->tanggalAktif, function($query) {
                $query->whereDate('created_at', $this->tanggalAktif);
            })
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'aktifPage');

        // dd($pelanggan);

        return view('livewire.pelanggan',
        [
            'pelanggans' => $pelanggans,
        ]);
    }
}
