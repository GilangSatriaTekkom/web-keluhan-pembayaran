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

        if (Str::startsWith($sessionId, 'user-')) {
            $userId = Str::after($sessionId, 'user-');
            $user = User::find($userId);
            $this->isAnonymous = !$user;
            $this->userId = $user?->id;
        }

        if (in_array("home", $parameters)) {
            return $this->cancelIntent($outputContexts);
        }

        if ($intent === 'Default Welcome Intent')
        {
            return $this->handleWelcomeIntent();
        }

        if (array_key_exists('yes_no', $parameters)) {


            $cekKeluhanBuat = $parameters['yes_no'] == 'yes' ? $this->handleComplaint($parameters, $session) : $this->intentFinal($parameters, $session, $intent);

            $cekTagihanHubungiCS = $parameters['yes_no'] == 'yes' ? $this->hubungiCS() : $this->intentFinal($parameters, $session, $intent);
        }


        return match ($intent) {
            'buatAkun'           => $this->handleCreateAccount($parameters, $session),
            'buatAkun_final'     => $this->intentFinal($parameters, $session, $intent),
            'buatAkun_nama'      => $this->handleCreateAccount($parameters, $session),
            'buatAkun_email'     => $this->handleCreateAccount($parameters, $session),
            'buatAkun_password'  => $this->handleCreateAccount($parameters, $session),
            'buatAkun_verifikasi'=> $this->handleCreateAccount($parameters, $session),

            'buatKeluhan'            => $this->handleComplaint($parameters, $session),
            'buatKeluhan_final'            => $this->intentFinal($parameters, $session, $intent),
            'buatKeluhan - judul'      => $this->handleComplaint($parameters, $session),
            'buatKeluhan - deskripsi keluhan'  => $this->handleComplaint($parameters, $session),
            'buatKeluhan_verifikasi' => $this->handleComplaint($parameters, $session),

            'bayarTagihan'    => $this->handleBillPayment($parameters, $session),
            'bayarTagihan_final'    => $this->intentFinal($parameters, $session, $intent),
            'bayarTagihan_select'    => $this->handleBillPaymentFilter($parameters, $session),
            'bayarTagihan_proses'    => $this->processBillPayment($outputContexts, $session, $queryResult),

            'CekKeluhan'    => $this->handleComplaintList($queryText, $parameters, $session),
            'cekKeluhan_final'    => $this->intentFinal($parameters, $session, $intent),
            'CekKeluhan_filter'    => $this->handleComplaintListFilter($queryText, $parameters, $session),
            'CekKeluhan_filter - nama' => $this->handleComplaintListFilter($queryText, $parameters, $session),


            'CekTagihan'    => $this->handleBillList($queryText, $parameters, $session),
            'cekTagihan_final'    => $this->intentFinal($parameters, $session, $intent),
            'CekTagihan_filter'    => $this->handleBillListFilter($parameters, $session),

            'TagihanInvoice'    => $this->downloadInvoice($parameters, $session, $invoiceService),
            'tagihanInvoice_final'    => $this->intentFinal($parameters, $session, $intent),
            'TagihanInvoice_filter'    => $this->downloadInvoice($parameters, $session, $invoiceService),

            'inginLogin' => $this->redirectToLogin(),
            'forwardWeb' => $this->forwardWeb($parameters),
            'cancelIntent' => $this->cancelIntent($outputContexts),
            'cekKeluhan_to_buatkeluhan' => $cekKeluhanBuat,
            'cektagihan_to_hubungics' => $cekTagihanHubungiCS,
            'endIntent' => $this->endIntent($outputContexts),

            default => $this->defaultResponse()
        };
    }

    protected function endIntent($outputContexts) {
        $resetContexts = array_map(function ($ctx) {
            return [
                'name' => $ctx['name'],
                'lifespanCount' => 0
            ];
        }, $outputContexts);

        return  [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [
                            [
                                $this->payloadDescription(null, ["Terimakasih, bila anda butuh sesuatu, bilang saja"]),
                            ]
                        ]
                    ]
                ]
                ],
                'outputContexts' => $resetContexts
        ];
    }

    protected function hubungiCS() {
        return [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        "richContent" => [
                            [
                                [
                                    "type" => "description",
                                    "text" => [
                                        "Silahkan tekan tombol dibawah ini, nantinya anda akan diarahkan ke Whatsapp nomor Customer Service dari layanan kami."
                                    ]
                                ],
                                [
                                    "text" => "Hubungi via WhatsApp",
                                    "icon" => [
                                        "color" => "#25D366",
                                        "type" => "chevron_right"
                                    ],
                                    "link" => "https://wa.me/6289609875689",
                                    "type" => "button"
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];
    }


    protected function forwardWeb($url)
    {
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
                    'payload' => [
                        "richContent"=> [
                            [
                                $this->payloadDescription(null, ["Tentu, Untuk login ke akun anda, silakan klik tombol di bawah ini."]),
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

        $chipsPart = !$this->isAnonymous ?  $this->payloadChips($this->welcomeChipsLogin) : $this->payloadChips($this->welcomeChipsGuest);

        return  [
            'fulfillmentMessages' => [
                [
                    'payload' => [
                        'richContent' => [
                            [
                                $this->payloadDescription(null, ["Baik, Kembali lagi ke menu awal, Apa ada yang bisa saya bantu ?"]),
                                $chipsPart
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

    protected function intentFinal($parameter, $session, $intent) {

        $description = [
            'buatAkun_final' => ["Ok, silahkan isi kembali form pembuatan akun baru."],
            'buatKeluhan_final' => ["Ok, silahkan isi kembali form pembuatan keluhan baru."],
            'bayarTagihan_final' => ["Ok, silahkan sebutkan tagihan yang ingin dibayar."],
            'cekKeluhan_final' => ["Ok, silahkan sebutkan keluhan yang ingin dicek."],
            'cekTagihan_final' => ["Ok, silahkan sebutkan tagihan yang ingin dicek."],
            'tagihanInvoice_final' => ["Ok, silahkan sebut invoice yang ingin di download."],
            'cekKeluhan_to_buatkeluhan' => [""]
        ];

        $outputContexts = [
            'buatAkun_final' => ["buatakun_context", "buatakun_filter"],
            'buatKeluhan_final' => ["buatkeluhan_context", "buatkeluhan_filter"],
            'bayarTagihan_final' => ["bayartagihan_context", "bayartagihan_filter"],
            'cekKeluhan_final' => ["cekkeluhan_context", "cekkeluhan_filter"],
            'cekTagihan_final' => ["cektagihan_context", "cektagihan_filter"],
            'tagihanInvoice_final' => ["tagihaninvoice_context", "tagihaninvoice_filter"],
            'cekKeluhan_to_buatkeluhan' => [""]
        ];

        $yesOrNo = $parameter['yes_no'] ?? null;
        $chipsPart = $this->isAnonymous ? $this->payloadChips($this->welcomeChipsGuest) : $this->payloadChips($this->welcomeChipsLogin);

        if ($yesOrNo == 'no') {
             return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                        $this->payloadDescription(null, ["Baik, Apa ada lagi yang bisa saya bantu ?"]),
                                        $chipsPart
                                ]
                            ]

                        ],
                    ]
                ],
                "outputContexts" => []
            ];
        } else {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                        $this->payloadDescription(null, $description[$intent]),
                                ]
                            ]

                        ]
                    ]
                ],
                "outputContexts" => $this->payloadContext($outputContexts[$intent], $session)['outputContexts']
            ];
        }
    }

    protected function downloadInvoice(array $params, string $session, $invoiceService) {

        if ($this->isAnonymous || $this->userId === null) {
            return $this->createLoginRequiredResponse('cek tagihan');
        }

        $isTagihanEmpty = Tagihan::where('user_id', $this->userId)
                            ->where('status_pembayaran', 'lunas')
                            ->doesntExist();

        if ($isTagihanEmpty) {
            return [
                'fulfillmentMessages' => [

                    [
                        'payload' => [
                            'richContent' => [
                                    [
                                        $this->payloadDescription(null, ["Sepertinya anda belum mempunyai tagihan yang sudah lunas, Harap lunasi tagihan anda terlebih dahulu.", " apa ada hal lain yang bisa saya bantu ?"])
                                    ],
                            ],
                        ]
                    ]
                ],
                'outputContexts' => []
            ];
        }

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
                                    $this->payloadDescription(null, ["Boleh beritahu saya invoice yang ingin di download ?"])
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["tagihaninvoice_filter", "tagihaninvoice_context"], $session)['outputContexts']
            ];
        }

        if(!empty($parameters['cancelIntent'])) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                        $this->payloadDescription(null, ["Baik, jika anda tidak jadi mendownload invoice, apakah ada lagi yang bisa saya bantu ? "]),
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => []
            ];
        }

        $userId = $this->userId;

        $query = Tagihan::query()->where('user_id', $userId)->where('status_pembayaran', 'lunas');

        if (!empty($params['tanggal'])) {
            $tanggal = $params['tanggal'] ?? null;

            if ($tanggal) {
                $query->whereDate('created_at', Carbon::parse($tanggal)->format('Y-m-d'));
            }
        }

        if(!empty($params['filter'])) {
            foreach ($params['filter'] as $filter) {
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
                        break;
                    default:
                        break;
                }
            }
        }

        if (!empty($params['id-invoice'])) {
            $query->where('id', $params['id-invoice']);
        }

        if (!empty($params['bulan_number'])) {
            $tanggal = $params['bulan_number'] ?? null;

            if ($tanggal) {
                $query->whereMonth('created_at', $tanggal);
            }
        }

        $bills = $query->get();
        Log::warning($bills);

        if ($bills->isEmpty()) {
            [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                        $this->payloadDescription(null, ["Sepertinya tidak ada invoice untuk kriteria yang anda minta.", "Apakah ingin mencoba mencari invoice yang  lain ?"]),
                                ]
                            ]

                        ]
                    ]
                ],
                "outputContexts" => $this->payloadContext(["tagihaninvoice_final_context"], $session)['outputContexts']
            ];
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
                ],
                "outputContexts" => $this->payloadContext(["tagihaninvoice_final_context"], $session)['outputContexts']
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
        // if ($this->isAnonymous) {
        //     return $this->createLoginRequiredResponse('membayar tagihan');
        // }

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

        if(!empty($parameters['cancelintent'])) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                        $this->payloadDescription(null, ["Baik, jika anda tidak jadi bayar tagihan, apakah ada lagi yang bisa saya bantu ? "]),
                                ]
                            ]
                        ]
                    ]
                ],
                'outputContexts' => []
            ];
        }

        $userId = $this->userId;

        $query = Tagihan::query()->where('user_id', $userId)->where('status_pembayaran', 'belum_lunas');

        if (!empty($parameters['tanggal'])) {
            $query->whereDate('created_at', \Carbon\Carbon::parse($parameters['tanggal'])->format('Y-m-d'));
        }

        if (!empty($params['bulan_number'])) {
            $tanggal = $params['bulan_number'] ?? null;

            if ($tanggal) {
                $query->whereMonth('created_at', $tanggal)->orWhereMonth('tgl_jatuh_tempo', $tanggal);
            }
        }

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

        if (!empty($parameters['filter'])) {
            $filter = $parameters['filter'];
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
                        break;
                    default:
                        break;
                }
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

        $billIds = $bills->pluck('id')->toArray();

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
                                    $this->payloadDescription(null, ["Saya cek Ada " . count($bills) . " tagihan yang harus dibayar. Silakan tekan tagihan yang mau dibayar yaa!", "Selain itu, apa ada tagihan yang ingin dibayar lagi ?"]),
                                ]
                            ]
                        ]
                    ]
                ],
                "outputContexts" => $this->payloadContext(["bayartagihan_final_context"], $session)['outputContexts']
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
                                        $this->payloadDescription(null, ["Tinggal satu tagihan lagi yang harus dibayar, Tekan tagihan kalau mau dibayar yaa! ", "Selain itu, apa ada tagihan yang ingin dibayar lagi ?"]),
                                ]
                            ]
                        ]
                    ]
                ],
                "outputContexts" => $this->payloadContext(["bayartagihan_final_context"], $session)['outputContexts']
            ];
    }

    protected function processBillPayment($inputData, string $session, $queryResult)
    {
        $nomorTagihan = null;

        if (isset($inputData['queryResult']) && isset($inputData['queryResult']['outputContexts'])) {
            $outputContexts = $inputData['queryResult']['outputContexts'];
        }
        else if (is_array($inputData) && isset($inputData[0]['name']) && isset($inputData[0]['parameters'])) {
            $outputContexts = $inputData;
        }
        else {
            Log::error("Unknown data format received");
            return $this->createTextResponse("Terjadi kesalahan sistem. Silakan coba lagi.");
        }

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
        if ($this->isAnonymous) {
            return $this->createLoginRequiredResponse('cek keluhan');
        }

        $isTagihanEmpty = Tiket::where('user_id', $this->userId)
                            ->doesntExist();

        if ($isTagihanEmpty) {
            return [
                'fulfillmentMessages' => [

                    [
                        'payload' => [
                            'richContent' => [
                                    [
                                        $this->payloadDescription(null, ["Sepertinya anda belum mempunyai keluhan apapun saat ini, Ingin membuat keluhan ?"])
                                    ],
                            ],
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["cekkeluhan_to_buatkeluhan", "cekkeluhan_context"], $session)['outputContexts']
            ];
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

        $isTagihanEmpty = Tiket::where('user_id', $this->userId)
                            ->doesntExist();

        if ($isTagihanEmpty) {
            return [
                'fulfillmentMessages' => [

                    [
                        'payload' => [
                            'richContent' => [
                                    [
                                        $this->payloadDescription(null, ["Sepertinya anda belum mempunyai keluhan apapun saat ini, Ingin membuat keluhan ?"])
                                    ],
                            ],
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["cekkeluhan_to_buatkeluhan", "cekkeluhan_context"], $session)['outputContexts']
            ];
        }

        if(!empty($parameters['cancelIntent'])) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                        $this->payloadDescription(null, ["Baik, jika anda tidak jadi cek keluhan, apakah ada lagi yang bisa saya bantu ? "]),
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

        $query = Tiket::query()->where('user_id', $userId);

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

        if (!empty($parameters['tanggal'])) {
            $query->whereDate('created_at', \Carbon\Carbon::parse($parameters['tanggal'])->format('Y-m-d'));
        }

        if (!empty($parameters['bulan_number'])) {
            $tanggal = $parameters['bulan_number'] ?? null;

            if ($tanggal) {
                $query->whereMonth('created_at', $tanggal);
            }
        }

        if (!empty($parameters['idKeluhan'])) {
            $query->where('id', $parameters['idKeluhan']);
        }

        if (!empty($parameters['filter'])) {
            foreach ($parameters['filter'] as $filter) {
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
                        break;
                    case 'menunggu':
                    case 'selesai':
                        $query->where('status', $filter);
                        break;
                    case 'kemarin':
                        $query->whereDate('created_at', now()->subDay()->toDateString());
                        break;
                    default:
                        break;
                }
            }
        }

        if (!empty($parameters['status'])) {
            $query->where('status', $parameters['status']);
        }

        $keluhan = $query->get();

        if ($keluhan->isEmpty()) {
            return [
                'fulfillmentMessages' => [

                    [
                        'payload' => [
                            'richContent' => [
                                    [
                                        $this->payloadDescription(null, ["Saya tidak menemukan keluhan yang anda maksud, Coba beritau saya lebih jelas keluhan yang anda maksud"])
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

            $listText[] = "Keluhan:'{$judul}' - Status {$status}";

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
                ],
                "outputContexts" => $this->payloadContext(["cekkeluhan_final_context"], $session)['outputContexts']
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

        $isTagihanEmpty = Tagihan::where('user_id', $this->userId)
                            ->doesntExist();

        if ($isTagihanEmpty) {
            return [
                'fulfillmentMessages' => [

                    [
                        'payload' => [
                            'richContent' => [
                                    [
                                        $this->payloadDescription(null, ["Sepertinya anda belum mempunyai tagihan sama sekali, Tagihan hanya akan muncul bila anda sudah berlangganan internet.", "Ingin menghubungi Customer Service untuk langganan internet ?"])
                                    ],
                            ],
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["cektagihan_to_hubungics", "cektagihan_context"], $session)['outputContexts']
            ];
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

        $isTagihanEmpty = Tagihan::where('user_id', $this->userId)
                            ->doesntExist();

        if ($isTagihanEmpty) {
            return [
                'fulfillmentMessages' => [

                    [
                        'payload' => [
                            'richContent' => [
                                    [
                                        $this->payloadDescription(null, ["Sepertinya anda belum mempunyai tagihan sama sekali, Tagihan hanya akan muncul bila anda sudah berlangganan internet.", "Ingin menghubungi Customer Service untuk langganan internet ?"])
                                    ],
                            ],
                        ]
                    ]
                ],
                'outputContexts' => $this->payloadContext(["cektagihan_to_hubungics", "cektagihan_context"], $session)['outputContexts']
            ];
        }

        $userId = $this->userId;

        if(!empty($parameters['cancelIntent'])) {
            return [
                'fulfillmentMessages' => [
                    [
                        'payload' => [
                            'richContent' => [
                                [
                                        $this->payloadDescription(null, ["Baik, jika anda tidak jadi cek tagihan, apakah ada lagi yang bisa saya bantu ? "]),
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

        if(!empty($params['filter'])) {
            foreach ($params['filter'] as $filter) {
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
                        break;
                    default:
                        break;
                }
            }
        }

        if (!empty($params['idTagihan'])) {
            $query->where('id', $params['idTagihan']);
        }

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
                ],
                'outputContexts' => $this->payloadContext(["cektagihan_final_context"], $session)['outputContexts']
            ];
    }

    /* ========================================================
     *          FUNGSI BANTUAN RESPONSE & SESSION
     * ======================================================== */

    public static function payloadEventTrigger(string $eventName, array $eventParameter): array
    {
        return [
            'name' => $eventName,
            'languageCode' => 'id',
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
                if (is_string($chip)) {
                    return ["text" => $chip];
                }

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
        $textElement = [
            "Sepertinya anda harus login terlebih dahulu untuk bertanya soal $intentAction, Dimohon untuk login dengan menekan tombol dibawah yaa..."
        ];

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

        $isAnonymousChip = $this->isAnonymous ? $this->payloadChips($this->welcomeChipsGuest) : $this->payloadChips($this->welcomeChipsLogin);

        $chipsPart = [
                        'fulfillmentMessages' => [
                            [
                                'payload' => [
                                    'richContent' => [
                                        [
                                            $isAnonymousChip
                                        ]
                                    ]
                                ]
                            ]
                        ]
                    ];

        $response = [
            'fulfillmentMessages' => array_merge(
                $textPart['fulfillmentMessages'],
                $chipsPart['fulfillmentMessages']
            )
        ];

        return $response;
    }


}

