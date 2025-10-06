<!--
=========================================================
* Material Dashboard 2 - v3.0.0
=========================================================

* Product Page: https://www.creative-tim.com/product/material-dashboard
* Copyright 2021 Creative Tim (https://www.creative-tim.com) & UPDIVISION (https://www.updivision.com)
* Licensed under MIT (https://www.creative-tim.com/license)
* Coded by www.creative-tim.com & www.updivision.com

=========================================================

* The above copyright notice and this permission notice shall be included in all copies or substantial portions of the Software.
-->
<!DOCTYPE html>
<html lang='en' dir="{{ Route::currentRouteName() == 'rtl' ? 'rtl' : '' }}">

<head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="apple-touch-icon" sizes="76x76" href="{{ secure_asset('assets') }}/img/apple-icon.png">
    <link rel="icon" type="image/png" href="{{ secure_asset('assets') }}/img/favicon.png">
    <title>
        Sukabumi Network
    </title>

    <!-- Metas -->
    @if (env('IS_DEMO'))
        <meta name="keywords"
            content="creative tim, updivision, material, html dashboard, laravel, livewire, laravel livewire, alpine.js, html css dashboard laravel, material dashboard laravel, livewire material dashboard, material admin, livewire dashboard, livewire admin, web dashboard, bootstrap 5 dashboard laravel, bootstrap 5, css3 dashboard, bootstrap 5 admin laravel, material dashboard bootstrap 5 laravel, frontend, responsive bootstrap 5 dashboard, material dashboard, material laravel bootstrap 5 dashboard" />
        <meta name="description"
            content="Dozens of handcrafted UI components, Laravel authentication, register & profile editing, Livewire & Alpine.js" />
        <meta itemprop="name" content="Material Dashboard 2 Laravel Livewire by Creative Tim & UPDIVISION" />
        <meta itemprop="description"
            content="Dozens of handcrafted UI components, Laravel authentication, register & profile editing, Livewire & Alpine.js" />
        <meta itemprop="image"
            content="https://s3.amazonaws.com/creativetim_bucket/products/600/original/material-dashboard-laravel-livewire.jpg" />
        <meta name="twitter:card" content="product" />
        <meta name="twitter:site" content="@creativetim" />
        <meta name="twitter:title" content="Material Dashboard 2 Laravel Livewire by Creative Tim & UPDIVISION" />
        <meta name="twitter:description"
            content="Dozens of handcrafted UI components, Laravel authentication, register & profile editing, Livewire & Alpine.js" />
        <meta name="twitter:creator" content="@creativetim" />
        <meta name="twitter:image"
            content="https://s3.amazonaws.com/creativetim_bucket/products/600/original/material-dashboard-laravel-livewire.jpg" />
        <meta property="fb:app_id" content="655968634437471" />
        <meta property="og:title" content="Material Dashboard 2 Laravel Livewire by Creative Tim & UPDIVISION" />
        <meta property="og:type" content="article" />
        <meta property="og:url" content="https://www.creative-tim.com/live/material-dashboard-laravel-livewire" />
        <meta property="og:image"
            content="https://s3.amazonaws.com/creativetim_bucket/products/600/original/material-dashboard-laravel-livewire.jpg" />
        <meta property="og:description"
            content="Dozens of handcrafted UI components, Laravel authentication, register & profile editing, Livewire & Alpine.js" />
        <meta property="og:site_name" content="Creative Tim" />
    @endif
    <!--     Fonts and icons     -->
    <link rel="stylesheet" type="text/css"
        href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900|Roboto+Slab:400,700" />
    <!-- Nucleo Icons -->
    <link href="{{ secure_asset('assets') }}/css/nucleo-icons.css" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="{{ secure_asset('assets') }}/css/nucleo-svg.css" rel="stylesheet" />
    <!-- Font Awesome Icons -->
    <script src="https://kit.fontawesome.com/42d5adcbca.js" crossorigin="anonymous"></script>
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons+Round" rel="stylesheet">
    <!-- CSS Files -->
    <link id="pagestyle" href="{{ secure_asset('assets') }}/css/material-dashboard.css?v=3.0.0" rel="stylesheet" />

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    @livewireStyles

    <style>
        .data-hover:hover {
            background-color: #f5f5f5;
        }

        .modal-backdrop {
            z-index: 1040000 !important;
        }

        .modal {
            z-index: 1050000 !important;
        }

        div:where(.swal2-container) {
            z-index: 1060000 !important;
        }

        /* Warna Theme Biru Modern */
        df-messenger {
            --df-messenger-bot-message: #e5efff;
            --df-messenger-user-message: #e5efff;
            --df-messenger-font-color: #020a14;
            --df-messenger-button-titlebar-color: #4285f4;
            --df-messenger-chat-background-color: #f9f9f9;
            --df-messenger-titlebar-background: linear-gradient(135deg, #4285f4 0%, #34a853 100%);
            z-index: 200000 !important;
            position: absolute;
        }

        /* Animasi & Border Radius */
        df-messenger-chat-bubble {
            border-radius: 12px !important;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1) !important;
            transition: transform 0.2s ease-in-out;
        }

        /* Tombol Menu Interaktif */
        .df-messenger-chips {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .df-messenger-chip {
            background: #4285f4 !important;
            color: white !important;
            border-radius: 20px !important;
            padding: 8px 16px !important;
            margin: 4px !important;
            transition: all 0.3s ease;
        }
        .df-messenger-chip:hover {
            background: #3367d6 !important;
            transform: translateY(-2px);
        }
    </style>
</head>

<body
    class="g-sidenav-show {{ Route::currentRouteName() == 'rtl' ? 'rtl' : '' }} {{ Route::currentRouteName() == 'register' || Route::currentRouteName() == 'static-sign-up' ? '' : 'bg-gray-200' }}">
    {{ $slot }}

    @livewire('components.buat-keluhan-modal')
    @livewire('components.login-modal')
    <script src="{{ secure_asset('assets') }}/js/core/popper.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/custom.js"></script>
    <script src="{{ secure_asset('assets') }}/js/core/bootstrap.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/plugins/perfect-scrollbar.min.js"></script>
    <script src="{{ secure_asset('assets') }}/js/plugins/smooth-scrollbar.min.js"></script>
    @stack('js')
    <script>
        var win = navigator.platform.indexOf('Win') > -1;
        if (win && document.querySelector('#sidenav-scrollbar')) {
            var options = {
                damping: '0.5'
            }
            Scrollbar.init(document.querySelector('#sidenav-scrollbar'), options);
        }
    </script>
    <!-- Github buttons -->
    <script async defer src="https://buttons.github.io/buttons.js"></script>
    <!-- Control Center for Material Dashboard: parallax effects, scripts for the example pages etc -->
    <script src="{{ secure_asset('assets') }}/js/material-dashboard.min.js?v=3.0.0"></script>

     @if(session()->has('alert'))
        <script>
            Swal.fire({
                title: '{{ session('alert')['title'] }}',
                text: '{{ session('alert')['message'] }}',
                icon: '{{ session('alert')['type'] }}',
                position: 'center',
                showConfirmButton: true,
                timer: 5000
            });
        </script>
    @endif

    @livewireScripts

   <script src="https://www.gstatic.com/dialogflow-console/fast/messenger/bootstrap.js?v=1"></script>

   <df-messenger
    chat-icon="https:&#x2F;&#x2F;cdn-icons-png.flaticon.com&#x2F;512&#x2F;8943&#x2F;8943377.png"
    intent="WELCOME"
    chat-title="{{ auth()->check() ? auth()->user()->name : 'Anonymous' }}"
    agent-id="76064ace-fcb9-4d65-9eb2-1e86f7c4ff57"
    session-id="{{ auth()->check() ? 'user-'.auth()->id() : 'guest-'.session()->getId() }}"
    language-code="en"
    user-id="CUSTOM_SESSION_ID"
    ></df-messenger>
</body>

<script src="https://app.sandbox.midtrans.com/snap/snap.js"
            data-client-key="{{ config('services.midtrans.client_key') }}"></script>
@if(View::hasSection('midtrans'))


    <script>
        document.addEventListener('livewire:init', function () {
            Livewire.on('midtrans-pay', function (data) {

                window.snap.pay(data.snapToken, {
                    onSuccess: function(result) {
                        Livewire.dispatch('pembayaran-berhasil', result);
                    },
                    onPending: function(result) {
                        Livewire.dispatch('pembayaran-pending', result);
                    },
                    onError: function(result) {
                        Livewire.dispatch('pembayaran-error', result);
                    },
                    onClose: function() {
                        console.log('Popup ditutup tanpa pembayaran');
                    }
                });
            });
        });
    </script>
@endif

<script>
    document.querySelector('df-messenger')
        .addEventListener('df-response-received', function(event) {
        const detectIntentResponse = event.detail.response;

        if (!detectIntentResponse) return;

        const intent = detectIntentResponse.queryResult?.intent?.displayName || '';
        const fulfillmentMessages = detectIntentResponse.queryResult?.fulfillmentMessages || [];
            fulfillmentMessages.forEach(msg => {

            if (msg.payload?.midtrans?.snap_token) {
                const token = msg.payload.midtrans.snap_token;
                console.warn('Tidak ada messages di response:', token);
                snap.pay(token, {
                onSuccess: function(result){ console.log('Pembayaran sukses', result); },
                onPending: function(result){ console.log('Pembayaran pending', result); },
                onError: function(result){ console.error('Pembayaran gagal', result); }
                });
            }
            });
    });

    // Contoh: jika kamu pakai Dialogflow Messenger Webhook
    window.addEventListener('df-response-received', function(event) {
        const responses = event.detail.response.queryResult.fulfillmentMessages;
        responses.forEach(msg => {
            if (msg.payload && msg.payload.openModal) {
                // Trigger modal Bootstrap (ganti id sesuai modal kamu)
                if (msg.payload.openModal === 'buatKeluhan') {
                    const modal = new bootstrap.Modal(document.getElementById('buatKeluhanModal'));
                    setTimeout(() => {
                        modal.show();
                        const dfMessenger = document.querySelector('df-messenger');
                        const payload = [
                            {
                                "type": "description",
                                "text": ["Menunggu proses pengisian form...."]
                            }
                        ];
                        dfMessenger.renderCustomCard(payload);
                    }, 3000);

                }
                if (msg.payload.openModal === 'buatAkun') {
                    const modal = new bootstrap.Modal(document.getElementById('buatAkun'));
                    setTimeout(() => {
                        modal.show();
                        const dfMessenger = document.querySelector('df-messenger');
                        const payload = [
                            {
                                "type": "description",
                                "text": ["Menunggu proses pengisian form...."]
                            }
                        ];
                        dfMessenger.renderCustomCard(payload);
                    }, 3000);
                }
                if (msg.payload.openModal === 'redirectWeb') {
                    const parameters = event.detail.response.queryResult.parameters;
                    window.location.href = parameters.redirect_url;
                }
            }
        });
    });



    let closedBySubmit = false; // deklarasi di global scope

    document.addEventListener('DOMContentLoaded', function () {
        const keluhanModal = document.getElementById('buatKeluhanModal');
        const loginModal = document.getElementById('buatAkun');
        if (keluhanModal) {
            keluhanModal.addEventListener('hidden.bs.modal', function () {
                if (closedBySubmit) {
                    // reset flag setelah submit sukses
                    closedBySubmit = false;
                    return;
                }

                const dfMessenger = document.querySelector('df-messenger');
                if (dfMessenger) {
                    const options = [
                                    'Buat akun',
                                    'Buat keluhan',
                                    'Bayar tagihan',
                                    'Cek keluhan',
                                    'Cek tagihan',
                                    'Hubungi CS',
                                    'Download Invoice'
                                ];
                    const payload = [
                        {
                            "type": "description",
                            "text": ["Anda telah membatalkan pembuatan keluhan. Ada lagi yang bisa saya bantu ?"]
                        },
                        {
                            "type" : "chips",
                            options: options.map(text => ({ text }))
                        }
                    ];

                     const event = new CustomEvent('df-messenger-request-event', {
                        detail: { eventName: 'buatkeluhan_final' } // event di Dialogflow
                    });
                    dfMessenger.dispatchEvent(event);
                    dfMessenger.renderCustomCard(payload);
                }
            });
        }

        loginModal.addEventListener('hidden.bs.modal', function () {
            if (closedBySubmit) {
                // reset flag setelah submit sukses
                closedBySubmit = false;
                return;
            }

            const dfMessenger = document.querySelector('df-messenger');
            if (dfMessenger) {
                const payload = [
                    {
                        "type": "description",
                        "text": ["Anda telah membatalkan pembuatan Akun. Ada lagi yang bisa saya bantu ?"]
                    }
                ];
                dfMessenger.renderCustomCard(payload);
            }
        });
    });

    document.addEventListener('livewire:init', function () {
        Livewire.on('buatKeluhan', function(data) {
            const modalEl = document.getElementById('buatKeluhanModal');
            const modal = bootstrap.Modal.getInstance(modalEl)
            || new bootstrap.Modal(modalEl);

            closedBySubmit = true; // set flag dulu
            modal.hide();          // baru tutup modal

            const dfMessenger = document.querySelector('df-messenger');
            const payload = [
                {
                    "type": "description",
                    "text": ["Keluhan anda sudah selesai dibuat. Apakah ada lagi yang bisa saya bantu ?"]
                }
            ];
            dfMessenger.renderCustomCard(payload);
        });
        Livewire.on('buatAkun', function(data) {
            const modalEl = document.getElementById('buatAkun');
            const modal = bootstrap.Modal.getInstance(modalEl)
            || new bootstrap.Modal(modalEl);

            closedBySubmit = true; // set flag dulu
            modal.hide();          // baru tutup modal

            const dfMessenger = document.querySelector('df-messenger');
            const payload = [
                {
                    "type": "description",
                    "text": ["Selamat... Akun baru anda telah selesai dibuat, Anda sudah bisa login dengan menekan button dibawah, Apakah ada lagi yang bisa saya bantu lagi ?"]
                },
                {
                    "link" : "{{ route('login') }}",
                    "text": "Masuk Laman Login",
                    "type": "button",
                        "icon": {
                            "color": "#206ed3ff",
                            "type": "chevron_right"
                        }
                },
            ];
            dfMessenger.renderCustomCard(payload);
            dfMessenger.sendEvent({ name: 'buatakun_final' });
        });
    });


    document.addEventListener('livewire:load', () => {
        Livewire.on('redirect', (data) => {
            setTimeout(() => {
                window.location.href = data.url;
            }, data.delay ?? 0);
        });
    });
</script>

</html>
