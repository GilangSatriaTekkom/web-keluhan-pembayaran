<div class="container-fluid px-2 px-md-4">
    @section('midtrans')
    @endsection
    <div class="card card-body mx-3 mx-md-4">

        {{-- Alert Sukses --}}
        @if (session('status'))
            <div class="alert alert-success alert-dismissible text-white" role="alert">
                <span class="text-sm">{{ session('status') }}</span>
                <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        @if($tagihan)
            {{-- Header ID & Status Pembayaran --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">Tagihan ID: {{ $id }}</h3>
                <span class="badge
                    @if($status == 'lunas') bg-success
                    @elseif($status == 'belum_lunas') bg-danger
                    @else bg-secondary @endif">
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </span>
            </div>

            {{-- Grid Info --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>User ID</h6>
                    <p class="text-muted">{{ $userId }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Langganan ID</h6>
                    <p class="text-muted">{{ $langganan }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Metode Pembayaran</h6>
                    <p class="text-muted">{{ $metode }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Jumlah Tagihan</h6>
                    <p class="text-muted">Rp {{ number_format($jumlah, 0, ',', '.') }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Tanggal Jatuh Tempo</h6>
                    <p class="text-muted">{{ formatTanggalIndonesia($jatuhTempo) }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Periode Tagihan</h6>
                    <p class="text-muted">{{ $periode }}</p>
                </div>
            </div>

            {{-- Tanggal Dibuat dan Update --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Dibuat Pada</h6>
                    <p class="text-muted">{{ formatTanggalIndonesia($created) }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Terakhir Diupdate</h6>
                    <p class="text-muted">{{ formatTanggalIndonesia($updated) }}</p>
                </div>
            </div>

            {{-- Tombol Edit --}}
            <div class="text-end">
                <button
                    wire:click="bayar"
                    class="btn btn-success"
                    >
                    Bayar Tagihan
                </button>
            </div>

            {{-- Modal Edit Tagihan --}}
            <div wire:ignore.self class="modal fade" id="editTagihanModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        {{-- Header Modal --}}
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Tagihan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        {{-- Body Modal --}}
                        <div class="modal-body">
                            <form wire:submit.prevent="updateTagihan">
                                <div class="row">
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Status Pembayaran</label>
                                        <select wire:model.defer="status" class="form-control border border-2 p-2">
                                            <option value="">Pilih Status</option>
                                            <option value="belum_lunas">Belum Lunas</option>
                                            <option value="lunas">Lunas</option>
                                            <option value="pending">Pending</option>
                                        </select>
                                        @error('status') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Metode Pembayaran</label>
                                        <input wire:model.defer="metode" type="text" class="form-control border border-2 p-2" placeholder="Contoh: Transfer Bank, Midtrans">
                                        @error('metode') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Jumlah Tagihan (Rp)</label>
                                        <input wire:model.defer="jumlah" type="number" class="form-control border border-2 p-2" placeholder="Masukkan jumlah tagihan">
                                        @error('jumlah') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Tanggal Jatuh Tempo</label>
                                        <input wire:model.defer="jatuhTempo" type="date" class="form-control border border-2 p-2">
                                        @error('jatuhTempo') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Periode Tagihan</label>
                                        <input wire:model.defer="periode" type="text" class="form-control border border-2 p-2" placeholder="Contoh: 2024-12">
                                        @error('periode') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </form>
                        </div>

                        {{-- Footer Modal --}}
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn bg-gradient-dark" wire:click="updateTagihan">Submit</button>
                        </div>

                    </div>
                </div>
            </div>

        @else
            {{-- Jika tagihan tidak ditemukan --}}
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                <p class="text-muted">Pilih tagihan untuk melihat detail</p>
            </div>
        @endif
    </div>
</div>
