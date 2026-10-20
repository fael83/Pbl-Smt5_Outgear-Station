<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Halaman data pelanggan pada panel admin.
 *
 * Isi halaman dibaca langsung dari tabel `users` (ERD: PENGGUNA) dengan
 * `role = customer`. Sama seperti halaman data peralatan, pencarian, filter
 * status, dan pagination dikerjakan di sisi database:
 *
 * - render penuh  -> GET /admin/pelanggan (view + partial tabel);
 * - interaksi JS  -> GET /admin/pelanggan/tabel (JSON berisi HTML tabel saja);
 * - ubah status   -> PATCH /admin/pelanggan/{pelanggan}/status;
 * - unduh data    -> GET /admin/pelanggan/ekspor (CSV sesuai filter aktif).
 *
 * Tanpa JavaScript, form filter dan form status tetap terkirim seperti biasa.
 */
class PelangganController extends Controller
{
    /**
     * Pilihan jumlah baris per halaman pada tabel data pelanggan.
     *
     * @var array<int, int>
     */
    private const PER_PAGE = [10, 25, 50];

    /**
     * Pilihan filter status akun. `semua` berarti tanpa filter.
     *
     * @var array<int, string>
     */
    private const STATUS = ['semua', 'aktif', 'nonaktif'];

    /**
     * Nilai kolom `status_akun` yang boleh disimpan (ERD: PENGGUNA.status_akun).
     *
     * @var array<int, string>
     */
    private const STATUS_AKUN = ['aktif', 'nonaktif'];

    /**
     * Ringkasan pelanggan dipakai di setiap request tetapi jarang berubah.
     * Database berada di Supabase (satu round-trip bisa lebih dari 1 detik),
     * jadi hasilnya disimpan sebentar di cache dan dihapus setiap ada
     * perubahan status akun.
     */
    private const CACHE_RINGKASAN = 'pelanggan.ringkasan';

    /**
     * Tampilkan data pelanggan langsung dari database (Supabase/PostgreSQL)
     * lengkap dengan pencarian, filter status, dan pagination.
     */
    public function index(Request $request): View
    {
        $filters = $this->filter($request);
        $daftarPelanggan = $this->daftarPelanggan($filters);

        return view('admin.pelanggan', [
            'daftarPelanggan' => $daftarPelanggan,
            'dataPelanggan' => $this->dataPelanggan($daftarPelanggan),
            'ringkasan' => $this->ringkasan(),
            'filters' => $filters,
            'adaFilter' => $this->adaFilter($filters),
        ]);
    }

    /**
     * Endpoint JSON untuk pencarian, filter, dan pagination tanpa memuat ulang
     * seluruh halaman. Hanya kartu tabel yang dikirim balik.
     */
    public function tabel(Request $request): JsonResponse
    {
        $filters = $this->filter($request);
        $daftarPelanggan = $this->daftarPelanggan($filters);

        return response()->json([
            'html' => view('admin.pelanggan._tabel', [
                'daftarPelanggan' => $daftarPelanggan,
                'filters' => $filters,
                'adaFilter' => $this->adaFilter($filters),
            ])->render(),
            'items' => $this->dataPelanggan($daftarPelanggan),
            'ringkasan' => $this->ringkasan(),
            'url' => route('admin.pelanggan', $this->parameterQuery($filters, true)),
        ]);
    }

