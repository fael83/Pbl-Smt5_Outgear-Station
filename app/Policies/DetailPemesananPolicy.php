<?php

namespace App\Policies;

use App\Models\DetailPemesanan;
use App\Models\User;

class DetailPemesananPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, DetailPemesanan $detailPemesanan): bool
    {
        return $user->isStaff() || $this->milikPelanggan($user, $detailPemesanan);
    }

    public function create(User $user): bool
    {
        return $user->isCustomer();
    }

    public function update(User $user, DetailPemesanan $detailPemesanan): bool
    {
        return $user->isAdmin()
            || ($this->milikPelanggan($user, $detailPemesanan) && $this->masihBisaDiubah($detailPemesanan));
    }

    public function delete(User $user, DetailPemesanan $detailPemesanan): bool
    {
        return $user->isAdmin()
            || ($this->milikPelanggan($user, $detailPemesanan) && $this->masihBisaDiubah($detailPemesanan));
    }

    protected function milikPelanggan(User $user, DetailPemesanan $detailPemesanan): bool
    {
        return $detailPemesanan->pemesanan->user_id === $user->id;
    }

    protected function masihBisaDiubah(DetailPemesanan $detailPemesanan): bool
    {
        return $detailPemesanan->pemesanan->status === 'menunggu_pembayaran';
    }
}
