<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;


class PaketInternet extends Model
{
     protected $fillable = ['nama_paket', 'kecepatan', 'harga'];

    public function langganans(): HasMany {
        return $this->hasMany(Langganan::class, 'paket_id');
    }
}
