<div>
    <!-- Trigger dari tabel admin -->
    <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#updateKeluhanModal" wire:click="loadTiket({{ $tiket_id }})">
        Update
    </button>

    <!-- Modal -->
    <div wire:ignore.self class="modal fade" id="updateKeluhanModal" tabindex="-1" role="dialog" aria-labelledby="updateKeluhanModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <form wire:submit.prevent="updateKeluhan">
                    <div class="modal-header">
                        <h5 class="modal-title" id="updateKeluhanModalLabel">Update Keluhan</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    <div class="modal-body">
                        <p><strong>Judul:</strong> {{ $judul }}</p>
                        <p><strong>Deskripsi:</strong> {{ $deskripsi }}</p>

                        <!-- Status -->
                        <div class="form-group mt-3">
                            <label for="status">Status Keluhan</label>
                            <select class="form-control" id="status" wire:model="status">
                                <option value="Menunggu">Menunggu</option>
                                <option value="Diproses">Diproses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                            @error('status') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Tanggapan -->
                        <div class="form-group mt-3">
                            <label for="tanggapan">Tanggapan / Hasil Perbaikan</label>
                            <textarea class="form-control" wire:model="tanggapan" id="tanggapan" rows="3"></textarea>
                            @error('tanggapan') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>

                        <!-- Teknisi -->
                        <div class="form-group mt-3">
                            <label for="teknisi">Teknisi</label>
                            <input type="text" class="form-control" id="teknisi" wire:model="teknisi" placeholder="Nama teknisi...">
                            @error('teknisi') <span class="text-danger text-sm">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn bg-gradient-secondary" data-bs-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn bg-gradient-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
