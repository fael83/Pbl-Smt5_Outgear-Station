<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\View\View;
use Illuminate\Support\Facades\Cache;

/**
 * Halaman "Data Pemesanan" di panel admin.
 *
 * Semua angka statistik dan baris tabel dibaca langsung dari database
 * (tabel pemesanan, users, detail_pemesanan, alat). Tidak ada data dummy.
 */
class PemesananController extends Controller
{
    /** Jumlah baris per halaman. */
    private const PER_HALAMAN = 10;

    /**
     * Status di database => label yang ditampilkan di filter dan badge.
     * Urutan di sini menjadi urutan di dropdown filter.
     */
    public const LABEL_STATUS = [
        'menunggu_pembayaran' => 'Menunggu Bayar',
        'dikonfirmasi' => 'Dikonfirmasi',
        'berlangsung' => 'Berlangsung',
        'selesai' => 'Selesai',
        'dibatalkan' => 'Dibatalkan',
    ];

    public function index(Request $request): View
    {
        $request->validate([
            'status' => ['nullable', 'string'],
            'q' => ['nullable', 'string', 'max:100'],
        ]);

        // Filter status hanya diterima kalau cocok dengan status yang valid.
        $status = $request->query('status');
        if (! array_key_exists((string) $status, self::LABEL_STATUS)) {
            $status = null;
        }

        $kataKunci = trim((string) $request->query('q', ''));

        $pemesanan = Pemesanan::query()
            ->with([
                'pelanggan:id,name,no_telepon',
                'detailPemesanan:id,pemesanan_id,alat_id,jumlah',
                'detailPemesanan.alat:id,nama',
            ])
            ->when($status, fn ($query) => $query->where('status', $status))
            ->when($kataKunci !== '', function ($query) use ($kataKunci) {
                // Satu grup where supaya OR di bawah tidak membocorkan filter status.
                $query->where(function ($grup) use ($kataKunci) {
                    $grup->whereLike('kode_pemesanan', "%{$kataKunci}%")
                        ->orWhereHas('pelanggan', function ($pelanggan) use ($kataKunci) {
                            $pelanggan->whereLike('name', "%{$kataKunci}%")
                                ->orWhereLike('no_telepon', "%{$kataKunci}%");
                        })
                        ->orWhereHas('detailPemesanan.alat', function ($alat) use ($kataKunci) {
                            $alat->whereLike('nama', "%{$kataKunci}%");
                        });
                });
            })
            ->orderByDesc('created_at')
            ->orderByDesc('id')
            ->paginate(self::PER_HALAMAN)
            ->withQueryString();

        return view('admin.pemesanan', [
            'pemesanan' => $pemesanan,
            'statistik' => $this->statistik(),
            'labelStatus' => self::LABEL_STATUS,
            'statusAktif' => $status,
            'kataKunci' => $kataKunci,
        ]);
    }

    /**
     * Angka untuk 4 kartu statistik di bagian atas halaman.
     *
     * @return array<string, int>
     */
    private function statistik(): array
    {
        // Di-cache 1 menit: sebelumnya 4 query ke Supabase di setiap buka halaman.
        return Cache::remember('pemesanan.statistik', now()->addMinute(), function (): array {
            $bulanIni = Carbon::now();
            $bulanLalu = Carbon::now()->subMonthNoOverflow();

            $hasil = Pemesanan::query()
                ->selectRaw("COALESCE(SUM(CASE WHEN status = 'menunggu_pembayaran' THEN 1 ELSE 0 END), 0) AS menunggu")
                ->selectRaw("COALESCE(SUM(CASE WHEN status = 'dikonfirmasi' THEN 1 ELSE 0 END), 0) AS dikonfirmasi")
                ->selectRaw("COALESCE(SUM(CASE WHEN status = 'berlangsung' THEN 1 ELSE 0 END), 0) AS berlangsung")
                ->selectRaw("COALESCE(SUM(CASE WHEN status = 'selesai' AND tanggal_selesai >= ? AND tanggal_selesai < ? THEN 1 ELSE 0 END), 0) AS selesai_ini", [
                    $bulanIni->copy()->startOfMonth(), $bulanIni->copy()->startOfMonth()->addMonth(),
                ])
                ->selectRaw("COALESCE(SUM(CASE WHEN status = 'selesai' AND tanggal_selesai >= ? AND tanggal_selesai < ? THEN 1 ELSE 0 END), 0) AS selesai_lalu", [
                    $bulanLalu->copy()->startOfMonth(), $bulanLalu->copy()->startOfMonth()->addMonth(),
                ])
                ->toBase()
                ->first();

            $menunggu = (int) $hasil->menunggu;
            $dikonfirmasi = (int) $hasil->dikonfirmasi;
            $berlangsung = (int) $hasil->berlangsung;

            return [
                'aktif' => $menunggu + $dikonfirmasi + $berlangsung,
                'menunggu_pembayaran' => $menunggu,
                'berlangsung' => $berlangsung,
                'selesai_bulan_ini' => (int) $hasil->selesai_ini,
                'selisih_selesai' => (int) $hasil->selesai_ini - (int) $hasil->selesai_lalu,
            ];
        });
    }
}
