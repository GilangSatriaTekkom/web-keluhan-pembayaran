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
    public function collection($data)
    {
       $tikets = Tiket::with(['user:id,name', 'detailTikets'])->get();

       if($data == 'tiket') {

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
            return $tikets->map(function ($tiket) {
                return [
                    'id'          => $tiket->id,
                    'status_pembayaran'      => $tiket->status_pembayaran,
                    'metode_pembayaran'      => $tiket->metode_pembayaran,
                    'jumlah_tagihan'      => $tiket->jumlah_tagihan,
                    'tgl_jatuh_tempo'      => $tiket->tgl_jatuh_tempo,
                    'created_at'  => $tiket->created_at,
                    'updated_at'  => $tiket->updated_at,
                    'user_name'   => $tiket->user ? $tiket->user->name : null,
                    // ganti 'message' dengan kolom yang kamu mau tampilkan
                ];
            });
        }

    }
    public function rekapPembayaran()
    {
       $tikets = Tagihan::with(['user:id,name',])->get();



    }

    protected $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

}
