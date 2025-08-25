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
use Illuminate\Support\Carbon;

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
        $queryText = $queryResult['queryText'] ?? '';
        $outputContexts = $queryResult['outputContexts'] ?? [];

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
            'buatAkun'           => $this->handleCreateAccount($parameters, $session),
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
            'bayarTagihan_select'    => $this->handleBillPaymentFilter($parameters, $session),
            'bayarTagihan_proses'    => $this->processBillPayment($outputContexts, $session),

            'CekKeluhan'    => $this->handleComplaintList($parameters, $session),
            'CekKeluhan_filter'    => $this->handleComplaintListFilter($parameters, $session),


            'CekTagihan'    => $this->handleBillList($queryText, $parameters, $session),
            'CekTagihan_filter'    => $this->handleBillListFilter($parameters, $session),


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
    protected function handleCreateAccount(array $parameters, string $session)
    {
        $collectedParams = $this->getSessionDataBuatAkun($session, 'collected_account_params') ?? [];

        $allParams = array_merge($collectedParams, $parameters);

        // Define the required parameter keys
        $requiredParams = ['nama', 'email', 'password'];

        $missingParams = [];
        foreach ($requiredParams as $param) {
            if (empty($allParams[$param])) {
                $missingParams[] = $param;
            }
        }

        if (empty($missingParams)) {
            // Clear the stored data after account creation if needed
            $richContent[] =
                [
                    'type' => 'description',
                    'title' => "Berikut data akun anda",
                    'text' => [
                        "Nama :" . $allParams['nama'],
                        "Email: " . $allParams['email'],
                        "Password: " . $allParams['password']
                    ],
                ];

            $nextIntents = ['buatAkun_context','buatAkun_verifikasi'];
            foreach ($nextIntents as $intent) {
                $contexts[] = [
                    'name' => $session . '/contexts/' . $intent,
                    'lifespanCount' => 1
                ];
            }

            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                $richContent,
                            [
                                [
                                    'type' => 'info',
                                    'subtitle' => ["Semua data sudah lengkap, apakah anda yakin ingin membuat akun ?"]
                                ]
                            ]
                        ]

                        ]
                    ]
                ],
                'outputContexts' => $contexts
            ];
        }

        $this->saveSessionDataBuatAkun($session, 'collected_account_params', $allParams);

        // NOW, guide the user to the next missing field.
        if (in_array('nama', $missingParams)) {
            $context = ["buatAkun_context","buatAkun_nama"];
            return $this->createContextResponse("Tentu, mari kita buat akun baru. Masukkan nama Anda:", $context, $session);
        }
        elseif (in_array('email', $missingParams)) {
            $context = ["buatAkun_context","buatAkun_email"];
            // You can personalize this since you know their name!
            $name = $allParams['nama'] ?? '';
            $response = "Terima kasih" . ($name ? ", $name" : "") . ". Sekarang, masukkan alamat email Anda:";
            return $this->createContextResponse($response, $context, $session);
        }
        elseif (in_array('password', $missingParams)) {
            $context = ["buatAkun_context","buatAkun_password"];
            return $this->createContextResponse("Terima kasih. Terakhir, buat kata sandi untuk akun Anda:", $context, $session);
        }
    }

    protected function handleAccountName(array $parameters, string $session)
    {
        $name = data_get($parameters, 'person.0.name', '');

        if (empty($name) || strlen($name) < 3) {
            return $this->createContextResponse(
                "Nama terlalu pendek, minimal 3 karakter. Masukkan nama lagi:",

                ['buatAkun_context','buatAkun_nama'],
                $session,
            );
        }
        $this->saveSessionData($session, ['name' => $name]);
        return $this->handleCreateAccount(['nama' => $name], $session);
    }

    protected function handleAccountEmail(array $parameters, string $session)
    {
        $email = $parameters['email'] ?? '';
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->createContextResponse(
                "Format email tidak valid. Masukkan email lagi:",

                ['buatAkun_context','buatAkun_email'],
                $session
            );
        }
        if (User::where('email', $email)->exists()) {
            return $this->createContextResponse(
                "Email sudah terdaftar. Masukkan email lain:",

                ['buatAkun_context','buatAkun_email'],
                $session
            );
        }
        $this->saveSessionData($session, ['email' => $email]);
        return $this->handleCreateAccount(['email' => $email], $session);
    }

    protected function handleAccountPassword(array $parameters, string $session)
    {
        $password = $parameters['password'] ?? '';
        if (empty($password) || strlen($password) < 6) {
            return $this->createContextResponse(
                "Password terlalu pendek. Masukkan password lagi:",

                ['buatAkun_context','buatAkun_password'],
                $session,
            );
        }
        $this->saveSessionData($session, ['password' => $password]);
        return $this->handleCreateAccount(['password' => $password], $session);
    }

    protected function handleAccountConfirmation(array $parameters, string $session)
    {
        $yesNo = strtolower($parameters['yes_no'] ?? '');
        if ($yesNo !== 'yes') {
            return $this->createContextResponse(
                "Pendaftaran dibatalkan. Mau coba lagi? Masukkan nama Anda:",

                ['buatAkun_nama'],
                $session
            );
        }
        $data = $this->getSessionData($session);
        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'role' => 'pelanggan',
            'status' => 'aktif'
        ]);

        $this->clearSessionDataBuatAkun($session, 'collected_account_params');
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

        $collectedParams = $this->getSessionDataBuatAkun($session, 'collected_account_params') ?? [];

        $allParams = array_merge($collectedParams, $parameters);

        // Define the required parameter keys
        $requiredParams = ['judul', 'deskripsi'];

        $missingParams = [];
        foreach ($requiredParams as $param) {
            if (empty($allParams[$param])) {
                $missingParams[] = $param;
            }
        }

        if (empty($missingParams)) {
            // Clear the stored data after account creation if needed
            $richContent[] =
                [
                    'type' => 'description',
                    'title' => "Berikut data keluhan anda",
                    'text' => [
                        "Judul :" . $allParams['judul'],
                        "Deskripsi: " . $allParams['deskripsi'],
                    ],
                ];

            $nextIntents = ['buatKeluhan_context','buatKeluhan_verifikasi'];
            foreach ($nextIntents as $intent) {
                $contexts[] = [
                    'name' => $session . '/contexts/' . $intent,
                    'lifespanCount' => 1
                ];
            }

            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                $richContent,
                            [
                                [
                                    'type' => 'info',
                                    'subtitle' => ["Semua data sudah lengkap, apakah anda yakin ingin membuat Keluhan ?"]
                                ]
                            ]
                        ]

                        ]
                    ]
                ],
                'outputContexts' => $contexts
            ];
        }

        $this->saveSessionDataBuatAkun($session, 'collected_account_params', $allParams);

        // NOW, guide the user to the next missing field.
        if (in_array('judul', $missingParams)) {
            $context = ["buatKeluhan_context","buatKeluhan_judul"];
            return $this->createContextResponse("Tentu, mari kita buat keluhan. Apa inti permasalahan anda ?", $context, $session);
        }

        elseif (in_array('deskripsi', $missingParams)) {
            $context = ["buatKeluhan_context","buatDeskripsi_deskripsi"];
            return $this->createContextResponse("Terima kasih. Coba jelaskan lebih rinci terkait keluhan Anda ini.", $context, $session);
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

                ['buatKeluhan_judul'],
                 $session,
            );
        }
        $this->saveSessionData($session, ['judul' => $judul]);
        return $this->handleComplaint(['judul' => $judul], $session);
    }

    protected function handleComplaintDescription(array $parameters, string $session)
    {
        $deskripsi = $parameters['deskripsi'] ?? '';
        if (empty($deskripsi) || strlen($deskripsi) < 10) {
            return $this->createContextResponse(
                "Deskripsi terlalu pendek, minimal 10 karakter. Masukkan lagi:",

                ['buatKeluhan_deskripsi'],
                $session
            );
        }
        $this->saveSessionData($session, ['deskripsi' => $deskripsi]);
        return $this->handleComplaint(['deskripsi' => $deskripsi], $session);
    }

    protected function handleComplaintConfirmation(array $parameters, string $session)
    {
        $yesNo = strtolower($parameters['yes_no'] ?? '');
        if ($yesNo !== 'yes') {
            return $this->createContextResponse(
                "Keluhan dibatalkan. Masukkan judul keluhan lagi:",
                ['buatKeluhan_judul'],
                $session
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
        // if ($this->isAnonymous) {
        //     return $this->createLoginRequiredResponse('membayar tagihan');
        // }
        $nonEmptyParams = array_filter($parameters);

        if ($nonEmptyParams) {
            return $this->handleBillPaymentFilter($parameters, $session);
        } else {
            return $this->createContextResponse("Tentu, Tagihan mana yang ingin anda bayar ?", ["bayarTagihan_filter", "bayarTagihan_context"], $session);
        }
    }

    protected function handleBillPaymentFilter(array $parameters, string $session)
    {
        // if ($this->isAnonymous) {
        //     return $this->createLoginRequiredResponse('membayar tagihan');
        // }

        // $userId = $this->userId;
        $userId = 10;

        $query = Tagihan::query()->where('user_id', $userId)->where('status_pembayaran', 'belum_lunas');

        // Filter berdasarkan tanggal (sys.date)
        if (!empty($parameters['tanggal'])) {
            $query->whereDate('created_at', \Carbon\Carbon::parse($parameters['tanggal'])->format('Y-m-d'));
        }

        // Filter berdasarkan bulan (custom entity)
        if (!empty($parameters['bulan'])) {
            $query->whereMonth('created_at', \Carbon\Carbon::parse($parameters['bulan'])->month);
            $query->whereYear('created_at', \Carbon\Carbon::parse($parameters['bulan'])->year);
        }

        // Filter berdasarkan id keluhan
        if (!empty($parameters['idTagihan'])) {
            $query->where('id', $parameters['idTagihan']);
        }

        // Filter berdasarkan filterCekKeluhan (custom logic, misal status)
        if (!empty($parameters['filterCekKeluhan'])) {
            foreach ($parameters['filterCekKeluhan'] as $filter) {
                switch ($filter) {
                    case 'bulan ini':
                        $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                        break;
                    case 'bulan kemarin':
                        $query->whereMonth('created_at', now()->subMonth()->month)
                            ->whereYear('created_at', now()->subMonth()->year);
                        break;
                    case 'lama':
                        $query->oldest();
                        break;
                    case 'semua':
                        // Tidak perlu filter tambahan
                        break;
                    default:
                        // Jika tidak dikenali, abaikan
                        break;
                }
            }
        }

        // Filter parameter opsional (custom, jika ada logika khusus)
        if (!empty($parameters['parameterOpsional'])) {
            // Tambahkan logika sesuai kebutuhan
        }

        $bills = $query->get();

        Log::info("Filtered bills count: " . $bills);

        if ($bills->isEmpty()) {
            return $this->createContextResponse(
                "Tidak ada tagihan yang harus dibayar",
                ['bayarTagihan'],
                $session,
            );
        }

        foreach ($bills as $bill) {
            $richContent[] =
                [
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



        Log::info("bills" . json_encode($bills));

        return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                $richContent,
                            [
                                [
                                    'type' => 'info',
                                    'subtitle' => ["Apakah anda yakin ingin membayar tagihan ini?"]
                                ]
                            ]
                        ]

                        ]
                    ]
                ],
                'outputContexts' => [
                    [
                        'name' => $session . '/contexts/bayarTagihan_proses',
                        'lifespanCount' => 1,
                        'parameters' => [
                            'nomorTagihan' => $bills->pluck('id')->toArray()
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

    protected function processBillPayment($inputData, string $session)
    {
        $nomorTagihan = null;

        Log::info('Input data received: ', is_array($inputData) ? $inputData : [$inputData]);

        // CASE 1: Jika yang diterima adalah full Dialogflow request
        if (isset($inputData['queryResult']) && isset($inputData['queryResult']['outputContexts'])) {
            $outputContexts = $inputData['queryResult']['outputContexts'];
            Log::info("Processing full Dialogflow request");
        }
        // CASE 2: Jika yang diterima hanya outputContexts array
        else if (is_array($inputData) && isset($inputData[0]['name']) && isset($inputData[0]['parameters'])) {
            $outputContexts = $inputData;
            Log::info("Processing only outputContexts array");
        }
        // CASE 3: Format tidak dikenali
        else {
            Log::error("Unknown data format received");
            return $this->createTextResponse("Terjadi kesalahan sistem. Silakan coba lagi.");
        }

        // Cari nomorTagihan dalam outputContexts
        foreach ($outputContexts as $context) {
            if (isset($context['parameters']['nomorTagihan']) && is_array($context['parameters']['nomorTagihan'])) {
                $nomorTagihan = (int) $context['parameters']['nomorTagihan'][0];
                Log::info("Found nomorTagihan in context: " . $nomorTagihan);
                break;
            }
        }

        Log::info("Processing payment for bill ID: " . $nomorTagihan);

        if ($nomorTagihan === null) {
            Log::error("Nomor tagihan tidak ditemukan dalam request");
            return $this->createTextResponse("Nomor tagihan tidak ditemukan. Silakan mulai proses pembayaran kembali.");
        }

        // ... sisa kode pembayaran ...
        $tagihan = Tagihan::where('user_id', $this->userId)
                        ->where('id', $nomorTagihan)
                        ->first();

        if (!$tagihan) {
            return $this->createContextResponse(
                "Tagihan tidak ditemukan. Silakan pilih lagi:",
                ['bayarTagihan-pilih'],
                $session
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
            Log::info("SnapToken generated successfully for bill ID: " . $nomorTagihan);
        } catch (\Exception $e) {
            Log::error('Midtrans Error: ' . $e->getMessage(), $params);
            return $this->createTextResponse("Terjadi error saat memproses pembayaran.");
        }

        return [
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

    protected function handleComplaintList(array $parameters, string $session)
    {
        if ($this->isAnonymous && $this->userId === null) {
            return $this->createLoginRequiredResponse('cek keluhan');
        }

        $nonEmptyParams = array_filter($parameters);

        if ($nonEmptyParams) {
            return $this->handleComplaintListFilter($parameters, $session);
        } else {
            return $this->createContextResponse("Tentu, Keluhan mana yang ingin anda lihat ?", ["cekKeluhan_filter", "cekKeluhan_context"], $session);
        }

    }


    protected function handleComplaintListFilter(array $parameters, string $session)
    {
        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('cek keluhan');
        }

        $userId = $this->userId;

        // $userId = 10;

        // Inisialisasi query
        $query = Tiket::query()->where('user_id', $userId);

        // Filter berdasarkan judul
        if (!empty($parameters['judul_tiket'])) {
            $query->where('category', 'like', '%' . $parameters['judul_tiket'][0] . '%');
        }

        // Filter berdasarkan tanggal (sys.date)
        if (!empty($parameters['tanggal'])) {
            $query->whereDate('created_at', \Carbon\Carbon::parse($parameters['tanggal'])->format('Y-m-d'));
        }

        // Filter berdasarkan bulan (custom entity)
        if (!empty($parameters['bulan'])) {
            $query->whereMonth('created_at', \Carbon\Carbon::parse($parameters['bulan'])->month);
            $query->whereYear('created_at', \Carbon\Carbon::parse($parameters['bulan'])->year);
        }

        // Filter berdasarkan id keluhan
        if (!empty($parameters['idKeluhan'])) {
            $query->where('id', $parameters['idKeluhan']);
        }

        // Filter berdasarkan filterCekKeluhan (custom logic, misal status)
        if (!empty($parameters['filterCekKeluhan'])) {
            foreach ($parameters['filterCekKeluhan'] as $filter) {
                switch ($filter) {
                    case 'bulan ini':
                        $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                        break;
                    case 'bulan kemarin':
                        $query->whereMonth('created_at', now()->subMonth()->month)
                            ->whereYear('created_at', now()->subMonth()->year);
                        break;
                    case 'lama':
                        $query->oldest();
                        break;
                    case 'semua':
                        // Tidak perlu filter tambahan
                        break;
                    case 'menunggu':
                    case 'selesai':
                        $query->where('status', $filter);
                        break;
                    default:
                        // Jika tidak dikenali, abaikan
                        break;
                }
            }
        }

        // Filter parameter opsional (custom, jika ada logika khusus)
        if (!empty($parameters['parameterOpsional'])) {
            // Tambahkan logika sesuai kebutuhan
        }

        $keluhan = $query->get();

        if ($keluhan->isEmpty()) {
            return $this->createTextResponse("Tidak ada keluhan");
        }

        return $this->createComplaintList($keluhan, $session);
    }

    protected function createComplaintList($keluhan, string $session): array
    {
        $descriptions = [];
        foreach ($keluhan as $item) {
            $descriptions[] = "ID: {$item->id} | Judul: {$item->category} | Status: {$item->status}";
        }

        return [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [
                            [
                                [
                                    "type" => "description",
                                    "title" => 'Berikut keluhan yang anda ingin cek',
                                    "text"  => $descriptions
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }


    /* ========================================================
     *          FUNGSI CEK LIHAT TAGIHAN
     * ======================================================== */

    protected function handleBillList($queryText, array $parameters, string $session)
    {

        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('cek tagihan');
        }

        $nonEmptyParams = array_filter($parameters);

        if ($nonEmptyParams) {
            return $this->handleBillListFilter($parameters, $session);
        } else {
            return $this->createContextResponse("Tentu, Tagihan mana yang ingin anda lihat ?", ["CekTagihan_filter", "cekTagihan_context"], $session);
        }

    }

    protected function handleBillListFilter(array $params, string $session)
    {

        if ($this->isAnonymous || $this->userId === null) {
            return $this->createLoginRequiredResponse('cek tagihan');
        }

        $userId = $this->userId;
        // $userId = 10;

        // Inisialisasi query
        $query = Tagihan::query()->where('user_id', $userId);

        // Filter berdasarkan periode (sys.date)
        if (!empty($params['periode'])) {
            // Jika periode berupa range (misal "2025-08"), bisa disesuaikan logikanya
            $query->whereDate('periode', '=', Carbon::parse($params['periode'])->format('Y-m-d'));
        }

        // Filter berdasarkan status (custom entity)
        if (!empty($params['status'])) {
            $query->where('status_pembayaran', $params['status'] == 'belum lunas' ? 'belum_lunas' : $params['status']);
        }

        if (!empty($params['tanggal'])) {
            $tanggal = $params['tanggal'] ?? null;

            if ($tanggal) {
                $query->whereDate('created_at', Carbon::parse($tanggal)->format('Y-m-d'));
            }
        }

        if(!empty($params['filterTagihan'])) {
            foreach ($params['filterTagihan'] as $filter) {
                switch ($filter) {
                    case 'bulan ini':
                        $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                        break;
                    case 'bulan kemarin':
                        $query->whereMonth('created_at', now()->subMonth()->month)
                            ->whereYear('created_at', now()->subMonth()->year);
                        break;
                    case 'lama':
                        $query->oldest();
                        break;
                    case 'semua':
                        // Tidak perlu filter tambahan
                        break;
                    default:
                        // Jika tidak dikenali, abaikan
                        break;
                }
            }
        }
        // Filter berdasarkan metode bayar
        if (!empty($params['metode'])) {
            $query->where('metode_bayar', $params['metode']);
        }

        // Filter berdasarkan id-tagihan
        if (!empty($params['id-tagihan'])) {
            $query->where('id', $params['id-tagihan']);
        }

        // Eksekusi query
        $tagihan = $query->get();
        Log::info("Filtered tagihan count: " . $tagihan);

        // Jika tidak ada parameter sekalipun, ambil semua data
        // (logika di atas sudah otomatis, karena query tetap kosong jika params kosong)

        // Bisa return data atau sesuaikan dengan kebutuhan
        return $this->createCollectionTagihanTextResponse("Berikut list tagihan sesuai yang anda inginkan", $tagihan, $params);
    }

    protected function createCollectionTagihanTextResponse(string $title, $collection, $param): array
    {
        // Jika collection kosong → beri pesan default
        if (empty($collection) || (is_object($collection) && method_exists($collection, 'isEmpty') && $collection->isEmpty())) {
            return [
                'fulfillmentMessages' => [
                    ['text' => ['text' => ["Tidak ada data yang sesuai."]]]
                ]
            ];
        }

        Log::debug("Creating rich content for collection: " . json_encode($collection));

        $jumlah = 0; // default 0 untuk menghindari null

        if (!empty($param['filterTagihan']) && in_array('jumlah', $param['filterTagihan'])) {
            Log::debug("Item jumlah_tagihan: ");
            $jumlah = $collection->sum('jumlah_tagihan');
        }

        $totalTagihan = $jumlah > 1 ? formatRupiah($jumlah): null;

        // Format setiap item dalam collection
        $items = collect($collection)->map(function ($item) {
            if (is_object($item) && isset($item->id)) {
                // Pastikan property ada sebelum mengakses
                $id = $item->id ?? 'N/A';
                $dueDate = formatTanggalIndonesia($item->tgl_jatuh_tempo) ?? 'N/A';

                $amount = formatRupiah($item->jumlah_tagihan);


                return "ID: {$id} | Jatuh Tempo: {$dueDate} | Tagihan: {$amount}";
            } elseif (is_array($item)) {
                // Jika array, gabungkan semua nilai jadi satu baris
                return implode(' | ', array_filter($item, function ($value) {
                    return !is_null($value) && $value !== '';
                }));
            } else {
                // Jika sudah berupa string
                return (string) $item;
            }
        })->toArray();


        $hasil = [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [
                            [
                                [
                                    "type" => "description",
                                    "title" => $title,
                                    "text" => $items
                                ],
                                [
                                    "type" => "description",
                                    "text" => $totalTagihan ? ["Total Tagihan: " . $totalTagihan] : []
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
        // Return format Dialogflow dengan richContent
        return $hasil;
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

    protected function createContextResponse(string $text, array $nextIntents, string $session): array
    {
        $contexts = [];
        foreach ($nextIntents as $intent) {
            $contexts[] = [
                'name' => $session . '/contexts/' . $intent,
                'lifespanCount' => 1
            ];
        }

        return [
            'fulfillmentMessages' => [
                ['text' => ['text' => [$text]]]
            ],
            'outputContexts' => $contexts
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

    //SESSION HANDLER UNTUK PEMBUATAN AKUN
    protected function saveSessionDataBuatAkun(string $session, string $key, array $data): void
    {
        $cacheKey = 'dialogflow_' . $session;
        $currentData = cache()->get($cacheKey, []);

        // Simpan data di dalam array berkey
        $currentData[$key] = array_merge($currentData[$key] ?? [], $data);

        cache()->put($cacheKey, $currentData, 300); // 300 detik = 5 menit
    }

    protected function getSessionDataBuatAkun(string $session, string $key): array
    {
        $cacheKey = 'dialogflow_' . $session;
        $sessionData = cache()->get($cacheKey, []);
        return $sessionData[$key] ?? [];
    }

    protected function clearSessionDataBuatAkun(string $session, string $key): void
    {
        $cacheKey = 'dialogflow_' . $session;
        $sessionData = cache()->get($cacheKey, []);

        unset($sessionData[$key]);

        cache()->put($cacheKey, $sessionData, 300);
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
