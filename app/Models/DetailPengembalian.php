<?php

namespace App\Models;

use Database\Factories\DetailPengembalianFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('detail_pengembalian')]
#[Fillable(['pengembalian_id', 'detail_pemesanan_id', 'jumlah_baik', 'jumlah_rusak', 'jumlah_hilang'])]
class DetailPengembalian extends Model
{
    /** @use HasFactory<DetailPengembalianFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah_baik' => 'integer',
            'jumlah_rusak' => 'integer',
            'jumlah_hilang' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<Pengembalian, $this>
     */
    public function pengembalian(): BelongsTo
    {
        return $this->belongsTo(Pengembalian::class);
    }

    /**
     * @return BelongsTo<DetailPemesanan, $this>
     */
    public function detailPemesanan(): BelongsTo
    {
        return $this->belongsTo(DetailPemesanan::class);
    }

    /**
     * @return HasMany<Denda, $this>
     */
    public function denda(): HasMany
    {
        return $this->hasMany(Denda::class);
    }
}
