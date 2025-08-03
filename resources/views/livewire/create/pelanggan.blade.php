<div class="container-fluid px-2 px-md-4">
    <div class="card card-body mx-3 mx-md-4">
        <h4 class="mb-4">Tambah Pelanggan</h4>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible text-white" role="alert">
                <span class="text-sm">{{ session('status') }}</span>
                <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form wire:submit.prevent='confirmSubmit'>
            <div class="row">
                <div class="mb-3 col-md-6">
                    <label class="form-label">Nama</label>
                    <input wire:model.defer="user.name" type="text" class="form-control border border-2 p-2">
                    @error('user.name') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Email</label>
                    <input wire:model.defer="user.email" type="email" class="form-control border border-2 p-2">
                    @error('user.email') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Langganan Field -->
                <div class="mb-3 col-md-6">
                    <label class="form-label">Paket Internet</label>
                    <select wire:model.defer="user.paket_internet_id" class="form-control border border-2 p-2">
                            <option value="">Pilih Paket</option>
                            @foreach($paketInternetOptions as $paket)
                                <option value="{{ $paket->id }}">
                                    {{ $paket->nama_paket }} - {{ $paket->kecepatan }}Mbps (Rp{{ number_format($paket->harga, 0, ',', '.') }})
                                </option>
                            @endforeach
                    </select>
                    @error('user.paket_internet_id') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Phone Field (required if langganan) -->
                <div class="mb-3 col-md-6">
                    <label class="form-label">No Hp</label>
                    <input wire:model.defer="user.phone" type="text" class="form-control border border-2 p-2">
                    @error('user.phone') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <!-- Location Field (required if langganan) -->
                <div class="mb-3 col-md-6">
                    <label class="form-label">Alamat</label>
                    <input wire:model.defer="user.location" type="text" class="form-control border border-2 p-2">
                    @error('user.location') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Password</label>
                    <input wire:model.defer="user.password" type="password" class="form-control border border-2 p-2">
                    @error('user.password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <button type="submit" class="btn bg-gradient-dark">Simpan</button>
        </form>
    </div>
</div>
