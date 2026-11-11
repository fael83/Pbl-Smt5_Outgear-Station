<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use App\Models\Pembayaran;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class LaporanController extends Controller
{
    /**
     * Query utama agar filter dipakai bersama
     * oleh halaman laporan dan fitur ekspor.
     */
    private function queryLaporan(Request $request): Builder
    {
        $query = Pemesanan::with(['pelanggan', 'pembayaran']);

        if ($request->filled('tanggal_mulai')) {
            $query->whereDate(
                'tanggal_mulai',
                '>=',
                $request->tanggal_mulai
            );
        }

        if ($request->filled('tanggal_selesai')) {
            $query->whereDate(
                'tanggal_mulai',
                '<=',
                $request->tanggal_selesai
            );
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        return $query;
    }

    /**
     * Membuat ringkasan laporan.
     */
    private function ringkasanLaporan(Builder $query): array
    {
        return [
            'total_pemesanan' => (clone $query)->count(),

            'pemesanan_selesai' => (clone $query)
                ->where('status', 'selesai')
                ->count(),

            'menunggu_pembayaran' => (clone $query)
                ->where('status', 'menunggu_pembayaran')
                ->count(),

            'total_pendapatan' => Pembayaran::where(
                'status_transaksi',
                'berhasil'
            )
                ->whereIn(
                    'pemesanan_id',
                    (clone $query)->select('id')
                )
                ->sum('jumlah'),
        ];
    }

    /**
     * Halaman laporan Owner.
     */
    public function index(Request $request)
    {
        $query = $this->queryLaporan($request);

        $ringkasan = $this->ringkasanLaporan($query);

        $pemesanan = (clone $query)
            ->orderByDesc('tanggal_mulai')
            ->paginate(10)
            ->withQueryString();

        // Semua data sesuai filter untuk grafik.
        $dataGrafik = (clone $query)
            ->orderBy('tanggal_mulai')
            ->get();

        // Grafik jumlah pemesanan per bulan.
        $kelompokBulan = $dataGrafik->groupBy(
            fn ($item) => $item->tanggal_mulai?->format('Y-m')
        )->filter(fn ($items, $bulan) => $bulan !== null);

        $grafikBulanan = [
            'labels' => $kelompokBulan->keys()
                ->map(fn ($bulan) => Carbon::createFromFormat(
                    'Y-m',
                    $bulan
                )->format('m/Y'))
                ->values(),

            'data' => $kelompokBulan
                ->map(fn ($items) => $items->count())
                ->values(),
        ];

        // Grafik status pemesanan.
        $namaStatus = [
            'menunggu_pembayaran' => 'Menunggu Pembayaran',
            'selesai' => 'Selesai',
            'dibatalkan' => 'Dibatalkan',
            'diproses' => 'Diproses',
        ];

        $kelompokStatus = $dataGrafik->groupBy('status');

        $grafikStatus = [
            'labels' => $kelompokStatus->keys()
                ->map(fn ($status) => $namaStatus[$status]
                    ?? ucfirst(str_replace('_', ' ', $status ?? '-')))
                ->values(),

            'data' => $kelompokStatus
                ->map(fn ($items) => $items->count())
                ->values(),
        ];

        return view('owner.laporan', compact(
            'pemesanan',
            'ringkasan',
            'grafikBulanan',
            'grafikStatus'
        ));
    }

    /**
     * Ekspor laporan ke CSV.
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

            // BOM agar karakter UTF-8 terbaca baik di Excel.
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
     * Ekspor laporan ke PDF.
     */
    public function exportPdf(Request $request)
    {
        $query = $this->queryLaporan($request);

        $ringkasan = $this->ringkasanLaporan($query);

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
}
