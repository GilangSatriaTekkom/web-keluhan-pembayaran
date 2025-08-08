<div class="container-fluid px-2 px-md-4">
    <div class="card card-body mx-3 mx-md-4">
        <h4 class="mb-4">Detail Karyawan</h4>

        @if (session('status'))
            <div class="alert alert-success alert-dismissible text-white" role="alert">
                <span class="text-sm">{{ session('status') }}</span>
                <button type="button" class="btn-close text-lg py-3 opacity-10" data-bs-dismiss="alert"
                    aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
        @endif

        <form wire:submit.prevent='updateUser'>
            <div class="row">

                <div class="mb-3 col-md-6">
                    <label class="form-label">Nama</label>
                    <input wire:model.defer="user.name" type="text" class="form-control border border-2 p-2" readonly>
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Email</label>
                    <input wire:model.defer="user.email" type="email" class="form-control border border-2 p-2" readonly>
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">No HP</label>
                    <input wire:model.defer="user.phone" type="text" class="form-control border border-2 p-2" readonly>
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Alamat</label>
                    <input wire:model.defer="user.location" type="text" class="form-control border border-2 p-2" readonly>
                </div>

                <div class="mb-3 col-md-6">
                    <label class="form-label">Password</label>
                    <input type="password" value="********" class="form-control border border-2 p-2" readonly>
                </div>

            </div>

            <button type="submit" class="btn bg-gradient-dark">Simpan Perubahan</button>
        </form>
    </div>
</div>
