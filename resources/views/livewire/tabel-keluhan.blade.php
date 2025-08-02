
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
                                <h6 class="text-white col text-capitalize ps-3">Keluhan Aktif</h6>
                                <div class="col text-end">
                                     <!-- Button trigger modal -->
                                    <a href="{{ route('tabel-keluhan.tambah') }}" class="btn bg-gradient-info">
                                        Buat Keluhan
                                    </a>
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
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @if($tiket->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada keluhan aktif</td>
                                            </tr>
                                        @else
                                            @foreach($tiket as $t)
                                                @if($t->status !== 'Selesai')
                                                    <tr onclick="window.location='test'" style="cursor:pointer"; class="data-hover">
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
                                                            <span class="badge badge-sm bg-gradient-success">{{ $t->status }}</span>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative flex flex-row justify-between mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Riwayat Keluhan</h6>
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
                                        @if($tiket->isEmpty())
                                            <tr>
                                                <td colspan="6" class="text-center">Tidak ada Riwayat Keluhan</td>
                                            </tr>
                                        @else
                                            @foreach($tiket as $t)
                                                @if($t->status === 'Selesai')
                                                    <tr onclick="window.location='test'" style="cursor:pointer"; class="data-hover">
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
                                                            <span class="badge badge-sm bg-gradient-success">{{ $t->status }}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <a href="javascript:;"
                                                                class="text-secondary font-weight-bold text-xs"
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
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
