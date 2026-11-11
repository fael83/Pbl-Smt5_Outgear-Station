<?php

namespace App\Models;

use Database\Factories\AlatFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
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
     * URL foto siap pakai untuk <img> (Supabase, URL luar, atau berkas lama di
     * public/images/alat). Null bila alat belum punya foto.
     *
     * @return Attribute<string|null, never>
     */
    protected function fotoUrl(): Attribute
    {
        return Attribute::get(fn (): ?string => \App\Support\FotoAlat::url($this->foto));
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
