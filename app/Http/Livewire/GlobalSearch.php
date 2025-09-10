<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\Tagihan;
use App\Models\Tiket;
use Illuminate\Support\Facades\Log;

class GlobalSearch extends Component
{
    public $query = '';
    public $results = [];
    public $showModal = false;

    public function updatedQuery()
    {
         $search = trim($this->query);

        if (strlen($search) < 2) {
            $this->showModal = false;
            $this->results = [];
            return;
        }

        $users = User::query()
            ->where('name', 'like', "%{$search}%")
            ->orWhere('email', 'like', "%{$search}%")
            ->orWhere('phone', 'like', "%{$search}%")
            ->get();

        $tagihan = Tagihan::query()
            ->where('id', 'like', "%{$search}%")
            ->orWhere('periode_tagihan', 'like', "%{$search}%")
            ->orWhere('status_pembayaran', 'like', "%{$search}%")
            ->orWhereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$search}%")
            )
            ->get();

        $tiket = Tiket::query()
            ->where('category', 'like', "%{$search}%")
            ->orWhere('status', 'like', "%{$search}%")
            ->orWhere('description', 'like', "%{$search}%")
            ->orWhereHas('user', fn($q) =>
                $q->where('name', 'like', "%{$search}%")
            )
            ->get();

        $this->results = [
            'users' => $users,
            'tagihan' => $tagihan,
            'tiket' => $tiket,
        ];

         $this->showModal = true;
    }


    public function closeModal()
    {
        $this->showModal = false;
        $this->query = '';
    }

    public function render()
    {
        return view('livewire.global-search');
    }
}
