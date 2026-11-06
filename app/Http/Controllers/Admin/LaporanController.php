<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use App\Models\Pemesanan;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

/**
 * Halaman laporan operasional pada panel admin.
 *
 * Semua angka dan baris dibaca dari database sesuai periode yang dipilih:
 *
 * - render penuh  -> GET /admin/laporan (view + partial tabel);
 * - interaksi JS  -> GET /admin/laporan/tabel (JSON berisi HTML tabel saja).
 *
 * Kartu ringkasan hanya dihitung ulang saat periode berubah atau tombol
 * "Muat ulang" ditekan; berganti tab, mencari, dan pindah halaman tidak
 * menyentuh angka ringkasan (hemat round-trip ke database).
 */
class LaporanController extends Controller
{
    /** @var array<int, int> */
    private const PER_PAGE = [10, 25, 50];

    private const TIPE = ['pendapatan', 'penyewaan', 'denda'];

    /** Periode bawaan: 30 hari terakhir (termasuk hari ini). */
    private const HARI_BAWAAN = 30;

    public function index(Request $request): View
    {
        $filters = $this->filter($request);

        return view('admin.laporan', [
            'data' => $this->dataTabel($filters),
            'filters' => $filters,
            'bawaan' => $this->periodeBawaan(),
            'ringkasan' => $this->ringkasan($filters),
        ]);
    }

    /**
     * Endpoint JSON untuk berganti tab, pencarian, periode, dan pagination
     * tanpa memuat ulang halaman.
     */
    public function tabel(Request $request): JsonResponse
    {
        $filters = $this->filter($request);

        $balasan = [
            'html' => view('admin.laporan._tabel', [
                'data' => $this->dataTabel($filters),
                'filters' => $filters,
            ])->render(),
            'url' => route('admin.laporan.index', $this->parameterQuery($filters, true)),
        ];

        if ($request->boolean('segarkan') || $request->boolean('ringkasan')) {
            $balasan['ringkasan'] = $this->ringkasan($filters);
        }

        return response()->json($balasan);
    }

    public function exportExcel(Request $request)
    {
        // Logika export Excel...
    }

    public function cetakPdf(Request $request)
    {
        // Logika cetak PDF...
    }

    /**
     * @return array{dari: string, sampai: string}
     */
    private function periodeBawaan(): array
    {
        return [
            'dari' => now()->subDays(self::HARI_BAWAAN - 1)->toDateString(),
            'sampai' => now()->toDateString(),
        ];
    }

    /**
     * Baca dan bersihkan filter dari query string.
     *
     * @return array{search: string, tipe: string, dari: string, sampai: string, per_page: int, page: int}
     */
    private function filter(Request $request): array
    {
        $bawaan = $this->periodeBawaan();

        $perPage = (int) $request->query('per_page', self::PER_PAGE[0]);

        if (! in_array($perPage, self::PER_PAGE, true)) {
            $perPage = self::PER_PAGE[0];
        }

        $tipe = (string) $request->query('tipe', self::TIPE[0]);

        if (! in_array($tipe, self::TIPE, true)) {
            $tipe = self::TIPE[0];
        }

        $dari = $this->bacaTanggal($request->query('dari')) ?? $bawaan['dari'];
        $sampai = $this->bacaTanggal($request->query('sampai')) ?? $bawaan['sampai'];

        // Rentang terbalik ditukar saja supaya tidak menghasilkan data kosong.
        if ($dari > $sampai) {
            [$dari, $sampai] = [$sampai, $dari];
        }

        return [
            'search' => trim((string) $request->query('search', '')),
            'tipe' => $tipe,
            'dari' => $dari,
            'sampai' => $sampai,
            'per_page' => $perPage,
            'page' => max(1, (int) $request->query('page', 1)),
        ];
    }

    private function bacaTanggal(mixed $nilai): ?string
    {
        if (! is_string($nilai) || preg_match('/^\d{4}-\d{2}-\d{2}$/', $nilai) !== 1) {
            return null;
        }

        try {
            $tanggal = Carbon::createFromFormat('!Y-m-d', $nilai);
        } catch (\Throwable) {
            return null;
        }

        return $tanggal !== false && $tanggal->toDateString() === $nilai ? $nilai : null;
    }

    /**
     * Data untuk tab yang sedang aktif.
     *
     * @param  array{search: string, tipe: string, dari: string, sampai: string, per_page: int, page: int}  $filters
     */
    private function dataTabel(array $filters): LengthAwarePaginator
    {
        $paginator = match ($filters['tipe']) {
            'penyewaan' => $this->daftarPenyewaan($filters),
            'denda' => $this->daftarDenda($filters),
            default => $this->daftarPendapatan($filters),
        };

        // Tautan pagination mengarah ke halaman penuh (bukan endpoint JSON).
        return $paginator
            ->withPath(route('admin.laporan.index'))
            ->appends($this->parameterQuery($filters));
    }

