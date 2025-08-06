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
                                <div class="col text-end">
                                     <!-- Button trigger modal -->
                                    <a href="{{ route('tabel-keluhan.tambah') }}" class="btn bg-gradient-info">
                                        Hubungi Teknisi
                                    </a>
                                    @if($this->allTasksCompleted())
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
                                            <th class="text-uppercase text-secondary text-xxs font-weight-bolder opacity-7 text-center">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($tasks as $index => $task)
                                        <tr wire:key="task-{{ $index }}">
                                            <td>
                                                <div class="d-flex px-2 py-1">
                                                    <div>
                                                        <h6 class="mb-0 text-sm {{ $task['completed'] ? 'text-decoration-line-through text-muted' : '' }}">
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
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
