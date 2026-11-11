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
        Schema::create('denda', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalian_id')
                ->constrained('pengembalian')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('aturan_denda_id')
                ->constrained('aturan_denda')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->foreignId('pemesanan_id')
                ->constrained('pemesanan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            // Nullable: denda keterlambatan tidak terikat pada satu baris alat.
            $table->foreignId('detail_pengembalian_id')
                ->nullable()
                ->constrained('detail_pengembalian')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->decimal('nilai_dasar', 12, 2);
            $table->decimal('jumlah_denda', 12, 2);
            $table->enum('status', ['belum_dibayar', 'lunas'])->default('belum_dibayar');
            $table->enum('jenis_denda', [
                'keterlambatan',
                'kerusakan_ringan',
                'kerusakan_berat',
                'kehilangan',
            ]);
            $table->decimal('nilai_tarif_terkait', 12, 2)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('denda');
    }
};
