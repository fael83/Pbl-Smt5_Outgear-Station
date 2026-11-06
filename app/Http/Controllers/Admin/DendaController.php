<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Denda;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\View\View;

/**
 * Halaman pengelolaan denda pada panel admin.
 *
 * Sama seperti halaman data pelanggan dan peralatan:
 *
 * - render penuh  -> GET /admin/denda (view + partial tabel);
 * - interaksi JS  -> GET /admin/denda/tabel (JSON berisi HTML tabel saja).
 *
 * Pencarian, filter status, dan pagination dikerjakan di database. Angka
 * ringkasan (kartu statistik) tidak bergantung pada filter, jadi endpoint
 * tabel hanya menghitungnya ulang saat tombol "Muat ulang" ditekan.
 */
class DendaController extends Controller
{
    /** @var array<int, int> */
    private const PER_PAGE = [10, 25, 50];

    /** Status yang boleh dipakai sebagai filter; `semua` berarti tanpa filter. */
    private const STATUS = ['belum_dibayar', 'lunas'];

    public function index(Request $request): View
    {
        $filters = $this->filter($request);

        return view('admin.denda', [
            'denda' => $this->daftarDenda($filters),
            'filters' => $filters,
            'ringkasan' => $this->ringkasan(),
        ]);
    }

    /**
     * Endpoint JSON untuk pencarian, filter, dan pagination tanpa memuat ulang
     * halaman. Hanya kartu tabel yang dikirim balik.
     */
    public function tabel(Request $request): JsonResponse
    {
        $filters = $this->filter($request);

        $balasan = [
            'html' => view('admin.denda._tabel', [
                'denda' => $this->daftarDenda($filters),
                'filters' => $filters,
            ])->render(),
            'url' => route('admin.denda.index', $this->parameterQuery($filters, true)),
        ];

        // Muat ulang (?segarkan=1) menghitung ulang kartu ringkasan.
        if ($request->boolean('segarkan') || $request->boolean('ringkasan')) {
            $balasan['ringkasan'] = $this->ringkasan();
        }

        return response()->json($balasan);
    }

    /**
     * Baca dan bersihkan filter dari query string.
     *
     * @return array{search: string, status: string, per_page: int, page: int}
     */
    private function filter(Request $request): array
    {
        $perPage = (int) $request->query('per_page', self::PER_PAGE[0]);

        if (! in_array($perPage, self::PER_PAGE, true)) {
            $perPage = self::PER_PAGE[0];
        }

        $status = (string) $request->query('status', 'semua');

        if (! in_array($status, self::STATUS, true)) {
            $status = 'semua';
        }

        return [
            'search' => trim((string) $request->query('search', '')),
            'status' => $status,
            'per_page' => $perPage,
            'page' => max(1, (int) $request->query('page', 1)),
        ];
    }

    /**
     * @param  array{search: string, status: string, per_page: int, page: int}  $filters
     * @return Builder<Denda>
     */
    private function queryDenda(array $filters): Builder
    {
        return Denda::query()
            ->when($filters['status'] !== 'semua', fn (Builder $query) => $query->where('status', $filters['status']))
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $search = $filters['search'];

                // Jenis denda disimpan sebagai "kerusakan_ringan"; pencarian
                // "kerusakan ringan" (dengan spasi) tetap menemukannya.
                $pola = '%'.str_replace(' ', '_', $search).'%';

                // Satu grup where supaya OR di dalamnya tidak membocorkan
                // filter status.
                $query->where(function (Builder $grup) use ($pola, $search): void {
                    $grup->whereLike('jenis_denda', $pola);

                    // Kolom ID bertipe angka, jadi dicocokkan sebagai angka
                    // (LIKE pada kolom bigint ditolak PostgreSQL). "#DND-05"
                    // dan "5" sama-sama menemukan denda nomor 5.
                    if (preg_match('/\d+/', $search, $cocok) === 1) {
                        $angka = (int) $cocok[0];

                        $grup->orWhere('id', $angka)
                            ->orWhere('pengembalian_id', $angka)
                            ->orWhere('pemesanan_id', $angka)
                            ->orWhere('detail_pengembalian_id', $angka);
                    }
                });
            });
    }

    /**
     * Satu halaman data denda sesuai filter.
     *
     * Kolom yang ditampilkan semuanya ada di tabel denda, jadi tidak ada
     * relasi yang dimuat (setiap relasi menambah satu round-trip ke database).
     *
     * @param  array{search: string, status: string, per_page: int, page: int}  $filters
     * @return LengthAwarePaginator<int, Denda>
     */
    private function daftarDenda(array $filters): LengthAwarePaginator
    {
        return $this->queryDenda($filters)
            ->latest()
            ->orderByDesc('id')
            ->paginate($filters['per_page'], ['*'], 'page', $filters['page'])
            // Tautan pagination mengarah ke halaman penuh (bukan endpoint JSON).
            ->withPath(route('admin.denda.index'))
            ->appends($this->parameterQuery($filters));
    }

    /**
     * Angka untuk lencana dan 3 kartu statistik, sudah diformat (1.234.567).
     * Cukup satu query dengan agregat bersyarat.
     *
     * @return array{belum_dibayar: string, keterlambatan: string, kerusakan: string, lunas: string}
     */
    private function ringkasan(): array
    {
        $hasil = Denda::query()
            ->selectRaw("
                sum(case when status = 'belum_dibayar' then 1 else 0 end) as belum_dibayar,
                sum(case when status = 'belum_dibayar' and jenis_denda = 'keterlambatan' then jumlah_denda else 0 end) as keterlambatan,
                sum(case when status = 'belum_dibayar' and jenis_denda in ('kerusakan_ringan', 'kerusakan_berat', 'kehilangan') then jumlah_denda else 0 end) as kerusakan,
                sum(case when status = 'lunas' then jumlah_denda else 0 end) as lunas
            ")
            ->toBase()
            ->first();

        $format = static fn (mixed $angka): string => number_format((float) $angka, 0, ',', '.');

        return [
            'belum_dibayar' => $format($hasil->belum_dibayar ?? 0),
            'keterlambatan' => $format($hasil->keterlambatan ?? 0),
            'kerusakan' => $format($hasil->kerusakan ?? 0),
            'lunas' => $format($hasil->lunas ?? 0),
        ];
    }

    /**
     * Parameter query untuk URL halaman (hanya yang berbeda dari bawaan).
     *
     * @param  array{search: string, status: string, per_page: int, page: int}  $filters
     * @return array<string, int|string>
     */
    private function parameterQuery(array $filters, bool $denganHalaman = false): array
    {
        $parameter = [];

        if ($filters['search'] !== '') {
            $parameter['search'] = $filters['search'];
        }

        if ($filters['status'] !== 'semua') {
            $parameter['status'] = $filters['status'];
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
