<?php

namespace App\Models;

use Database\Factories\PengembalianFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('pengembalian')]
#[Fillable(['pemesanan_id', 'user_id', 'waktu_pengambilan', 'waktu_submit', 'hari_terlambat'])]
class Pengembalian extends Model
{
    /** @use HasFactory<PengembalianFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'waktu_pengambilan' => 'datetime',
            'waktu_submit' => 'datetime',
            'hari_terlambat' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Pemesanan, $this>
     */
    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class);
    }

    /**
     * ERD: petugas_id (PENGGUNA).
     *
     * @return BelongsTo<User, $this>
     */
    public function petugas(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<DetailPengembalian, $this>
     */
    public function detailPengembalian(): HasMany
    {
        return $this->hasMany(DetailPengembalian::class);
    }

    /**
     * @return HasMany<Denda, $this>
     */
    public function denda(): HasMany
    {
        return $this->hasMany(Denda::class);
    }
}
