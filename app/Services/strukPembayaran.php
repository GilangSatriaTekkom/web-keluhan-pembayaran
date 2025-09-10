<?php

namespace App\Services;

use LaravelDaily\Invoices\Invoice;
use LaravelDaily\Invoices\Classes\Buyer;
use LaravelDaily\Invoices\Classes\InvoiceItem;
use App\Models\Tagihan;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class strukPembayaran
{
    public function __construct()
    {
        //
    }

    public function generate(Tagihan $pembayaran, $filePath = null)
    {
        // Data customer
        $customer = new Buyer([
            'name'          => $this->safeString($pembayaran->user->name),
            'custom_fields' => [
                'email'    => $this->safeString($pembayaran->user->email),
                'No Hp'    => $this->safeString($pembayaran->user->phone ?? '-'),
                'Alamat' => $this->safeString($pembayaran->user->location ?? '-'),
                'ID User'  => $pembayaran->user->id,
            ],
        ]);

         // Data seller (provider internet)
        $seller = new Buyer([
            'name'          => 'Sukabumi Network',
            'custom_fields' => [
                'Alamat' => 'Jl. Arca No.08, Gunungpuyuh, Kec. Gunungpuyuh, Kota Sukabumi, Jawa Barat 43123',
            ],
        ]);

        // Data paket internet
        $paketNama  = $this->safeString($pembayaran->langganan?->paket?->nama_paket ?? 'Paket Tidak Ditemukan');
        $paketHarga = $pembayaran->langganan?->paket?->harga ?? 0;

        $item = (new InvoiceItem())
            ->title($paketNama)
            ->pricePerUnit($paketHarga);

        // Buat invoice
        $invoice = Invoice::make()
            ->seller($seller)
            ->buyer($customer)
            ->addItem($item)
            ->sequence($pembayaran->id)
            ->currencySymbol('Rp')
            ->currencyFormat('{SYMBOL} {VALUE}')
            ->currencyCode('IDR');

        if ($filePath) {
            // Ambil full path dari disk invoices
            Storage::disk('invoices')->put($filePath, $invoice->stream());
        }

        return $invoice;
    }

    /**
     * Pastikan string aman untuk UTF-8 (hindari error encoding).
     */
    private function safeString($string)
    {
        $safe = mb_convert_encoding($string ?? '', 'UTF-8', 'UTF-8');
        Log::info('safeString dijalankan', [
            'input'  => $string,
            'output' => $safe,
        ]);
        return $safe;
    }
}
