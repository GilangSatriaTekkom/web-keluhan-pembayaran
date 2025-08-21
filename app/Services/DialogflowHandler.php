<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\Controller;
use App\Http\Livewire\LihatData\LihatPembayaran;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Tiket;
use App\Models\Tagihan;
use Midtrans\Snap;
use Midtrans\Config;

class DialogflowHandler extends Controller
{
    protected $isAnonymous = true;
    protected $userId;

    public function handleWebhook(Request $request)
    {
        Log::info('Dialogflow webhook request:', $request->all());

        $queryResult = $request->input('queryResult');
        $intent = $queryResult['intent']['displayName'] ?? null;
        $parameters = $queryResult['parameters'] ?? [];
        $session = $request->input('session');
        $sessionId = Str::afterLast($session, '/');

        // Cek user login dari sessionId
        if (Str::startsWith($sessionId, 'user-')) {
            $userId = Str::after($sessionId, 'user-');
            $user = User::find($userId);
            $this->isAnonymous = !$user;
            $this->userId = $user?->id;
        }

        // Handle Welcome Intent
        if ($intent === 'Default Welcome Intent')
        {
            return $this->handleWelcomeIntent();
        }



        return match ($intent) {
            // --- Pendaftaran akun ---
            'buatAkun_nama'      => $this->handleAccountName($parameters, $session),
            'buatAkun_email'     => $this->handleAccountEmail($parameters, $session),
            'buatAkun_password'  => $this->handleAccountPassword($parameters, $session),
            'buatAkun_verifikasi'=> $this->handleAccountConfirmation($parameters, $session),

            // --- Buat Keluhan ---
            'buatKeluhan'            => $this->handleComplaint($parameters, $session),
            'buatKeluhan_judul'      => $this->handleComplaintTitle($parameters, $session),
            'buatKeluhan_deskripsi'  => $this->handleComplaintDescription($parameters, $session),
            'buatKeluhan_verifikasi' => $this->handleComplaintConfirmation($parameters, $session),

            'bayarTagihan'    => $this->handleBillPayment($parameters, $session),
            'bayarTagihan_select'    => $this->processBillPayment($parameters, $session),
            'CekKeluhan'    => $this->handleComplaintList($session),
            'CekTagihan'    => $this->handleBillList($session),

            default => $this->defaultResponse()
        };
    }

    protected function handleWelcomeIntent()
    {
        $textPart = $this->createTextResponse(
            "Halo! Selamat datang di layanan kami. Apa ada yang bisa kami bantu?"
        );

        $chipsPart = $this->createChipsResponse([
            'Buat akun',
            'Buat keluhan',
            'Bayar tagihan',
            'Cek keluhan',
            'Cek tagihan',
            'Hubungi CS',
            'Pertanyaan layanan'
        ]);

        return [
            'fulfillmentMessages' => array_merge(
                $textPart['fulfillmentMessages'],
                $chipsPart['fulfillmentMessages']
            )
        ];
    }


    /* ========================================================
     *               FUNGSI PENDAFTARAN AKUN
     * ======================================================== */
    protected function handleAccountName(array $parameters, string $session)
    {
        $name = data_get($parameters, 'person.0.name', '');
        Log::debug("message", ['name' => $name]);
        if (empty($name) || strlen($name) < 3) {
            return $this->createContextResponse(
                "Nama terlalu pendek, minimal 3 karakter. Masukkan nama lagi:",
                $session,
                'buatAkun_nama'
            );
        }
        $this->saveSessionData($session, ['name' => $name]);
        return $this->createContextResponse(
            "Nama disimpan. Sekarang masukkan email Anda:",
            $session,
            'buatAkun_email'
        );
    }

