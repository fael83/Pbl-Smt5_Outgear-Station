<?php

use App\Enums\Role;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

it('shows this month revenue and keeps pending payments in the verification track', function () {
    $this->travelTo(now()->setDate(2026, 11, 15)->setTime(12, 0));
    Cache::forget('admin:dashboard:data');

    $admin = User::create([
        'name' => 'Admin',
        'email' => 'admin@example.test',
        'password' => 'password',
        'role' => Role::Admin,
    ]);

    $customer = User::create([
        'name' => 'Pelanggan Uji',
        'email' => 'customer@example.test',
        'password' => 'password',
        'role' => Role::Customer,
    ]);

    $pemesanan = Pemesanan::create([
        'kode_pemesanan' => 'ORD-TEST-1',
        'user_id' => $customer->id,
        'tanggal_mulai' => '2026-11-20',
        'tanggal_selesai' => '2026-11-21',
        'total_biaya' => 300000,
        'jumlah_dp' => 150000,
        'status' => 'menunggu_pembayaran',
    ]);

    Pembayaran::create([
        'pemesanan_id' => $pemesanan->id,
        'jenis_pembayaran' => 'dp',
        'metode_pembayaran' => 'qris',
        'jumlah' => 150000,
        'status_transaksi' => 'berhasil',
        'waktu_bayar' => '2026-11-10 10:00:00',
    ]);

    Pembayaran::create([
        'pemesanan_id' => $pemesanan->id,
        'jenis_pembayaran' => 'dp',
        'metode_pembayaran' => 'qris',
        'jumlah' => 50000,
        'status_transaksi' => 'berhasil',
        'waktu_bayar' => '2026-10-31 23:59:59',
    ]);

    Pembayaran::create([
        'pemesanan_id' => $pemesanan->id,
        'jenis_pembayaran' => 'pelunasan',
        'metode_pembayaran' => 'qris',
        'jumlah' => 25000,
        'status_transaksi' => 'pending',
    ]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk()
        ->assertSee('Pendapatan Bulan Ini')
        ->assertSee('150.000')
        ->assertSee('1 transaksi berhasil')
        ->assertSee('Aktivitas Booking Terbaru')
        ->assertSee('Verifikasi Pembayaran')
        ->assertSee('Pelanggan Uji')
        ->assertSee(route('admin.pembayaran.index'));
});
