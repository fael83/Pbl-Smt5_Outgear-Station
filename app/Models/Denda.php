<?php

namespace App\Models;

use Database\Factories\DendaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('denda')]
#[Fillable([
    'pengembalian_id',
    'aturan_denda_id',
    'pemesanan_id',
    'detail_pengembalian_id',
    'nilai_dasar',
    'jumlah_denda',
    'status',
    'jenis_denda',
    'nilai_tarif_terkait',
])]
class Denda extends Model
{
    /** @use HasFactory<DendaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nilai_dasar' => 'decimal:2',
            'jumlah_denda' => 'decimal:2',
            'nilai_tarif_terkait' => 'decimal:2',
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
     * @return BelongsTo<AturanDenda, $this>
     */
    public function aturanDenda(): BelongsTo
    {
        return $this->belongsTo(AturanDenda::class);
    }

    /**
     * @return BelongsTo<Pemesanan, $this>
     */
    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class);
    }

    /**
     * @return BelongsTo<DetailPengembalian, $this>
     */
    public function detailPengembalian(): BelongsTo
    {
        return $this->belongsTo(DetailPengembalian::class);
    }
}
