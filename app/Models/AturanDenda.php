<?php

namespace App\Models;

use Database\Factories\AturanDendaFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Table('aturan_denda')]
#[Fillable(['jenis_denda', 'perhitungan_denda', 'nilai_denda', 'user_id'])]
class AturanDenda extends Model
{
    /** @use HasFactory<AturanDendaFactory> */
    use HasFactory;

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'nilai_denda' => 'decimal:2',
        ];
    }

    /**
     * ERD: diubah_oleh (PENGGUNA).
     *
     * @return BelongsTo<User, $this>
     */
    public function pengubah(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * @return HasMany<Denda, $this>
     */
    public function denda(): HasMany
    {
        return $this->hasMany(Denda::class);
    }
}
