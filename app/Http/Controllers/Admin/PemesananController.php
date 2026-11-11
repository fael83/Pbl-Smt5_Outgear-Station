<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pemesanan;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

/**
 * Halaman "Data Pemesanan" di panel admin.
 *
 * Semua angka statistik dan baris tabel dibaca langsung dari database
 * (tabel pemesanan, users, detail_pemesanan, alat). Tidak ada data dummy.
 */
class PemesananController extends Controller
{
    /** Jumlah baris per halaman (bawaan). */
    private const PER_HALAMAN = 10;

    /** Pilihan jumlah baris per halaman yang diterima dari query string. */
    private const PILIHAN_PER_HALAMAN = [10, 25, 50];

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

    /**
     * Render penuh halaman: kartu statistik + kartu tabel.
     */
    public function index(Request $request): View
    {
        $filters = $this->filter($request);

        return view('admin.pemesanan.index', [
            'pemesanan' => $this->daftarPemesanan($filters),
            'statistik' => $this->statistik(),
            'labelStatus' => self::LABEL_STATUS,
            'statusAktif' => $filters['status'],
            'kataKunci' => $filters['search'],
            'perPage' => $filters['per_page'],
        ]);
    }

    /**
     * Endpoint JSON untuk pencarian, filter, dan pagination tanpa memuat ulang
     * halaman. Hanya kartu tabel yang dikirim balik; kartu statistik tidak
     * bergantung pada filter sehingga tidak dihitung ulang (hemat query).
     */
    public function tabel(Request $request): JsonResponse
    {
        $filters = $this->filter($request);

        return response()->json([
            'html' => view('admin.pemesanan._tabel', [
                'pemesanan' => $this->daftarPemesanan($filters),
                'labelStatus' => self::LABEL_STATUS,
                'statusAktif' => $filters['status'],
                'kataKunci' => $filters['search'],
                'perPage' => $filters['per_page'],
            ])->render(),
            'url' => route('admin.pemesanan', $this->parameterQuery($filters, true)),
        ]);
    }

    /**
     * Baca dan bersihkan filter dari query string.
     *
     * @return array{status: ?string, search: string, per_page: int, page: int}
     */
    private function filter(Request $request): array
    {
        $request->validate([
            'status' => ['nullable', 'string'],
            'search' => ['nullable', 'string', 'max:100'],
            'q' => ['nullable', 'string', 'max:100'],
            'per_page' => ['nullable', 'integer'],
            'page' => ['nullable', 'integer', 'min:1'],
        ]);

        // Filter status hanya diterima kalau cocok dengan status yang valid.
        $status = $request->query('status');
        if (! array_key_exists((string) $status, self::LABEL_STATUS)) {
            $status = null;
        }

        $perPage = (int) $request->query('per_page', self::PER_HALAMAN);
        if (! in_array($perPage, self::PILIHAN_PER_HALAMAN, true)) {
            $perPage = self::PER_HALAMAN;
        }

        return [
            'status' => $status,
            // `q` dipertahankan supaya tautan lama (?q=...) tetap berfungsi.
            'search' => trim((string) ($request->query('search') ?? $request->query('q', ''))),
            'per_page' => $perPage,
            'page' => max(1, (int) $request->query('page', 1)),
        ];
    }

    /**
     * Satu halaman data pemesanan sesuai filter.
     *
     * @param  array{status: ?string, search: string, per_page: int, page: int}  $filters
     * @return LengthAwarePaginator<int, Pemesanan>
     */
    private function daftarPemesanan(array $filters): LengthAwarePaginator
    {
        $status = $filters['status'];
        $kataKunci = $filters['search'];

        return Pemesanan::query()
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
            // Tautan pagination mengarah ke halaman penuh (bukan endpoint JSON).
            ->paginate($filters['per_page'], ['*'], 'page', $filters['page'])
            ->withPath(route('admin.pemesanan'))
            ->appends($this->parameterQuery($filters));
    }

    /**
     * Parameter query untuk URL halaman (hanya yang berbeda dari bawaan).
     *
     * @param  array{status: ?string, search: string, per_page: int, page: int}  $filters
     * @return array<string, int|string>
     */
    private function parameterQuery(array $filters, bool $denganHalaman = false): array
    {
        $parameter = array_filter([
            'status' => $filters['status'],
            'search' => $filters['search'],
        ], static fn ($nilai): bool => filled($nilai));

        if ($filters['per_page'] !== self::PER_HALAMAN) {
            $parameter['per_page'] = $filters['per_page'];
        }

        if ($denganHalaman && $filters['page'] > 1) {
            $parameter['page'] = $filters['page'];
        }

        return $parameter;
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