    /**
     * Awal dan akhir (eksklusif) rentang waktu dari filter tanggal.
     *
     * @param  array{dari: string, sampai: string}  $filters
     * @return array{0: Carbon, 1: Carbon}
     */
    private function rentang(array $filters): array
    {
        $awal = Carbon::parse($filters['dari'])->startOfDay();
        $akhir = Carbon::parse($filters['sampai'])->startOfDay()->addDay();

        return [$awal, $akhir];
    }

    /**
     * Pendapatan per hari: pembayaran berhasil dikelompokkan per tanggal bayar.
     */
    private function daftarPendapatan(array $filters): LengthAwarePaginator
    {
        [$awal, $akhir] = $this->rentang($filters);
        $kataKunci = $filters['search'];

        return DB::table('pembayaran')
            ->selectRaw("DATE(waktu_bayar) as tanggal")
            ->selectRaw('count(*) as jumlah_transaksi')
            ->selectRaw("sum(case when jenis_pembayaran <> 'denda' then jumlah else 0 end) as pendapatan_sewa")
            ->selectRaw("sum(case when jenis_pembayaran = 'denda' then jumlah else 0 end) as pendapatan_denda")
            ->selectRaw('sum(jumlah) as total')
            ->where('status_transaksi', 'berhasil')
            ->where('waktu_bayar', '>=', $awal)
            ->where('waktu_bayar', '<', $akhir)
            ->when($kataKunci !== '', function ($query) use ($kataKunci): void {
                $query->where(function ($grup) use ($kataKunci): void {
                    $grup->whereLike('transaction_id', "%{$kataKunci}%")
                        ->orWhereIn('pemesanan_id', DB::table('pemesanan')
                            ->select('id')
                            ->whereLike('kode_pemesanan', "%{$kataKunci}%"));
                });
            })
            ->groupByRaw('DATE(waktu_bayar)')
            ->orderByDesc('tanggal')
            ->paginate($filters['per_page'], ['*'], 'page', $filters['page']);
    }

    /**
     * Histori penyewaan: pemesanan yang tanggal mulainya jatuh di periode.
     */
    private function daftarPenyewaan(array $filters): LengthAwarePaginator
    {
        $kataKunci = $filters['search'];

        return Pemesanan::query()
            ->join('users', 'users.id', '=', 'pemesanan.user_id')
            ->select('pemesanan.*', 'users.name as nama_pelanggan')
            ->whereDate('pemesanan.tanggal_mulai', '>=', $filters['dari'])
            ->whereDate('pemesanan.tanggal_mulai', '<=', $filters['sampai'])
            ->when($kataKunci !== '', function (Builder $query) use ($kataKunci): void {
                $query->where(function (Builder $grup) use ($kataKunci): void {
                    $grup->whereLike('pemesanan.kode_pemesanan', "%{$kataKunci}%")
                        ->orWhereLike('users.name', "%{$kataKunci}%");
                });
            })
            ->orderByDesc('pemesanan.tanggal_mulai')
            ->orderByDesc('pemesanan.id')
            ->paginate($filters['per_page'], ['*'], 'page', $filters['page']);
    }

    /**
     * Laporan denda: denda yang tercatat pada periode.
     */
    private function daftarDenda(array $filters): LengthAwarePaginator
    {
        [$awal, $akhir] = $this->rentang($filters);
        $search = $filters['search'];

        return Denda::query()
            ->where('created_at', '>=', $awal)
            ->where('created_at', '<', $akhir)
            ->when($search !== '', function (Builder $query) use ($search): void {
                // Jenis disimpan sebagai "kerusakan_ringan"; "kerusakan ringan" tetap ketemu.
                $pola = '%'.str_replace(' ', '_', $search).'%';

                $query->where(function (Builder $grup) use ($pola, $search): void {
                    $grup->whereLike('jenis_denda', $pola);

                    // Kolom ID bertipe angka: dicocokkan sebagai angka (PostgreSQL
                    // menolak LIKE pada bigint).
                    if (preg_match('/\d+/', $search, $cocok) === 1) {
                        $angka = (int) $cocok[0];

                        $grup->orWhere('id', $angka)
                            ->orWhere('pemesanan_id', $angka)
                            ->orWhere('pengembalian_id', $angka);
                    }
                });
            })
            ->latest()
            ->orderByDesc('id')
            ->paginate($filters['per_page'], ['*'], 'page', $filters['page']);
    }

