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
        $perStatus = Pemesanan::query()
            ->selectRaw('status, count(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $menunggu = (int) ($perStatus['menunggu_pembayaran'] ?? 0);
        $dikonfirmasi = (int) ($perStatus['dikonfirmasi'] ?? 0);
        $berlangsung = (int) ($perStatus['berlangsung'] ?? 0);

        $bulanIni = Carbon::now();
        $bulanLalu = Carbon::now()->subMonthNoOverflow();

        $selesaiBulanIni = $this->hitungSelesaiPadaBulan($bulanIni);
        $selesaiBulanLalu = $this->hitungSelesaiPadaBulan($bulanLalu);

        return [
            // "Aktif" = semua pesanan yang belum selesai atau dibatalkan.
            'aktif' => $menunggu + $dikonfirmasi + $berlangsung,
            'menunggu_pembayaran' => $menunggu,
            'berlangsung' => $berlangsung,
            'selesai_bulan_ini' => $selesaiBulanIni,
            'selisih_selesai' => $selesaiBulanIni - $selesaiBulanLalu,
        ];
    }

    /** Jumlah pemesanan berstatus selesai yang tanggal_selesai-nya jatuh di bulan tersebut. */
    private function hitungSelesaiPadaBulan(Carbon $bulan): int
    {
        return Pemesanan::query()
            ->where('status', 'selesai')
            ->whereYear('tanggal_selesai', $bulan->year)
            ->whereMonth('tanggal_selesai', $bulan->month)
            ->count();
    }
}
