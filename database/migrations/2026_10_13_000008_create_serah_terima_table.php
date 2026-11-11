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
        Schema::create('serah_terima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')
                ->constrained('pemesanan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            // ERD: petugas_id -> PENGGUNA (users)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->dateTime('waktu_serah_terima')->nullable();
            $table->string('nama_ktp');
            $table->string('nomor_ktp');
            $table->string('foto_ktp')->nullable();
            $table->enum('status_serah_terima', ['diambil', 'dikembalikan'])
                ->default('diambil');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serah_terima');
    }
};