    /**
     * Aktifkan / nonaktifkan akun pelanggan.
     *
     * Status baru dikirim lewat input tersembunyi pada form di setiap baris
     * tabel, sehingga aksi ini idempoten dan tetap bekerja tanpa JavaScript.
     */
    public function ubahStatus(Request $request, User $pelanggan): RedirectResponse|JsonResponse
    {
        // Akun staf internal tidak boleh diubah dari halaman pelanggan.
        abort_unless($pelanggan->isCustomer(), 404);

        $data = $request->validate([
            'status_akun' => ['required', 'string', Rule::in(self::STATUS_AKUN)],
        ]);

        $pelanggan->update(['status_akun' => $data['status_akun']]);
        $this->lupakanCache();

        $pesan = $data['status_akun'] === 'aktif'
            ? "Akun \"{$pelanggan->name}\" berhasil diaktifkan kembali."
            : "Akun \"{$pelanggan->name}\" berhasil dinonaktifkan.";

        if ($request->wantsJson()) {
            $pelanggan->loadCount('pemesanan');

            return response()->json([
                'message' => $pesan,
                'id' => $pelanggan->id,
                'baris' => view('admin.pelanggan._baris', [
                    'pelanggan' => $pelanggan,
                    'filters' => $this->filter($request, true),
                ])->render(),
                'item' => $this->dataSatuPelanggan($pelanggan),
                'ringkasan' => $this->ringkasan(),
            ]);
        }

        return $this->kembaliKeDaftar($request)->with('success', $pesan);
    }

    /**
     * Unduh data pelanggan (sesuai filter aktif) sebagai berkas CSV.
     */
    public function ekspor(Request $request): StreamedResponse
    {
        $filters = $this->filter($request);
        $namaBerkas = 'pelanggan-'.now()->format('Ymd-His').'.csv';

        return response()->streamDownload(function () use ($filters): void {
            $keluaran = fopen('php://output', 'w');

            if ($keluaran === false) {
                return;
            }

            // BOM UTF-8 supaya Excel membaca karakter Indonesia dengan benar.
            fwrite($keluaran, "\xEF\xBB\xBF");

            fputcsv($keluaran, [
                'ID',
                'Nama',
                'Email',
                'No. Telepon',
                'Status Akun',
                'Total Booking',
                'Bergabung',
            ], ',', '"', '');

            // chunk() menjaga pemakaian memori tetap kecil saat data bertambah.
            $this->queryPelanggan($filters)
                ->withCount('pemesanan')
                ->orderBy('users.id')
                ->chunk(200, function ($baris) use ($keluaran): void {
                    foreach ($baris as $pelanggan) {
                        fputcsv($keluaran, [
                            '#PL-'.str_pad((string) $pelanggan->id, 3, '0', STR_PAD_LEFT),
                            $pelanggan->name,
                            (string) $pelanggan->email,
                            (string) ($pelanggan->no_telepon ?? ''),
                            $pelanggan->status_akun === 'aktif' ? 'Aktif' : 'Nonaktif',
                            (int) ($pelanggan->pemesanan_count ?? 0),
                            $pelanggan->created_at?->translatedFormat('d M Y') ?? '-',
                        ], ',', '"', '');
                    }
                });

            fclose($keluaran);
        }, $namaBerkas, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Normalisasi parameter filter dari query string (halaman penuh) atau dari
     * body request (respons AJAX setelah mengubah status akun).
     *
     * @return array{search: string, status: string, per_page: int, page: int}
     */
    private function filter(Request $request, bool $dariInput = false): array
    {
        $ambil = fn (string $kunci, mixed $bawaan = null): mixed => $dariInput
            ? $request->input($kunci, $bawaan)
            : $request->query($kunci, $bawaan);

        $perPage = (int) $ambil('per_page', self::PER_PAGE[0]);

        if (! in_array($perPage, self::PER_PAGE, true)) {
            $perPage = self::PER_PAGE[0];
        }

        $status = (string) $ambil('status', 'semua');

        if (! in_array($status, self::STATUS, true)) {
            $status = 'semua';
        }

        return [
            'search' => trim((string) $ambil('search', '')),
            'status' => $status,
            'per_page' => $perPage,
            'page' => max(1, (int) $ambil('page', 1)),
        ];
    }

    /**
     * Query dasar pelanggan + seluruh filter aktif (tanpa kolom hasil).
     *
     * Hanya akun dengan peran pelanggan yang ditampilkan; akun staf internal
     * (admin, petugas, owner) dikelola di tempat lain.
     *
     * @param  array{search: string, status: string, per_page: int, page: int}  $filters
     * @return Builder<User>
     */
    private function queryPelanggan(array $filters): Builder
    {
        return User::query()
            ->where('role', Role::Customer)
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $search = $filters['search'];

                // Escape wildcard LIKE supaya "%" dan "_" dicari sebagai
                // karakter biasa, bukan sebagai pola pencarian.
                $pola = '%'.addcslashes($search, '%_\\').'%';

                // Semua kondisi pencarian (termasuk pencarian ID) berada dalam
                // satu grup agar filter status tetap diterapkan dengan AND.
                $query->where(function (Builder $query) use ($pola, $search): void {
                    $query->whereLike('name', $pola)
                        ->orWhereLike('email', $pola)
                        ->orWhereLike('no_telepon', $pola);

                    // ID ditampilkan sebagai #PL-001, jadi pencarian "001",
                    // "PL-001", maupun "#PL-001" menemukan pelanggan yang sama.
                    if (preg_match('/\d+/', $search, $cocok) === 1) {
                        $query->orWhereKey((int) $cocok[0]);
                    }
                });
            })
            ->when($filters['status'] === 'aktif', fn (Builder $query) => $query->where('status_akun', 'aktif'))
            ->when($filters['status'] === 'nonaktif', fn (Builder $query) => $query->where('status_akun', '<>', 'aktif'));
    }

