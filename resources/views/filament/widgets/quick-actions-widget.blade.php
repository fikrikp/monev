<x-filament::widget>
    {{-- Card utama untuk Quick Actions --}}
    <x-filament::card>
        {{-- Judul "Quick Actions" --}}
        <h2 class="text-lg font-bold tracking-tight sm:text-xl dark:text-white">Quick Actions</h2>

        {{-- Container untuk tombol-tombol --}}
        <div class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2 md:grid-cols-4 lg:grid-cols-4 xl:grid-cols-4">
            {{-- Tombol 1: Tambah Pengguna --}}
            <a href="{{ $addUserUrl }}" class="flex items-center justify-center p-3 text-center bg-gray-100 hover:bg-gray-200 rounded-lg shadow-sm transition duration-150 ease-in-out dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium dark:text-gray-200">
                <x-filament::icon icon="heroicon-o-user-plus" class="w-5 h-5 mr-2 text-primary-500" />
                Add User
            </a>

            {{-- Tombol 2: Tambah Area --}}
            <a href="{{ $addAreaUrl }}" class="flex items-center justify-center p-3 text-center bg-gray-100 hover:bg-gray-200 rounded-lg shadow-sm transition duration-150 ease-in-out dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium dark:text-gray-200">
                <x-filament::icon icon='heroicon-o-home-modern' class="w-5 h-5 mr-2 text-primary-500" />
                Add Area
            </a>

            {{-- Tombol 3: Tambah Barang --}}
            <a href="{{ $addBarangUrl }}" class="flex items-center justify-center p-3 text-center bg-gray-100 hover:bg-gray-200 rounded-lg shadow-sm transition duration-150 ease-in-out dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium dark:text-gray-200">
                <x-filament::icon icon="heroicon-o-cube" class="w-5 h-5 mr-2 text-primary-500" />
                Add Item
            </a>

            {{-- Tombol 4: Lihat Laporan --}}
            <a href="{{ $viewReportUrl }}" class="flex items-center justify-center p-3 text-center bg-gray-100 hover:bg-gray-200 rounded-lg shadow-sm transition duration-150 ease-in-out dark:bg-gray-800 dark:hover:bg-gray-700 text-sm font-medium dark:text-gray-200">
                <x-filament::icon icon="heroicon-o-document-text" class="w-5 h-5 mr-2 text-primary-500" />
                Generate Repots
            </a>
        </div>
    </x-filament::card>
</x-filament::widget>