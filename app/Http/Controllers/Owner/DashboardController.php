<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\DetailPengembalian;
use App\Models\Denda;
use Illuminate\Support\Facades\DB;

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
            'total_pemesanan' => Pemesanan::count(),
            'pemesanan_selesai' => Pemesanan::where('status', 'selesai')->count(),
            'menunggu_pembayaran' => Pemesanan::where('status', 'menunggu_pembayaran')->count(),
            'total_pendapatan' => Pembayaran::where('status_transaksi', 'berhasil')->sum('jumlah'),
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

        // Lima alat paling sering disewa berdasarkan total kuantitas
        $alatPalingSeringDisewa = DB::table('detail_pemesanan')
            ->join('pemesanan', 'detail_pemesanan.pemesanan_id', '=', 'pemesanan.id')
            ->join('alat', 'detail_pemesanan.alat_id', '=', 'alat.id')
            ->where('pemesanan.status', 'selesai')
            ->select(
                'alat.id',
                'alat.nama',
                DB::raw('SUM(detail_pemesanan.jumlah) as total_disewa')
            )
            ->groupBy('alat.id', 'alat.nama')
            ->orderByDesc('total_disewa')
            ->limit(5)
            ->get();

        /*
        |--------------------------------------------------------------------------
        | DATA PERLU PERHATIAN
        |--------------------------------------------------------------------------
        */

        // 1. Pemesanan yang menunggu pembayaran
        $perluPembayaran = Pemesanan::with('pelanggan')
            ->where('status', 'menunggu_pembayaran')
            ->latest()
            ->get();

        // 2. Pemesanan terlambat dan belum memiliki data pengembalian
        $terlambat = DB::table('pemesanan')
            ->leftJoin('pengembalian', 'pemesanan.id', '=', 'pengembalian.pemesanan_id')
            ->whereDate('pemesanan.tanggal_selesai', '<', now()->toDateString())
            ->whereNull('pengembalian.id')
            ->select(
                'pemesanan.id',
                'pemesanan.kode_pemesanan',
                'pemesanan.tanggal_selesai'
            )
            ->orderBy('pemesanan.tanggal_selesai')
            ->get();

        // 3. Denda yang belum dibayar
        $dendaBelumDibayar = Denda::with([
            'pemesanan',
            'detailPengembalian.detailPemesanan.alat',
        ])
            ->where('status', 'belum_dibayar')
            ->latest()
            ->get();

        // 4. Detail barang rusak atau hilang
        $barangBermasalah = DetailPengembalian::with([
            'pengembalian.pemesanan',
            'detailPemesanan.alat',
        ])
            ->where(function ($query) {
                $query->where('jumlah_rusak', '>', 0)
                    ->orWhere('jumlah_hilang', '>', 0);
            })
            ->latest()
            ->get();

        $perhatian = [
            'perlu_pembayaran' => $perluPembayaran,
            'terlambat' => $terlambat,
            'denda_belum_dibayar' => $dendaBelumDibayar,
            'barang_bermasalah' => $barangBermasalah,
        ];

        return view('owner.dashboard', compact(
            'ringkasan',
            'pemesananTerbaru',
            'perhatian',
            'alatPalingSeringDisewa'
        ));
    }
}
