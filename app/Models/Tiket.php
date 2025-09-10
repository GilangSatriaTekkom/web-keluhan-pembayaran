<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tiket extends Model
{
      protected $fillable = ['user_id', 'category', 'status', 'description'];

    public function user(): BelongsTo {
        return $this->belongsTo(User::class);
    }

    public function detailTikets(): HasMany {
        return $this->hasMany(DetailTiket::class, 'tiket_id');
    }

    public function cs()
    {
        return $this->belongsTo(User::class, 'cs_menangani', 'id');
    }
}
