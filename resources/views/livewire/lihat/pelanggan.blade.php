<div class="container-fluid px-2 px-md-4">
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

        @if($user)
            {{-- Header Nama & Status --}}
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h3 class="mb-0">{{ $user->name }}</h3>
                <span class="badge
                    @if($user->status == 'aktif') bg-success
                    @elseif($user->status == 'nonaktif') bg-danger
                    @else bg-secondary @endif">
                    {{ ucfirst($user->status) }}
                </span>
            </div>

            {{-- Grid Info 2 Kolom --}}
            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Email</h6>
                    <p class="text-muted">{{ $user->email }}</p>
                </div>
                <div class="col-md-6">
                    <h6>No HP</h6>
                    <p class="text-muted">{{ $user->phone }}</p>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <h6>Paket Internet</h6>

                    <p class="text-muted">
                        @if($langganan)
                        {{ $langganan->paket->nama_paket }}
                        @else
                        <em>Belum berlangganan</em>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    <h6>Harga</h6>

                    <p class="text-muted">
                        @if($langganan)
                        {{formatRupiah($langganan->paket->harga)}}
                        @else
                            <em>Belum berlangganan</em>
                        @endif
                    </p>

                </div>
                <div class="col-md-6">
                    <h6>Dibuat Pada</h6>
                    <p class="text-muted">{{ formatTanggalIndonesia($user->created_at) }}</p>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <h6>Terakhir Diupdate</h6>
                    <p class="text-muted">{{ formatTanggalIndonesia($user->updated_at) }}</p>
                </div>
            </div>

            {{-- Alamat Full Width --}}
            <div class="mb-4">
                <h6>Alamat</h6>
                <div class="border p-3 rounded bg-light">
                    {{ $user->location }}
                </div>
            </div>

            {{-- Tombol Edit --}}
            <div class="text-end">
                <button class="btn btn-info" data-bs-toggle="modal" data-bs-target="#editModal">
                    Edit
                </button>
            </div>

            {{-- Modal Edit Pelanggan --}}
            <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-lg">
                    <div class="modal-content">

                        {{-- Header Modal --}}
                        <div class="modal-header">
                            <h5 class="modal-title">Edit Data Pelanggan</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        {{-- Body Modal --}}
                        <div class="modal-body">
                            <form wire:submit.prevent="updatePelanggan">
                                <div class="row">
                                    <!-- Nama -->
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Nama</label>
                                        <input wire:model.defer="name" type="text"
                                            class="form-control border border-2 p-2"
                                            placeholder="Masukkan nama lengkap">
                                        @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Email -->
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Email</label>
                                        <input wire:model.defer="email" type="email"
                                            class="form-control border border-2 p-2"
                                            placeholder="contoh@email.com">
                                        @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- No HP -->
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">No HP</label>
                                        <input wire:model.defer="phone" type="text"
                                            class="form-control border border-2 p-2"
                                            placeholder="+6281234567890"
                                            oninput="
                                                        if (event.inputType !== 'deleteContentBackward') {
                                                            if (this.value !== '' && !this.value.startsWith('+62')) {
                                                                this.value = '+62' + this.value.replace(/^(\+62|62|0)*/, '');
                                                            }
                                                        }
                                                    ">
                                        @error('phone') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Paket Internet -->
                                    <div class="mb-3 col-md-6">
                                        <label class="form-label">Paket Internet</label>
                                        <select wire:model.defer="paket_internet_id"
                                            class="form-control border border-2 p-2">
                                            <option value="">Pilih Paket</option>
                                            @foreach($paketInternetOptions as $paket)
                                                <option value="{{ $paket->id }}">
                                                    {{ $paket->nama_paket }} - {{ $paket->kecepatan }}Mbps
                                                    (Rp{{ number_format($paket->harga, 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('paket_internet_id') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>

                                    <!-- Alamat -->
                                    <div class="mb-3 col-12">
                                        <label class="form-label">Alamat</label>
                                        <input wire:model.defer="alamat" type="text"
                                            class="form-control border border-2 p-2"
                                            placeholder="Masukkan alamat lengkap">
                                        @error('alamat') <small class="text-danger">{{ $message }}</small> @enderror
                                    </div>
                                </div>
                            </form>
                        </div>


                        {{-- Footer Modal --}}
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="button" class="btn bg-gradient-dark" wire:click="updateEdit")>Submit</button>
                        </div>


                    </div>
                </div>
            </div>

        @else
            {{-- Jika user tidak ditemukan --}}
            <div class="text-center py-4">
                <i class="fas fa-info-circle fa-2x text-muted mb-3"></i>
                <p class="text-muted">Pilih pelanggan untuk melihat detail</p>
            </div>
        @endif
    </div>
</div>
