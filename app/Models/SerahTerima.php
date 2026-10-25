<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SerahTerima extends Model
{
    protected $table = 'serah_terima';

    protected $fillable = [
        'pemesanan_id',
        'user_id',
        'waktu_serah_terima',
        'nama_ktp',
        'nomor_ktp',
        'foto_ktp',
        'status_serah_terima',
    ];

    public function pemesanan()
    {
        return $this->belongsTo(Pemesanan::class, 'pemesanan_id');
    }

    public function petugas()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}