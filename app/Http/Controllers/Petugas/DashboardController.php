<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Pemesanan;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hariIni = now()->toDateString();

        // Pesanan yang tanggal mulai atau tanggal selesainya hari ini.
        $pesananHariIni = Pemesanan::with('pelanggan')
            ->where(function ($query) use ($hariIni) {
                $query->whereDate('tanggal_mulai', $hariIni)
                    ->orWhereDate('tanggal_selesai', $hariIni);
            })
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal_mulai')
            ->get();

        // Alat yang memiliki stok rusak.
        $alatRusak = Alat::where('stok_rusak', '>', 0)
            ->orderByDesc('stok_rusak')
            ->get();

        return view('petugas.dashboard', compact(
            'pesananHariIni',
            'alatRusak'
        ));
    }
}
