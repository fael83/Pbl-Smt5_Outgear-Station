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
        Schema::create('pemesanan', function (Blueprint $table) {
            $table->id();
            $table->string('kode_pemesanan')->unique();
            // ERD: pelanggan_id -> PENGGUNA (users)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->decimal('total_biaya', 12, 2)->default(0);
            $table->decimal('jumlah_dp', 12, 2)->default(0);
            $table->enum('status', [
                'menunggu_pembayaran',
                'dikonfirmasi',
                'berlangsung',
                'selesai',
                'dibatalkan',
            ])->default('menunggu_pembayaran');
            $table->dateTime('waktu_pembatalan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pemesanan');
    }
};
