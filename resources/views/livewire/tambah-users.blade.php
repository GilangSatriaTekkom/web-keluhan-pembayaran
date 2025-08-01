

<!-- Modal Tambah Karyawan -->
<div class="modal fade" id="modalTambahKaryawan" tabindex="-1" role="dialog" aria-labelledby="modalTambahKaryawanLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <form action="{{ route('tambah-users') }}" method="POST">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title">Tambah Karyawan</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span>&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <livewire:components.forms.form-users />
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Simpan</button>
          <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
        </div>
      </div>
    </form>
  </div>
</div>
