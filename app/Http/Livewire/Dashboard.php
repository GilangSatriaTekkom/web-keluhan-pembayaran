<?php

namespace App\Http\Livewire;

use App\Models\User;
use App\Models\Tagihan;
use App\Models\Tiket;
use App\Models\Langganan;
use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\RekapExport;

class Dashboard extends Component
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

    public function exportExcelPembayaran()
    {
        $data = 'pembayaran';
        $rekap = new RekapExport($data);
        return Excel::download($rekap, 'rekapPembayaran.xlsx');
    }

    public function exportExcelKeluhan()
    {
        return Excel::download(new RekapExport('tiket'), 'rekapKeluhan.xlsx');
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
            $baseQuery->where('user_id', $userId)->where('status', '!=', 'selesai')->whereMonth('created_at', now()->month)->get();
        } else {
            $baseQuery->whereMonth('created_at', now()->month)->where('status', '!=', 'selesai')->get();
        }

        $baseQueryTagihan = Tagihan::with('user');
        if ($user->role === 'pelanggan') {
            $baseQueryTagihan->where('user_id', $userId)->where('status_pembayaran', 'belum_lunas')->whereMonth('created_at', now()->month)->get();
        } else {
            $baseQueryTagihan->whereMonth('created_at', now()->month)->where('status_pembayaran', 'belum_lunas')->get();
        }

        // Query untuk tiket selesai
        $keluhan = (clone $baseQuery)
            ->where(function($query) {
                $query->where('category', 'like', "%{$this->searchAktif}%")
                ->orwhere('id', 'like', "%{$this->searchAktif}%")
                ->orWhere('status', 'like', "%{$this->searchAktif}%")
                ->orWhereHas('user', function($q) {
                    $q->where('name', 'like', "%{$this->searchAktif}%");
                });

            })
            ->when($this->tanggalAktif, function($query) {
                $query->whereDate('created_at', $this->tanggalAktif);
            })
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'selesaiPage');

        $tagihanBelumLunas = (clone $baseQueryTagihan)
            ->where(function($query) {
                $query->where('id', 'like', "%{$this->searchSelesai}%")
                      ->orWhereHas('user', function($q) {
                          $q->where('name', 'like', "%{$this->searchSelesai}%");
                      })
                      ->orWhereHas('langganan', function($q) {
                            $q->whereHas('paket', function($q) {
                                $q->where('nama_paket', 'like', "%{$this->searchSelesai}%");
                            });
                      });
            })
            ->when($this->tanggalSelesai, function($query) {
                $query->whereDate('tgl_jatuh_tempo', $this->tanggalSelesai);
            })
            ->orderByDesc('created_at')
            ->paginate(5, ['*'], 'aktifPage');

        $totalTagihan = Tagihan::where('user_id', $userId)
            ->where('status_pembayaran', 'belum_lunas')
            ->sum('jumlah_tagihan');

        $keluhanDirespon = Tiket::where('user_id', $userId)
            ->where('status', 'Selesai')
            ->whereMonth('created_at', now()->month)
            ->count();

        // Function web page admin

        $totalTagihanBelumLunas = Tagihan::where('status_pembayaran', 'belum_lunas')->get();
        $totalKeluhanBelumDitangani = Tiket::where('status', 'Menunggu')->get();
        $totalKeluhanSedangProses = Tiket::where('status', 'proses')->get();

        return view('livewire.dashboard',
        [
            'users' => User::all(),
            'jumlahTagihans' => $totalTagihan ? formatRupiah($totalTagihan) : null,
            'tagihanBelumLunas' => $tagihanBelumLunas,
            'keluhanBulanIni' => $keluhan,
            'tiket' => Tiket::all(),
            'keluhanDirespon' => $keluhanDirespon,
            'langganans' => Langganan::all(),
            'totalTagihanBelumLunas' => count($totalTagihanBelumLunas),
            'totalKeluhanBelumDitangani' => count($totalKeluhanBelumDitangani),
            'totalKeluhanSedangProses' => count($totalKeluhanSedangProses)
        ]
    );
    }
}
