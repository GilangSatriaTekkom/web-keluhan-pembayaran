<?php

namespace App\Http\Livewire\Auth;

use App\Models\User;
use Livewire\Component;
use Illuminate\Support\Facades\Log;
use Jantinnerezo\LivewireAlert\Facades\LivewireAlert;

class Register extends Component
{

    public $name ='';
    public $email = '';
    public $password = '';
    public $password_confirmation = '';
    public $role = 'pelanggan';
    public $status = 'aktif';


    protected $rules=[
     'name' => 'required|min:3',  // Uncomment dan tambahkan validasi
    'email' => 'required|email|unique:users,email',
    'password' => 'required|min:5|confirmed',
    'role' => 'required|in:pelanggan,admin',  // Contoh validasi untuk role
    'status' => 'required|in:aktif,nonaktif'  // Contoh validasi status
    ];


    public function store()
    {
         try {
            $validatedData = $this->validate();

            User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'password' => Hash::make($validatedData['password']),
                'role' => $this->role,
                'status' => $this->status
            ]);

            LivewireAlert::title('Register Berhasil!')
                ->success()
                ->text('Silahkan Untuk Melakukan Login!')
                ->withConfirmButton('Ok')
                ->onConfirm('returnLogin')
                ->show();

        } catch (\Exception $e) {
             LivewireAlert::title('Ada Kesalahan!')
                ->error()
                ->show();
        }
    }

    public function returnLogin() {
        return redirect()->route('login');
    }

    public function render()
    {
        return view('livewire.auth.register');
    }

    // Di dalam component
    public function updated($propertyName)
    {
        $this->validateOnly($propertyName);

        // Jika ingin trigger event ke frontend
        if($propertyName === 'password' || $propertyName === 'password_confirmation') {
            $this->dispatch('passwordUpdated');
        }
    }
}
