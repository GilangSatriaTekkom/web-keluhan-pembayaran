<div>
    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="keluhanModal" tabindex="-1" role="dialog" aria-labelledby="keluhanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="submitKeluhan">
                    <div class="modal-header">
                        <h5 class="modal-title" id="keluhanModalLabel">Form Keluhan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <!-- Judul Keluhan -->
                        <div class="form-group">
                            <label for="judul">Judul Keluhan</label>
                            <input type="text" class="form-control" id="judul" wire:model="judul" placeholder="Contoh: Internet putus total">
                            @error('judul') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Deskripsi -->
                        <div class="form-group mt-3">
                            <label for="deskripsi">Deskripsi Keluhan</label>
                            <textarea class="form-control" id="deskripsi" wire:model="deskripsi" rows="3" placeholder="Detail keluhan..."></textarea>
                            @error('deskripsi') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Pilih Jenis Gangguan -->
                        <div class="form-group mt-3">
                            <label for="jenis">Jenis Gangguan</label>
                            <select class="form-control" wire:model="jenis_gangguan" id="jenis">
                                <option value="">-- Pilih --</option>
                                <option value="Lambat">Koneksi Lambat</option>
                                <option value="Putus">Tidak Ada Internet</option>
                                <option value="Router">Masalah Router</option>
                            </select>
                            @error('jenis_gangguan') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn bg-gradient-primary">Kirim Keluhan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
