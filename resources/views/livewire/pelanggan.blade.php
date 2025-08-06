<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <div class="card my-4">
                <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                    <div class="bg-gradient-primary shadow-primary border-radius-lg pt-4 pb-3" style="display: flex; flex-direction: row; justify-content: space-between; align-items: center;">
                        <h6 class="text-white mx-3"><strong>Data Pelanggan</strong></h6>
                        <div class="me-3 my-3 text-end">
                            <livewire:components.button-form />
                        </div>
                    </div>
                </div>
                <div class="card-body px-0 pb-2">
                    <div class="table-responsive p-0">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        ID
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        NAMA
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        EMAIL
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        NO HP
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        STATUS
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        AKSI
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($pelanggans as $pelanggan)

                                    <tr >
                                        <td class="px-3">
                                            <p class="text-sm mb-0">{{ $pelanggan->id }}</p>
                                        </td>
                                        <td>
                                            <div class="d-flex flex-column justify-content-center">
                                                <h6 class="mb-0 text-sm">{{ $pelanggan->name }}</h6>
                                            </div>
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="text-xs text-secondary mb-0">{{ $pelanggan->email }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            <p class="text-xs text-secondary mb-0">{{ $pelanggan->phone }}</p>
                                        </td>
                                        <td class="align-middle text-center">
                                            @if ($pelanggan->status == 'aktif')
                                                <span class="badge badge-sm bg-gradient-success">Aktif</span>
                                            @else
                                                <span class="badge badge-sm bg-gradient-danger">Tidak Aktif</span>
                                            @endif
                                        </td>
                                        <td class="align-middle text-center">
                                            <a rel="tooltip" class="btn btn-success btn-link"
                                                href="{{ route('pelanggan.edit', $pelanggan->id) }}"
                                                title="Edit">
                                                <i class="material-icons">edit</i>
                                            </a>
                                            <form action="{{ route('pelanggan.destroy', $pelanggan->id) }}" method="POST"
                                                style="display: inline;">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-link" title="Hapus"
                                                    onclick="return confirm('Yakin ingin menghapus pelanggan ini?')">
                                                    <i class="material-icons">close</i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>

                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <p class="text-secondary text-xs font-weight-bold mb-0">Tidak ada data pelanggan.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
