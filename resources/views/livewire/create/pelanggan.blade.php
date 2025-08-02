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

        <form wire:submit.prevent='submit'>
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

                <div class="mb-3 col-md-6">
                    <label class="form-label">Telepon</label>
                    <input wire:model.defer="user.phone" type="text" class="form-control border border-2 p-2">
                    @error('user.phone') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Lokasi</label>
                    <input wire:model.defer="user.location" type="text" class="form-control border border-2 p-2">
                    @error('user.location') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3 col-md-12">
                    <label class="form-label">Tentang</label>
                    <textarea wire:model.defer="user.about" class="form-control border border-2 p-2"
                        rows="4"></textarea>
                    @error('user.about') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Password</label>
                    <input wire:model.defer="user.password" type="password" class="form-control border border-2 p-2">
                    @error('user.password') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label">Role</label>
                    <select wire:model.defer="user.role" class="form-control border border-2 p-2">
                        <option value="">Pilih Role</option>
                        <option value="admin">Admin</option>
                        <option value="user">User</option>
                        <!-- Tambahkan opsi sesuai kebutuhan -->
                    </select>
                    @error('user.role') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3 col-md-3">
                    <label class="form-label">Status</label>
                    <select wire:model.defer="user.status" class="form-control border border-2 p-2">
                        <option value="">Pilih Status</option>
                        <option value="aktif">Aktif</option>
                        <option value="nonaktif">Nonaktif</option>
                    </select>
                    @error('user.status') <small class="text-danger">{{ $message }}</small> @enderror
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Tanggal Daftar</label>
                    <input wire:model.defer="user.tgl_daftar" type="date" class="form-control border border-2 p-2">
                    @error('user.tgl_daftar') <small class="text-danger">{{ $message }}</small> @enderror
                </div>
            </div>

            <button type="submit" class="btn bg-gradient-dark">Simpan</button>
        </form>
    </div>
</div>
