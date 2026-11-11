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
        Schema::create('detail_pengembalian', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pengembalian_id')
                ->constrained('pengembalian')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('detail_pemesanan_id')
                ->constrained('detail_pemesanan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('jumlah_baik')->default(0);
            $table->integer('jumlah_rusak')->default(0);
            $table->integer('jumlah_hilang')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_pengembalian');
    }
};
