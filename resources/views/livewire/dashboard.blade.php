<div>
    <!-- Navbar -->
    <!-- End Navbar -->
    <div class="container-fluid py-4">
        @auth
            @if (Auth::user()->role !== 'admin')
                <div class="row">
                        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div
                                        class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">weekend</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Tagihan</p>
                                        @if ($jumlahTagihans !== null)
                                            <h4 class="mb-0">{{ $jumlahTagihans }}</h4>
                                        @else
                                            <h4 class="mb-0">Tidak ada tagihan Sekarang</h4>
                                        @endif
                                    </div>
                                </div>
                                <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than
                                        lask week</p>
                                </div>
                            </div>
                        </div>
                        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div
                                        class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">person</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Keluhan Direspon Bulan Ini</p>
                                        @if ($keluhanDirespon)
                                            <h4 class="mb-0">{{ $keluhanDirespon }}</h4>
                                        @else
                                            <h4 class="mb-0">Tidak ada Keluhan selesai bulan ini</h4>
                                        @endif
                                    </div>
                                </div>
                                <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+3% </span>than
                                        lask month</p>
                                </div>
                            </div>
                        </div>
                    </div>
            @else
                <div class="row">
                    <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div
                                    class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-icons opacity-10">person</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Total Pelanggan Belum Bayar</p>
                                    @if ($totalTagihanBelumLunas !== null)
                                        <h4 class="mb-0">{{ $totalTagihanBelumLunas }}</h4>
                                    @else
                                        <h4 class="mb-0">Tagihan semua pelanggan lunas</h4>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div
                                    class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-icons opacity-10">person</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Keluhan Sedang Proses</p>
                                    @if ($totalKeluhanSedangProses !== null)
                                        <h4 class="mb-0">{{ $totalKeluhanSedangProses }}</h4>
                                    @else
                                        <h4 class="mb-0">Tidak ada keluhan dalam proses</h4>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-4 col-sm-4 mb-xl-0 mb-4">
                        <div class="card">
                            <div class="card-header p-3 pt-2">
                                <div
                                    class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                    <i class="material-icons opacity-10">person</i>
                                </div>
                                <div class="text-end pt-1">
                                    <p class="text-sm mb-0 text-capitalize">Keluhan Menunggu</p>
                                    @if ($totalKeluhanBelumDitangani !== null)
                                        <h4 class="mb-0">{{ $totalKeluhanBelumDitangani }}</h4>
                                    @else
                                        <h4 class="mb-0">Tidak ada keluhan menunggu</h4>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            @endif


            @if (Auth::user()->role !== 'admin')
                <div class="row">
                        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div
                                        class="icon icon-lg icon-shape bg-gradient-dark shadow-dark text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">paid</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Total Tagihan Anda</p>
                                        @if ($jumlahTagihans !== null)
                                            <h4 class="mb-0">{{ $jumlahTagihans }}</h4>
                                        @else
                                            <h4 class="mb-0">Tidak ada tagihan Sekarang</h4>
                                        @endif
                                    </div>
                                </div>
                                {{-- <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+55% </span>than
                                        lask week</p>
                                </div> --}}
                            </div>
                        </div>
                        <div class="col-xl-6 col-sm-6 mb-xl-0 mb-4">
                            <div class="card">
                                <div class="card-header p-3 pt-2">
                                    <div
                                        class="icon icon-lg icon-shape bg-gradient-primary shadow-primary text-center border-radius-xl mt-n4 position-absolute">
                                        <i class="material-icons opacity-10">feedback</i>
                                    </div>
                                    <div class="text-end pt-1">
                                        <p class="text-sm mb-0 text-capitalize">Keluhan Direspon Bulan Ini</p>
                                        @if ($keluhanDirespon)
                                            <h4 class="mb-0">{{ $keluhanDirespon }}</h4>
                                        @else
                                            <h4 class="mb-0">Tidak ada Keluhan selesai bulan ini</h4>
                                        @endif
                                    </div>
                                </div>
                                {{-- <hr class="dark horizontal my-0">
                                <div class="card-footer p-3">
                                    <p class="mb-0"><span class="text-success text-sm font-weight-bolder">+3% </span>than
                                        lask month</p>
                                </div> --}}
                            </div>
                        </div>
                    </div>
            @endif
        @endauth
        <div class="col mt-4 mb-4">

            @auth
                @if (Auth::user()->role == 'admin')
                <div class="col-lg-12 mt-4 col-md-6 mb-md-0 mb-4">
                    <div class="card">
                        <div class="card-header pb-0">
                            <div class="row bg-gradient-primary d-flex shadow-primary border-radius-lg pt-4 pb-3">
                                <div class="col row">
                                    <h6 class="col-3" style="color: white;">Keluhan bulan ini</h6>
                                    <div class="col row text-end mb-3" style="margin-right: 18px; gap: 12px; display: flex;">
                                            <!-- Button trigger modal -->
                                        <div class="col">
                                            <livewire:components.button-form />
                                        </div>

                                        <div class="col-md" style="background-color: white; border-radius: 999px;">
                                            <input type="text" wire:model.live.debounce.300ms="searchAktif"
                                                class="form-control"
                                                placeholder="Cari tiket, customer, atau kategori...">
                                        </div>
                                        <div class="col-md-3" style="background-color: white; border-radius: 999px;">
                                            <input type="date" wire:model.live="tanggalAktif" class="form-control">
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive">
                                <table class="table universal-search align-items-center mb-0">

                                    <tbody>
                                        @if ($keluhanBulanIni->isEmpty())
                                            <tr>
                                                <td colspan="3" class="text-center">Tidak ada Keluhan Bulan Ini</td>
                                            </tr>
                                            @else
                                                @foreach ($keluhanBulanIni as $keluhan)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $keluhan->category }}</h6>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ formatTanggalIndonesia($keluhan->created_at) }}</h6>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        <span class="badge badge-sm {{ $keluhan->status != 'menunggu' ? 'bg-gradient-warning' : 'bg-gradient-danger' }}">{{ $keluhan->status }}</span>
                                                    </td>

                                                    {{-- @auth
                                                        @if (Auth::user()->role == 'admin') --}}
                                                            <td class="align-middle text-center">
                                                                <a href="{{ route('lihat.keluhan', ['id' => $keluhan->id]) }}"
                                                                    class="btn col btn-info mb-3"
                                                                    data-toggle="tooltip" data-original-title="Edit user">
                                                                    Lihat Detail
                                                                </a>
                                                            </td>
                                                        {{-- @endif
                                                    @endauth --}}
                                                </tr>
                                                @endforeach
                                            @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @endif
                @if (Auth::user()->role !== 'admin')
                    <div class="col-lg-12 col-md-6 mb-md-0 mb-4">
                        <div class="card">
                            <div class="card-header pb-0">
                                <div class="row bg-gradient-primary d-flex shadow-primary border-radius-lg pt-4 pb-3">
                                    <div class="col row">
                                        <h6 class="col-3" style="color: white;">Tagihan Belum Lunas</h6>
                                        <div class="col row text-end mb-3" style="margin-right: 18px; gap: 12px; display: flex;">
                                                    <!-- Button trigger modal -->
                                                <div class="col">
                                                    <livewire:components.button-form />
                                                </div>

                                                <div class="col-md" style="background-color: white; border-radius: 999px;">
                                                    <input type="text" wire:model.live.debounce.300ms="searchAktif"
                                                        class="form-control"
                                                        placeholder="Cari tiket, customer, atau kategori...">
                                                </div>
                                                <div class="col-md-3" style="background-color: white; border-radius: 999px;">
                                                    <input type="date" wire:model.live="tanggalAktif" class="form-control">
                                                </div>
                                            </div>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body px-0 pb-2">
                                <div class="table-responsive">
                                    <table class="table align-items-center mb-0">
                                        <thead>
                                            <tr>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Paket Internet</th>
                                                <th
                                                    class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                    Tenggat</th>
                                                <th
                                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Status</th>
                                                <th
                                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Aksi</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @if ($tagihanBelumLunas->isEmpty())
                                            <tr>
                                                <td colspan="3" class="text-center">Tidak ada tagihan belum lunas</td>
                                            </tr>
                                            @else
                                                @foreach ($tagihanBelumLunas as $tagihans)
                                                <tr>
                                                    <td>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ $tagihans->langganan->paket->nama_paket }}</h6>
                                                        </div>
                                                    </td>
                                                    <td>
                                                        <div class="d-flex flex-column justify-content-center">
                                                            <h6 class="mb-0 text-sm">{{ formatTanggalIndonesia($tagihans->tgl_jatuh_tempo) }}</h6>
                                                        </div>
                                                    </td>
                                                    <td class="align-middle text-center text-sm">
                                                        <span class="badge badge-sm {{ $tagihans->status_pembayaran != 'belum_lunas' ? 'bg-gradient-success' : 'bg-gradient-danger' }}">{{ $tagihans->status_pembayaran == 'belum_lunas' ? 'Belum Lunas' : 'Lunas' }}</span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('lihat.pembayaran', ['id' => $tagihans->id]) }}"
                                                            class="btn col btn-info mb-3"
                                                            data-toggle="tooltip" data-original-title="Edit user">
                                                            Lihat Detail
                                                        </a>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            @endif

                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth
            <div class="col-lg-12 mt-4 col-md-6 mb-md-0 mb-4">
                <div class="card">
                    <div class="card-header pb-0">
                        <div class="row bg-gradient-primary d-flex shadow-primary border-radius-lg pt-4 pb-3">
                            <div class="col row">
                                <h6 class="col-3" style="color: white;">Keluhan bulan ini</h6>
                                <div class="col row text-end mb-3" style="margin-right: 18px; gap: 12px; display: flex;">
                                        <!-- Button trigger modal -->
                                    <div class="col">
                                        <livewire:components.button-form />
                                    </div>

                                    <div class="col-md" style="background-color: white; border-radius: 999px;">
                                        <input type="text" wire:model.live.debounce.300ms="searchAktif"
                                            class="form-control"
                                            placeholder="Cari tiket, customer, atau kategori...">
                                    </div>
                                    <div class="col-md-3" style="background-color: white; border-radius: 999px;">
                                        <input type="date" wire:model.live="tanggalAktif" class="form-control">
                                    </div>
                                </div>
                            </div>

                        </div>
                    </div>
                    <div class="card-body px-0 pb-2">
                        <div class="table-responsive">
                            <table class="table align-items-center mb-0">
                                <thead>
                                    <tr>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Judul Keluhan</th>
                                        <th
                                            class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                            Tanggal Pembuatan Invoice</th>
                                        <th
                                            class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                            Status</th>

                                        {{-- @auth
                                            @if (Auth::user()->role == 'admin') --}}
                                                <th
                                                    class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                    Aksi</th>
                                            {{-- @endif
                                        @endauth --}}
                                    </tr>
                                </thead>
                                <tbody>
                                    @if ($keluhanBulanIni->isEmpty())
                                        <tr>
                                            <td colspan="3" class="text-center">Tidak ada Keluhan Bulan Ini</td>
                                        </tr>
                                        @else
                                            @foreach ($keluhanBulanIni as $keluhan)
                                            <tr>
                                                <td>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ $keluhan->category }}</h6>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="d-flex flex-column justify-content-center">
                                                        <h6 class="mb-0 text-sm">{{ formatTanggalIndonesia($keluhan->created_at) }}</h6>
                                                    </div>
                                                </td>
                                                <td class="align-middle text-center text-sm">
                                                    <span class="badge badge-sm {{ $keluhan->status != 'menunggu' ? 'bg-gradient-warning' : 'bg-gradient-danger' }}">{{ $keluhan->status }}</span>
                                                </td>

                                                {{-- @auth
                                                    @if (Auth::user()->role == 'admin') --}}
                                                        <td class="align-middle text-center">
                                                            <a href="{{ route('lihat.keluhan', ['id' => $keluhan->id]) }}"
                                                                class="btn col btn-info mb-3"
                                                                data-toggle="tooltip" data-original-title="Edit user">
                                                                Lihat Detail
                                                            </a>
                                                        </td>
                                                    {{-- @endif
                                                @endauth --}}
                                            </tr>
                                            @endforeach
                                        @endif
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
</div>
@push('js')
<script src="{{ asset('assets') }}/js/plugins/chartjs.min.js"></script>
<script>
    var ctx = document.getElementById("chart-bars").getContext("2d");

    new Chart(ctx, {
        type: "bar",
        data: {
            labels: ["M", "T", "W", "T", "F", "S", "S"],
            datasets: [{
                label: "Sales",
                tension: 0.4,
                borderWidth: 0,
                borderRadius: 4,
                borderSkipped: false,
                backgroundColor: "rgba(255, 255, 255, .8)",
                data: [50, 20, 10, 22, 50, 10, 40],
                maxBarThickness: 6
            }, ],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: 'rgba(255, 255, 255, .2)'
                    },
                    ticks: {
                        suggestedMin: 0,
                        suggestedMax: 500,
                        beginAtZero: true,
                        padding: 10,
                        font: {
                            size: 14,
                            weight: 300,
                            family: "Roboto",
                            style: 'normal',
                            lineHeight: 2
                        },
                        color: "#fff"
                    },
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: 'rgba(255, 255, 255, .2)'
                    },
                    ticks: {
                        display: true,
                        color: '#f8f9fa',
                        padding: 10,
                        font: {
                            size: 14,
                            weight: 300,
                            family: "Roboto",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });


    var ctx2 = document.getElementById("chart-line").getContext("2d");

    new Chart(ctx2, {
        type: "line",
        data: {
            labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Mobile apps",
                tension: 0,
                borderWidth: 0,
                pointRadius: 5,
                pointBackgroundColor: "rgba(255, 255, 255, .8)",
                pointBorderColor: "transparent",
                borderColor: "rgba(255, 255, 255, .8)",
                borderColor: "rgba(255, 255, 255, .8)",
                borderWidth: 4,
                backgroundColor: "transparent",
                fill: true,
                data: [50, 40, 300, 320, 500, 350, 200, 230, 500],
                maxBarThickness: 6

            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: 'rgba(255, 255, 255, .2)'
                    },
                    ticks: {
                        display: true,
                        color: '#f8f9fa',
                        padding: 10,
                        font: {
                            size: 14,
                            weight: 300,
                            family: "Roboto",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#f8f9fa',
                        padding: 10,
                        font: {
                            size: 14,
                            weight: 300,
                            family: "Roboto",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });

    var ctx3 = document.getElementById("chart-line-tasks").getContext("2d");

    new Chart(ctx3, {
        type: "line",
        data: {
            labels: ["Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"],
            datasets: [{
                label: "Mobile apps",
                tension: 0,
                borderWidth: 0,
                pointRadius: 5,
                pointBackgroundColor: "rgba(255, 255, 255, .8)",
                pointBorderColor: "transparent",
                borderColor: "rgba(255, 255, 255, .8)",
                borderWidth: 4,
                backgroundColor: "transparent",
                fill: true,
                data: [50, 40, 300, 220, 500, 250, 400, 230, 500],
                maxBarThickness: 6

            }],
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false,
                }
            },
            interaction: {
                intersect: false,
                mode: 'index',
            },
            scales: {
                y: {
                    grid: {
                        drawBorder: false,
                        display: true,
                        drawOnChartArea: true,
                        drawTicks: false,
                        borderDash: [5, 5],
                        color: 'rgba(255, 255, 255, .2)'
                    },
                    ticks: {
                        display: true,
                        padding: 10,
                        color: '#f8f9fa',
                        font: {
                            size: 14,
                            weight: 300,
                            family: "Roboto",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
                x: {
                    grid: {
                        drawBorder: false,
                        display: false,
                        drawOnChartArea: false,
                        drawTicks: false,
                        borderDash: [5, 5]
                    },
                    ticks: {
                        display: true,
                        color: '#f8f9fa',
                        padding: 10,
                        font: {
                            size: 14,
                            weight: 300,
                            family: "Roboto",
                            style: 'normal',
                            lineHeight: 2
                        },
                    }
                },
            },
        },
    });

</script>
@endpush
