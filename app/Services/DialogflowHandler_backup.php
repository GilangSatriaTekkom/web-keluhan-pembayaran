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
use App\Models\Langganan;
use App\Models\PaketInternet;
use Midtrans\Snap;
use Midtrans\Config;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;

class DialogflowHandler extends Controller
{
    protected $isAnonymous = true;
    protected $userId;
    protected $buttonLamanLogin;

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

        // component
        $this->buttonLamanLogin = [
                                    "link" => route('login'),
                                    "text"=> "Masuk Laman Login",
                                    "type"=> "button",
                                        "icon"=> [
                                        "color"=> "#206ed3ff",
                                        "type"=> "chevron_right"
                                    ]
                                ];


        $invoiceService = new \App\Services\StrukPembayaran();

        // Cek user login dari sessionId
        if (Str::startsWith($sessionId, 'user-')) {
            $userId = Str::after($sessionId, 'user-');
            $user = User::find($userId);
            $this->isAnonymous = !$user;
            $this->userId = $user?->id;
        }

        if (in_array("home", $parameters)) {
            return $this->cancelIntent($outputContexts);
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
            'buatKeluhan - judul'      => $this->handleComplaintTitle($parameters, $session),
            'buatKeluhan - deskripsi keluhan'  => $this->handleComplaintDescription($parameters, $session),
            'buatKeluhan_verifikasi' => $this->handleComplaintConfirmation($parameters, $session),

            'bayarTagihan'    => $this->handleBillPayment($parameters, $session),
            'bayarTagihan_select'    => $this->handleBillPaymentFilter($parameters, $session),
            'bayarTagihan_proses'    => $this->processBillPayment($outputContexts, $session, $queryResult),

            'CekKeluhan'    => $this->handleComplaintList($parameters, $session),
            'CekKeluhan_filter'    => $this->handleComplaintListFilter($parameters, $session),


            'CekTagihan'    => $this->handleBillList($queryText, $parameters, $session),
            'CekTagihan_filter'    => $this->handleBillListFilter($parameters, $session),

            'TagihanInvoice'    => $this->downloadInvoice($parameters, $session, $invoiceService),
            'TagihanInvoice_filter'    => $this->downloadInvoice($parameters, $session, $invoiceService),

            'inginLogin' => $this->redirectToLogin(),
            'forwardWeb' => $this->forwardWeb($parameters),



            default => $this->defaultResponse()
        };
    }

    protected function forwardWeb($url)
    {
        // pastikan key yang dikirim memang ada
        if (!isset($url['redirect_url'])) {
            Log::error("redirect_link not found in payload");
            return "Parameter redirect_link tidak ditemukan.";
        }

        Log::warning($url['redirect_url']);

        return [
            'parameters' => [
                $url
            ],
            'fulfillmentMessages' => [

                [
                    'payload' => [
                        'openModal' => 'redirectWeb'
                    ]
                ]
            ]
        ];
    }

    protected function redirectToLogin()
    {

        if (!$this->isAnonymous) {
            $userName = User::where('id', $this->userId)->value('name');
            $textPart = $this->createTextResponse(
                "Anda sudah login dengan nama $userName. Silakan pilih layanan yang Anda perlukan."
            );

            $chipsPart = $this->createChipsResponse([
                'Buat akun',
                'Buat keluhan',
                'Bayar tagihan',
                'Cek keluhan',
                'Cek tagihan',
                'Hubungi CS',
                'Download Invoice'
            ]);

            return [
                'fulfillmentMessages' => array_merge(
                    $textPart['fulfillmentMessages'],
                    $chipsPart['fulfillmentMessages']
                )
            ];
        }

        return  [
            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => ["Tentu, Untuk login ke akun anda, silakan klik tombol di bawah ini."]
                    ]
                ],
                [
                    'payload' => [
                        "richContent"=> [
                            [
                                $this->buttonLamanLogin
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }

    protected function cancelIntent($outputContexts)
    {

        $resetContexts = array_map(function ($ctx) {
            return [
                'name' => $ctx['name'],
                'lifespanCount' => 0
            ];
        }, $outputContexts);

        Log::info('Resetting contexts:', $resetContexts);


        return  [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [
                            [
                                $this->payloadDescription(null, ["Ok, saya batalkan permintaan anda. ada lagi yang bisa saya bantu ?"])
                            ]
                        ]
                    ]
                ]
                ],
                'outputContexts' => $resetContexts
        ];
    }

    protected function handleWelcomeIntent()
    {
        if (!$this->isAnonymous) {
            $name = User::where('id', $this->userId)->value('name');
            $textPart = $this->payloadDescription(
                null,
                ["Halo! Selamat datang $name.","Apakah ada yang bisa saya bantu hari ini?"]
            );

            $chipsPart = $this->payloadChips([
                'Buat akun',
                'Buat keluhan',
                'Bayar tagihan',
                'Cek keluhan',
                'Cek tagihan',
                'Hubungi CS',
                'Download Invoice'
            ]);

            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $textPart,
                                    $chipsPart
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        } else {
            $textPart = $this->payloadDescription(null,
                ["Halo! Saya Assisten pembantu anda, Apa ada yang bisa saya bantu?"]
            );

            $chipsPart = $this->payloadChips([
                'Buat akun',
                'Hubungi CS',
                "Login"
            ]);

            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $textPart,
                                    $chipsPart
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
    }

    protected function downloadInvoice(array $params, string $session, $invoiceService) {

        if ($this->isAnonymous || $this->userId === null) {
            return $this->createLoginRequiredResponse('cek tagihan');
        }

        $userId = $this->userId;
        // $userId = 20;

        // Inisialisasi query
        $query = Tagihan::query()->where('user_id', $userId)->where('status_pembayaran', 'lunas');

        // Filter berdasarkan periode (sys.date)
        if (!empty($params['periode'])) {
            // Jika periode berupa range (misal "2025-08"), bisa disesuaikan logikanya
            $query->whereDate('periode', '=', Carbon::parse($params['periode'])->format('Y-m-d'));
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
                    case 'nunggak':
                        $query->where('tgl_jatuh_tempo', '<', now()->format('Y-m-d'));
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

        // Filter berdasarkan id-tagihan
        if (!empty($params['id-invoice'])) {
            $query->where('id', $params['id-invoice']);
        }

        // Filter berdasarkan bulan (custom entity)
        if (!empty($params['bulan_number'])) {
            $tanggal = $params['bulan_number'] ?? null;

            if ($tanggal) {
                $query->whereMonth('created_at', $tanggal);
            }
        }

        // Eksekusi query
        $bills = $query->get();

        if ($bills->isEmpty()) {
            return $this->createTextResponse("Tidak ada invoice");
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

        if (count($bills) > 1) {
            // ambil id tiap bills untuk chips
            $billIds = $bills->pluck('id')->toArray();

            // buat chips dari billIds
            $chips = $this->createChipsResponse(
                array_map(fn($id) => 'Invoice ' . $id, $billIds)
            );

            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                $richContent,
                                [
                                    [
                                        'type' => 'info',
                                        'subtitle' => ["Ada " . count($bills) . " tagihan yang punya invoice. Silakan pilih salah satu."]
                                    ]
                                ],
                            $chips['fulfillmentMessages'][0]['payload']['richContent'][0],
                        ]

                        ]
                    ]
                ],
                'outputContexts' => [
                    [
                        'name' => $session . '/contexts/tagihanInvoice_filter',
                        'lifespanCount' => 1,
                    ],
                    [
                        'name' => $session . '/contexts/tagihanInvoice_context',
                        'lifespanCount' => 1,
                    ]
                ]

            ];
        }

        $bill = $bills->first();

        $fileName = 'struk_' . $bill->id . '.pdf';

        // Generate invoice
        $invoiceService->generate($bill, $fileName);

        Storage::disk('invoices')->download($fileName);

        return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            "richContent"=> [
                                [
                                    [
                                        "text"=> "Download Invoice",
                                        "icon"=> [
                                            "type"=> "chevron_right",
                                            "color"=> "#d3259fff"
                                        ],
                                        "type"=> "button",
                                        "link"=> route('invoices.download', $fileName)
                                    ]
                                ]
                            ]
                        ]
                    ]
                ]
        ];
    }


    /* ========================================================
     *               FUNGSI PENDAFTARAN AKUN
     * ======================================================== */
    protected function handleCreateAccount(array $parameters, string $session)
    {

        return [
            'fulfillmentMessages' => [

                [
                    'payload' => [
                        'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Untuk membuat akun baru, silahkan isi form yang sudah disediakan."])
                                ],
                        ],
                        'openModal' => 'buatAkun'
                    ]
                ]
            ]
        ];


        $collectedParams = $this->getSessionDataBuatAkun($session, 'collected_account_params') ?? [];

        $allParams = array_merge($collectedParams, $parameters);

        $requiredParams = ['person', 'email', 'password'];

        $missingParams = [];
        foreach ($requiredParams as $param) {
            if (empty($allParams[$param])) {
                $missingParams[] = $param;
            }
        }

        if (empty($missingParams)) {
            $this->saveSessionDataBuatAkun($session, 'collected_account_params', $allParams);

            $nama = is_array($allParams['person']) ? ($allParams['person']['name'] ?? '') : $allParams['person'];

            $richContent[] =
                [
                    'type' => 'description',
                    'title' => "Berikut data akun anda",
                    'text' => [
                        "Nama :" . $nama,
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
        if (in_array('person', $missingParams)) {
            $context = ["buatAkun_context","buatAkun_nama"];
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Untuk membuat akun baru, Saya perlu mengetahui nama, email dan password yang ingin digunakan", "Boleh saya tau nama anda siapa ?"])
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => $this->contextResponse($context, $session)
            ];
        }
        elseif (in_array('email', $missingParams)) {
            $context = ["buatAkun_context","buatAkun_email"];
            // You can personalize this since you know their name!
            $name = $allParams['nama'] ?? '';
            $response = "Halo" . ($name ? ", $name" : "") . ". Sekarang, boleh beri tahu saya email yang ingin digunakan ?";
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, [$response])
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => $this->contextResponse($context, $session)
            ];
        }

        elseif (in_array('password', $missingParams)) {
            $context = ["buatAkun_context","buatAkun_password"];
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Terima kasih. Terakhir, buat kata sandi untuk akun Anda:"])
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => $this->contextResponse($context, $session)
            ];
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
        $this->saveSessionData($session, ['person' => $name]);
        return $this->handleCreateAccount(['person' => $name], $session);
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
            $this->clearSessionDataBuatAkun($session, 'collected_account_params');
            return $this->createTextResponse("Pendaftaran dibatalkan.");
        }

        $data = $this->getSessionDataBuatAkun($session, 'collected_account_params') ?? [];

        // $data = $this->getSessionData($session);

        // Sesuaikan key dengan bentuk data
        $nama = $data['person']['name'] ?? ($data['person'] ?? '');
        $email = $data['email'] ?? '';
        $password = $data['password'] ?? '';

        if (User::where('email', $email)->exists()) {
            return $this->createContextResponse(
                "Email sudah terdaftar. Masukkan email lain:",

                ['buatAkun_context','buatAkun_email'],
                $session
            );
        }

        User::create([
            'name' => $nama,
            'email' => $email,
            'password' => $password,
            'role' => 'pelanggan',
            'status' => 'aktif'
        ]);

        $this->clearSessionDataBuatAkun($session, 'collected_account_params');
        return $this->createTextResponse("Akun berhasil dibuat untuk {$nama}. Silakan login!");
    }

    /* ========================================================
     *               FUNGSI BUAT KELUHAN
     * ======================================================== */
    protected function handleComplaint(array $parameters, string $session)
    {

        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('membuat keluhan');
        }

        return [
            'fulfillmentMessages' => [

                [
                    'payload' => [
                        'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Tentu, Untuk membuat keluhan baru, silahkan isikan kedalam form yang sudah disediakan."])
                                ],
                        ],
                        'openModal' => 'buatKeluhan'
                    ]
                ]
            ]
        ];

        $collectedParams = $this->getSessionDataBuatAkun($session, 'collected_account_params') ?? [];

        $allParams = array_merge($collectedParams, $parameters);



        $requiredParams = ['judul', 'deskripsi'];

        $missingParams = [];
        foreach ($requiredParams as $param) {
            if (empty($allParams[$param])) {
                $missingParams[] = $param;
            }
        }

        if (empty($missingParams)) {
            $richContent[] =
                [
                    'type' => 'description',
                    'title' => "Berikut data keluhan anda",
                    'text' => [
                        "Kategori :" . $allParams['judul'],
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

        Log::info("Missing params for complaint:", $missingParams);

        $this->saveSessionDataBuatAkun($session, 'collected_account_params', $allParams);

        // NOW, guide the user to the next missing field.
        if (in_array('judul', $missingParams)) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Tentu, sebelum menjelaskan lebih rinci terkait keluhan anda, Boleh beri tau saya masalah yang sedang dihadapi ?"])
                                ]
                            ]
                        ]
                    ]
                ]
            ];
        }
        elseif (in_array('deskripsi', $missingParams)) {
            $judul = $allParams['judul'] ?? '';
            return  [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Baiklah, jadi masalah yang sedang anda hadapi itu terkait $judul, Boleh beri tau saya lebih rinci tentang masalah tersebut ?", "Misalkan dari kapan masalah ini terjadi dan apa saja yang sudah anda coba lakukan ?"])
                                ]
                            ]
                        ]
                    ]
                ]
            ];
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
            return $this->createTextResponse(
                "Pembuatan Keluhan dibatalkan"
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

        $isTagihanEmpty = Tagihan::where('user_id', $this->userId)
                            ->where('status_pembayaran', 'belum_lunas')
                            ->doesntExist();

        if ($isTagihanEmpty) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Belum ada lagi nih tagihan yang harus dibayar, Apakah ada yang ingin anda lakukan lagi ?"])
                                ],
                            ],
                        ]
                    ]
                ],
                $this->payloadContext(['bayarTagihan'],$session)
            ];
        }
        $nonEmptyParams = array_filter($parameters);

        if ($nonEmptyParams) {
            return $this->handleBillPaymentFilter($parameters, $session);
        } else {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Sebelum bayar tagihan, Boleh beritahu saya tagihan mana yang mau anda bayar ?."])
                                ]
                            ]
                        ]
                    ]
                ],
                $this->payloadContext(["bayarTagihan_filter", "bayarTagihan_context"], $session)
            ];
        }
    }

    protected function handleBillPaymentFilter(array $parameters, string $session)
    {
        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('membayar tagihan');
        }

        $isTagihanEmpty = Tagihan::where('user_id', $this->userId)
                            ->where('status_pembayaran', 'belum_lunas')
                            ->doesntExist();

        if ($isTagihanEmpty) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Belum ada lagi nih tagihan yang harus dibayar, Apakah ada yang ingin anda lakukan lagi ?"])
                                ],
                            ],
                        ]
                    ]
                ],
                $this->payloadContext(['bayarTagihan'],$session)
            ];
        }

        $userId = $this->userId;
        // $userId = 19;

        $query = Tagihan::query()->where('user_id', $userId)->where('status_pembayaran', 'belum_lunas');

        // Filter berdasarkan tanggal (sys.date)
        if (!empty($parameters['tanggal'])) {
            $query->whereDate('created_at', \Carbon\Carbon::parse($parameters['tanggal'])->format('Y-m-d'));
        }

        // Filter berdasarkan bulan (custom entity)
        if (!empty($params['bulan_number'])) {
            $tanggal = $params['bulan_number'] ?? null;

            if ($tanggal) {
                $query->whereMonth('created_at', $tanggal)->orWhereMonth('tgl_jatuh_tempo', $tanggal);
            }
        }

        // Filter berdasarkan id keluhan
        if (!empty($parameters['idTagihan'])) {
            $query->where('id', $parameters['idTagihan']);
        }

        if (!empty($parameters['paket_internet'])) {
            $idPaket = PaketInternet::where('nama_paket', $parameters['paket_internet'])->value('id');

            if ($idPaket) {
                $langgananIds = Langganan::where('paket_id', $idPaket)
                    ->where('user_id', $this->userId)
                    ->pluck('id');

                if ($langgananIds->isNotEmpty()) {
                    $query->where('langganan_id', $langgananIds);
                }
            }
        }

        // Filter berdasarkan filterCekKeluhan (custom logic, misal status)
        if (!empty($parameters['filterBayarTagihan'])) {
            $filter = $parameters['filterBayarTagihan'];
                switch ($filter) {
                    case 'bulan ini':
                        $query->whereMonth('created_at', now()->month)
                            ->whereYear('created_at', now()->year);
                        break;
                    case 'bulan kemarin':
                        $query->whereDate('created_at', '<', now()->startOfMonth());
                        break;
                    case 'lama':
                        $query->oldest();
                        break;
                    case 'kemarin':
                            $query->whereDate('created_at', now()->subDay()->toDateString());
                            break;
                    case 'jatuh tempo':
                        $query->whereDate('created_at', now()->toDateString());
                        break;

                    case 'tertunggak':
                        $query->whereDate('created_at', '<', now()->toDateString());
                        break;

                    case 'belum_jatuh_tempo':
                        $query->whereDate('created_at', '>', now()->toDateString());
                        break;
                    case 'semua':
                        // Tidak perlu filter tambahan
                        break;
                    default:
                        // Jika tidak dikenali, abaikan
                        break;
                }
        }

        // Filter parameter opsional (custom, jika ada logika khusus)
        if (!empty($parameters['parameterOpsional'])) {
            // Tambahkan logika sesuai kebutuhan
        }

        $bills = $query->get();

        if ($bills->isEmpty()) {
            $filtered = array_values(array_filter($parameters)); // buang null/kosong & reset index

            if (count($filtered) > 1) {
                $gabungan = implode(", ", $filtered);
            } elseif (count($filtered) === 1) {
                $gabungan = $filtered[0];
            } else {
                $gabungan = "";
            }

            return [
                'fulfillmentMessages' => [

                    [
                        'payload' => [
                            'richContent' => [
                                    [
                                        $this->payloadDescription(null, ["Saat ini, anda tidak ada tagihan yang harus dibayar, Apakah ada yang ingin anda lakukan lagi ?"])
                                    ],
                            ],
                        ]
                    ]
                ],
                $this->payloadContext(['bayarTagihan'],$session)
            ];
        }


        // ambil id tiap bills untuk chips
        $billIds = $bills->pluck('id')->toArray();

        // buat chips dari billIds
        $chips = $this->createChipsResponse($billIds);

        $listText = [];
        $listParams = [];
        foreach ($bills as $bill) {
            $paket = $bill->langganan->paket->nama_paket;
            $tgl_jatuh_tempo = $bill->tgl_jatuh_tempo;
            $jumlah_tagihan = formatRupiah($bill->jumlah_tagihan);

            $listText[] = "Paket {$paket} - Tenggat {$tgl_jatuh_tempo} - {$jumlah_tagihan}";
            $listParams[] = [
                "id_tagihan" => $bill->id,
                "yes_no" => "yes"
            ];
        }

        $listTagihan = $this->payloadList($listText, null, 'bayarTagihan_proses', $listParams);

        if (count($listTagihan) > 1) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    ...$listTagihan
                                ],
                                [
                                    $this->payloadDescription(null, ["Saya cek Ada " . count($bills) . " tagihan yang harus dibayar. Silakan tekan tagihan yang mau dibayar yaa!"]),
                                ]

                            ]
                        ]
                    ]
                ],
                'outputContexts' => [
                    [
                        'name' => $session . '/contexts/bayarTagihan_filter',
                        'lifespanCount' => 1,
                    ],
                    [
                        'name' => $session . '/contexts/bayarTagihan_context',
                        'lifespanCount' => 1,
                    ]
                ]

            ];
        }

        return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    ...$listTagihan
                                ],
                                [
                                        $this->payloadDescription(null, ["Tinggal satu tagihan lagi yang harus dibayar, Tekan tagihan kalau mau dibayar yaa! "]),
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

    protected function processBillPayment($inputData, string $session, $queryResult)
    {
        $nomorTagihan = null;

        // CASE 1: Jika yang diterima adalah full Dialogflow request
        if (isset($inputData['queryResult']) && isset($inputData['queryResult']['outputContexts'])) {
            $outputContexts = $inputData['queryResult']['outputContexts'];
        }
        // CASE 2: Jika yang diterima hanya outputContexts array
        else if (is_array($inputData) && isset($inputData[0]['name']) && isset($inputData[0]['parameters'])) {
            $outputContexts = $inputData;
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
                break;
            }
        }



        if ($nomorTagihan === null) $nomorTagihan = $queryResult['parameters']['id_tagihan'] ?? null;
        Log::info("Nomor Tagihan ditemukan: " . ($nomorTagihan ?? 'null'));
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
            return $this->createContextResponse("Tentu, Keluhan mana yang ingin anda lihat ?", ["cekKeluhan", "cekKeluhan_context"], $session);
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

        if (!empty($parameters['bulan']) || !empty($parameters['number']) || !empty($parameters['operator'])) {

            // 1. Jika ada OPERATOR (>, <, =, dll)
            if (!empty($parameters['operator'])) {
                // Jika ada number, tambahkan ke tanggal sekarang
                if (!empty($parameters['number'])) {
                    $targetDate = now()->addMonths($parameters['number']);
                } else {
                    $targetDate = now();
                }
                $query->where('created_at', $parameters['operator'], $targetDate);
            }

            // 2. Jika ada NUMBER tanpa operator (misal: 2 bulan ke depan)
            if (!empty($parameters['number']) && empty($parameters['operator'])) {
                $startDate = now();
                $endDate   = now()->addMonths($parameters['number']);
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }

            // 3. Jika ada BULAN spesifik (format bebas: Januari 2025 atau 2025-08)
            if (!empty($parameters['bulan'])) {
                $bulan = \Carbon\Carbon::parse($parameters['bulan']);
                $query->whereMonth('created_at', $bulan->month)
                    ->whereYear('created_at', $bulan->year);
            }
        }

        if (!empty($parameters['bulan_number'])) {
            $tanggal = $parameters['bulan_number'] ?? null;

            if ($tanggal) {
                $query->whereMonth('created_at', $tanggal);
            }
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
                    case 'kemarin':
                        $query->whereDate('created_at', now()->subDay()->toDateString());
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
            ],
            'outputContexts' => [
                [
                    'name' => $session . '/contexts/cekKeluhan',
                    'lifespanCount' => 0,
                    // 'parameters' => [
                    //     'nomorTagihan' => $bills->pluck('id')->toArray()
                    // ]
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
            return $this->createContextResponse("Tentu, Tagihan mana yang ingin anda lihat ?", ["CekTagihan", "cekTagihan_context"], $session);
        }

    }

    protected function handleBillListFilter(array $params, string $session)
    {

        if ($this->isAnonymous || $this->userId === null) {
            return $this->createLoginRequiredResponse('cek tagihan');
        }

        $userId = $this->userId;
        // $userId = 10;

        $query = Tagihan::query()->where('user_id', $userId);

        if (!empty($params['periode'])) {
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

        if (!empty($params['bulan_number'])) {
            $tanggal = $params['bulan_number'] ?? null;

            if ($tanggal) {
                $query->whereMonth('created_at', $tanggal);
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
                    case 'nunggak':
                        $query->where('tgl_jatuh_tempo', '<', now()->format('Y-m-d'));
                        break;
                    case 'kemarin':
                        $query->whereDate('created_at', now()->subDay()->toDateString());
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

        if (!empty($params['idTagihan'])) {
            $query->where('id', $params['idTagihan']);
        }

        // Eksekusi query
        $tagihan = $query->get();

        if ($tagihan->isEmpty()) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Ups, Sepertinya tidak ada tagihan yang sesuai dengan kriteria anda. Apakah ada yang ingin anda lakukan lagi ?"])
                                ],
                            ]
                        ]
                    ]
                ],
                $this->payloadContext(['cekTagihan'], $session)
            ];
        }

        $listText = [];
        $redirectWeb = [];
        $url = rtrim(env('APP_STATIC_URL'), '/ ');

        foreach ($tagihan as $bill) {
            $paket = $bill->langganan->paket->nama_paket;
            $tgl_jatuh_tempo = $bill->tgl_jatuh_tempo;
            $jumlah_tagihan = formatRupiah($bill->jumlah_tagihan);

            $listText[] = "Paket {$paket} - Tenggat {$tgl_jatuh_tempo} - {$jumlah_tagihan}";

            $redirectWeb[] = [
                "redirect_url" => $url . "/tabel-pembayaran/lihat/" . $bill->id
            ];
        }

        $listTagihan = $this->payloadList($listText, null, 'forwardWeb', $redirectWeb);

        return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    ...$listTagihan
                                ],
                                [
                                        $this->payloadDescription(null, ["Berikut saya tampilkan tagihan sesuai dengan yang anda minta, Silahkan klik tagihan jika ingin melihat detail selengkapnya.", "Selain itu apakah ada yang ingin anda lakukan lagi ?"]),
                                ]
                            ]

                        ]
                    ]
                ]
            ];

        return $this->createCollectionTagihanTextResponse("Berikut list tagihan sesuai yang anda inginkan", $tagihan, $params, $session);
    }

    protected function createCollectionTagihanTextResponse(string $title, $collection, $param, $session): array
    {
        // Jika collection kosong → beri pesan default
        if (empty($collection) || (is_object($collection) && method_exists($collection, 'isEmpty') && $collection->isEmpty())) {
            return [
                'fulfillmentMessages' => [
                    ['text' => ['text' => ["Tidak ada data yang sesuai."]]]
                ]
            ];
        }

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
            ],
            'outputContexts' => [
                [
                    'name' => $session . '/contexts/cekTagihan_context',
                    'lifespanCount' => 0,
                ]
            ]
        ];
        return $hasil;
    }

    /* ========================================================
     *          FUNGSI BANTUAN RESPONSE & SESSION
     * ======================================================== */

    public static function payloadDescription(string $title = null, array $textLines = null): array
    {
        return [
                    "type" => "description",
                    ...(isset($title) ? ["title" => $title] : []),
                    ...(isset($textLines) ? ["text" => $textLines] : [])
        ];

    }

    public static function payloadChips(array $chips): array
    {
        return [
            "type" => "chips",
            "options" => array_map(function ($chip) {
                // Jika string → jadikan text saja
                if (is_string($chip)) {
                    return ["text" => $chip];
                }

                // Jika array → wajib ada text, link opsional
                $option = ["text" => $chip["text"] ?? ""];
                if (isset($chip["link"])) {
                    $option["link"] = $chip["link"];
                }

                return $option;
            }, $chips)
        ];
    }

    protected function payloadContext(array $nextIntents, string $session): array
    {
        $contexts = [];
        foreach ($nextIntents as $intent) {
            $contexts[] = [
                'name' => $session . '/contexts/' . $intent,
                'lifespanCount' => 1
            ];
        }

        return [
            'outputContexts' => $contexts
        ];
    }

    public static function payloadButton(string $text, ): array
    {
        return [
            "type" => "button",
            "text" => "text",
        ];
    }

    public static function payloadList(
        array $titles,
        array $subtitles = null,
        string $eventName,
        array $arrayParameters = null
    ): array {
        $listItems = [];

        foreach ($titles as $index => $title) {
            $listItems[] = [
                "type" => "list",
                "title" => $title,
                "subtitle" => $subtitles[$index] ?? "",
                "event" => [
                    "name" => $eventName,
                    "parameters" => $arrayParameters[$index] ?? []
                ],
            ];
        }

        Log::info('Generated list items: ', $listItems);

        return $listItems;
    }





    protected function createTextResponse(string $text): array
    {
        return [
            'fulfillmentMessages' => [
                ['text' => ['text' => [$text]]]
            ]
        ];
    }

    protected function createTextChipsResponse(string $text, array $options): array
    {
        return [
            'fulfillmentMessages' => [
                ['text' => ['text' => [$text]]],
                [
                    'payload' => [
                        'richContent' => [
                            [
                                [
                                    'type' => 'chips',
                                    'options' => array_map(fn($text) => ['text' => $text], $options)
                                ]
                            ]
                        ],
                    ]
                ]
            ]
        ];
    }

    protected function contextResponse(array $nextIntents, string $session): array
    {
        $contexts = [];
        foreach ($nextIntents as $intent) {
            $contexts[] = [
                'name' => $session . '/contexts/' . $intent,
                'lifespanCount' => 1
            ];
        }

        return $contexts;
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
            "Sepertinya anda harus login terlebih dahulu untuk bertanya soal $intentAction, Dimohon untuk login dengan menekan tombol dibawah yaa..."
        ];

        // Jika kode di dalam try berhasil, kembalikan respons ini
        return [
            'fulfillmentMessages' => [
                [
                    'text' => [
                        'text' => $textElement,    // baris 1: teks
                    ],
                ],
                [
                    'payload' => [
                        'richContent' => [
                            [
                                $this->buttonLamanLogin
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
        $textPart = $this->createTextResponse(
            "Maaf, saya tidak mengerti pertanyaan Anda. Pertanyaan yang bisa kami jawab harus berkaitan dengan layanan yang kami sediakan dibawah."
        );

        $chipsPart = $this->createChipsResponse([
            'Buat akun',
            'Buat keluhan',
            'Bayar tagihan',
            'Cek keluhan',
            'Cek tagihan',
            'Hubungi CS',
            'Download Invoice'
        ]);

        return [
            'fulfillmentMessages' => array_merge(
                $textPart['fulfillmentMessages'],
                $chipsPart['fulfillmentMessages']
            )
        ];

        return $this->createTextResponse("Maaf, saya tidak mengerti pertanyaan Anda.");
    }

    public function cancelKeluhan(Request $request)
    {

        Log:info('Keluhan dibatalkan oleh user.');

        return  [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [
                            [
                                $this->payloadDescription(null, ["Ok, saya batalkan permintaan anda. ada lagi yang bisa saya bantu ?"])
                            ]
                        ]
                    ]
                ]
            ],
            'outputContexts' => []
        ];
    }


}

