    <div class="">
        <!-- Navbar -->
        <!-- End Navbar -->
        <div class="container-fluid py-4">
            <div class="row">
                <div class="col-12">
                    <div class="card my-4">
                        <div class="card-header p-0 position-relative mt-n4 mx-3 z-index-2">
                             <div class="bg-gradient-primary row shadow-primary border-radius-lg pt-4 pb-3">
                                <h6 class="text-white col text-capitalize ps-3">Tugas Keluhan</h6>
                                <div class="col-8 text-end">
                                    @auth
                                     <!-- Button trigger modal -->
                                     @if (Auth::user()->role == 'admin')
                                        <button data-bs-toggle="modal" data-bs-target="#editModal" class="btn bg-gradient-info">
                                                Tambah Teknisi Penanggung Jawab
                                        </button>
                                     @endif
                                    @endauth
                                    <button wire:click="redirectToWhatsAppPelanggan" class="btn bg-gradient-info">
                                        Hubungi Pelanggan
                                    </button>
                                    @auth
                                        <button
                                            @if (collect($listTeknisi)->isEmpty()|| $CS == 'null')
                                                disabled
                                            @endif
                                            wire:click="redirectToWhatsApp" class="btn bg-gradient-info">
                                            @if(Auth::user()->role == 'admin')
                                                Hubungi Teknisi
                                            @else
                                                Hubungi Customer Service
                                            @endif
                                        </button>
                                    @endauth

                                    @if($this->allTasksCompleted() && Auth::user()->role == 'admin')
                                        <button wire:click="selesaikanKeluhan" class="btn bg-gradient-success ms-2">
                                            Selesaikan Keluhan
                                        </button>
                                    @endif
                                </div>
                            </div>
                        </div>
                        <div class="card-body px-0 pb-2">
                            <div class="table-responsive p-0">
                                <table class="table align-items-center mb-0">
                                    <thead>
                                        <tr>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Tugas</th>
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">Status</th>
                                            @if(Auth::user()->role == 'teknisi')
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                                            @endif
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tasks as $index => $task)
                                        <tr wire:key="task-{{ $index }}">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <h6 class="mb-0 text-sm {{ $task['completed'] ? 'text-muted' : '' }}">
                                                            {{ $task['task'] }}
                                                        </h6>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                @if($task['completed'])
                                                    <span class="badge badge-sm bg-gradient-success">Selesai</span>
                                                @else
                                                    <span class="badge badge-sm bg-gradient-warning">Dalam Proses</span>
                                                @endif
                                            </td>
                                            @auth
                                                <!-- Button trigger modal -->
                                                @if (Auth::user()->role == 'teknisi')
                                            <td class="align-middle text-center">
                                                <div class="form-check d-inline-block">
                                                    <input
                                                        type="checkbox"
                                                        class="form-check-input"
                                                        wire:model.lazy="tasks.{{ $index }}.completed"
                                                        wire:change.debounce.500ms="updateTaskStatus({{ $index }})"
                                                        id="task-check-{{ $index }}"
                                                        {{ $task['completed'] ? 'checked' : '' }}
                                                    >
                                                    <label class="form-check-label cursor-pointer" for="task-check-{{ $index }}">
                                                        Tandai Selesai
                                                    </label>
                                                </div>

                                                <button
                                                    wire:click="removeTask({{ $index }})"
                                                    class="btn btn-link text-danger text-xs p-0 ms-2"
                                                    title="Hapus Tugas"
                                                >
                                                    <i class="fas fa-trash-alt"></i>
                                                </button>

                                            </td>
                                                @endif
                                            @endauth
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                @auth
                                     <!-- Button trigger modal -->
                                     @if (Auth::user()->role == 'teknisi')
                                        <!-- Add new task form -->
                                        <div class="mt-3" style="padding: 0px 1.5rem;">
                                            <div class="input-group">
                                                <input
                                                    type="text"
                                                    wire:model="newTask"
                                                    class="form-control"
                                                    placeholder="Tambah tugas baru"
                                                >
                                                <button wire:click="addTask" class="btn btn-primary">
                                                    Tambah
                                                </button>
                                            </div>
                                            @error('newTask') <span class="text-danger">{{ $message }}</span> @enderror
                                        </div>
                                    @endif
                                    <span style ="padding-left: 24px; color: gray"> *Hubungi Customer Service jika seluruh tugas sudah selesai.</span>
                                @endauth
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>


       {{-- Modal Edit Karyawan --}}
        <div wire:ignore.self class="modal fade" id="editModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                    {{-- Header Modal --}}
                    <div class="modal-header">
                        <h5 class="modal-title">Pilih Teknisi Penanggung Jawab</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Body Modal --}}
                    <div class="modal-body">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">Nama</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">No HP</th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Pilih</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($allTeknisi as $teknisi)
                                <tr>
                                    <td>
                                        <div class="d-flex px-2 py-1">
                                            <div>
                                                <h6 class="mb-0 text-sm">
                                                    {{ $teknisi->name }}
                                                </h6>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $teknisi->phone }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <div class="form-check d-inline-block">
                                            <input
                                                type="checkbox"
                                                class="form-check-input"
                                                wire:model="selectedTeknisi"
                                                value="{{ $teknisi->id }}"
                                                id="teknisi-check-{{ $teknisi->id }}"
                                            >
                                            <label class="form-check-label cursor-pointer" for="teknisi-check-{{ $teknisi->id }}">
                                                Pilih
                                            </label>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="button" class="btn bg-gradient-dark" wire:click="updateEdit">Submit</button>
                    </div>

                </div>
            </div>
        </div>

        {{-- Modal WhatsApp Teknisi --}}
        <div wire:ignore.self class="modal fade" id="teknisiModal" tabindex="-1" aria-labelledby="teknisiModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                <div class="modal-content">

                    {{-- Header Modal --}}
                    <div class="modal-header">
                        <h5 class="modal-title">Hubungi Teknisi via WhatsApp</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>

                    {{-- Body Modal --}}
                    <div class="modal-body">
                        <table class="table align-items-center mb-0">
                            <thead>
                                <tr>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7">
                                        Nama
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 ps-2">
                                        No HP
                                    </th>
                                    <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">
                                        WhatsApp
                                    </th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($listTeknisi as $teknisi)
                                <tr>
                                    <td>
                                        <h6 class="mb-0 text-sm">{{ $teknisi->name }}</h6>
                                    </td>
                                    <td>
                                        <span class="text-secondary text-xs font-weight-bold">
                                            {{ $teknisi->phone }}
                                        </span>
                                    </td>
                                    <td class="align-middle text-center">
                                        <a
                                            href="https://wa.me/{{ preg_replace('/^0/', '62', $teknisi->phone) }}"
                                            target="_blank"
                                            class="btn btn-success btn-sm"
                                        >
                                            <i class="fab fa-whatsapp"></i> Chat
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Footer Modal --}}
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>

                </div>
            </div>
        </div>




    </div>

    <script>
        window.addEventListener('show-teknisi-modal', () => {
            var myModal = new bootstrap.Modal(document.getElementById('teknisiModal'));
            myModal.show();
        });
    </script>
