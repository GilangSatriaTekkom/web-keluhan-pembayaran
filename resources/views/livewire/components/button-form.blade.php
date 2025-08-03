<div class="tambah-button--wrapper">
    @use('Illuminate\Support\Str')

    @if(Str::endsWith(url()->current(), 'karyawan'))
        <a href="{{ route('karyawan.tambah') }}" class="btn col btn-info mb-3" data-toggle="modal" data-target="#modalTambahKaryawan">
            <i class="fas fa-user-plus"></i> Tambah Karyawan
        </a>
    @elseif(Str::endsWith(url()->current(), 'pelanggan'))
        <a href="{{ route('pelanggan.tambah') }}" class="btn col btn-info mb-3" data-toggle="modal" data-target="#modalTambahKaryawan">
            <i class="fas fa-user-plus"></i> Tambah Pelanggan
        </a>
    @endif
</div>