    /**
     * Angka untuk 4 kartu ringkasan, sudah diformat.
     *
     * Semua agregat diambil dalam SATU query (sub-select) supaya hanya satu
     * round-trip ke database; ditambah satu query untuk alat terlaris.
     *
     * @param  array{dari: string, sampai: string}  $filters
     * @return array<string, int|string>
     */
    private function ringkasan(array $filters): array
    {
        [$awal, $akhir] = $this->rentang($filters);

        // Periode pembanding: rentang sama panjang tepat sebelum periode ini.
        $hari = max(1, (int) $awal->diffInDays($akhir));
        $awalLalu = $awal->copy()->subDays($hari);

        $hasil = DB::selectOne(
            "select
                (select coalesce(sum(jumlah), 0) from pembayaran where status_transaksi = 'berhasil' and waktu_bayar >= ? and waktu_bayar < ?) as pendapatan,
                (select coalesce(sum(jumlah), 0) from pembayaran where status_transaksi = 'berhasil' and waktu_bayar >= ? and waktu_bayar < ?) as pendapatan_lalu,
                (select count(*) from pemesanan where tanggal_mulai >= ? and tanggal_mulai < ? and status <> 'dibatalkan') as transaksi,
                (select coalesce(sum(jumlah_denda), 0) from denda where created_at >= ? and created_at < ?) as denda,
                (select coalesce(sum(stok_disewa), 0) from alat) as disewa,
                (select coalesce(sum(stok_total), 0) from alat) as total_stok",
            [
                $awal, $akhir,
                $awalLalu, $awal,
                $awal->toDateString(), $akhir->toDateString(),
                $awal, $akhir,
            ],
        );

        $alatTeratas = DB::table('detail_pemesanan')
            ->join('pemesanan', 'pemesanan.id', '=', 'detail_pemesanan.pemesanan_id')
            ->join('alat', 'alat.id', '=', 'detail_pemesanan.alat_id')
            ->where('pemesanan.tanggal_mulai', '>=', $awal->toDateString())
            ->where('pemesanan.tanggal_mulai', '<', $akhir->toDateString())
            ->where('pemesanan.status', '<>', 'dibatalkan')
            ->groupBy('alat.id', 'alat.nama')
            ->orderByRaw('sum(detail_pemesanan.jumlah) desc')
            ->value('alat.nama');

        $pendapatan = (float) $hasil->pendapatan;
        $pendapatanLalu = (float) $hasil->pendapatan_lalu;
        $totalStok = (int) $hasil->total_stok;

        if ($pendapatanLalu > 0) {
            $persen = (int) round(($pendapatan - $pendapatanLalu) / $pendapatanLalu * 100);
            $tren = $persen > 0 ? 1 : ($persen < 0 ? -1 : 0);
            $catatan = ($persen > 0 ? '↑ +' : ($persen < 0 ? '↓ ' : '')).$persen.'% dari periode sebelumnya';
        } else {
            $tren = 0;
            $catatan = $pendapatan > 0 ? 'Belum ada pembanding periode sebelumnya' : 'Belum ada pemasukan pada periode ini';
        }

        $format = static fn (float|int $angka): string => number_format($angka, 0, ',', '.');

        return [
            'pendapatan' => $format($pendapatan),
            'pendapatan_catatan' => $catatan,
            'pendapatan_tren' => $tren,
            'transaksi' => $format((int) $hasil->transaksi),
            'denda' => $format((float) $hasil->denda),
            'utilisasi' => $totalStok > 0 ? (int) round((int) $hasil->disewa / $totalStok * 100) : 0,
            'alat_teratas' => $alatTeratas !== null ? $alatTeratas.' paling sering disewa' : 'Belum ada penyewaan pada periode ini',
            'periode' => $this->labelPeriode($filters),
        ];
    }

    /**
     * @param  array{dari: string, sampai: string}  $filters
     */
    public static function labelPeriode(array $filters): string
    {
        $dari = Carbon::parse($filters['dari'])->locale('id')->translatedFormat('d M Y');
        $sampai = Carbon::parse($filters['sampai'])->locale('id')->translatedFormat('d M Y');

        return $dari === $sampai ? $dari : "{$dari} – {$sampai}";
    }

    /**
     * Parameter query untuk URL halaman (hanya yang berbeda dari bawaan).
     *
     * @param  array{search: string, tipe: string, dari: string, sampai: string, per_page: int, page: int}  $filters
     * @return array<string, int|string>
     */
    private function parameterQuery(array $filters, bool $denganHalaman = false): array
    {
        $bawaan = $this->periodeBawaan();
        $parameter = [];

        if ($filters['search'] !== '') {
            $parameter['search'] = $filters['search'];
        }

        if ($filters['tipe'] !== self::TIPE[0]) {
            $parameter['tipe'] = $filters['tipe'];
        }

        if ($filters['dari'] !== $bawaan['dari']) {
            $parameter['dari'] = $filters['dari'];
        }

        if ($filters['sampai'] !== $bawaan['sampai']) {
            $parameter['sampai'] = $filters['sampai'];
        }

        if ($filters['per_page'] !== self::PER_PAGE[0]) {
            $parameter['per_page'] = $filters['per_page'];
        }

        if ($denganHalaman && $filters['page'] > 1) {
            $parameter['page'] = $filters['page'];
        }

        return $parameter;
    }
}
