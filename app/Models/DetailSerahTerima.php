<?php

namespace App\Models;

use Database\Factories\DetailSerahTerimaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('detail_serah_terima')]
#[Fillable(['serah_terima_id', 'detail_pemesanan_id', 'jumlah_disiapkan', 'kondisi_awal'])]
class DetailSerahTerima extends Model
{
    /** @use HasFactory<DetailSerahTerimaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah_disiapkan' => 'integer',
        ];
    }

    /**
     * @return BelongsTo<SerahTerima, $this>
     */
    public function serahTerima(): BelongsTo
    {
        return $this->belongsTo(SerahTerima::class);
    }

    /**
     * @return BelongsTo<DetailPemesanan, $this>
     */
    public function detailPemesanan(): BelongsTo
    {
        return $this->belongsTo(DetailPemesanan::class);
    }
}
