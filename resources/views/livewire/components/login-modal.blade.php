<div>
    <div wire:ignore.self style="z-index:1050000" class="modal fade" id="buatAkun" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                {{-- Header Modal --}}
                <div class="modal-header">
                    <h5 class="modal-title">Buat Akun</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body Modal --}}
                <div class="modal-body">
                    <form wire:submit.prevent="submitAkun">
                        <div class="row">
                            <!-- Nama -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Nama</label>
                                <input wire:model.defer="name" type="text" class="form-control border border-2 p-2" placeholder="Masukkan nama lengkap">
                                @error('name') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Email -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Email</label>
                                <input wire:model.defer="email" type="email" class="form-control border border-2 p-2" placeholder="contoh@email.com">
                                @error('email') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Password -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Password</label>
                                <input wire:model.defer="password" type="password" class="form-control border border-2 p-2" placeholder="Masukkan password">
                                @error('password') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <!-- Confirm Password -->
                            <div class="mb-3 col-md-6">
                                <label class="form-label">Konfirmasi Password</label>
                                <input wire:model.defer="password_confirmation" type="password" class="form-control border border-2 p-2" placeholder="Ulangi password">
                                @error('password_confirmation') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer Modal --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn bg-gradient-dark" wire:click="submitAkun">Submit</button>
                </div>

            </div>
        </div>
    </div>
</div>
