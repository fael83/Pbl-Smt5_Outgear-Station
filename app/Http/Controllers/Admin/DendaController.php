<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman pengelolaan denda pada panel admin.
 */
class DendaController extends Controller
{
    public function __invoke(Request $request): View
    {
        $barisPerHalaman = $request->integer('per_page', 10);
        $barisPerHalaman = in_array($barisPerHalaman, [10, 25, 50], true) ? $barisPerHalaman : 10;
        $status = $request->query('status', 'semua');
        $status = in_array($status, ['belum_dibayar', 'lunas'], true) ? $status : 'semua';
        $cari = trim((string) $request->query('search', ''));

        $query = Denda::query()->with([
            'pengembalian',
            'pemesanan',
            'detailPengembalian.detailPemesanan.alat',
        ]);

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        if ($cari !== '') {
            $query->where(function ($query) use ($cari): void {
                $query->where('id', 'like', "%{$cari}%")
                    ->orWhere('pengembalian_id', 'like', "%{$cari}%")
                    ->orWhere('pemesanan_id', 'like', "%{$cari}%")
                    ->orWhere('detail_pengembalian_id', 'like', "%{$cari}%");
            });
        }

        return view('admin.denda', [
            'denda' => $query->latest()->paginate($barisPerHalaman)->withQueryString(),
            'filters' => [
                'search' => $cari,
                'status' => $status,
                'per_page' => $barisPerHalaman,
            ],
            'jumlahBelumDibayar' => Denda::query()->where('status', 'belum_dibayar')->count(),
            'totalKeterlambatan' => Denda::query()
                ->where('status', 'belum_dibayar')
                ->where('jenis_denda', 'keterlambatan')
                ->sum('jumlah_denda'),
            'totalKerusakan' => Denda::query()
                ->where('status', 'belum_dibayar')
                ->whereIn('jenis_denda', ['kerusakan_ringan', 'kerusakan_berat', 'kehilangan'])
                ->sum('jumlah_denda'),
            'totalLunas' => Denda::query()->where('status', 'lunas')->sum('jumlah_denda'),
        ]);
    }
}
