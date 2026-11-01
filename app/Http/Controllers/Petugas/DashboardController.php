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

        // 1. Jadwal Ambil Hari Ini
        $ambilHariIni = Pemesanan::with('pelanggan')
            ->whereDate('tanggal_mulai', $hariIni)
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal_mulai')
            ->get();

        // 2. Jadwal Kembali Hari Ini
        $kembaliHariIni = Pemesanan::with('pelanggan')
            ->whereDate('tanggal_selesai', $hariIni)
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal_selesai')
            ->get();

        // 3. Alat Rusak
        $alatRusak = Alat::where('stok_rusak', '>', 0)
            ->orderByDesc('stok_rusak')
            ->get();

        // Ringkasan untuk 3 kotak metrik di atas
        $ringkasan = [
            'total_ambil' => $ambilHariIni->count(),
            'total_kembali' => $kembaliHariIni->count(),
            'sedang_disewa' => Pemesanan::where('status', 'sedang_berlangsung')->count(),
            'total_rusak' => Alat::sum('stok_rusak'),
        ];

        return view('petugas.dashboard', compact(
            'ambilHariIni',
            'kembaliHariIni',
            'alatRusak',
            'ringkasan'
        ));
    }
}