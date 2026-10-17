<?php

namespace App\Models;

use Database\Factories\AlatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('alat')]
#[Fillable([
    'kategori_id',
    'nama',
    'deskripsi',
    'foto',
    'tarif_per_hari',
    'nilai_pengganti',
    'stok_total',
    'stok_tersedia',
    'stok_disewa',
    'stok_rusak',
])]
class Alat extends Model
{
    /** @use HasFactory<AlatFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'tarif_per_hari' => 'decimal:2',
            'nilai_pengganti' => 'decimal:2',
            'stok_total' => 'integer',
            'stok_tersedia' => 'integer',
            'stok_disewa' => 'integer',
            'stok_rusak' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Kategori, $this>
     */
    public function kategori(): BelongsTo
    {
        return $this->belongsTo(Kategori::class);
    }

    /**
     * @return HasMany<DetailPemesanan, $this>
     */
    public function detailPemesanan(): HasMany
    {
        return $this->hasMany(DetailPemesanan::class);
    }
}
