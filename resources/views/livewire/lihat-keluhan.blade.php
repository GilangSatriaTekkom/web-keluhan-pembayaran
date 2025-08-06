<div class="container-fluid px-2 px-md-4">
    <div class="card card-body mx-3 mx-md-4">
        @if($complaint)
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4>Detail Keluhan</h4>
                <button wire:click="$emit('closeDetail')" class="btn btn-sm btn-secondary">
                    <i class="fas fa-times"></i> Tutup
                </button>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Kategori Keluhan</h6>
                    <p class="text-muted">{{ $complaint->category }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Status</h6>
                    <span class="badge
                        @if($complaint->status == 'Belum Diproses') bg-danger
                        @elseif($complaint->status == 'Diproses') bg-warning
                        @else bg-success @endif">
                        {{ $complaint->status }}
                    </span>
                </div>
            </div>

            <div class="mb-3">
                <h6>Deskripsi Lengkap</h6>
                <div class="border p-3 rounded bg-light">
                    {!! nl2br(e($complaint->description)) !!}
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <h6>Dibuat Pada</h6>
                    <p class="text-muted">{{ $complaint->created_at->format('d F Y H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <h6>Terakhir Diupdate</h6>
                    <p class="text-muted">{{ $complaint->updated_at->format('d F Y H:i') }}</p>
                </div>
            </div>

            <div class="row">
            @if($complaint->user)
                <div class="col-md-6 mt-3">
                    <h6>Dibuat Oleh</h6>
                    <div class="d-flex align-items-center">
                        <div class="avatar avatar-sm me-2">
                            <span class="avatar-initial rounded-circle bg-primary text-white">
                                {{ substr($complaint->user->name, 0, 1) }}
                            </span>
                        </div>
                        <div>
                            <p class="mb-0">{{ $complaint->user->name }}</p>
                            <small class="text-muted">{{ $complaint->user->email }}</small>
                        </div>
                    </div>
                </div>
            @endif
                <div class="col-md-6 row mt-3">
                    @if($authUser->role == 'admin')
                        <div class="col-md-4" style="place-content: end;">
                            <button @if($complaint->status == 'proses') disabled @endif  wire:click="alertPopup('Proses Keluhan', 'Yakin proses keluhan ini?', 'prosesKeluhan')"
                                class="btn btn-md col btn-info mb-3"
                                data-toggle="tooltip" data-original-title="Edit user">
                                Proses Keluhan
                            </button>
                        </div>
                    @endif
                    <div class="col-md-4" style="place-content: end;">
                    @if($authUser->role == 'admin')
                        @if($complaint->status == 'proses')
                            <button wire:click='openTaskModal'
                                class="btn btn-md col btn-info mb-3"
                                data-toggle="tooltip" data-original-title="Edit user">
                                Update tugasan
                            </button>
                        @endif
                    @endif
                    </div>
                </div>
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                <p class="text-muted">Pilih keluhan untuk melihat detail</p>
            </div>
        @endif
    </div>
</div>
