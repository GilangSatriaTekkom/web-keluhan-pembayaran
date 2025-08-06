<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetailTiket extends Model
{
   protected $fillable = ['tiket_id', 'tasks', 'isDone'];

    public function tiket(): BelongsTo {
        return $this->belongsTo(Tiket::class);
    }
}
