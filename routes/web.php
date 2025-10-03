<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
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
use App\Http\Livewire\StaticPage\StaticIndex;
use App\Http\Livewire\Tables;
use App\Http\Livewire\VirtualReality;
use App\Http\Livewire\TabelKeluhan;
use App\Http\Livewire\TabelPembayaran;
use App\Http\Livewire\Karyawan;
use App\Http\Livewire\Teknisi;
use App\Http\Livewire\Pelanggan;
use App\Http\Livewire\TambahUsers;
use App\Http\Livewire\Create\Teknisi as TeknisiTambah;
use App\Http\Livewire\Create\Karyawan as KaryawanTambah;
use App\Http\Livewire\Create\Pelanggan as PelangganTambah;
use App\Http\Livewire\Create\Keluhan as KeluhanTambah;
use App\Http\Livewire\LihatKeluhan;
use App\Http\Livewire\TasksKeluhan;
use App\Http\Livewire\LihatData\LihatPembayaran;
use App\Http\Livewire\Lihat\Teknisi as LihatTeknisi;
use App\Http\Livewire\Lihat\Karyawan as LihatKaryawan;
use App\Http\Livewire\Lihat\Pelanggan as LihatPelanggan;
use App\Http\Controllers\MidtransController;
use Illuminate\Support\Facades\Storage;

use App\Models\User;
use App\Services\DialogflowHandler;
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

Route::get('/', StaticIndex::class)->name('landing-page');

Route::post('/dialogflow-cancel', [\App\Services\DialogflowHandler::class, 'cancelKeluhan'])->name('dialogflow.cancel');
Route::get('/contact', function(){
    $phoneNumber = '6289609875689';
    $url = "https://wa.me/{$phoneNumber}";
    return redirect()->away($url);
})->name('contact');

Route::get('forgot-password', ForgotPassword::class)->middleware('guest')->name('password.forgot');
Route::get('reset-password/{id}', ResetPassword::class)->middleware('signed')->name('reset-password');


Route::get('/redirect-web', function () {
    // ambil url target dari query string
    $targetUrl = request()->query('redirect_url');

    Log::warning("Redirecting to: " . $targetUrl);

    return redirect()->away($targetUrl);
})->name('redirect.web');

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
    Route::get('static-sign-in', StaticSignIn::class)->name('static-sign-in');
    Route::get('static-sign-up', StaticSignUp::class)->name('static-sign-up');

    Route::get('tabel-keluhan', TabelKeluhan::class)->name('tabel-keluhan.index');
    Route::get('tabel-keluhan/lihat/{id}', LihatKeluhan::class)->name('lihat.keluhan');

    Route::get('tabel-pembayaran', TabelPembayaran::class)->name('tabel-pembayaran.index');
    Route::get('tabel-pembayaran/lihat/{id}', LihatPembayaran::class)->name('lihat.pembayaran');
    Route::get('tabel-keluhan/tambah', KeluhanTambah::class)->name('tabel-keluhan.tambah');

    Route::get('/bayar-tagihan', function () {
        return view('bayar-tagihan'); // Blade ini extend base.php
    })->name('bayar-tagihan');

    Route::get('/download-invoice/{file}', function ($file) {
        return Storage::disk('invoices')->download($file);
    })->name('invoices.download');


   });

Route::prefix('teknisi')->middleware(['auth', 'role:teknisi'])->group(function () {
    Route::get('tabel-keluhan/tasks/{id}', TasksKeluhan::class)->name('tasks.keluhan.teknisi');
});

Route::prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('tabel-keluhan/tasks/{id}', TasksKeluhan::class)->name('tasks.keluhan');
    Route::get('pelanggan', Pelanggan::class)->name('pelanggan');
    Route::get('pelanggan/lihat/{id}', LihatPelanggan::class)->name('pelanggan.lihat');
    Route::get('pelanggan/tambah', PelangganTambah::class)->name('pelanggan.tambah');
    Route::post('karyawan/add', Karyawan::class)->name('karyawan.add');
    Route::put('pelanggan/update/{id}', [Pelanggan::class, 'edit'])->name('pelanggan.edit');
    Route::delete('pelanggan/destroy/{id}', [Pelanggan::class, 'destroy'])->name('pelanggan.destroy');

    Route::get('karyawan', Karyawan::class)->name('karyawan');
    Route::get('karyawan/lihat/{id}', LihatKaryawan::class)->name('karyawan.lihat');
    Route::get('karyawan/tambah', KaryawanTambah::class)->name('karyawan.tambah');
    Route::post('karyawan/add', Karyawan::class)->name('karyawan.add');
    Route::put('karyawan/update/{id}', [Karyawan::class, 'edit'])->name('karyawan.edit');
    Route::delete('karyawan/destroy/{id}', [Karyawan::class, 'destroy'])->name('karyawan.destroy');

    Route::get('teknisi', Teknisi::class)->name('teknisi');
    Route::get('teknisi/lihat/{id}', LihatTeknisi::class)->name('teknisi.lihat');
    Route::get('teknisi/tambah', TeknisiTambah::class)->name('teknisi.tambah');
    Route::post('teknisi/add', Teknisi::class)->name('teknisi.add');
    Route::put('teknisi/update/{id}', [Teknisi::class, 'edit'])->name('teknisi.edit');
    Route::delete('teknisi/destroy/{id}', [Teknisi::class, 'destroy'])->name('teknisi.destroy');

    Route::put('tabel-keluhan/update/{id}', [Pelanggan::class, 'edit'])->name('tabel-keluhan.edit');
    Route::delete('tabel-keluhan/destroy/{id}', [Pelanggan::class, 'destroy'])->name('tabel-keluhan.destroy');

});
