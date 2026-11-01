<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Alat;
use App\Models\Pemesanan;
use App\Models\Pembayaran;

class DashboardController extends Controller
{
    public function index()
    {
        // Ringkasan inventaris dan pemesanan
        $ringkasan = [
            'total_alat' => Alat::count(),
            'total_stok' => Alat::sum('stok_total'),
            'stok_tersedia' => Alat::sum('stok_tersedia'),
            'stok_disewa' => Alat::sum('stok_disewa'),
            'stok_rusak' => Alat::sum('stok_rusak'),

            // Ringkasan pemesanan
            'total_pemesanan' => Pemesanan::count(),

            'pemesanan_selesai' => Pemesanan::where(
                'status',
                'selesai'
            )->count(),

            'menunggu_pembayaran' => Pemesanan::where(
                'status',
                'menunggu_pembayaran'
            )->count(),

            // Total nominal seluruh pembayaran berhasil
            'total_pendapatan' => Pembayaran::where(
                'status_transaksi',
                'berhasil'
            )->sum('jumlah'),
        ];

        // Lima pemesanan terbaru
        $pemesananTerbaru = Pemesanan::with('pelanggan')
            ->latest()
            ->limit(5)
            ->get()
            ->map(function ($item) {
                return [
                    'kode' => $item->kode_pemesanan,
                    'pelanggan' => $item->pelanggan?->name ?? '-',
                    'tanggal' => $item->tanggal_mulai
                        ? $item->tanggal_mulai->format('d M Y')
                        : '-',
                    'total' => $item->total_biaya,
                    'status' => $item->status,
                ];
            });

        return view('owner.dashboard', compact(
            'ringkasan',
            'pemesananTerbaru'
        ));
    }
}