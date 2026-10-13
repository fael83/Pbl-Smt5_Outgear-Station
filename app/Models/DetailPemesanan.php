<?php

namespace App\Models;

use Database\Factories\DetailPemesananFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('detail_pemesanan')]
#[Fillable(['pemesanan_id', 'alat_id', 'jumlah', 'tarif_per_hari', 'subtotal'])]
class DetailPemesanan extends Model
{
    /** @use HasFactory<DetailPemesananFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah' => 'integer',
            'tarif_per_hari' => 'decimal:2',
            'subtotal' => 'decimal:2',
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
     * @return BelongsTo<Alat, $this>
     */
    public function alat(): BelongsTo
    {
        return $this->belongsTo(Alat::class);
    }

    /**
     * @return HasMany<DetailPengembalian, $this>
     */
    public function detailPengembalian(): HasMany
    {
        return $this->hasMany(DetailPengembalian::class);
    }

    /**
     * @return HasMany<DetailSerahTerima, $this>
     */
    public function detailSerahTerima(): HasMany
    {
        return $this->hasMany(DetailSerahTerima::class);
    }
}
