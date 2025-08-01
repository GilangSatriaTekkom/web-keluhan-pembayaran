<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;


class Langganan extends Model
{
   protected $fillable = ['user_id', 'paket_id', 'status_langganan', 'tanggal_mulai', 'tanggal_berakhir'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function paket(): BelongsTo {
        return $this->belongsTo(PaketInternet::class, 'paket_id');
    }

    public function tagihans()
    {
        return $this->hasMany(Tagihan::class);
    }
}
