<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Melengkapi tabel `users` (ERD: PENGGUNA) dengan kolom yang belum ada.
     * Mapping ERD -> Laravel:
     *   nama        -> name        (sudah ada)
     *   email       -> email       (sudah ada)
     *   kata_sandi  -> password    (sudah ada)
     *   peran       -> role        (sudah ada, enum: customer|admin|petugas|owner)
     */
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('no_telepon')->nullable();
            $table->enum('status_akun', ['aktif', 'nonaktif'])->default('aktif');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['no_telepon', 'status_akun']);
        });
    }
};
