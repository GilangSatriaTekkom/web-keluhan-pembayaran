<?php

namespace App\Http\Livewire\LihatData;

use Livewire\Component;
use App\Models\Tagihan;
use App\Models\User;
use Midtrans\Snap;
use Midtrans\Config;


class LihatPembayaran extends Component
{
    public $status;
    public $id;
    public $userId;
    public $langganan;
    public $metode;
    public $jumlah;
    public $jatuhTempo;
    public $periode;
    public $created;
    public $updated;
    public $tagihan;
    public $user;

    public $snapToken;

    public function mount($id)
    {
        $tagihan = Tagihan::findOrFail($id);
        $this->status = $tagihan->status_pembayaran;
        $this->id    = $tagihan->id;
        $this->userId   = $tagihan->user_id;
        $this->langganan  = $tagihan->langganan_id;
        $this->metode   = $tagihan->metode_pembayaran;
        $this->jumlah  = $tagihan->jumlah_tagihan;
        $this->jatuhTempo  = $tagihan->tgl_jatuh_tempo;
        $this->periode  = $tagihan->periode_tagihan;
        $this->created  = $tagihan->created_at;
        $this->updated  = $tagihan->updated_at;
        $this->tagihan    = $tagihan;

        $user = User::where('id', $tagihan->user_id)->first();
        $this->user = $user;

        $this->initMidtrans();
    }

    protected function initMidtrans()
    {


        Config::$serverKey = config('services.midtrans.server_key');
        Config::$isProduction = config('services.midtrans.is_production');
        Config::$isSanitized = true;
        Config::$is3ds = true;

        $this->clientKey = config('services.mindtrans.client_key');
    }

    // Generate snap token dari tagihan
    public function generateSnapToken()
    {
        $params = [
            'transaction_details' => [
                'order_id' => 'TAGIHAN-' . $this->id . '-' . time(),
                'gross_amount' => $this->jumlah,
            ],
            'customer_details' => [
                'first_name' => 'User ' . $this->userId,
                'email' => $this->user->email,
                'phone' => $this->user->phone,
            ],
            'enabled_payments' => ["other_qris", 'bank_transfer', 'credit_card', 'gopay', 'shopeepay'],
        ];

        try {
            $this->snapToken = Snap::getSnapToken($params);
        } catch (\Exception $e) {
            $this->dispatch('midtrans-error', ['message' => $e->getMessage()]);
            \Log::error('Midtrans Error: ' . $e->getMessage(), $params);
        }
    }

    // Fungsi untuk memulai pembayaran
    public function bayar()
    {
        $this->generateSnapToken();
        // dd($this->snapToken);

        // Kirim event ke frontend supaya panggil payment popup midtrans dengan token
        $this->dispatch('midtrans-pay', snapToken: $this->snapToken);
    }

    public function render()
    {
        return view('livewire.lihat-data.lihat-pembayaran');
    }
}
