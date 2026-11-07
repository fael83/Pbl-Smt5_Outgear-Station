<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Metode pembayaran kini ditentukan pelanggan di pop-up Midtrans (qris, gopay,
 * bank_transfer, credit_card, dst.), jadi kolomnya harus bebas teks dan boleh
 * kosong sampai pelanggan benar-benar memilih metode.
 *
 * Data lama (qris/card) tetap aman.
 */
return new class extends Migration
{
    public function up(): void
    {
        if (DB::getDriverName() === 'pgsql') {
            // enum() di PostgreSQL = varchar + CHECK constraint; constraint-nya harus dibuang.
            DB::statement('ALTER TABLE pembayaran DROP CONSTRAINT IF EXISTS pembayaran_metode_pembayaran_check');
            DB::statement('ALTER TABLE pembayaran ALTER COLUMN metode_pembayaran DROP NOT NULL');
            DB::statement('ALTER TABLE pembayaran ALTER COLUMN metode_pembayaran TYPE VARCHAR(50)');

            return;
        }

        Schema::table('pembayaran', function (Blueprint $table) {
            $table->string('metode_pembayaran', 50)->nullable()->change();
        });
    }

    public function down(): void
    {
        // Sengaja dibiarkan kosong: mengembalikan enum akan gagal bila sudah ada
        // metode selain qris/card.
    }
};
