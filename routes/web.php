<?php

use App\Http\Livewire\Auth\ForgotPassword;
use App\Http\Livewire\Auth\Login;
use App\Http\Livewire\Auth\Register;
use App\Http\Livewire\Auth\ResetPassword;
use App\Http\Livewire\Billing;
use Illuminate\Support\Facades\Route;
use App\Http\Livewire\Dashboard;
use App\Http\Livewire\ExampleLaravel\UserManagement;
use App\Http\Livewire\ExampleLaravel\UserProfile;
use App\Http\Livewire\Notifications;
use App\Http\Livewire\Profile;
use App\Http\Livewire\RTL;
use App\Http\Livewire\StaticSignIn;
use App\Http\Livewire\StaticSignUp;
use App\Http\Livewire\Tables;
use App\Http\Livewire\VirtualReality;
use App\Http\Livewire\TabelKeluhan;
use App\Http\Livewire\TabelPembayaran;
use App\Http\Livewire\Karyawan;
use App\Http\Livewire\Pelanggan;
use App\Http\Livewire\TambahUsers;
use GuzzleHttp\Middleware;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function(){
    return redirect('sign-in');
});

Route::get('forgot-password', ForgotPassword::class)->middleware('guest')->name('password.forgot');
Route::get('reset-password/{id}', ResetPassword::class)->middleware('signed')->name('reset-password');



Route::get('sign-up', Register::class)->middleware('guest')->name('register');
Route::get('sign-in', Login::class)->middleware('guest')->name('login');

Route::get('user-profile', UserProfile::class)->middleware('auth')->name('user-profile');
Route::get('user-management', UserManagement::class)->middleware('auth')->name('user-management');

Route::group(['middleware' => 'auth'], function () {
    Route::get('dashboard', Dashboard::class)->name('dashboard');
    Route::get('billing', Billing::class)->name('billing');
    Route::get('profile', Profile::class)->name('profile');
    Route::get('tables', Tables::class)->name('tables');
    Route::get('notifications', Notifications::class)->name("notifications");
    Route::get('virtual-reality', VirtualReality::class)->name('virtual-reality');
    Route::get('static-sign-in', StaticSignIn::class)->name('static-sign-in');
    Route::get('static-sign-up', StaticSignUp::class)->name('static-sign-up');
    Route::get('rtl', RTL::class)->name('rtl');

    Route::get('tabel-keluhan', TabelKeluhan::class)->name('tabel-keluhan.index');
    Route::get('tabel-pembayaran', TabelPembayaran::class)->name('tabel-pembayaran.index');


   });

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
     Route::get('pelanggan', Pelanggan::class)->name('pelanggan');
    Route::post('pelanggan/add', TambahUsers::class)->name('tambah-users');
    Route::put('pelanggan/update/{id}', [Pelanggan::class, 'edit'])->name('pelanggan.edit');
    Route::delete('pelanggan/destroy/{id}', [Pelanggan::class, 'destroy'])->name('pelanggan.destroy');

    Route::get('karyawan', Karyawan::class)->name('karyawan');
    Route::post('karyawan/add', TambahUsers::class)->name('tambah-users');
    Route::put('karyawan/update/{id}', [Karyawan::class, 'edit'])->name('karyawan.edit');
    Route::delete('karyawan/destroy/{id}', [Karyawan::class, 'destroy'])->name('karyawan.destroy');

});
