<?php

namespace App\Http\Livewire\Create;

use Livewire\Component;

class Keluhan extends Component
{
    public $tiket = [
        'category' => '',
        'status' => '',
        'description' => '',
    ];

    protected $rules = [
        'tiket.category' => 'required|string|max:255',
        'tiket.status' => 'required|string|max:50',
        'tiket.description' => 'required|string',
    ];

    public function submit()
    {
        $this->validate();

        Tiket::create([
            'user_id' => Auth::id(), // ambil user yang login
            'category' => $this->tiket['category'],
            'status' => $this->tiket['status'],
            'description' => $this->tiket['description'],
        ]);

        session()->flash('status', 'Keluhan berhasil ditambahkan.');

        $this->reset('tiket');
    }

    public function render()
    {
        return view('livewire.create.keluhan');
    }
}
