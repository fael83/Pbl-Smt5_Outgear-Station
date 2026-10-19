<?php

namespace App\Models;

use Database\Factories\PemesananFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('pemesanan')]
#[Fillable([
    'kode_pemesanan',
    'user_id',
    'tanggal_mulai',
    'tanggal_selesai',
    'total_biaya',
    'jumlah_dp',
    'status',
    'waktu_pembatalan',
])]
class Pemesanan extends Model
{
    /** @use HasFactory<PemesananFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tanggal_mulai' => 'date',
            'tanggal_selesai' => 'date',
            'total_biaya' => 'decimal:2',
            'jumlah_dp' => 'decimal:2',
            'waktu_pembatalan' => 'datetime',
        ];
    }

    /**
     * Lama sewa dalam hari, menghitung tanggal mulai dan tanggal selesai
     * secara inklusif. Sewa 1-3 Oktober berarti 3 hari.
     */
    public function lamaSewa(): int
    {
        $selisihHari = (int) $this->tanggal_mulai->copy()->startOfDay()
            ->diffInDays($this->tanggal_selesai->copy()->startOfDay());

        return max(1, $selisihHari + 1);
    }

    /**
     * ERD: pelanggan_id (PENGGUNA).
     *
     * @return BelongsTo<User, $this>
     */
    public function pelanggan(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<DetailPemesanan, $this>
     */
    public function detailPemesanan(): HasMany
    {
        return $this->hasMany(DetailPemesanan::class);
    }

    /**
     * @return HasMany<Pengembalian, $this>
     */
    public function pengembalian(): HasMany
    {
        return $this->hasMany(Pengembalian::class);
    }

    /**
     * @return HasMany<SerahTerima, $this>
     */
    public function serahTerima(): HasMany
    {
        return $this->hasMany(SerahTerima::class);
    }

    /**
     * @return HasMany<Denda, $this>
     */
    public function denda(): HasMany
    {
        return $this->hasMany(Denda::class);
    }

    /**
     * @return HasMany<Pembayaran, $this>
     */
    public function pembayaran(): HasMany
    {
        return $this->hasMany(Pembayaran::class);
    }
}
