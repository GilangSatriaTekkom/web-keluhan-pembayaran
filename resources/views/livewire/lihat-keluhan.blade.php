<div class="container-fluid px-2 px-md-4">
    <div class="card card-body mx-3 mx-md-4">
        @if($complaint)
            <div class="d-flex row justify-content-between align-items-center mb-4">
                <h4 class="col">Keluhan: {{ $complaint->category }}</h4>
                {{-- <button wire:click="$emit('closeDetail')" class="btn btn-sm btn-secondary">
                    <i class="fas fa-times"></i> Tutup
                </button> --}}

                <div class="col">
                    <span class="badge
                        @if($complaint->status == 'menunggu') bg-danger
                        @elseif($complaint->status == 'selesai') bg-success
                        @else bg-warning @endif" style="float: inline-end;">
                        {{ $complaint->status }}
                    </span>
                </div>
            </div>

            <div class="row mb-3">
                {{-- <div class="col-md-6">
                    <h6>Keluhan Pelanggan</h6>
                    <p class="text-muted">{{ $complaint->category }}</p>
                </div> --}}
            </div>

             <div class="row">
                @if($complaint->user)
                    <div class="col-md-4 mb-3">
                        <h6>Dibuat Oleh</h6>
                        <div class="d-flex align-items-center">
                            {{-- <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded-circle bg-primary text-white">
                                    {{ substr($complaint->user->name, 0, 1) }}
                                </span>
                            </div> --}}
                            <div>
                                <p class="mb-0">{{ $complaint->user->name }}</p>
                                {{-- <small class="text-muted">{{ $complaint->user->email }}</small> --}}
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-4 mb-3">
                    <h6>Dibuat Pada</h6>
                    <p class="text-muted">{{ $complaint->created_at->format('d F Y H:i') }}</p>
                </div>
            </div>

            <div class="row">
                @if($complaint->user)
                    <div class="col-md-4 mb-3">
                        <h6>Penanggung Jawab Customer Service</h6>
                        <div class="d-flex align-items-center">
                            {{-- <div class="avatar avatar-sm me-2">
                                <span class="avatar-initial rounded-circle bg-primary text-white">
                                    {{ substr($complaint->user->name, 0, 1) }}
                                </span>
                            </div> --}}
                            <div>
                                @if ($complaint->cs_menangani)
                                    <p class="text-muted">{{ $complaint->cs->name }}</p>
                                @else
                                    <p class="text-muted">Tidak ada teknisi yang menangani</p>
                                @endif
                                {{-- <small class="text-muted">{{ $complaint->user->email }}</small> --}}
                            </div>
                        </div>
                    </div>
                @endif
                <div class="col-md-4 mb-3">
                    <h6>Penanggung Jawab Teknisi</h6>
                    @if ($complaint->nama_teknisi_menangani)
                        <p class="text-muted">{{ $complaint->nama_teknisi_menangani }}</p>
                    @else
                         <p class="text-muted">Tidak ada teknisi yang menangani</p>
                    @endif
                </div>
            </div>

            <div class="mb-3">
                <h6>Deskripsi Lengkap</h6>
                <div class="p-3 rounded bg-transparent">
                    @if ($complaint->description === null)
                        <p class="text-muted fst-italic">Tidak ada deskripsi.</p>
                    @else
                        {!! nl2br(e($complaint->description)) !!}
                    @endif

                </div>
            </div>

            <div class="row text-start">
                @if($complaint->status == 'proses' || $complaint->status == 'menunggu')
                <div class="col-md-7 row mt-3">
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
                @endif
            </div>
        @else
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                <p class="text-muted">Pilih keluhan untuk melihat detail</p>
            </div>
        @endif
    </div>
</div>
