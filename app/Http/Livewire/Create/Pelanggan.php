<?php

namespace App\Http\Livewire\Create;

use Livewire\Component;
use App\Models\User;
use App\Models\Langganan;
use App\Models\PaketInternet;
use Illuminate\Support\Facades\Hash;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Pelanggan extends Component
    {
        public $user = [
            'name' => '',
            'email' => '',
            'phone' => '',
            'location' => '',
            'about' => '',
            'password' => '',
            'role' => 'pelanggan',
            'status' => 'aktif',
            'paket_internet_id' => ''
        ];

        protected $rules = [
            'user.name' => 'required|string|max:255',
            'user.email' => 'required|email|unique:users,email',
            'user.phone' => 'nullable|string|max:20',
            'user.location' => 'nullable|string|max:255',
            'user.about' => 'nullable|string',
            'user.password' => 'required|string|min:6',
            'user.role' => 'required|string',
            'user.status' => 'required|string',
            'user.paket_internet_id' => 'nullable|exists:paket_internets,id',
            ];

        public function confirmSubmit()
        {

            if ($this->user['paket_internet_id'] &&
                (empty($this->user['phone']) || empty($this->user['location']))) {
                LivewireAlert::title('Peringatan')
                    ->text('Masukan nomor hp dan alamat jika ingin berlangganan!')
                    ->warning()
                    ->show();
                return;
            }

            LivewireAlert::title('Konfirmasi')
                ->text('Yakin buat pelanggan?')
                ->question()
                ->asConfirm()
                ->onConfirm('submit')
                ->onDeny('onDenyHandler')
                ->show();
        }

        public function onDenyHandler()
        {
            LivewireAlert::title('Informasi')
                ->text('Pembuatan pelanggan dibatalkan')
                ->info()
                ->show();
        }

        public function submit()
        {
            try {
                $this->validate();

                $user = User::create([
                    'name' => $this->user['name'],
                    'email' => $this->user['email'],
                    'phone' => $this->user['phone'],
                    'location' => $this->user['location'],
                    'about' => $this->user['about'],
                    'password' => $this->user['password'],
                    'role' => $this->user['role'],
                    'status' => $this->user['status'],
                ]);

                if ($this->user['paket_internet_id']) {
                    Langganan::create([
                        'user_id' => $user->id,
                        'paket_id' => $this->user['paket_internet_id'],
                        'status_langganan' => 'aktif',
                    ]);
                }

                LivewireAlert::title('Sukses')
                    ->text('Pelanggan berhasil dibuat!')
                    ->success()
                    ->withConfirmButton('OK')
                    ->onConfirm('returnToList')
                    ->show();

                $this->reset('user');
            } catch (\Exception $e) {
                LivewireAlert::title('Error')
                    ->text('Terjadi kesalahan: ' . $e->getMessage())
                    ->error()
                    ->show();
            }
        }

        public function returnToList()
        {
            return redirect()->route('pelanggan');
        }

        public function render()
        {
            return view('livewire.create.pelanggan',[
            'paketInternetOptions' => PaketInternet::all()
            ]);
        }
    }
