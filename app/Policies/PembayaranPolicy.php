<?php

namespace App\Policies;

use App\Models\Pembayaran;
use App\Models\Pemesanan;
use App\Models\User;

class PembayaranPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Staf internal melihat semua; pelanggan hanya pembayaran miliknya.
     */
    public function view(User $user, Pembayaran $pembayaran): bool
    {
        return $user->isStaff() || $pembayaran->pemesanan->user_id === $user->id;
    }

    /**
     * Pembayaran (DP/pelunasan/denda) dibuat oleh pelanggan pemilik pemesanan.
     */
    public function create(User $user, Pemesanan $pemesanan): bool
    {
        return $user->isCustomer() && $pemesanan->user_id === $user->id;
    }

    /**
     * Status transaksi diperbarui sistem/webhook Midtrans (admin boleh koreksi).
     */
    public function update(User $user, Pembayaran $pembayaran): bool
    {
        return $user->isAdmin();
    }

    public function delete(User $user, Pembayaran $pembayaran): bool
    {
        return $user->isAdmin();
    }
}