    /**
     * Ambil satu halaman data pelanggan.
     *
     * Sama seperti tabel peralatan: total baris diambil lewat window function
     * `count(*) over()` sehingga daftar + total cukup satu query, sedangkan
     * jumlah booking dihitung sebagai subquery `withCount`.
     *
     * @param  array{search: string, status: string, per_page: int, page: int}  $filters
     */
    private function daftarPelanggan(array $filters): LengthAwarePaginator
    {
        $baris = $this->queryPelanggan($filters)
            ->select('users.*')
            ->withCount('pemesanan')
            ->selectRaw('count(*) over() as total_hasil')
            ->orderByDesc('users.created_at')
            ->orderBy('users.name')
            ->forPage($filters['page'], $filters['per_page'])
            ->get();

        $total = (int) ($baris->first()?->total_hasil ?? 0);

        // Halaman di luar jangkauan tidak mengembalikan baris sama sekali,
        // sehingga total perlu dihitung terpisah. Kasus ini jarang terjadi.
        if ($baris->isEmpty() && $filters['page'] > 1) {
            $total = $this->queryPelanggan($filters)->count();
        }

        return new LengthAwarePaginator(
            $baris->values(),
            $total,
            $filters['per_page'],
            $filters['page'],
            ['path' => route('admin.pelanggan'), 'query' => $this->parameterQuery($filters)],
        );
    }

    /**
     * Ringkasan pelanggan (tidak terpengaruh filter).
     *
     * Dihitung dengan `SUM(CASE WHEN ...)` agar tetap portabel di PostgreSQL
     * (Supabase) maupun SQLite (test), tanpa fungsi khusus salah satu vendor.
     *
     * @return array{total_pelanggan: int, pelanggan_aktif: int, pelanggan_nonaktif: int, pelanggan_baru: int}
     */
    private function ringkasan(): array
    {
        $awalBulan = now()->startOfMonth()->toDateTimeString();

        /** @var array{total_pelanggan: int, pelanggan_aktif: int, pelanggan_nonaktif: int, pelanggan_baru: int} $ringkasan */
        $ringkasan = Cache::remember($this->kunciRingkasan(), now()->addMinute(), function () use ($awalBulan): array {
            $hasil = User::query()
                ->where('role', Role::Customer)
                ->selectRaw('COUNT(*) AS total_pelanggan')
                ->selectRaw("COALESCE(SUM(CASE WHEN status_akun = 'aktif' THEN 1 ELSE 0 END), 0) AS pelanggan_aktif")
                ->selectRaw("COALESCE(SUM(CASE WHEN status_akun <> 'aktif' THEN 1 ELSE 0 END), 0) AS pelanggan_nonaktif")
                ->selectRaw('COALESCE(SUM(CASE WHEN created_at >= ? THEN 1 ELSE 0 END), 0) AS pelanggan_baru', [$awalBulan])
                ->toBase()
                ->first();

            return [
                'total_pelanggan' => (int) ($hasil->total_pelanggan ?? 0),
                'pelanggan_aktif' => (int) ($hasil->pelanggan_aktif ?? 0),
                'pelanggan_nonaktif' => (int) ($hasil->pelanggan_nonaktif ?? 0),
                'pelanggan_baru' => (int) ($hasil->pelanggan_baru ?? 0),
            ];
        });

        return $ringkasan;
    }

