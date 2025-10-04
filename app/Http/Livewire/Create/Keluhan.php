<?php

namespace App\Http\Livewire\Create;

use Livewire\Component;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;
use App\Models\Tiket;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class Keluhan extends Component
{
    public $tiket = [
        'judul' => '',
        'status' => 'Menunggu',
        'description' => '',
    ];

    protected $rules = [
        'tiket.judul' => 'required|string|max:255',
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
                'judul' => $this->tiket['judul'],
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

    // public function store(Request $request)
    // {

    //    $tiket = Tiket::create([
    //         'user_id' => Auth::id(),
    //         'judul' => $request, // default value
    //         'status' => 'menunggu', // status default
    //         'description' => $request->deskripsi_keluhan,
    //         'judul' => $request->judul_keluhan // tambahkan kolom judul jika perlu
    //     ]);

    //     return [
    //         'fulfillmentText' => "Keluhan Anda telah tercatat. ID Tiket: {$tiket->id}",
    //         'tiket_id' => $tiket->id
    //     ];
    // }

    public function return() {
        return redirect()->route('tabel-keluhan.index');
    }

    public function render()
    {
        return view('livewire.create.keluhan');
    }
}
