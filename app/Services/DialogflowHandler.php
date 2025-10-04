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
    protected $userId = 2;
    protected $buttonLamanLogin;
    protected $welcomeChipsGuest = [
                                    'Buat akun',
                                    'Hubungi CS',
                                    "Login"
                                ];
    protected $welcomeChipsLogin = [
                                    'Buat akun',
                                    'Buat keluhan',
                                    'Bayar tagihan',
                                    'Cek keluhan',
                                    'Cek tagihan',
                                    'Hubungi CS',
                                    'Download Invoice'
                                ];

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
            'buatAkun_nama'      => $this->handleCreateAccount($parameters, $session),
            'buatAkun_email'     => $this->handleCreateAccount($parameters, $session),
            'buatAkun_password'  => $this->handleCreateAccount($parameters, $session),
            'buatAkun_verifikasi'=> $this->handleCreateAccount($parameters, $session),

            // --- Buat Keluhan ---
            'buatKeluhan'            => $this->handleComplaint($parameters, $session),
            'buatKeluhan - judul'      => $this->handleComplaint($parameters, $session),
            'buatKeluhan - deskripsi keluhan'  => $this->handleComplaint($parameters, $session),
            'buatKeluhan_verifikasi' => $this->handleComplaint($parameters, $session),

            'bayarTagihan'    => $this->handleBillPayment($parameters, $session),
            'bayarTagihan_select'    => $this->handleBillPaymentFilter($parameters, $session),
            'bayarTagihan_proses'    => $this->processBillPayment($outputContexts, $session, $queryResult),

            'CekKeluhan'    => $this->handleComplaintList($queryText, $parameters, $session),
            'CekKeluhan_filter'    => $this->handleComplaintListFilter($queryText, $parameters, $session),
            'CekKeluhan_filter - nama' => $this->handleComplaintListFilter($queryText, $parameters, $session),


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

            $chipsPart = $this->createChipsResponse($this->welcomeChipsLogin);

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
                                $this->payloadDescription(null, ["Ok, saya batalkan permintaan anda. Apa ada lagi yang bisa saya bantu ?"])
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

            $chipsPart = $this->payloadChips($this->welcomeChipsLogin);

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

            $chipsPart = $this->payloadChips($this->welcomeChipsGuest);

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

        // if ($this->isAnonymous || $this->userId === null) {
        //     return $this->createLoginRequiredResponse('cek tagihan');
        // }

        $filledParams = array_filter($params, function ($value) {
            return !empty($value);
        });

        if(count($filledParams) === 0) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Boleh beritahu saya invoice yang ingin di download     ?"])
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["tagihaninvoice_filter", "tagihaninvoice_context"], $session)['outputContexts']
            ];
        }

        $userId = $this->userId;
        // $userId = 20;

        // Inisialisasi query
        $query = Tagihan::query()->where('user_id', $userId)->where('status_pembayaran', 'lunas');

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



        $listText = [];
        $redirectWeb = [];
        $url = rtrim(env('APP_STATIC_URL'), '/ ');

        foreach ($bills as $bill) {
            $paket = $bill->langganan->paket->nama_paket;
            $tgl_jatuh_tempo = $bill->tgl_jatuh_tempo;
            $jumlah_tagihan = formatRupiah($bill->jumlah_tagihan);

            $listText[] = "Invoice {$paket} - Tenggat {$tgl_jatuh_tempo} - {$jumlah_tagihan}";

            $fileName = 'struk_' . $bill->id . '.pdf';

            // Generate invoice
            $invoiceService->generate($bill, $fileName);

            Storage::disk('invoices')->download($fileName);

            $redirectWeb[] = [
                "redirect_url" => $url . "/download-invoice/" . $fileName
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
                                        $this->payloadDescription(null, ["Berikut invoice sesuai dengan yang anda minta, Silahkan klik untuk mendownload invoice.", "Selain itu apakah ada invoice lain yang ingin anda cek ?"]),
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
                'outputContexts' => []
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
                'outputContexts' => $this->payloadContext(["bayartagihan_filter", "bayartagihan_context"], $session)['outputContexts']
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
                'outputContexts' => []
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

        if (!empty($params['status'])) {
            $query->where('status_pembayaran', 'belum_lunas');
        }

        $bills = $query->get();

        if ($bills->isEmpty()) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                    [
                                        $this->payloadDescription(null, ["Tidak ada tagihan yang sesuai dengan kriteria anda, Apakah anda ingin membayar tagihan yang belum lunas ?"])
                                    ],
                            ],
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["bayarTagihan"], $session)['outputContexts']
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

    protected function handleComplaintList($queryText, array $parameters, string $session)
    {
        if ($this->isAnonymous && $this->userId === null) {
            return $this->createLoginRequiredResponse('cek keluhan');
        }

        $nonEmptyParams = array_filter($parameters);

        if ($nonEmptyParams) {
            return $this->handleComplaintListFilter($parameters, $session);
        } else {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Sebelumnya, boleh anda jelaskan keluhan yang ingin anda cek ?"])
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["cekkeluhan_filter", "cekkeluhan_context"], $session)['outputContexts']
            ];
        }

    }


    protected function handleComplaintListFilter($queryText, array $parameters, string $session)
    {
        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('cek keluhan');
        }

        $userId = $this->userId;

        if(!empty($parameters['cancelIntent'])) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                        $this->payloadDescription(null, ["Pengecekan keluhan dibatalkan, apakah ada lagi yang bisa saya bantu ? "]),
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => []
            ];
        }

        $filledParams = array_filter($parameters, function ($value) {
            return !empty($value);
        });

        if(count($filledParams) === 0) {
            $response =  [
                'followupEventInput' => $this->payloadEventTrigger("cekkeluhan_filter-nama", ['judul_tiket' => $queryText]),
                'outputContexts' => $this->payloadContext(["CekKeluhan_filter-followup"], $session)['outputContexts']
            ];

            return $response;
        }

        // $userId = 10;

        // Inisialisasi query
        $query = Tiket::query()->where('user_id', $userId);

        // Filter berdasarkan judul
        if (!empty($parameters['judul_tiket'])) {

            $words = explode(" ", strtolower($parameters['judul_tiket']));

            $stopWords = ['saya', 'ingin', 'yang', 'dan', 'tentang', 'itu', 'di', 'ke', 'dengan'];
            $filteredWords = array_diff($words, $stopWords);

            $query->where(function ($q) use ($filteredWords) {
                foreach ($filteredWords as $word) {
                    $q->orWhere('judul', 'like', '%' . $word . '%');
                }
            });
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
            return [
                'fulfillmentMessages' => [

                    [
                        'payload' => [
                            'richContent' => [
                                    [
                                        $this->payloadDescription(null, ["Saya tidak menemukan keluhan yang anda maksud, Apakah keluhan yang anda maksud itu keluhan yang masih proses ?"])
                                    ],
                            ],
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["cekKeluhan", "cekKeluhan_context"], $session)['outputContexts']
            ];
        }

        $listText = [];
        $redirectWeb = [];
        $url = rtrim(env('APP_STATIC_URL'), '/ ');

        foreach ($keluhan as $keluhan) {
            $judul = $keluhan->judul;
            $status = $keluhan->status;

            $listText[] = "Keluhan {$judul} - Status {$status}";

            $redirectWeb[] = [
                "redirect_url" => $url . "/tabel-keluhan/lihat/" . $keluhan->id
            ];
        }

        $listKeluhan = $this->payloadList($listText, null, 'forwardWeb', $redirectWeb);

        return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    ...$listKeluhan
                                ],
                                [
                                        $this->payloadDescription(null, ["Berikut saya tampilkan keluhan sesuai dengan yang anda minta, Silahkan klik keluhan jika ingin melihat detail selengkapnya.", "Apa ada keluhan yang ingin di cek lagi ?"]),
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

            $response = [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                    $this->payloadDescription(null, ["Mohon jelaskan lebih rinci tagihan yang ingin anda cek ?"])
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["cektagihan_filter", "cekTagihan_context"], $session)['outputContexts']
            ];
            Log::info("User asked to check bills without parameters: ", $response);
            return $response;
        }

    }

    protected function handleBillListFilter(array $params, string $session)
    {

        if ($this->isAnonymous || $this->userId === null) {
            return $this->createLoginRequiredResponse('cek tagihan');
        }

        $userId = $this->userId;
        // $userId = 10;

        if(!empty($parameters['cancelIntent'])) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                        $this->payloadDescription(null, ["Pengecekan tagihan dibatalkan, apakah ada lagi yang bisa saya bantu ? "]),
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => []
            ];
        }

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
                                    $this->payloadDescription(null, ["Ups, Sepertinya tidak ada tagihan yang sesuai dengan kriteria anda. Apakah tagihan yang ingin di cek itu tagihan yang belum lunas ?"])
                                ],
                            ]
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["cekKeluhan", "cekKeluhan_context"], $session)['outputContexts']
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
    }

    /* ========================================================
     *          FUNGSI BANTUAN RESPONSE & SESSION
     * ======================================================== */

    public static function payloadEventTrigger(string $eventName, array $eventParameter): array
    {
        return [
            'name' => $eventName,  // nama event di Dialogflow
            'languageCode' => 'id',        // sesuaikan bahasa agent-mu
            'parameters' => [
                ...$eventParameter
            ]
        ];
    }
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
                    'payload' => [
                        'richContent' => [
                            [
                                $this->payloadDescription(null, $textElement)
                            ]
                        ]
                    ]
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
        $textPart = [
                        'fulfillmentMessages' => [
                            [
                                'payload' => [
                                    'richContent' => [
                                        [
                                            $this->payloadDescription(null, [
                                                "Maaf, saya tidak mengerti pertanyaan Anda." ,"Pertanyaan yang bisa kami jawab harus berkaitan dengan layanan yang kami sediakan dibawah."
                                            ])
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ];

        $chipsPart = $this->isAnonymous ? $this->createChipsResponse($this->welcomeChipsGuest) : $this->createChipsResponse($this->welcomeChipsLogin);

        $response = [
            'fulfillmentMessages' => array_merge(
                $textPart['fulfillmentMessages'],
                $chipsPart['fulfillmentMessages']
            )
        ];

        return $response;
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

