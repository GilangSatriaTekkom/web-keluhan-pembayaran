<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Langganan;

class GenerateTagihanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'generate:tagihan';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */

    public function handle()
    {
        $langganans = Langganan::where('status_langganan', 'aktif')->get();

        foreach ($langganans as $langganan) {
            $tanggalTagihan = $langganan->created_at->startOfMonth();

            while ($tanggalTagihan <= now()) {
                Tagihan::firstOrCreate([
                    'langganan_id' => $langganan->id,
                    'periode_tagihan' => $tanggalTagihan
                ], [
                    'tgl_jatuh_tempo' => $tanggalTagihan->copy()->addDays(7),
                    'jumlah_tagihan' => $langganan->paket->harga,
                    'status_pembayaran' => 'belum_lunas'
                ]);

                $tanggalTagihan->addMonth();
            }
        }
    }
}
