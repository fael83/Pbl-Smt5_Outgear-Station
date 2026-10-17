<?php

namespace App\Models;

use Database\Factories\SerahTerimaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('serah_terima')]
#[Fillable([
    'pemesanan_id',
    'user_id',
    'waktu_serah_terima',
    'nama_ktp',
    'nomor_ktp',
    'foto_ktp',
    'status_serah_terima',
])]
class SerahTerima extends Model
{
    /** @use HasFactory<SerahTerimaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'waktu_serah_terima' => 'datetime',
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
     * @return HasMany<DetailSerahTerima, $this>
     */
    public function detailSerahTerima(): HasMany
    {
        return $this->hasMany(DetailSerahTerima::class);
    }
}
