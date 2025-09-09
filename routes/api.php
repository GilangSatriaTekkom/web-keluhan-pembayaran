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

Route::post('/dialogflow-webhook', function (Request $request) {
    return response()->json(
        app(DialogflowHandler::class)->handleWebhook($request)
    );
});
