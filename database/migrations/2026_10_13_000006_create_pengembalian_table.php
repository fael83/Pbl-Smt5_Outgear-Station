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
        Schema::create('pengembalian', function (Blueprint $table) {
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
            $table->dateTime('waktu_pengambilan')->nullable();
            $table->dateTime('waktu_submit')->nullable();
            $table->integer('hari_terlambat')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pengembalian');
    }
};