    protected function handleAccountEmail(array $parameters, string $session)
    {
        $email = $parameters['email'] ?? '';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->createContextResponse(
                "Format email tidak valid. Masukkan email lagi:",
                $session,
                'buatAkun_email'
            );
        }
        if (User::where('email', $email)->exists()) {
            return $this->createContextResponse(
                "Email sudah terdaftar. Masukkan email lain:",
                $session,
                'buatAkun_email'
            );
        }
        $this->saveSessionData($session, ['email' => $email]);
        return $this->createContextResponse(
            "Email disimpan. Sekarang buat password minimal 6 karakter:",
            $session,
            'buatAkun_password'
        );
    }

    protected function handleAccountPassword(array $parameters, string $session)
    {
        $password = $parameters['password'] ?? '';
        if (empty($password) || strlen($password) < 6) {
            return $this->createContextResponse(
                "Password terlalu pendek. Masukkan password lagi:",
                $session,
                'buatAkun_password'
            );
        }
        $this->saveSessionData($session, ['password' => $password]);
        $data = $this->getSessionData($session);
        $preview = "Nama: {$data['name']}\nEmail: {$data['email']}\nPassword: {$password}";
        return $this->createContextResponse(
            "Berikut data akun Anda:\n$preview\nApakah sudah benar? (yes/no)",
            $session,
            'buatAkun_konfirmasi'
        );
    }

    protected function handleAccountConfirmation(array $parameters, string $session)
    {
        $yesNo = strtolower($parameters['yes_no'] ?? '');
        if ($yesNo !== 'yes') {
            return $this->createContextResponse(
                "Pendaftaran dibatalkan. Mau coba lagi? Masukkan nama Anda:",
                $session,
                'buatAkun_nama'
            );
        }
        $data = $this->getSessionData($session);
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'role' => 'pelanggan',
            'status' => 'aktif'
        ]);
        return $this->createTextResponse("Akun berhasil dibuat untuk {$data['name']}. Silakan login!");
    }

    /* ========================================================
     *               FUNGSI BUAT KELUHAN
     * ======================================================== */
    protected function handleComplaint(array $parameters, string $session)
    {
        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('membuat keluhan');
        }
    }
    protected function handleComplaintTitle(array $parameters, string $session)
    {
        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('membuat keluhan');
        }
        $judul = $parameters['judul'] ?? '';
        if (empty($judul) || strlen($judul) < 5) {
            return $this->createContextResponse(
                "Judul keluhan terlalu pendek, minimal 5 karakter. Masukkan lagi:",
                $session,
                'buatKeluhan_judul'
            );
        }
        $this->saveSessionData($session, ['judul' => $judul]);
        return $this->createContextResponse(
            "Judul disimpan. Sekarang masukkan deskripsi keluhan Anda:",
            $session,
            'buatKeluhan_deskripsi'
        );
    }

    protected function handleComplaintDescription(array $parameters, string $session)
    {
        $deskripsi = $parameters['deskripsi'] ?? '';
        if (empty($deskripsi) || strlen($deskripsi) < 10) {
            return $this->createContextResponse(
                "Deskripsi terlalu pendek, minimal 10 karakter. Masukkan lagi:",
                $session,
                'buatKeluhan_deskripsi'
            );
        }
        $this->saveSessionData($session, ['deskripsi' => $deskripsi]);
        $data = $this->getSessionData($session);
        $preview = "Judul: {$data['judul']}\nDeskripsi: {$deskripsi}";
        return $this->createContextResponse(
            "Berikut keluhan Anda:\n$preview\nApakah sudah benar? (yes/no)",
            $session,
            'buatKeluhan_konfirmasi'
        );
    }

    protected function handleComplaintConfirmation(array $parameters, string $session)
    {
        $yesNo = strtolower($parameters['yes_no'] ?? '');
        if ($yesNo !== 'yes') {
            return $this->createContextResponse(
                "Keluhan dibatalkan. Masukkan judul keluhan lagi:",
                $session,
                'buatKeluhan_judul'
            );
        }
        $data = $this->getSessionData($session);
        Tiket::create([
            'user_id' => $this->userId,
            'category' => $parameters['judul'],
            'description' => $parameters['deskripsi'],
            'status' => 'menunggu'
        ]);
        return $this->createTextResponse("Keluhan berhasil dibuat. Tim kami akan segera memprosesnya.");
    }

    /* ========================================================
     *               FUNGSI BAYAR TAGIHAN
     * ======================================================== */

    protected function handleBillPayment(array $parameters, string $session)
    {
        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('membayar tagihan');
        }

        $bills = Tagihan::where('user_id', $this->userId)
                ->where('status_pembayaran', '!=', 'lunas')
                ->get();

        if (!$bills) {
            return $this->createContextResponse(
                "Tidak ada tagihan yang harus dibayar",
                $session,
                'bayarTagihan'
            );
        }

        $richContent = [
            [
                'type' => 'info',
                'title' => "📋 Daftar Tagihan Belum Lunas",
                'subtitle' => "Pilih tagihan yang ingin dibayar:"
            ]
        ];

        foreach ($bills as $bill) {
            $richContent[] = [
                'type' => 'chips',
                'options' => [
                    [
                        'text' => "Bayar " . $bill->id,
                        'link' => url('/generate-midtrans/' . $bill->id),
                        'event' => [
                            'name' => 'bayar_tagihan',
                            'parameters' => [
                                'nomor_tagihan' => $bill->id
                            ]
                        ]
                    ]
                ]
            ];

            $richContent[] = [
                'type' => 'description',
                'title' => "Tagihan #" . $bill->id,
                'text' => [
                    "💵 Jumlah:" . formatRupiah($bill->jumlah_tagihan),
                    "📅 Jatuh Tempo: " . $bill->tgl_jatuh_tempo,
                    "🔄 Status: " . $this->getStatusBadge($bill->status_pembayaran)
                ],
                'icon' => [
                    'type' => 'receipt',
                    'color' => '#FF5722'
                ]
            ];
        }

        return [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [$richContent]
                    ]
                ]
            ]
        ];
    }

    protected function getStatusBadge($status)
    {
        $badges = [
            'belum_bayar' => '🔴 Belum Bayar',
            'jatuh_tempo' => '⚠️ Jatuh Tempo',
            'pending' => '🟡 Pending'
        ];

        return $badges[$status] ?? ucfirst($status);
    }

    protected function processBillPayment($parameters, string $session)
    {
        $nomorTagihan = $parameters ?? null;

        $tagihan = Tagihan::where('user_id', $this->userId)
                        ->where('id', $nomorTagihan)
                        ->first();

        if (!$tagihan) {
            return $this->createContextResponse(
                "Tagihan tidak ditemukan. Silakan pilih lagi:",
                $session,
                'bayarTagihan-pilih'
            );
        }

        // Generate SnapToken
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
        \Midtrans\Config::$isSanitized = true;
        \Midtrans\Config::$is3ds = true;

        $user = User::find($tagihan->user_id);

        $params = [
            'transaction_details' => [
                'order_id' => 'TAGIHAN-' . $tagihan->id . '-' . time(),
                'gross_amount' => $tagihan->jumlah_tagihan,
            ],
            'customer_details' => [
                'first_name' => $user->name,
                'email' => $user->email,
                'phone' => $user->phone,
            ],
            'enabled_payments' => ["other_qris", 'bank_transfer', 'credit_card', 'gopay', 'shopeepay'],
        ];

        try {
            $snapToken = \Midtrans\Snap::getSnapToken($params);
        } catch (\Exception $e) {
            \Log::error('Midtrans Error: ' . $e->getMessage(), $params);
            return $this->createTextResponse("Terjadi error saat memproses pembayaran.");
        }

        return  [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'midtrans' => [
                            'snap_token' => $snapToken
                        ]
                    ]
                ]
            ]
        ];
    }

    /* ========================================================
     *               FUNGSI CEK LIHAT KELUHAN
     * ======================================================== */

    protected function handleComplaintList(string $session)
    {
        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('cek keluhan');
        }

        $keluhan = Tiket::where('user_id', $this->userId)
                        ->latest()
                        ->take(5)
                        ->get();

        if ($keluhan->isEmpty()) {
            return $this->createTextResponse("Anda belum memiliki keluhan.");
        }

        $list = $keluhan->map(fn($k) =>
            "- {$k->judul} [status: {$k->status}]"
        )->implode("\n");

        return $this->createTextResponse("Berikut keluhan Anda:\n$list");
    }

    /* ========================================================
     *          FUNGSI CEK LIHAT TAGIHAN
     * ======================================================== */

    protected function handleBillList(string $session)
    {
        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('cek tagihan');
        }

        $tagihan = Tagihan::where('user_id', $this->userId)
                        ->latest()
                        ->take(5)
                        ->get();

        if ($tagihan->isEmpty()) {
            return $this->createTextResponse("Anda tidak memiliki tagihan.");
        }

        $list = $tagihan->map(fn($t) =>
            "- #{$t->nomor}: Rp {$t->jumlah} [status: {$t->status}]"
        )->implode("\n");

        return $this->createTextResponse("Berikut tagihan Anda:\n$list");
    }



    /* ========================================================
     *          FUNGSI BANTUAN RESPONSE & SESSION
     * ======================================================== */
    protected function createTextResponse(string $text): array
    {
        return [
            'fulfillmentMessages' => [
                ['text' => ['text' => [$text]]]
            ]
        ];
    }

    protected function createContextResponse(string $text, string $session, string $nextIntent): array
    {
        return [
            'fulfillmentMessages' => [
                ['text' => ['text' => [$text]]]
            ],
            'outputContexts' => [
                [
                    'name' => $session . '/contexts/' . $nextIntent,
                    'lifespanCount' => 1
                ]
            ]
        ];
    }


    protected function createLoginRequiredResponse(string $intentAction = 'aksi ini'): array
    {

        // Teks yang muncul di atas button
        $textElement = [
            'type' => 'info',
            'subtitle' => ["Anda harus login terlebih dahulu untuk $intentAction."]
        ];

        // Button untuk login
        $buttonElement = [
            'type' => 'button',
            'icon' => [
                'type' => 'login',   // bisa diganti icon lain sesuai Material Icon
                'color' => '#FF9800'
            ],
            'text' => 'Login Sekarang',
            'link' => url('/sign-in'), // Ini adalah baris yang mungkin bisa menimbulkan error
            'event' => new \stdClass() // kosong, tidak ada event
        ];

        // Jika kode di dalam try berhasil, kembalikan respons ini
        return [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [
                            [
                                $textElement,    // baris 1: teks
                                $buttonElement   // baris 2: button
                            ]
                        ]
                    ]
                ]
            ],
        ];
    }


    protected function saveSessionData(string $session, array $data): void
    {
        cache()->put('dialogflow_' . $session, array_merge(
            cache()->get('dialogflow_' . $session, []),
            $data
        ), 300);
    }

    protected function getSessionData(string $session): array
    {
        return cache()->get('dialogflow_' . $session, []);
    }

    protected function defaultResponse(): array
    {
        return $this->createTextResponse("Maaf, saya tidak mengerti pertanyaan Anda.");
    }

    protected function createChipsResponse(array $options): array
    {
        return [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [[
                            [
                                'type' => 'chips',
                                'options' => array_map(fn($text) => ['text' => $text], $options)
                            ]
                        ]]
                    ]
                ]
            ]
        ];
    }

    /**
 * Membuat response button dinamis untuk Dialogflow
 *
 * @param string $text       Text yang muncul di button
 * @param string $link       URL yang dikunjungi saat button diklik
 * @param string $iconType   Jenis icon dari Material Icons (default: 'chevron_right')
 * @param string $iconColor  Warna hex icon (default: '#FF9800')
 * @param array  $event      Event Dialogflow yang ingin dijalankan (default: [])
 *
 * @return array
 */
    protected function createButtonResponse(
        string $text,
        string $link = '',
        string $iconType = 'chevron_right',
        string $iconColor = '#FF9800',
        array $event = []
    ): array {
        return [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [[
                            [
                                'type' => 'button',
                                'icon' => [
                                    'type' => $iconType,
                                    'color' => $iconColor
                                ],
                                'text' => $text,
                                'link' => $link,
                                'event' => $event
                            ]
                        ]]
                    ]
                ]
            ]
        ];
    }


}
