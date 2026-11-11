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
        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                ->constrained('kategori')
                ->cascadeOnUpdate()
                ->restrictOnDelete();
            $table->string('nama');
            $table->text('deskripsi')->nullable();
            $table->string('foto')->nullable();
            $table->decimal('tarif_per_hari', 12, 2)->default(0);
            $table->decimal('nilai_pengganti', 12, 2)->default(0);
            $table->integer('stok_total')->default(0);
            $table->integer('stok_tersedia')->default(0);
            $table->integer('stok_disewa')->default(0);
            $table->integer('stok_rusak')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
