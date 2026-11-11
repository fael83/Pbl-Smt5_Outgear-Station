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
        Schema::create('detail_serah_terima', function (Blueprint $table) {
            $table->id();
            $table->foreignId('serah_terima_id')
                ->constrained('serah_terima')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->foreignId('detail_pemesanan_id')
                ->constrained('detail_pemesanan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            $table->integer('jumlah_disiapkan')->default(0);
            $table->enum('kondisi_awal', ['baik', 'kurang_baik'])->default('baik');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('detail_serah_terima');
    }
};
