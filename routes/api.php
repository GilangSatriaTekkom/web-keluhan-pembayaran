<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\KeluhanController;
use App\Http\Controllers\TagihanController;
use App\Http\Controllers\CSController;
use Illuminate\Support\Facades\Validator;

use App\Models\User;
use App\Services\DialogflowHandler;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/midtrans/notification', function (Request $request) {
    Log::info('Midtrans notification (API)', $request->json()->all());

    $notif = new \Midtrans\Notification();

    $transaction = $notif->transaction_status;
    $order_id = $notif->order_id;

    // Ambil ID tagihan dari format order_id
    $parts = explode('-', $order_id);
    $tagihanId = $parts[1] ?? null;

    if ($transaction == 'settlement' && $tagihanId) {
        \App\Models\Tagihan::where('id', $tagihanId)
            ->update(['status_pembayaran' => 'lunas']);
    }

    return response()->json(['status' => 'ok']);
});

// Route::post('/dialogflow-webhook', function (Request $request) {

//     $queryResult = $request->input('queryResult');
//     $intent = $queryResult['intent']['displayName'] ?? null;
//     $parameters = $queryResult['parameters'] ?? [];

//     $responseText = "Maaf, saya belum bisa memproses permintaan ini.";

//     // Handle Welcome Intent
//     if ($intent === 'Default Welcome Intent') {
//         $response = [
//             'fulfillmentMessages' => [
//                 [
//                     'text' => [
//                         'text' => [
//                             "Halo! Selamat datang di layanan kami. Berikut menu yang tersedia:\n\n" .
//                             "1️⃣ Buat akun\n" .
//                             "2️⃣ Buat keluhan\n" .
//                             "3️⃣ Bayar tagihan\n" .
//                             "4️⃣ Cek/lihat keluhan\n" .
//                             "5️⃣ Cek/lihat tagihan\n" .
//                             "6️⃣ Hubungi CS\n" .
//                             "7️⃣ Pertanyaan seputar layanan\n\n" .
//                             "Ketik nomor menu (1-7) atau klik tombol di bawah."
//                         ]
//                     ]
//                 ],
//                 [
//                     'payload' => [
//                         'richContent' => [
//                             [
//                                 [
//                                     'type' => 'chips',
//                                     'options' => [
//                                         ['text' => '1. Buat akun'],
//                                         ['text' => '2. Buat keluhan'],
//                                         ['text' => '3. Bayar tagihan'],
//                                         ['text' => '4. Cek keluhan'],
//                                         ['text' => '5. Cek tagihan'],
//                                         ['text' => '6. Hubungi CS'],
//                                         ['text' => '7. Pertanyaan layanan']
//                                     ]
//                                 ]
//                             ]
//                         ]
//                     ]
//                 ]
//             ]
//         ];

//         return response()->json($response);
//     }



//     switch ($intent) {
//         case 'buatAkun_verifikasi':
//             try {
//                 if($parameters['yes_no'] == 'yes') {
//                     // Ambil nama dari $parameters['person']['name']
//                     $name = $parameters['person']['name'] ?? '';
//                     $email = $parameters['email'] ?? '';
//                     $password = $parameters['password'] ?? '';

//                     // Validasi data
//                     if (empty($name)) {
//                         $responseText = "Nama tidak boleh kosong!";
//                         break;
//                     }

//                     if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
//                         $responseText = "Format email tidak valid!";
//                         break;
//                     }

//                     // Cek apakah email sudah terdaftar
//                     $existingUser = User::where('email', $email)->first();

//                     if ($existingUser) {
//                         $responseText = "Email $email sudah terdaftar. Gunakan email lain.";
//                     } else {
//                         // Buat user baru
//                         User::create([
//                             'name' => $name,
//                             'email' => $email,
//                             'password' => bcrypt($password),
//                             'role' => 'pelanggan',
//                             'status' => 'aktif'
//                         ]);
//                         $responseText = "Akun berhasil dibuat untuk $name, Silahkan login!";
//                     }
//                 } else {
//                     $responseText = "Pendaftaran akun digagalkan, adakah yang bisa saya bantu lagi ?";
//                     return response()->json([
//                         'fulfillmentText' => $responseText
//                     ]);
//                 }
//             } catch (\Exception $e) {
//                 $responseText = "Terjadi kesalahan sistem: " . $e->getMessage();
//             }
//             break;


//         case 'Buat Keluhan':
//             $controller = app(KeluhanController::class);
//             $req = new Request($parameters);
//             $controller->store($req);
//             $responseText = "Keluhan Anda telah dicatat. Terima kasih.";
//             break;

//         case 'Bayar Tagihan':
//             $controller = app(TagihanController::class);
//             $req = new Request($parameters);
//             $controller->bayar($req);
//             $responseText = "Tagihan berhasil dibayar.";
//             break;

//         case 'Cek Keluhan':
//             $controller = app(KeluhanController::class);
//             $req = new Request($parameters);
//             $data = $controller->show($req);
//             $responseText = "Status keluhan: {$data->status}";
//             break;

//         case 'Cek Tagihan':
//             $controller = app(TagihanController::class);
//             $req = new Request($parameters);
//             $data = $controller->cek($req);
//             $responseText = "Tagihan Anda: Rp{$data->jumlah}";
//             break;

//         case 'Hubungi CS':
//             $controller = app(CSController::class);
//             $req = new Request($parameters);
//             $controller->hubungi($req);
//             $responseText = "CS akan menghubungi Anda segera.";
//             break;

//         case 'Pertanyaan Umum':
//             $controller = app(PertanyaanUmumController::class);
//             $req = new Request($parameters);
//             $jawaban = $controller->jawab($req);
//             $responseText = $jawaban;
//             break;
//     }

//     return response()->json([
//         'fulfillmentText' => $responseText
//     ]);
// });

Route::post('/dialogflow-webhook', function (Request $request) {
    return response()->json(
        app(DialogflowHandler::class)->handleWebhook($request)
    );
});
