<?php

namespace App\Observers;

use App\Models\Langganan;
use Illuminate\Support\Carbon;
use App\Models\Tagihan;
use App\Jobs\GenerateTagihanJob;

class LanggananObserver
{
    /**
     * Handle the Langganan "created" event.
     */
    public function created(Langganan $langganan): void
    {
         // Generate tagihan pertama
        $this->generateTagihan($langganan, now());

        // Schedule tagihan bulan berikutnya
        $nextMonth = now()->addMonth();
        GenerateTagihanJob::dispatch($langganan, $nextMonth)
            ->delay($nextMonth);
    }

    private function generateTagihan(Langganan $langganan, Carbon $tanggal)
    {
        Tagihan::create([
            'langganan_id' => $langganan->id,
            // 'tanggal_tagihan' => $tanggal,
            'user_id' => $langganan->user_id,
            'tgl_jatuh_tempo' => $tanggal->copy()->addDays(7), // Jatuh tempo 7 hari setelah tagihan
            'periode_tagihan' => $tanggal->format('Y-m'),
            'jumlah_tagihan' => $langganan->paket->harga,
            'status_pembayaran' => 'Belum Lunas'
        ]);
    }

    /**
     * Handle the Langganan "updated" event.
     */
    public function updated(Langganan $langganan): void
    {
        //
    }

    /**
     * Handle the Langganan "deleted" event.
     */
    public function deleted(Langganan $langganan): void
    {
        //
    }

    /**
     * Handle the Langganan "restored" event.
     */
    public function restored(Langganan $langganan): void
    {
        //
    }

    /**
     * Handle the Langganan "force deleted" event.
     */
    public function forceDeleted(Langganan $langganan): void
    {
        //
    }
}
