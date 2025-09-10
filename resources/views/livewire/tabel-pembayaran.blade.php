    <div class="">
        <!-- Navbar -->
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary row shadow-primary border-radius-lg pt-4 pb-3">
                                <div class="left-wrap col-6 row">
                                    <h6 class="text-white col text-capitalize ps-3">Tagihan Tertunda</h6>
                                    {{-- <div class="col " style="height: fit-content;">
                                        <button wire:click="exportExcel" class="btn col btn-info" style="margin-bottom: unset;" data-toggle="modal" data-target="#modalTambahKaryawan">
                                            <i class="fas fa-user-plus"></i> Rekap Bulanan
                                        </button>
                                    </div> --}}
                                </div>




                                <div class="row col mb-3" style="gap: 12px; display: flex;">
                                    <div class="col-md-7" style="background-color: white; border-radius: 999px;">
                                        <input type="text" wire:model.live.debounce.300ms="searchAktif"
                                            class="form-control"
                                            placeholder="Cari tiket, customer, atau kategori...">
                                    </div>
                                    <div class="col-md-4" style="background-color: white; border-radius: 999px;">
                                        <input type="date" wire:model.live="tanggalAktif" class="form-control">
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
                                                Nomor Tagihan</th>

                                            @if (Auth::user()->role === 'admin')
                                            <th
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Nama Pelanggan</th>
                                            @endif

                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Nama Paket</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Jatuh Tempo</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Jumlah Tagihan</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                               Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if($langgananAktif->isEmpty())
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada tagihan tertunda</td>
                                        </tr>
                                    @else
                                        @if (Auth::user()->role === 'admin')
                                            @foreach($langgananAktifAdmin as $tagihan)
                                                @if($tagihan->status_pembayaran !== 'Lunas')
                                                    <tr >
                                                        <td>TGHN{{ $tagihan->id }}</td>
                                                        <td>{{ $tagihan->user->name }}</td>
                                                        <td class="text-center">{{ $tagihan->langganan->paket->nama_paket }}</td>
                                                        <td class="text-center">{{ formatTanggalIndonesia($tagihan->tgl_jatuh_tempo) }}</td>
                                                        <td class="text-center">{{ formatRupiah($tagihan->jumlah_tagihan) }}</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-sm {{ $tagihan->status_pembayaran == 'belum_lunas' ? 'bg-gradient-danger' : 'bg-gradient-success'}}">{{ $tagihan->status_pembayaran == 'belum_lunas' ? 'Belum Lunas' : 'Lunas'}}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <a href="{{ route('lihat.pembayaran', ['id' => $tagihan->id]) }}"
                                                                class="btn col btn-info mb-3"
                                                                data-toggle="tooltip" data-original-title="Edit user">
                                                                Lihat Detail
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @else
                                            @foreach($langgananAktif as $tagihan)
                                                @if($tagihan->status_pembayaran !== 'Lunas')
                                                    <tr >
                                                        <td>TGHN{{ $tagihan->id }}</td>
                                                        <td class="text-center">{{ $tagihan->langganan->paket->nama_paket }}</td>
                                                        <td class="text-center">{{ formatTanggalIndonesia($tagihan->tgl_jatuh_tempo) }}</td>
                                                        <td class="text-center">{{ formatRupiah($tagihan->jumlah_tagihan) }}</td>
                                                        <td class="text-center">
                                                            <span class="badge badge-sm {{ $tagihan->status_pembayaran == 'belum_lunas' ? 'bg-gradient-danger' : 'bg-gradient-success'}}">{{ $tagihan->status_pembayaran == 'belum_lunas' ? 'Belum Lunas' : 'Lunas'}}</span>
                                                        </td>
                                                        <td class="align-middle text-center">
                                                            <a href="{{ route('lihat.pembayaran', ['id' => $tagihan->id]) }}"
                                                                class="btn col btn-info mb-3"
                                                                data-toggle="tooltip" data-original-title="Edit user">
                                                                Lihat Detail
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                        @endif
                                    @endif
                                </tbody>

                                </table>
                            </div>
                            <div class="p-3">
                                {{ $langgananAktif->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary row shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white col text-capitalize ps-3">Tagihan Lunas</h6>
                                <div class="row col mb-3" style="gap: 12px; display: flex;">
                                    <div class="col-md-7" style="background-color: white; border-radius: 999px;">
                                        <input type="text" wire:model.live.debounce.300ms="searchAktif"
                                            class="form-control"
                                            placeholder="Cari tiket, customer, atau kategori...">
                                    </div>
                                    <div class="col-md-4" style="background-color: white; border-radius: 999px;">
                                        <input type="date" wire:model.live="tanggalAktif" class="form-control">
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
                                                Nomor Tagihan</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Nama Paket</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Jatuh Tempo</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Jumlah Tagihan</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                               Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if($langgananSelesai->isEmpty())
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada Riwayat Tagihan</td>
                                        </tr>
                                    @else
                                        @foreach($langgananSelesai as $tagihan)
                                            @if($tagihan->status_pembayaran === 'lunas')
                                            <tr >
                                                    <td>TGHN{{ $tagihan->id }}</td>
                                                    <td class="text-center">{{ $tagihan->langganan->paket->nama_paket }}</td>
                                                    <td class="text-center">{{ formatTanggalIndonesia($tagihan->tgl_jatuh_tempo) }}</td>
                                                    <td class="text-center">{{ formatRupiah($tagihan->jumlah_tagihan) }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-sm {{ $tagihan->status_pembayaran == 'belum_lunas' ? 'bg-gradient-danger' : 'bg-gradient-success'}}">{{ $tagihan->status_pembayaran == 'belum_lunas' ? 'Belum Lunas' : 'Lunas'}}</span>
                                                    </td>
                                                    <td class="align-middle text-center">
                                                        <a href="{{ route('lihat.pembayaran', ['id' => $tagihan->id]) }}"
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
                                {{ $langgananSelesai->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
