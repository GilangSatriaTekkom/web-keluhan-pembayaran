<div class="container-fluid px-2 px-md-4">
    <div class="card card-body mx-3 mx-md-4">
        @if($tagihan)
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Detail Pembayaran</h4>
                {{-- <button wire:click="$emit('closeDetail')" class="btn btn-sm btn-secondary">
                    <i class="fas fa-times"></i> Tutup
                </button> --}}
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Status Pembayaran</h6>
                    <span class="badge
                        @if($tagihan->status_pembayaran == 'Belum Dibayar') bg-danger
                        @elseif($tagihan->status_pembayaran == 'Diproses') bg-warning
                        @else bg-success @endif">
                        {{ $tagihan->status_pembayaran }}
                    </span>
                </div>
                <div class="col-md-6">
                    <h6>Metode Pembayaran</h6>
                    <p class="text-muted">{{ $tagihan->metode_pembayaran ?? 'Belum dipilih' }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Jumlah Tagihan</h6>
                    <p class="text-muted">Rp {{ number_format($tagihan->jumlah_tagihan, 0, ',', '.') }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Tanggal Jatuh Tempo</h6>
                    <p class="text-muted">{{ \Carbon\Carbon::parse($tagihan->tgl_jatuh_tempo)->format('d F Y') }}</p>
                </div>
            </div>

            <div class="mb-3">
                <h6>Periode Tagihan</h6>
                <p class="text-muted">{{ $tagihan->periode_tagihan }}</p>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6>Dibuat Pada</h6>
                    <p class="text-muted">{{ $tagihan->created_at->format('d F Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Terakhir Diupdate</h6>
                    <p class="text-muted">{{ $tagihan->updated_at->format('d F Y H:i') }}</p>
                </div>
            </div>

            <div class="row">
                @if($tagihan->user)
                <div class="col-md-6 mt-3">
                    <h6>Pelanggan</h6>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded-circle bg-primary text-white">
                                {{ substr($tagihan->user->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <p class="mb-0">{{ $tagihan->user->name }}</p>
                            <small class="text-muted">{{ $tagihan->user->email }}</small>
                        </div>
                    </div>
                </div>
                @endif

                @if($tagihan->langganan)
                <div class="col-md-6 mt-3">
                    <h6>Layanan Langganan</h6>
                    <p class="mb-0">{{ $tagihan->langganan->nama_layanan ?? 'Tidak tersedia' }}</p>
                    <small class="text-muted">ID: {{ $tagihan->langganan_id }}</small>
                </div>
                @endif
            </div>

            @if($tagihan->status_pembayaran == 'Belum Dibayar' || $tagihan->status_pembayaran == 'Diproses')
            <div class="row mt-4">
                @if($authUser->role == 'admin')
                <div class="col-md-6" style="place-content: end;">
                    <button wire:click="alertPopup('Konfirmasi Pembayaran', 'Yakin konfirmasi pembayaran ini?', 'konfirmasiPembayaran')"
                        class="btn btn-md btn-success mb-3"
                        data-toggle="tooltip" data-original-title="Konfirmasi Pembayaran">
                        Konfirmasi Pembayaran
                    </button>
                </div>
                @endif

                @if($authUser->role == 'pelanggan' && $tagihan->status_pembayaran == 'Belum Dibayar')
                <div class="col-md-6" style="place-content: end;">
                    <button wire:click="openPembayaranModal"
                        class="btn btn-md btn-primary mb-3"
                        data-toggle="tooltip" data-original-title="Bayar Tagihan">
                        Bayar Sekarang
                    </button>
                </div>
                @endif
            </div>
            @endif
        @else
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                <p class="text-muted">Pilih tagihan untuk melihat detail</p>
            </div>
        @endif
    </div>
</div>
