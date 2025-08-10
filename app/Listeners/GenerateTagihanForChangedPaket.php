<?php

namespace App\Listeners;

use App\Events\PaketInternetChanged;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class GenerateTagihanForChangedPaket
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(PaketInternetChanged $event): void
    {

        $event->user->tagihans()
            ->where('status', 'belum_lunas')
            ->delete();

        $this->generateTagihan($event->user);
    }
}
