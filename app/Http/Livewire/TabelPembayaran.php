<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\Langganan;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use App\Services\RekapService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapExport;

class TabelPembayaran extends Component
{

    use WithPagination;

    public $searchAktif = '';
    public $tanggalAktif = '';
    public $searchSelesai = '';
    public $tanggalSelesai = '';

    protected $paginationTheme = 'bootstrap';

    // Reset pagination jika filter berubah
    public function updatingSearchAktif() { $this->resetPage('aktifPage'); }
    public function updatingTanggalAktif() { $this->resetPage('aktifPage'); }
    public function updatingSearchSelesai() { $this->resetPage('selesaiPage'); }
    public function updatingTanggalSelesai() { $this->resetPage('selesaiPage'); }

    public function exportExcel()
    {
        $data = 'pembayaran';
        $rekap = new RekapExport($data);
        return Excel::download($rekap->rekapPembayaran(), 'rekapPembayaran.xlsx');
    }

    public function exportPdf()
    {
        $data = ['payments' => $this->payments];
        return RekapService::exportPdf('exports.pembayaran', $data, 'pembayaran.pdf');
    }


    public function render()
    {
        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $user = User::find($userId);

        $baseQuery = Tagihan::with('user');
        if ($user->role === 'pelanggan') {
            $baseQuery->where('user_id', $userId);
        }

        // Query untuk tiket aktif
        $langgananAktif = (clone $baseQuery)
            ->where('status_pembayaran', '!=', 'lunas')
            ->where(function($query) {
                $query->where('id', 'like', "%{$this->searchAktif}%")
                      ->orWhereHas('user', function($q) {
                          $q->where('name', 'like', "%{$this->searchAktif}%");
                      })
                      ->orWhereHas('langganan', function($q) {
                            $q->whereHas('paket', function($q) {
                                $q->where('nama_paket', 'like', "%{$this->searchAktif}%");
                            });
                      });
            })
            ->when($this->tanggalAktif, function($query) {
                $query->whereDate('created_at', $this->tanggalAktif);
            })
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'aktifPage');

        // Query untuk tiket selesai
        $langgananSelesai = (clone $baseQuery)
            ->where('status_pembayaran', 'lunas')
            ->where(function($query) {
                $query->where('id', 'like', "%{$this->searchSelesai}%")
                      ->orWhereHas('user', function($q) {
                          $q->where('name', 'like', "%{$this->searchSelesai}%");
                      })
                      ->orWhereHas('langganan', function($q) {
                            $q->whereHas('paket', function($q) {
                                $q->where('nama_paket', 'like', "%{$this->searchAktif}%");
                            });
                      });
            })
            ->when($this->tanggalSelesai, function($query) {
                $query->whereDate('updated_at', $this->tanggalSelesai);
            })
            ->orderByDesc('updated_at')
            ->paginate(5, ['*'], 'selesaiPage');

        $langgananAktifAdmin = Tagihan::whereHas('user')->whereHas('langganan')->orderByDesc('created_at')->paginate(5, ['*'], 'aktifPage');

        return view('livewire.tabel-pembayaran',

        [
            'langgananAktif' => $langgananAktif,
            'langgananAktifAdmin' => $langgananAktifAdmin,
            'langgananSelesai' => $langgananSelesai,
            'tagihans' => Tagihan::where('user_id', $userId)->get(),
            'langganans' => Langganan::all(),
        ]
    );
    }
}
