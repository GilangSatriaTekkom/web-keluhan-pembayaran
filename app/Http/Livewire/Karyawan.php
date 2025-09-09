<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;

class Karyawan extends Component
{

    use WithPagination;

    public $searchAktif = '';
    public $tanggalAktif = '';

    protected $paginationTheme = 'bootstrap';

    // Reset pagination jika filter berubah
    public function updatingSearchAktif() { $this->resetPage('aktifPage'); }
    public function updatingTanggalAktif() { $this->resetPage('aktifPage'); }


    public $userId;

    public function mount() {
        $this->userId = Auth::id();
    }


    public function render()
    {
        $userId = Auth::id();
        if (!$userId && !Auth::user()->isAdmin()) {
            return redirect()->route('login');
        }

        $baseQuery = User::where('role', 'admin');

        // Query untuk tiket aktif
        $karyawans = (clone $baseQuery)
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


        return view('livewire.karyawan',
        [
            'karyawans' => $karyawans,
        ]);
    }
}
