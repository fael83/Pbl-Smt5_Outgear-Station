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
        Schema::create('pembayaran', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pemesanan_id')
                ->constrained('pemesanan')
                ->cascadeOnUpdate()
                ->cascadeOnDelete();
            // order_id / transaction_id dari Midtrans.
            $table->string('transaction_id')->nullable()->unique();
            $table->string('midtrans')->nullable();
            $table->enum('jenis_pembayaran', ['dp', 'pelunasan', 'denda']);
            $table->enum('metode_pembayaran', ['qris', 'card']);
            $table->decimal('jumlah', 12, 2);
            $table->enum('status_transaksi', ['pending', 'berhasil', 'gagal'])
                ->default('pending');
            $table->dateTime('waktu_bayar')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pembayaran');
    }
};
