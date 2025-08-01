<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tagihan extends Model
{
    protected $fillable = ['user_id', 'langganan_id', 'status_pembayaran', 'metode_pembayaran', 'jumlah_tagihan', 'tgl_jatuh_tempo', 'periode_tagihan'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function historiPembayarans(): HasMany {
        return $this->hasMany(HistoriPembayaran::class);
    }

    public function langganan()
    {
        return $this->belongsTo(Langganan::class);
    }

}
