<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\Tiket;
use App\Models\Langganan;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Services\RekapService;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapExport;


class TabelKeluhan extends Component
{

    use WithPagination;

    public $searchAktif = '';
    public $tanggalAktif = '';
    public $searchSelesai = '';
    public $tanggalSelesai = '';

    public $payments;
    public $complaints;

    protected $paginationTheme = 'bootstrap';

    // Reset pagination jika filter berubah
    public function updatingSearchAktif() { $this->resetPage('aktifPage'); }
    public function updatingTanggalAktif() { $this->resetPage('aktifPage'); }
    public function updatingSearchSelesai() { $this->resetPage('selesaiPage'); }
    public function updatingTanggalSelesai() { $this->resetPage('selesaiPage'); }

    public function mount()
    {
        $this->complaints = Tiket::with([
                'user:id,name',     // relasi user, ambil hanya id & name
                'detailTikets'      // perbaiki: sesuai nama relasi di model
            ])
            ->whereHas('user')
            ->whereHas('detailTikets')
            ->whereMonth('created_at', Carbon::now()->month)
            ->get();
    }

    public function exportExcel()
    {
        return Excel::download(new RekapExport('tiket'), 'rekapKeluhan.xlsx');
    }

    public function exportPdf()
    {
        $data = ['tiket' => $this->complaints];
        return RekapService::exportPdf('exports.keluhan', $data, 'keluhan.pdf');
    }


    public function render()
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        $baseQuery = Tiket::with('user');
        if ($user->role === 'pelanggan') {
            $baseQuery->where('user_id', $userId);
        } else {
            $baseQuery->get();
        }

        // Query untuk tiket aktif
        $tiketAktif = (clone $baseQuery)
            ->where('status', '!=', 'selesai')
            ->where(function($query) {
                $query->where('id', 'like', "%{$this->searchAktif}%")
                ->orWhere('status', 'like', "%{$this->searchAktif}%")
                ->orWhereHas('user', function($q) {
                    $q->where('name', 'like', "%{$this->searchAktif}%");
                })
                ->orWhere('category', 'like', "%{$this->searchAktif}%");
            })
            ->when($this->tanggalAktif, function($query) {
                $query->whereDate('created_at', $this->tanggalAktif);
            })
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'aktifPage');

        // Query untuk tiket selesai
        $tiketSelesai = (clone $baseQuery)
            ->where('status', 'selesai')
            ->where(function($query) {
                $query->where('id', 'like', "%{$this->searchSelesai}%")
                      ->orWhereHas('user', function($q) {
                          $q->where('name', 'like', "%{$this->searchSelesai}%");
                      })
                      ->orWhere('category', 'like', "%{$this->searchSelesai}%");
            })
            ->when($this->tanggalSelesai, function($query) {
                $query->whereDate('updated_at', $this->tanggalSelesai);
            })
            ->orderByDesc('updated_at')
            ->paginate(5, ['*'], 'selesaiPage');

        return view('livewire.tabel-keluhan', [
            'tiketAktif' => $tiketAktif,
            'tiketSelesai' => $tiketSelesai,
            'users' => $user,
            'langganans' => Langganan::all(),
            'tagihans' => Tagihan::all(),
        ]);
    }
}
