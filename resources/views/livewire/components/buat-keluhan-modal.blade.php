<div>
    <div wire:ignore.self style="z-index:1050000" class="modal fade" id="buatKeluhanModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                {{-- Header Modal --}}
                <div class="modal-header">
                    <h5 class="modal-title">Buat Keluhan Baru</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                {{-- Body Modal --}}
                <div class="modal-body">
                    <form wire:submit.prevent="submitKeluhan">
                        <div class="row">
                            <div class="mb-3">
                                <label class="form-label">Sebutkan keluhan anda</label>
                                <input type="text" placeholder="Gangguan Internet" wire:model.defer="tiket.judul" class="form-control border border-2 p-2">
                                @error('tiket.judul') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>

                            <div class="mb-3 col-md-12">
                                <label class="form-label">Jelaskan keluhan lebih detail</label>
                                <textarea wire:model.defer="tiket.description" class="form-control border border-2 p-2" rows="4"
                                    placeholder="Jelaskan Lebih Rinci..."></textarea>
                                @error('tiket.description') <small class="text-danger">{{ $message }}</small> @enderror
                            </div>
                        </div>
                    </form>
                </div>

                {{-- Footer Modal --}}
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="button" class="btn bg-gradient-dark" wire:click="submitKeluhan">Kirim Keluhan</button>
                </div>

            </div>
        </div>
    </div>
</div>
