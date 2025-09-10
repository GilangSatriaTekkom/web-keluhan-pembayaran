<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use App\Models\Tiket;
use App\Models\Tagihan;

class RekapExport implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
       $tikets = Tiket::with(['user:id,name', 'detailTikets'])->get();
       $tagihans = Tagihan::with(['user:id,name'])->get();

    //    dd($tagihans);

        if($this->data == 'tiket') {

            return $tikets->map(function ($tiket) {
                return [
                    'id'          => $tiket->id,
                    'status'      => $tiket->status,
                    'description' => $tiket->description,
                    'created_at'  => $tiket->created_at,
                    'updated_at'  => $tiket->updated_at,
                    'user_name'   => $tiket->user ? $tiket->user->name : null,
                    'detail'      => $tiket->detailTikets->pluck('message')->join(', '),
                    // ganti 'message' dengan kolom yang kamu mau tampilkan
                ];
            });
        } else {
            return $tagihans->map(function ($tagihan) {
                return [
                    'id'          => $tagihan->id,
                    'status_pembayaran'      => $tagihan->status_pembayaran,
                    'metode_pembayaran'      => $tagihan->metode_pembayaran,
                    'jumlah_tagihan'      => $tagihan->jumlah_tagihan,
                    'tgl_jatuh_tempo'      => $tagihan->tgl_jatuh_tempo,
                    'created_at'  => $tagihan->created_at,
                    'updated_at'  => $tagihan->updated_at,
                    'user_name'   => $tagihan->user ? $tagihan->user->name : null,
                    // ganti 'message' dengan kolom yang kamu mau tampilkan
                ];
            });
        }

    }

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

}
