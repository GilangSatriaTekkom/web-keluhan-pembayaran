    <div class="">
        <!-- Navbar -->
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary row shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white col text-capitalize ps-3">Tagihan Tertunda</h6>

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
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Periode Tagihan</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Tanggal Terbit</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Jatuh Tempo</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                               Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if($tagihans->isEmpty())
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada tagihan tertunda</td>
                                        </tr>
                                    @else
                                        @foreach($tagihans as $tagihan)
                                            @if($tagihan->status !== 'Lunas')
                                                <tr >
                                                    <td>TGHN{{ $tagihan->id }}</td>
                                                    <td>{{ formatTanggalIndonesia($tagihan->periode_tagihan) }}</td>
                                                    <td class="text-center">{{ formatTanggalIndonesia($tagihan->created_at) }}</td>
                                                    <td class="text-center">{{ formatTanggalIndonesia($tagihan->tgl_jatuh_tempo) }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-sm bg-gradient-danger">{{ $tagihan->status_pembayaran }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="#" class="text-secondary font-weight-bold text-xs">Lihat Detail</a>
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
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                            <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white text-capitalize ps-3">Pembayaran Lunas</h6>
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
                                                class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                                Periode Tagihan</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Tanggal Terbit</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Jatuh Tempo</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                                Status</th>
                                            <th
                                                class="text-center text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                               Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                    @if($tagihans->isEmpty())
                                        <tr>
                                            <td colspan="6" class="text-center">Tidak ada Riwayat Tagihan</td>
                                        </tr>
                                    @else
                                        @foreach($tagihans as $tagihan)
                                            @if($tagihan->status === 'Lunas')
                                            <tr >
                                                    <td>{{ $tagihan->id }}</td>
                                                    <td>{{ formatTanggalIndonesia($tagihan->periode_tagihan) }}</td>
                                                    <td class="text-center">{{ formatTanggalIndonesia($tagihan->created_at) }}</td>
                                                    <td class="text-center">{{ formatTanggalIndonesia($tagihan->tgl_jatuh_tempo) }}</td>
                                                    <td class="text-center">
                                                        <span class="badge badge-sm bg-gradient-danger">{{ $tagihan->status_pembayaran }}</span>
                                                    </td>
                                                    <td class="text-center">
                                                        <a href="#" class="text-secondary font-weight-bold text-xs">Lihat Detail</a>
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