    /**
     * Kunci cache ringkasan. Bulan ikut disertakan karena kartu "pelanggan
     * baru" berganti nilai begitu bulan berjalan berganti.
     */
    private function kunciRingkasan(): string
    {
        return self::CACHE_RINGKASAN.'.'.now()->format('Y-m');
    }

    /**
     * Data ringkas per pelanggan untuk Alpine (butuh array, bukan model).
     *
     * @param  iterable<int, User>  $daftarPelanggan
     * @return array<int, array<string, mixed>>
     */
    private function dataPelanggan(iterable $daftarPelanggan): array
    {
        $data = [];

        // LengthAwarePaginator juga iterable (mengembalikan item-nya), jadi
        // paginator maupun array/Collection bisa langsung dipakai di sini.
        foreach ($daftarPelanggan as $pelanggan) {
            $data[] = $this->dataSatuPelanggan($pelanggan);
        }

        return $data;
    }

    /**
     * @return array{id: int, nama: string, email: string, no_telepon: string, status_akun: string, jumlah_pemesanan: int, bergabung: string}
     */
    private function dataSatuPelanggan(User $pelanggan): array
    {
        return [
            'id' => (int) $pelanggan->id,
            'nama' => (string) $pelanggan->name,
            'email' => (string) $pelanggan->email,
            'no_telepon' => (string) ($pelanggan->no_telepon ?? ''),
            'status_akun' => (string) ($pelanggan->status_akun ?? 'aktif'),
            'jumlah_pemesanan' => (int) ($pelanggan->pemesanan_count ?? 0),
            'bergabung' => $pelanggan->created_at?->translatedFormat('d M Y') ?? '-',
        ];
    }

    /**
     * Parameter query yang dipertahankan pada tautan pagination/tab.
     *
     * @param  array{search: string, status: string, per_page: int, page: int}  $filters
     * @return array<string, mixed>
     */
    private function parameterQuery(array $filters, bool $denganHalaman = false): array
    {
        $parameter = array_filter([
            'search' => $filters['search'],
            'status' => $filters['status'] !== 'semua' ? $filters['status'] : null,
            'per_page' => $filters['per_page'] !== self::PER_PAGE[0] ? $filters['per_page'] : null,
        ], static fn (mixed $nilai): bool => filled($nilai));

        if ($denganHalaman && $filters['page'] > 1) {
            $parameter['page'] = $filters['page'];
        }

        return $parameter;
    }

    /**
     * @param  array{search: string, status: string, per_page: int, page: int}  $filters
     */
    private function adaFilter(array $filters): bool
    {
        return $filters['search'] !== '' || $filters['status'] !== 'semua';
    }

    /**
     * Hapus cache yang terpengaruh perubahan status akun.
     */
    private function lupakanCache(): void
    {
        Cache::forget($this->kunciRingkasan());
    }

    /**
     * Kembali ke halaman daftar sambil mempertahankan filter yang aktif.
     */
    private function kembaliKeDaftar(Request $request): RedirectResponse
    {
        $filters = array_filter(
            [
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'per_page' => $request->input('per_page'),
            ],
            fn ($nilai) => filled($nilai) && $nilai !== 'semua',
        );

        return redirect()->route('admin.pelanggan', $filters);
    }
}
