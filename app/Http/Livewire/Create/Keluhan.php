<?php

namespace App\Http\Livewire\Create;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Tiket;
use Illuminate\Support\Facades\Auth;

class Keluhan extends Component
{
    public $tiket = [
        'category' => '',
        'status' => 'Menunggu',
        'description' => '',
    ];

    protected $rules = [
        'tiket.category' => 'required|string|max:255',
        'tiket.status' => 'required|string|max:50',
        'tiket.description' => 'required|string',
    ];

    public function alert() {
        LivewireAlert::title('Yakin buat tiket ?')
                ->success()
                ->text('Pembuatan keluhan tidak dapat dikembalikan!')
                ->asConfirm()
                ->onConfirm('submit')
                ->onDeny('onDenyHandler')
                ->show();
    }

    public function onDenyHandler() {
        // Contoh: Reset field atau tampilkan alert lain
        $this->reset('tiket');
        LivewireAlert::info('Anda membatalkan pembuatan tiket.');
    }

    public function submit()
    {
        try {
        $this->validate();

        Tiket::create([
            'user_id' => Auth::id(), // ambil user yang login
            'category' => $this->tiket['category'],
            'status' => $this->tiket['status'],
            'description' => $this->tiket['description'],
        ]);

        LivewireAlert::title('Buat Keluhan Berhasil!')
                ->success()
                ->withConfirmButton('Ok')
                ->onConfirm('return')
                ->show();

        // $this->reset('tiket');
        }

        catch (\Exception $e) {
             LivewireAlert::title('Ada Kesalahan!')
                ->error()
                ->show();
        }
    }

    public function return() {
        return redirect()->route('tabel-keluhan.index');
    }

    public function render()
    {
        return view('livewire.create.keluhan');
    }
}
