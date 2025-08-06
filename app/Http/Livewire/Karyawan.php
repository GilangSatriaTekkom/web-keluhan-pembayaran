<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Karyawan extends Component
{

    public $userId;

    public function mount() {
        $this->userId = Auth::id();
    }


    public function render()
    {
        $userId = Auth::id();
        if (!$userId && !Auth::user()->isAdmin()) {
            return redirect()->route('login');
        }


        return view('livewire.karyawan',
        [
            'karyawans' => User::where('role', 'admin')->get(),
        ]);
    }
}
