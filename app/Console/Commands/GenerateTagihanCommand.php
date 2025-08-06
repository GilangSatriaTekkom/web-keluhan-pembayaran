<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateTagihanCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:generate-tagihan-command';

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
        $langganans = Langganan::where('status', 'aktif')->get();

        foreach ($langganans as $langganan) {
            $tanggalTagihan = $langganan->created_at->startOfMonth();

            while ($tanggalTagihan <= now()) {
                Tagihan::firstOrCreate([
                    'langganan_id' => $langganan->id,
                    'tanggal_tagihan' => $tanggalTagihan
                ], [
                    'jatuh_tempo' => $tanggalTagihan->copy()->addDays(7),
                    'jumlah' => $langganan->paket->harga,
                    'status' => 'belum_lunas'
                ]);

                $tanggalTagihan->addMonth();
            }
        }
    }
}
