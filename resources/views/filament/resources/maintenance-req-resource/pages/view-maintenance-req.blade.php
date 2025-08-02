<x-filament::section>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        {{-- Kolom Kiri: Informasi Detail --}}
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-gray-500 dark:text-gray-400">Nama Staff Pelapor</h3>
                <p class="text-lg">{{ $record->user?->name ?? $record->nama_staff ?? '-' }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-500 dark:text-gray-400">Area / Ruangan</h3>
                <p class="text-lg">{{ $record->barang?->room?->area?->area_name ?? '-' }} / {{ $record->barang?->room?->room_name ?? '-' }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-500 dark:text-gray-400">Kategori Barang</h3>
                <p class="text-lg">{{ $record->barang?->category?->category_name ?? '-' }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-500 dark:text-gray-400">Nama Barang</h3>
                <p class="text-lg">{{ $record->barang?->item_name ?? '-' }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-500 dark:text-gray-400">Type</h3>
                <p class="text-lg">{{ $record->barang?->type ?? '-' }}</p>
            </div>
            <div>
                <h3 class="font-semibold text-gray-500 dark:text-gray-400">Status Saat Ini</h3>
                <span class="px-2 py-1 text-sm font-medium text-white bg-primary-600 rounded-md">
                    {{ \Illuminate\Support\Str::of($record->status ?? '-')->replace('_', ' ')->title() }}
                </span>
            </div>

            @php
            // Otorisasi
            $canUpdateStatusOrImage = auth()->user()->role === 'supervisor';
            $canEditEvaluation = auth()->user()->role === 'chief_engineering';

            // Daftar status
            $allStatuses = [
            ''=>'',
            'our_purchasing' => 'Our Purchasing',
            'waiting_material' => 'Waiting Material',
            'in_progress' => 'In Progress',
            'done' => 'Done'
            ];
            @endphp

            {{-- Form Ubah Status --}}
            @if($canUpdateStatusOrImage)
            <div class="mt-6 space-x-2">
                <h3 class="font-semibold text-gray-500 dark:text-gray-400">Ubah Status:</h3>
                @foreach ($allStatuses as $value => $label)
                <form method="POST" action="{{ route('evaluasi.ubah-status', $record->id) }}" class="inline">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="status" value="{{ $value }}">
                    <button type="submit"
                        class="px-8 py-1 bg-primary-600 text-white text-sm rounded hover:bg-primary-700 transition mr-2">
                        {{ $label }}
                    </button>
                </form>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Kolom Kanan: Gambar dan Kondisi --}}
        <div class="space-y-4">
            <div>
                <h3 class="font-semibold text-gray-500 dark:text-gray-400">Deskripsi Masalah (Kondisi)</h3>
                <p class="text-lg whitespace-pre-wrap">{{ $record->problem }}</p>
            </div>

            <div>
                <h3 class="font-semibold text-gray-500 dark:text-gray-400">Evaluasi</h3>

                <form method="POST" action="{{ route('evaluasi.kirim', $record->id) }}">
                    @csrf

                    <div class="mb-4">
                        <textarea
                            name="evaluasi"
                            id="evaluasi"
                            rows="3"
                            class="block w-full mt-1 border-gray-300 rounded-lg shadow-sm focus:ring-primary-500 focus:border-primary-500
                    dark:bg-gray-700 dark:border-gray-600 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500"
                            @if (auth()->user()->role !== 'chief_engineering') disabled @endif>{{ $record->evaluasi }}</textarea>
                    </div>

                    @if (auth()->user()->role === 'chief_engineering')
                    <div class="mt-4">
                        <button type="submit"
                            class="px-2 py-1 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition">
                            Kirim Evaluasi
                        </button>
                    </div>
                    @endif
                </form>
            </div>

            <div>
                <h3 class="font-semibold text-gray-500 dark:text-gray-400">Foto Kondisi</h3>

                @if ($canUpdateStatusOrImage)
                <form method="POST" action="{{ route('evaluasi.update-gambar', $record->id) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')

                    <div class="mb-4 mt-2">
                        <input
                            type="file"
                            name="condition_pict"
                            accept="image/*"
                            class="block w-full text-sm text-gray-500
                            file:mr-4 file:py-2 file:px-4
                            file:rounded-md file:border-0
                            file:text-sm file:font-semibold
                            file:bg-primary-50 file:text-primary-700
                            hover:file:bg-primary-100">
                    </div>

                    <button type="submit"
                        class="px-6 py-1 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 transition mb-4">
                        Update Gambar
                    </button>
                </form>
                @endif

                @if ($record->condition_pict_path)
                <img src="{{ asset('storage/'.$record->condition_pict_path) }}"
                    alt="Foto kondisi barang yang dilaporkan untuk maintenance"
                    class="mt-4 rounded-lg max-w-sm">
                @else
                <p class="text-gray-500 mt-2">Tidak ada gambar.</p>
                @endif
            </div>
        </div>
    </div>
</x-filament::section>