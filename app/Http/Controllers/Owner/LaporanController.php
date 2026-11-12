<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use App\Models\Denda;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | LAPORAN PENYEWAAN
    |--------------------------------------------------------------------------
    */

    /**
     * Query utama laporan penyewaan.
     * Filter tanggal menggunakan waktu_bayar pada tabel pembayaran.
     */
    private function queryLaporan(Request $request): Builder
    {
        // Filter tanggal pembayaran untuk eager loading
        // dan pencarian pemesanan.
        $filterPembayaran = function ($q) use ($request) {
            if ($request->filled('tanggal_mulai')) {
                $q->whereDate(
                    'waktu_bayar',
                    '>=',
                    $request->tanggal_mulai
                );
            }

            if ($request->filled('tanggal_selesai')) {
                $q->whereDate(
                    'waktu_bayar',
                    '<=',
                    $request->tanggal_selesai
                );
            }
        };

        // Mengambil pemesanan beserta pelanggan dan pembayaran.
        // Pembayaran mengikuti periode yang dipilih.
        $query = Pemesanan::with([
            'pelanggan',
            'pembayaran' => $filterPembayaran,
        ]);

        // Jika tanggal difilter, pemesanan harus memiliki
        // pembayaran dalam periode yang sama.
        if (
            $request->filled('tanggal_mulai') ||
            $request->filled('tanggal_selesai')
        ) {
            $query->whereHas(
                'pembayaran',
                $filterPembayaran
            );
        }

        // Filter berdasarkan status pemesanan.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query;
    }

    /**
     * Menghitung ringkasan laporan penyewaan.
     */
    private function ringkasanLaporan(
        Builder $query,
        Request $request
    ): array {
        // Mengambil pembayaran berhasil dari pemesanan
        // yang sudah sesuai dengan filter laporan.
        $queryPendapatan = Pembayaran::where(
            'status_transaksi',
            'berhasil'
        )->whereIn(
            'pemesanan_id',
            (clone $query)->select('id')
        );

        // Filter tanggal pembayaran.
        if ($request->filled('tanggal_mulai')) {
            $queryPendapatan->whereDate(
                'waktu_bayar',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_selesai')) {
            $queryPendapatan->whereDate(
                'waktu_bayar',
                '<=',
                $request->tanggal_selesai
            );
        }

        return [
            'total_pemesanan' => (clone $query)->count(),

            'pemesanan_selesai' => (clone $query)
                ->where('status', 'selesai')
                ->count(),

            'menunggu_pembayaran' => (clone $query)
                ->where('status', 'menunggu_pembayaran')
                ->count(),

            'total_pendapatan' => $queryPendapatan->sum('jumlah'),
        ];
    }

    /**
     * Halaman utama laporan penyewaan.
     */
    public function index(Request $request)
    {
        $query = $this->queryLaporan($request);

        $ringkasan = $this->ringkasanLaporan(
            $query,
            $request
        );

        // Data tabel dengan pagination.
        $pemesanan = (clone $query)
            ->orderByDesc('tanggal_mulai')
            ->paginate(10)
            ->withQueryString();

        // Data untuk grafik.
        $dataGrafik = (clone $query)
            ->orderBy('tanggal_mulai')
            ->get();

        // Grafik pemesanan per bulan berdasarkan tanggal mulai sewa.
        $kelompokBulan = $dataGrafik->groupBy(
            fn($item) => $item->tanggal_mulai?->format('Y-m')
        )->filter(
            fn($items, $bulan) => $bulan !== null
        );

        $grafikBulanan = [
            'labels' => $kelompokBulan->keys()
                ->map(fn($bulan) => Carbon::createFromFormat(
                    'Y-m',
                    $bulan
                )->format('m/Y'))
                ->values(),

            'data' => $kelompokBulan
                ->map(fn($items) => $items->count())
                ->values(),
        ];

        // Status pemesanan yang ditampilkan pada grafik.
        $namaStatus = [
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'dikonfirmasi' => 'Dikonfirmasi',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
        ];

        // Grafik selalu menampilkan seluruh status,
        // termasuk status yang jumlah pemesanannya masih 0.
        $grafikStatus = [
            'labels' => array_values($namaStatus),

            'data' => collect(array_keys($namaStatus))
                ->map(fn($status) => $dataGrafik
                    ->where('status', $status)
                    ->count())
                ->values(),
        ];
        // Respons AJAX untuk memperbarui ringkasan, tabel, dan grafik.
        if ($request->ajax()) {
            return response()->json([
                'ringkasan_html' => view(
                    'owner.partials.laporan-ringkasan',
                    compact('ringkasan')
                )->render(),

                'tabel_html' => view(
                    'owner.partials.laporan-tabel',
                    compact('pemesanan')
                )->render(),

                'grafikBulanan' => $grafikBulanan,
                'grafikStatus' => $grafikStatus,
            ]);
        }

        return view('owner.laporan', compact(
            'pemesanan',
            'ringkasan',
            'grafikBulanan',
            'grafikStatus'
        ));
    }

    /**
     * Ekspor laporan penyewaan ke CSV.
     */
    public function exportCsv(Request $request)
    {
        $data = $this->queryLaporan($request)
            ->orderByDesc('tanggal_mulai')
            ->get();

        $namaFile = 'laporan-penyewaan-'
            . now()->format('Ymd-His')
            . '.csv';

        return response()->streamDownload(function () use ($data) {
            $file = fopen('php://output', 'w');

            // BOM agar karakter UTF-8 terbaca dengan baik di Excel.
            fwrite($file, "\xEF\xBB\xBF");

            fputcsv($file, [
                'Kode Pemesanan',
                'Pelanggan',
                'Tanggal Mulai',
                'Tanggal Selesai',
                'Total Biaya',
                'Status Pemesanan',
                'Status Pembayaran',
            ]);

            foreach ($data as $item) {
                $pembayaranBerhasil = $item->pembayaran
                    ->contains('status_transaksi', 'berhasil');

                fputcsv($file, [
                    $item->kode_pemesanan,
                    $item->pelanggan?->name ?? '-',
                    $item->tanggal_mulai?->format('Y-m-d') ?? '-',
                    $item->tanggal_selesai?->format('Y-m-d') ?? '-',
                    $item->total_biaya ?? 0,
                    $item->status ?? '-',
                    $pembayaranBerhasil ? 'Berhasil' : 'Belum Berhasil',
                ]);
            }

            fclose($file);
        }, $namaFile, [
            'Content-Type' => 'text/csv; charset=UTF-8',
        ]);
    }

    /**
     * Ekspor laporan penyewaan ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = $this->queryLaporan($request);

        $ringkasan = $this->ringkasanLaporan(
            $query,
            $request
        );

        $pemesanan = (clone $query)
            ->orderByDesc('tanggal_mulai')
            ->get();

        $pdf = Pdf::loadView('owner.laporan-pdf', compact(
            'ringkasan',
            'pemesanan'
        ))->setPaper('a4', 'landscape');

        return $pdf->download(
            'laporan-penyewaan-' . now()->format('Ymd-His') . '.pdf'
        );
    }


    /*
    |--------------------------------------------------------------------------
    | LAPORAN TREN PENDAPATAN BULANAN
    |--------------------------------------------------------------------------
    */

    private function queryPendapatan(Request $request)
    {
        $query = Pembayaran::with('pemesanan')
            ->where('status_transaksi', 'berhasil')
            ->whereNotNull('waktu_bayar');

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'waktu_bayar',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(
                'waktu_bayar',
                '<=',
                $request->tanggal_selesai
            );
        }

        return $query;
    }

    public function trenPendapatan(Request $request)
    {
        $dataPembayaran = $this->queryPendapatan($request)
            ->orderBy('waktu_bayar')
            ->get();

        $kelompokBulan = $dataPembayaran->groupBy(
            fn($item) => $item->waktu_bayar?->format('Y-m')
        )->filter(
            fn($items, $bulan) => $bulan !== null
        );

        $grafikPendapatan = [
            'labels' => $kelompokBulan->keys()
                ->map(fn($bulan) => Carbon::createFromFormat(
                    'Y-m',
                    $bulan
                )->format('M Y'))
                ->values(),

            'data' => $kelompokBulan
                ->map(fn($items) => (float) $items->sum('jumlah'))
                ->values(),
        ];

        $ringkasan = [
            'total_pendapatan' => $dataPembayaran->sum('jumlah'),

            'total_transaksi' => $dataPembayaran->count(),

            'rata_rata_transaksi' => $dataPembayaran->count() > 0
                ? $dataPembayaran->avg('jumlah')
                : 0,
        ];

        $pendapatanBulanan = $kelompokBulan->map(
            function ($items, $bulan) {
                return [
                    'bulan' => Carbon::createFromFormat(
                        'Y-m',
                        $bulan
                    )->translatedFormat('F Y'),

                    'jumlah_transaksi' => $items->count(),

                    'total_pendapatan' => $items->sum('jumlah'),
                ];
            }
        )->values();

        return view('owner.laporan-pendapatan', compact(
            'ringkasan',
            'grafikPendapatan',
            'pendapatanBulanan'
        ));
    }


    /*
|--------------------------------------------------------------------------
| LAPORAN REKAP DENDA
|--------------------------------------------------------------------------
*/

