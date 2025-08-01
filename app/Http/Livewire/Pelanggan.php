<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Pelanggan extends Component
{
    public function render()
    {

        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }


        return view('livewire.pelanggan',
        [
            'pelanggans' => User::where('role', 'pelanggan')->get(),
        ]);
    }
}
