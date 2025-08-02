<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('maintenance_req', function (Blueprint $table) {
            $table->id();

            $table->foreignId('id_barang')->constrained('barang')->onDelete('cascade');

            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

            // --- PERBAIKAN PENTING DI SINI ---
            // Jadikan kolom nama_staff nullable agar tidak error jika tidak ada nilai yang diberikan
            $table->string('nama_staff')->nullable();
            // ---------------------------------

            $table->text('problem');
            $table->text('evaluasi')->nullable();

            $table->enum('status', ['pending', 'our_purchasing', 'waiting_material', 'in_progress', 'done'])->default('pending')->nullable();

            $table->string('condition_pict_path')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('maintenance_req');
    }
};
