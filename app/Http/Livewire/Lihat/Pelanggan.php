<?php

namespace App\Http\Livewire\Lihat;

use Livewire\Component;
use App\Models\User;
use App\Models\PaketInternet;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Auth;
use App\Jobs\GenerateTagihanJob;
use App\Models\Langganan;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Pelanggan extends Component
{

    public $user;
    public $user_id;
    public $name;
    public $email;
    public $alamat;
    public $phone;
    public $status;
    public $paket_internet_id;
    public $paketInternetOptions;
    public $langganan;
    public $paketBaru;

    public function mount($id)
    {
        $user = User::findOrFail($id);

        $this->user_id           = $user->id;
        $this->name              = $user->name;
        $this->email             = $user->email;
        $this->alamat            = $user->location;
        $this->phone             = $user->phone;
        $this->status            = $user->status;
        $langganan = Langganan::with('paket')->where('user_id', $user->id)->where('status_langganan', 'aktif')->first();
        $this->langganan = $langganan;
        $this->paket_internet_id = $langganan ? $langganan->paket_id : null;
        $this->user              = $user;

        $this->paketInternetOptions = PaketInternet::all();
    }

    public function updateEdit()
    {
         LivewireAlert::title("Perhatian")
                ->text("Apakah Anda yakin ingin memperbarui data Pelanggan ini?")
                ->question()
                ->asConfirm()
                ->onConfirm("alertGantiPaket")
                ->show();
    }

    public function alertGantiPaket() {


         if ($this->langganan && $this->paket_internet_id != $this->langganan->paket_id) {
            LivewireAlert::title("Yakin Merubah Paket?")
                ->warning()
                ->text("Biaya awal bulan akan kembali dikenakan jika merubah paket!")
                ->question()
                ->asConfirm()
                ->onConfirm("updatePelanggan", ['gantiPaket' => true])
                ->withConfirmButton('Ya, Ganti Paket!')
                ->show();
        } elseif (!$this->langganan || $this->langganan->paket_id == null) {
            $this->updatePelangganDenganPaket();
        } else {
            $this->updatePelanggan(['gantiPaket' => false]);
        }
    }

    public function updatePelangganDenganPaket()
    {
        User::findOrFail($this->user_id)->update([
            'name'               => $this->name,
            'email'              => $this->email,
            'location'              => $this->alamat,
            'phone'              => $this->phone,
            'status'             => $this->status,
        ]);

        $langgananBaru = Langganan::create([
            'user_id' => $this->user_id,
            'paket_id' => $this->paket_internet_id,
            'status_langganan' => 'aktif',
        ]);

         LivewireAlert::title("Data Berhasil diperbarui")
                ->success()
                ->onConfirm("redirectToLihatPelanggan")
                ->withConfirmButton('Ok!')
                ->show();
    }
    public function updatePelanggan($data = [])
    {

         $gantiPaket = $data['gantiPaket'] ?? false;


        User::findOrFail($this->user_id)->update([
            'name'               => $this->name,
            'email'              => $this->email,
            'location'              => $this->alamat,
            'phone'              => $this->phone,
            'status'             => $this->status,
        ]);

        if($gantiPaket) {
             Langganan::where('id', $this->langganan->id)->update([
                'status_langganan'  => 'nonaktif'
            ]);

            $langgananBaru = Langganan::create([
                'user_id' => $this->user_id,
                'paket_id' => $this->paket_internet_id,
                'status_langganan' => 'aktif',
            ]);

        }

         LivewireAlert::title("Data Berhasil diperbarui")
                ->success()
                ->onConfirm("redirectToLihatPelanggan")
                ->withConfirmButton('Ok!')
                ->show();
    }

    private function generateInitialTagihan(User $user, Langganan $langgananBaru)
    {
        $jumlahTagihan = $langgananBaru->paket->harga;
        // Tagihan untuk bulan ini
        Tagihan::create([
            'user_id' => $user->id,
            'langganan_id' => $langgananBaru->id,
            'periode_tagihan' => now(),
            'tgl_jatuh_tempo' => now()->addDays(7),
            'jumlah_tagihan' => $jumlahTagihan,
            'status_pembayaran' => 'belum_lunas'
        ]);

    }

    public function redirectToLihatPelanggan()
    {
        return redirect()->route('pelanggan.lihat', ['id' => $this->user_id]);
    }

    public function render()
    {
        return view('livewire.lihat.pelanggan');
    }
}
