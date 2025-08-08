<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\Langganan;
use App\Models\PaketInternet;
use Illuminate\Support\Facades\Auth;

class Pelanggan extends Component
{
    public function render()
    {

        $userId = Auth::id();
        if (!$userId) {
            return redirect()->route('login');
        }

        $users = User::with(['langganans.paket'])
                ->where('role', 'pelanggan')
                ->get();

        Log::debug("message", ['users' => $users]);



        return view('livewire.pelanggan', compact('users'));
    }
}
