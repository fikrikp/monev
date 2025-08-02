<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash; // ✅ PENTING: Import Hash facade
use App\Models\User; // ✅ PENTING: Import model User Anda

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Jalankan seeder CategorySeeder (tetap ada)
        $this->call([
            CategorySeeder::class,
        ]);

        // --- ✅ TAMBAHKAN LOGIKA PEMBUATAN PENGGUNA DI SINI ---

        // 1. Akun Admin
        User::create([
            'name' => 'Admin',
            'fullname' => 'Intan',
            'email' => 'admin@gmail.com', // Ganti dengan email yang Anda inginkan
            'password' => Hash::make('admin'), // Ganti 'password123' dengan password yang kuat
            'role' => 'admin', // Pastikan nama kolom 'role' dan nilainya sesuai dengan di database Anda
        ]);
    }
}
