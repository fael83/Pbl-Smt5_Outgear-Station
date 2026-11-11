<?php

namespace App\Models;

use Database\Factories\PembayaranFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Table;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Table('pembayaran')]
#[Fillable([
    'pemesanan_id',
    'transaction_id',
    'midtrans',
    'jenis_pembayaran',
    'metode_pembayaran',
    'jumlah',
    'status_transaksi',
    'waktu_bayar',
])]
class Pembayaran extends Model
{
    /** @use HasFactory<PembayaranFactory> */
    use HasFactory;

    /**
     * Angka lencana sidebar/dashboard bergantung pada tabel ini, jadi cache-nya
     * dibuang setiap kali ada data yang disimpan atau dihapus.
     */
    protected static function booted(): void
    {
        static::saved(static fn () => \App\Support\BadgeAdmin::lupakan());
        static::deleted(static fn () => \App\Support\BadgeAdmin::lupakan());
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'jumlah' => 'decimal:2',
            'waktu_bayar' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Pemesanan, $this>
     */
    public function pemesanan(): BelongsTo
    {
        return $this->belongsTo(Pemesanan::class);
    }
}
