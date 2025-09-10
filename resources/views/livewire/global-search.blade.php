<div>
    <!-- Input di navbar -->
    <input
        type="text"
        wire:model.live.debounce.300ms="query"
        placeholder="Search..."
        class="px-4 py-2 rounded-lg border focus:outline-none focus:ring focus:ring-indigo-200"
    >

    <!-- Modal overlay -->
    @if($showModal)
    <div style="position: fixed; z-index: 50;" class="fixed inset-0 bg-black bg-opacity-50 flex justify-center items-start z-50">
        <div style="margin-top: unset; max-height: 70vh" class="bg-white w-3/4 max-h-[80vh] overflow-y-auto rounded-lg shadow-lg p-4 relative">
            {{-- <!-- Tombol close -->
            <button wire:click="closeModal" class="absolute top-2 right-2 text-gray-600 hover:text-black">
                ✕
            </button> --}}

            <h2 class="text-lg font-bold mb-4">Hasil pencarian untuk "{{ $query }}"</h2>

            @php
                $totalResults = $results['users']->count()
                                + $results['tagihan']->count()
                                + $results['tiket']->count();
            @endphp

            @if($totalResults === 0)
                <p class="text-gray-500 italic">Tidak ada data ditemukan</p>
            @else

                @if(count($results['users']) > 0)
                    <div class="mb-3">
                        <h3 class="font-bold mt-2">Users</h3>
                        @foreach($results['users'] as $user)
                            <a href="{{ route('pelanggan.lihat', $user->id) }}"
                            class="block px-3 py-2 hover:bg-gray-100 rounded">
                                👤 {{ $user->name }} - {{ $user->status }}
                            </a>
                        @endforeach
                    </div>
                @endif

                {{-- Tagihan --}}
                @if($results['tagihan']->isNotEmpty())
                    <h3 class="font-bold mt-2">Tagihan</h3>
                    <ul class="mb-3">
                        @foreach($results['tagihan'] as $tagih)
                            <li class="border-b py-1">
                                <a href="{{ route('lihat.pembayaran', $tagih->id) }}"
                                class="hover:underline block truncate"
                                title="No: {{ $tagih->id }} jatuh tempo: {{ $tagih->tgl_jatuh_tempo }}) - {{ $tagih->status_pembayaran }}">
                                    No: {{ $tagih->id }} jatuh tempo: {{ $tagih->tgl_jatuh_tempo }} - {{ $tagih->status_pembayaran }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

                {{-- Tiket --}}
                @if($results['tiket']->isNotEmpty())
                    <h3 class="font-bold mt-2">Tiket</h3>
                    <ul class="mb-3">
                        @foreach($results['tiket'] as $t)
                            <li class="border-b py-1">
                                <a href="{{ route('lihat.keluhan', $t->id) }}"
                                class="hover:underline block truncate"
                                title="{{ $t->category }} - {{ $t->description }} - {{ $t->status }}">
                                    {{ $t->category }} - {{ $t->description }} - {{ $t->status }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                @endif

            @endif
        </div>
    </div>
    @else
    @endif
</div>
