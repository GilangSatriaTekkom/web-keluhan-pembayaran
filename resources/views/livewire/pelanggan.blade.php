<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary d-flex row shadow-primary border-radius-lg pt-4 pb-3" style="justify-content: space-between; align-items: center;">
                        <h6 class="text-white col-2 mx-3"><strong>Data Pelanggan</strong></h6>
                        <div class="col-9 row text-end mb-3" style="margin-right: 18px; gap: 12px; display: flex;">
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
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        NAMA
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        EMAIL
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        NO HP
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        STATUS
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        LANGGANAN
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        AKSI
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pelanggans as $pelanggan)
                                    <tr >
                                        <td>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $pelanggan->name }}</h6>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="text-xs text-secondary mb-0">{{ $pelanggan->email }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="text-xs text-secondary mb-0">{{ $pelanggan->phone ?? 'kosong' }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if ($pelanggan->status == 'aktif')
                                                <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="text-xs text-secondary mb-0">{{ optional(optional($pelanggan->langganans->first())->paket)->nama_paket ?? 'Tidak ada paket' }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <a href="{{ route('pelanggan.lihat', ['id' => $pelanggan->id]) }}"
                                                class="btn col btn-info mb-3"
                                                data-toggle="tooltip" data-original-title="Edit user">
                                                Lihat Detail
                                            </a>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <p class="text-secondary text-xs font-weight-bold mb-0">Tidak ada data pelanggan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div class="p-3">
                        {{ $pelanggans->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