public function rekapDenda(Request $request)
{
    // Query utama data transaksi denda.
    $query = Denda::with(['pemesanan.pelanggan']);

    // Filter berdasarkan tanggal mulai penyewaan.
    if ($request->filled('tanggal_mulai')) {
        $query->whereHas('pemesanan', function ($q) use ($request) {
            $q->whereDate(
                'tanggal_mulai',
                '>=',
                $request->tanggal_mulai
            );
        });
    }

    // Filter berdasarkan tanggal selesai penyewaan.
    if ($request->filled('tanggal_selesai')) {
        $query->whereHas('pemesanan', function ($q) use ($request) {
            $q->whereDate(
                'tanggal_mulai',
                '<=',
                $request->tanggal_selesai
            );
        });
    }

    // Filter berdasarkan status pembayaran denda.
    if ($request->filled('status')) {
        $query->where('status', $request->status);
    }

    // Filter berdasarkan jenis denda.
    if ($request->filled('jenis_denda')) {
        $query->where('jenis_denda', $request->jenis_denda);
    }

    // Data denda untuk tabel dengan pagination.
    $dataDenda = (clone $query)
        ->latest()
        ->paginate(10)
        ->withQueryString();

    // Mengambil seluruh data sesuai filter untuk ringkasan dan grafik.
    $semuaDenda = (clone $query)->get();

    // Ringkasan laporan denda.
    $ringkasan = [
        'total_denda' => $semuaDenda->sum('jumlah_denda'),

        'jumlah_data' => $semuaDenda->count(),

        'belum_dibayar' => $semuaDenda
            ->where('status', 'belum_dibayar')
            ->sum('jumlah_denda'),

        'sudah_dibayar' => $semuaDenda
            ->where('status', 'dibayar')
            ->sum('jumlah_denda'),
    ];

    // Mengambil daftar jenis denda dari tabel aturan_denda.
    // Dengan demikian, semua jenis tetap tersedia meskipun
    // belum memiliki transaksi denda.
    $jenisDenda = \Illuminate\Support\Facades\DB::table('aturan_denda')
        ->orderBy('id')
        ->pluck('jenis_denda');

    // Grafik berdasarkan seluruh jenis aturan denda.
    // Jenis yang belum memiliki transaksi akan bernilai 0.
    $grafikJenis = [
        'labels' => $jenisDenda
            ->map(fn($jenis) => ucfirst(
                str_replace('_', ' ', $jenis)
            ))
            ->values(),

        'data' => $jenisDenda
            ->map(fn($jenis) => (float) $semuaDenda
                ->where('jenis_denda', $jenis)
                ->sum('jumlah_denda'))
            ->values(),
    ];

    // Menampilkan halaman laporan rekap denda.
    return view('owner.laporan-denda', compact(
        'dataDenda',
        'ringkasan',
        'grafikJenis',
        'jenisDenda'
    ));
}
}