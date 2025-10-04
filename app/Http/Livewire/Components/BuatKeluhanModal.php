<?php

namespace App\Http\Livewire\Components;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Tiket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class BuatKeluhanModal extends Component
{

    public $tiket = [
        'judul' => '',
        'status' => 'Menunggu',
        'description' => '',
    ];

    public function submitKeluhan()
    {
        $this->validate([
            'tiket.judul' => 'required|string|max:255',
            'tiket.status' => 'required|string|max:255',
            'tiket.description' => 'required|string',
        ]);

        Tiket::create([
            'user_id' => Auth::id(),
            'judul' => $this->tiket['judul'],
            'status' => $this->tiket['status'],
            'description' => $this->tiket['description'],
        ]);

        $this->reset('tiket');

        // Tutup modal
        $this->dispatch('buatKeluhan', ['id' => 'buatKeluhanModal']);
    }

    public function render()
    {
        return view('livewire.components.buat-keluhan-modal');
    }
}
