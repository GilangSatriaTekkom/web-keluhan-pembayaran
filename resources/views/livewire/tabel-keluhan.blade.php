
    <div class="">
        <livewire:modal-form-keluhan />
        <!-- Navbar -->
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary row shadow-primary border-radius-lg pt-4 pb-3">
                                <div class="left-wrap col-6 row">
                                    <h6 class="text-white col text-capitalize ps-3">
                                        @if (Auth::user()->role == 'pelanggan' || Auth::user()->role == 'admin')
                                        Keluhan Aktif
                                        @else
                                        Keluhan Perlu Diatasi
                                        @endif
                                    </h6>
                                    {{-- <div class="col " style="height: fit-content;">
                                        <button wire:click="exportExcel" class="btn col btn-info" style="margin-bottom: unset;" data-toggle="modal" data-target="#modalTambahKaryawan">
                                            <i class="fas fa-user-plus"></i> Rekap Bulanan
                                        </button>
                                    </div> --}}
                                </div>

                                <div class="col-6 text-end" style="margin-right: 20px;">
                                    <div class="row mb-3" style="gap: 12px; display: flex;">
                                        <!-- Button trigger modal -->
                                        @auth
                                            @if (Auth::user()->role == 'pelanggan')
                                                <a href="{{ route('tabel-keluhan.tambah') }}" class="btn col bg-gradient-info" style="margin-bottom: 0px;">
                                                    Buat Keluhan
                                                </a>
                                            @endif
                                        @endauth
                                        <div class="col-md-5" style="background-color: white; border-radius: 999px;">
                                            <input type="text" wire:model.live.debounce.300ms="searchAktif"
                                                class="form-control"
                                                placeholder="Cari tiket, customer, atau kategori...">
                                        </div>
                                        <div class="col-md" style="background-color: white; border-radius: 999px;">
                                            <input type="date" wire:model.live="tanggalAktif" class="form-control">
                                        </div>

                                    </div>


                                </div>

                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Nomor Tiket</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Customer</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Keluhan</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Tanggal Dibuat</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($tiketAktif->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada keluhan aktif</td>
                                            </tr>
                                        @else
                                            @foreach($tiketAktif as $t)
                                                @if($t->status !== 'selesai')
                                                    <tr>
                                                        <td>
                                                            <div class="my-auto">
                                                                <h6 class="mb-0 text-sm">TK{{ $t->id }}</h6>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <p class="text-sm font-weight-bold mb-0">{{ $t->user->name }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-xs font-weight-bold">{{ $t->category }}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-sm font-weight-bold mb-0">{{ formatTanggalIndonesia($t->created_at) }}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="badge badge-sm {{ $t->status == 'menunggu' ? 'bg-gradient-danger' : 'bg-gradient-warning'}}">{{ $t->status }}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <a href="{{ route('lihat.keluhan', ['id' => $t->id]) }}"
                                                                class="btn col btn-info mb-3"
                                                                data-toggle="tooltip" data-original-title="Edit user">
                                                                Lihat Detail
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                {{ $tiketAktif->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative flex flex-row justify-between mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary row shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white col text-capitalize ps-3">Riwayat Keluhan</h6>

                                <div class="col-6 text-end">
                                    <div class="row mb-3" style="gap: 12px; display: flex;">
                                        <div class="col-md-7 " style="background-color: white; border-radius: 999px;">
                                            <input type="text" wire:model.live.debounce.300ms="searchSelesai"
                                                class="form-control"
                                                placeholder="Cari tiket, customer, atau kategori...">
                                        </div>
                                        <div class="col-md-4" style="background-color: white; border-radius: 999px;">
                                            <input type="date" wire:model.live="tanggalSelesai" class="form-control">
                                        </div>
                                    </div>


                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center justify-content-center mb-0">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Nomor Tiket</th>
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Customer</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Keluhan</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Tanggal Selesai</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($tiketSelesai->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada Riwayat Keluhan</td>
                                            </tr>
                                        @else
                                            @foreach($tiketSelesai as $t)
                                                @if($t->status === 'selesai')
                                                    <tr >
                                                        <td>
                                                            <div class="my-auto">
                                                                <h6 class="mb-0 text-sm">TK{{ $t->id }}</h6>
                                                            </div>
                                                        </td>
                                                        <td>
                                                            <p class="text-sm font-weight-bold mb-0">{{ $t->user->name }}</p>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-xs font-weight-bold">{{ $t->category }}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="text-sm font-weight-bold mb-0">{{ formatTanggalIndonesia($t->updated_at) }}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <span class="badge badge-sm bg-gradient-success">{{ $t->status }}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <a href="{{ route('lihat.keluhan', ['id' => $t->id]) }}"
                                                                class="btn col btn-info mb-3"
                                                                data-toggle="tooltip" data-original-title="Edit user">
                                                                Lihat Detail
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-3">
                                {{ $tiketSelesai->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
