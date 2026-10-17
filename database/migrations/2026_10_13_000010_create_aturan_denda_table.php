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
        Schema::create('aturan_denda', function (Blueprint $table) {
            $table->id();
            // Satu aturan per jenis denda.
            $table->enum('jenis_denda', [
                'keterlambatan',
                'kerusakan_ringan',
                'kerusakan_berat',
                'kehilangan',
            ])->unique();
            $table->enum('perhitungan_denda', ['per_hari', 'persentase']);
            $table->decimal('nilai_denda', 12, 2);
            // ERD: diubah_oleh -> PENGGUNA (users)
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('aturan_denda');
    }
};
