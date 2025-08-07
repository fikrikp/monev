<x-filament::section>
    @php
    $canUpdateStatusOrImage = auth()->user()->role === 'supervisor';
    $canEditEvaluation = auth()->user()->role === 'chief_engineering';

    $allStatuses = [
    'our_purchasing' => 'Our Purchasing',
    'waiting_material' => 'Waiting Material',
    'in_progress' => 'In Progress',
    'done' => 'Done'
    ];
    @endphp

    <div class="space-y-8">
        {{-- Bagian Aksi Utama --}}
        <div class="bg-gray-100 dark:bg-gray-800 p-6 rounded-lg">
            <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Lakukan Tindakan</h2>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <h3 class="font-semibold text-gray-500 dark:text-gray-400">Evaluasi</h3>
                    <form method="POST" action="{{ route('evaluasi.kirim', $record->id) }}" class="mt-2">
                        @csrf
                        @method('PUT')
                        <textarea name="evaluasi" rows="4" class="block w-full border-gray-300 rounded-md dark:bg-gray-700 dark:border-gray-600 dark:text-white" @if (!$canEditEvaluation) readonly @endif>{{ $record->evaluasi }}</textarea>
                        @if ($canEditEvaluation)
                        <button type="submit" class="mt-2 px-4 py-2 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700 w-full">Kirim Evaluasi</button>
                        @endif
                    </form>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-500 dark:text-gray-400">Status Maintenance:
                        <span class="px-2 py-1 text-sm font-medium text-white bg-primary-600 rounded-md">
                            {{ \Illuminate\Support\Str::of($record->status ?? '-')->replace('_', ' ')->title() }}
                        </span>
                    </h3>
                    @if($canUpdateStatusOrImage)
                    <div class="mt-2 grid grid-cols-2 gap-2">
                        @foreach ($allStatuses as $value => $label)
                        <form method="POST" action="{{ route('evaluasi.ubah-status', $record->id) }}">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="status" value="{{ $value }}">
                            <button type="submit" class="w-full py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 dark:bg-gray-700 dark:text-gray-200 dark:hover:bg-gray-600">
                                {{ $label }}
                            </button>
                        </form>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Bagian Informasi Laporan & Foto --}}
        <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-md grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Informasi Laporan</h2>
                <div>
                    <h3 class="font-semibold text-gray-500 dark:text-gray-400">Nama Staff Pelapor</h3>
                    <p class="text-lg">{{ $record->user?->name ?? $record->nama_staff ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-500 dark:text-gray-400">Area / Ruangan</h3>
                    <p class="text-lg">{{ $record->barang?->room?->area?->area_name ?? '-' }} / {{ $record->barang?->room?->room_name ?? '-' }}</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-500 dark:text-gray-400">Kategori & Nama Barang</h3>
                    <p class="text-lg">{{ $record->barang?->category?->category_name ?? '-' }} / {{ $record->barang?->item_name ?? '-' }} ({{ $record->barang?->type ?? '-' }})</p>
                </div>
                <div>
                    <h3 class="font-semibold text-gray-500 dark:text-gray-400">Deskripsi Masalah</h3>
                    <p class="text-lg whitespace-pre-wrap">{{ $record->problem }}</p>
                </div>
            </div>
            <div class="space-y-4">
                <h2 class="text-2xl font-bold text-gray-800 dark:text-gray-200 mb-4">Foto Kondisi</h2>
                @if ($record->condition_pict_path)
                <img src="{{ asset('storage/'.$record->condition_pict_path) }}"
                    alt="Foto kondisi barang yang dilaporkan"
                    class="rounded-lg w-full h-auto object-cover max-h-96">
                @else
                <p class="text-gray-500">Tidak ada gambar.</p>
                @endif
                @if ($canUpdateStatusOrImage)
                <form method="POST" action="{{ route('evaluasi.update-gambar', $record->id) }}" enctype="multipart/form-data" class="flex items-center space-x-2 mt-4">
                    @csrf
                    @method('PUT')
                    <input type="file" name="condition_pict" accept="image/*" class="text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 dark:file:bg-gray-700 dark:file:text-white">
                    <button type="submit" class="px-4 py-2 bg-primary-600 text-white font-semibold rounded-md hover:bg-primary-700">Update</button>
                </form>
                @endif
            </div>
        </div>
    </div>
</x-filament::section>