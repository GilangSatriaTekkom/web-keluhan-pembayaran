<div class="container-fluid px-2 px-md-4">

    <div class="card card-body mx-3 mx-md-4">
        <h4 class="mb-4">Tambah Keluhan</h4>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible text-white" role="alert">
                <span class="text-sm">{{ session('status') }}</span>
                <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form wire:submit.prevent="alert">
            <div class="row">
                {{-- <div class="mb-3">
                    <label class="form-label">Kategori Keluhan</label>
                    <select wire:model.defer="tiket.category" class="form-control border border-2 p-2">
                        <option value="">Pilih Kategori</option>
                        <option value="Teknis">Teknis</option>
                        <option value="Administrasi">Administrasi</option>
                        <option value="Layanan">Layanan</option>
                    </select>
                    @error('tiket.category') <small class="text-danger">{{ $message }}</small> @enderror
                </div> --}}
                <div class="mb-3">
                    <label class="form-label">Sebutkan kategori keluhan anda</label>
                    <input type="text" placeholder="Gangguan Internet" wire:model.defer="tiket.category" class="form-control border border-2 p-2">
                    @error('tiket.category') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                {{-- <div class="mb-3 col-md-6">
                    <label class="form-label">Status</label>
                    <select wire:model.defer="tiket.status" class="form-control border border-2 p-2">
                        <option value="">Pilih Status</option>
                        <option value="Belum Diproses">Belum Diproses</option>
                        <option value="Diproses">Diproses</option>
                        <option value="Selesai">Selesai</option>
                    </select>
                    @error('tiket.status') <small class="text-danger">{{ $message }}</small> @enderror
                </div> --}}

                <div class="mb-3 col-md-12">
                    <label class="form-label">Jelaskan keluhan lebih detail</label>
                    <textarea wire:model.defer="tiket.description" class="form-control border border-2 p-2" rows="4"
                        placeholder="Jelaskan Lebih Rinci..."></textarea>
                    @error('tiket.description') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <button type="submit" class="btn bg-gradient-dark">Kirim Keluhan</button>
        </form>
    </div>
</div>
