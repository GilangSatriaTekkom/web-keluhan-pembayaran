<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class HistoriPembayaran extends Model
{
     protected $fillable = ['tagihan_id', 'user_id', 'metode_pembayaran', 'status_pembayaran', 'jumlah_dibayar', 'tanggal_pembayaran'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function tagihan(): BelongsTo {
        return $this->belongsTo(Tagihan::class);
    }
}
