<div>
    <div class="container-fluid py-4">
        <div class="col mt-4 mb-4">
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
                                                        <h6 class="mb-0 text-sm">{{ $keluhan->judul }}</h6>
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
