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
        Schema::create('detail_pemesanan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')
                ->constrained('pemesanan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('alat_id')
                ->constrained('alat')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->integer('jumlah');
            // Snapshot tarif saat pemesanan dibuat.
            $table->decimal('tarif_per_hari', 12, 2);
            $table->decimal('subtotal', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pemesanan');
    }
};
