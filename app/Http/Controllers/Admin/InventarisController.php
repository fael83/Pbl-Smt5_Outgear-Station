<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AlatRequest;
use App\Models\Alat;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\View\View;

class InventarisController extends Controller
{
    /**
     * Pilihan jumlah baris per halaman pada tabel data peralatan.
     *
     * @var array<int, int>
     */
    private const PER_PAGE = [10, 25, 50];

    /**
     * Pilihan filter kondisi stok.
     *
     * @var array<int, string>
     */
    private const KONDISI = ['semua', 'tersedia', 'habis', 'rusak'];

    /**
     * Ringkasan stok dipakai di setiap request tetapi jarang berubah. Karena
     * database berada di Supabase (satu round-trip bisa lebih dari 1 detik),
     * hasilnya disimpan sebentar di cache dan dihapus setiap ada perubahan.
     */
    private const CACHE_RINGKASAN = 'peralatan.ringkasan';

    /**
     * Daftar kategori + jumlah alat. Dipakai untuk dropdown filter dan untuk
     * melengkapi relasi `kategori` pada baris tabel tanpa query tambahan.
     */
    private const CACHE_KATEGORI = 'peralatan.kategori';

    /**
     * Tampilkan data peralatan langsung dari database (Supabase/PostgreSQL)
     * lengkap dengan pencarian, filter kategori, filter kondisi, dan pagination.
     */
    public function index(Request $request): View
    {
        $this->authorize('viewAny', Alat::class);

        $filters = $this->filter($request);
        $daftarAlat = $this->daftarAlat($filters);

        return view('admin.peralatan', [
            'daftarAlat' => $daftarAlat,
            'dataAlat' => $this->dataAlat($daftarAlat),
            'ringkasan' => $this->ringkasan(),
            'daftarKategori' => $this->daftarKategori(),
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
        $this->authorize('viewAny', Alat::class);

        $filters = $this->filter($request);
        $daftarAlat = $this->daftarAlat($filters);

        return response()->json([
            'html' => view('admin.peralatan._tabel', [
                'daftarAlat' => $daftarAlat,
                'filters' => $filters,
                'adaFilter' => $this->adaFilter($filters),
            ])->render(),
            'items' => $this->dataAlat($daftarAlat),
            'ringkasan' => $this->ringkasan(),
            'url' => route('admin.peralatan.index', $this->parameterQuery($filters, true)),
        ]);
    }

    /**
     * Normalisasi parameter filter dari query string (halaman penuh) atau dari
     * body request (respons AJAX setelah menyimpan data).
     *
     * @return array{search: string, kategori: int, kondisi: string, per_page: int, page: int}
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

        $kondisi = (string) $ambil('kondisi', 'semua');

        if (! in_array($kondisi, self::KONDISI, true)) {
            $kondisi = 'semua';
        }

        return [
            'search' => trim((string) $ambil('search', '')),
            'kategori' => max(0, (int) $ambil('kategori', 0)),
            'kondisi' => $kondisi,
            'per_page' => $perPage,
            'page' => max(1, (int) $ambil('page', 1)),
        ];
    }

    /**
     * Query dasar alat + seluruh filter aktif (tanpa kolom hasil).
     *
     * @param  array{search: string, kategori: int, kondisi: string, per_page: int, page: int}  $filters
     * @return Builder<Alat>
     */
    private function queryAlat(array $filters): Builder
    {
        return Alat::query()
            ->when($filters['search'] !== '', function (Builder $query) use ($filters): void {
                $search = $filters['search'];

                // Escape wildcard LIKE supaya "%" dan "_" dicari sebagai
                // karakter biasa, bukan sebagai pola pencarian.
                $pola = '%'.addcslashes($search, '%_\\').'%';

                // Semua kondisi pencarian (termasuk pencarian ID) harus berada
                // di dalam satu grup agar filter kategori/kondisi tetap
                // diterapkan dengan logika AND.
                $query->where(function (Builder $query) use ($pola, $search): void {
                    $query->whereLike('nama', $pola)
                        ->orWhereLike('deskripsi', $pola)
                        ->orWhereHas(
                            'kategori',
                            fn (Builder $kategori) => $kategori->whereLike('nama', $pola),
                        );

                    // ID ditampilkan sebagai #AL-001, jadi pencarian "001",
                    // "AL-001", maupun "#AL-001" tetap menemukan alat yang sama.
                    if (preg_match('/\d+/', $search, $cocok) === 1) {
                        $query->orWhereKey((int) $cocok[0]);
                    }
                });
            })
            ->when($filters['kategori'] > 0, fn (Builder $query) => $query->where('alat.kategori_id', $filters['kategori']))
            ->when($filters['kondisi'] === 'tersedia', fn (Builder $query) => $query->where('stok_tersedia', '>', 0))
            ->when($filters['kondisi'] === 'habis', fn (Builder $query) => $query->where('stok_tersedia', '<=', 0))
            ->when($filters['kondisi'] === 'rusak', fn (Builder $query) => $query->where('stok_rusak', '>', 0));
    }

    /**
     * Ambil satu halaman data alat.
     *
     * Jumlah total baris diambil lewat window function `count(*) over()`
     * sehingga daftar + total cukup satu query (paginate() butuh dua query).
     * Relasi kategori dilengkapi dari cache agar tidak menambah round-trip.
     *
     * @param  array{search: string, kategori: int, kondisi: string, per_page: int, page: int}  $filters
     */
    private function daftarAlat(array $filters): LengthAwarePaginator
    {
        $baris = $this->queryAlat($filters)
            ->select('alat.*')
            ->selectRaw('count(*) over() as total_hasil')
            ->orderBy('alat.nama')
            ->forPage($filters['page'], $filters['per_page'])
            ->get();

        $total = (int) ($baris->first()?->total_hasil ?? 0);

        // Halaman di luar jangkauan tidak mengembalikan baris sama sekali,
        // sehingga total perlu dihitung terpisah. Kasus ini jarang terjadi.
        if ($baris->isEmpty() && $filters['page'] > 1) {
            $total = $this->queryAlat($filters)->count();
        }

        $kategori = $this->daftarKategori()->keyBy('id');

        $baris->each(fn (Alat $alat) => $alat->setRelation('kategori', $kategori->get($alat->kategori_id)));

        return new LengthAwarePaginator(
            $baris->values(),
            $total,
            $filters['per_page'],
            $filters['page'],
            ['path' => route('admin.peralatan.index'), 'query' => $this->parameterQuery($filters)],
        );
    }

    /**
     * Ringkasan stok seluruh inventaris (tidak terpengaruh filter).
     *
     * Selain total unit, dihitung juga jumlah *jenis alat* per kondisi supaya
     * tab status di halaman data peralatan bisa menampilkan angka asli tanpa
     * query tambahan.
     *
     * @return array{total_alat: int, total_unit: int, unit_tersedia: int, unit_disewa: int, unit_rusak: int, alat_tersedia: int, alat_habis: int, alat_rusak: int}
     */
    private function ringkasan(): array
    {
        /** @var array{total_alat: int, total_unit: int, unit_tersedia: int, unit_disewa: int, unit_rusak: int, alat_tersedia: int, alat_habis: int, alat_rusak: int} $ringkasan */
        $ringkasan = Cache::remember(self::CACHE_RINGKASAN, now()->addMinute(), function (): array {
            $hasil = Alat::query()
                ->selectRaw('COUNT(*) AS total_alat')
                ->selectRaw('COALESCE(SUM(stok_total), 0) AS total_unit')
                ->selectRaw('COALESCE(SUM(stok_tersedia), 0) AS unit_tersedia')
                ->selectRaw('COALESCE(SUM(stok_disewa), 0) AS unit_disewa')
                ->selectRaw('COALESCE(SUM(stok_rusak), 0) AS unit_rusak')
                ->selectRaw('COALESCE(SUM(CASE WHEN stok_tersedia > 0 THEN 1 ELSE 0 END), 0) AS alat_tersedia')
                ->selectRaw('COALESCE(SUM(CASE WHEN stok_tersedia <= 0 THEN 1 ELSE 0 END), 0) AS alat_habis')
                ->selectRaw('COALESCE(SUM(CASE WHEN stok_rusak > 0 THEN 1 ELSE 0 END), 0) AS alat_rusak')
                ->toBase()
                ->first();

            return [
                'total_alat' => (int) ($hasil->total_alat ?? 0),
                'total_unit' => (int) ($hasil->total_unit ?? 0),
                'unit_tersedia' => (int) ($hasil->unit_tersedia ?? 0),
                'unit_disewa' => (int) ($hasil->unit_disewa ?? 0),
                'unit_rusak' => (int) ($hasil->unit_rusak ?? 0),
                'alat_tersedia' => (int) ($hasil->alat_tersedia ?? 0),
                'alat_habis' => (int) ($hasil->alat_habis ?? 0),
                'alat_rusak' => (int) ($hasil->alat_rusak ?? 0),
            ];
        });

        return $ringkasan;
    }

    /**
     * Daftar kategori beserta jumlah alatnya.
     *
     * Yang disimpan di cache adalah array biasa, bukan model Eloquent, karena
     * `cache.serializable_classes` bernilai false (model tidak boleh di-
     * unserialize dari cache). Model dihidrasi ulang dari array tersebut tanpa
     * menyentuh database.
     *
     * @return Collection<int, Kategori>
     */
    private function daftarKategori(): Collection
    {
        /** @var array<int, array{id: int, nama: string, alat_count: int}> $baris */
        $baris = Cache::remember(
            self::CACHE_KATEGORI,
            now()->addMinutes(10),
            fn (): array => Kategori::query()
                ->withCount('alat')
                ->orderBy('nama')
                ->get()
                ->map(fn (Kategori $kategori): array => [
                    'id' => $kategori->id,
                    'nama' => $kategori->nama,
                    'alat_count' => (int) $kategori->alat_count,
                ])
                ->all(),
        );

        /** @var Collection<int, Kategori> $daftarKategori */
        $daftarKategori = Kategori::hydrate($baris);

        return $daftarKategori;
    }

    /**
     * Data ringkas per alat untuk modal edit (Alpine butuh array, bukan model).
     *
     * @param  iterable<int, Alat>  $daftarAlat
     * @return array<int, array<string, mixed>>
     */
    private function dataAlat(iterable $daftarAlat): array
    {
        $data = [];

        // LengthAwarePaginator juga iterable (mengembalikan item-nya), jadi
        // paginator maupun array/Collection bisa langsung dipakai di sini.
        foreach ($daftarAlat as $alat) {
            $data[] = $this->dataSatuAlat($alat);
        }

        return $data;
    }

    /**
     * @return array<string, mixed>
     */
    private function dataSatuAlat(Alat $alat): array
    {
        return [
            'id' => $alat->id,
            'nama' => $alat->nama,
            'kategori_id' => (string) $alat->kategori_id,
            'deskripsi' => (string) ($alat->deskripsi ?? ''),
            'tarif_per_hari' => (float) $alat->tarif_per_hari,
            'nilai_pengganti' => (float) $alat->nilai_pengganti,
            'stok_total' => (int) $alat->stok_total,
            'stok_disewa' => (int) $alat->stok_disewa,
            'stok_rusak' => (int) $alat->stok_rusak,
            'update_url' => route('admin.peralatan.update', $alat),
        ];
    }

    /**
     * Parameter query yang perlu dibawa saat filter atau pagination berubah.
     *
     * @param  array{search: string, kategori: int, kondisi: string, per_page: int, page: int}  $filters
     * @return array<string, mixed>
     */
    private function parameterQuery(array $filters, bool $denganHalaman = false): array
    {
        $parameter = array_filter([
            'search' => $filters['search'],
            'kategori' => $filters['kategori'] ?: null,
            'kondisi' => $filters['kondisi'] !== 'semua' ? $filters['kondisi'] : null,
            'per_page' => $filters['per_page'] !== self::PER_PAGE[0] ? $filters['per_page'] : null,
        ], fn (mixed $nilai): bool => filled($nilai));

        if ($denganHalaman && $filters['page'] > 1) {
            $parameter['page'] = $filters['page'];
        }

        return $parameter;
    }

    /**
     * @param  array{search: string, kategori: int, kondisi: string, per_page: int, page: int}  $filters
     */
    private function adaFilter(array $filters): bool
    {
        return $filters['search'] !== '' || $filters['kategori'] > 0 || $filters['kondisi'] !== 'semua';
    }

    /**
     * Hapus cache yang terpengaruh perubahan data alat.
     */
    private function lupakanCache(): void
    {
        Cache::forget(self::CACHE_RINGKASAN);
        Cache::forget(self::CACHE_KATEGORI);
    }

    /**
     * Simpan alat baru ke database.
     */
    public function store(AlatRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $stokTotal = (int) $data['stok_total'];
        $stokRusak = (int) ($data['stok_rusak'] ?? 0);

        $alat = Alat::create([
            'kategori_id' => $data['kategori_id'],
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tarif_per_hari' => $data['tarif_per_hari'],
            'nilai_pengganti' => $data['nilai_pengganti'],
            'stok_total' => $stokTotal,
            'stok_tersedia' => max(0, $stokTotal - $stokRusak),
            'stok_disewa' => 0,
            'stok_rusak' => $stokRusak,
        ]);

        $this->lupakanCache();
        $pesan = "Alat \"{$alat->nama}\" berhasil ditambahkan.";

        if ($request->wantsJson()) {
            return $this->responsJson($alat, $request, $pesan);
        }

        return $this->kembaliKeDaftar($request)->with('success', $pesan);
    }

    /**
     * Perbarui data alat yang sudah ada.
     */
    public function update(AlatRequest $request, Alat $alat): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        $stokTotal = (int) $data['stok_total'];
        $stokRusak = (int) ($data['stok_rusak'] ?? 0);
        $stokDisewa = (int) $alat->stok_disewa;

        $alat->update([
            'kategori_id' => $data['kategori_id'],
            'nama' => $data['nama'],
            'deskripsi' => $data['deskripsi'] ?? null,
            'tarif_per_hari' => $data['tarif_per_hari'],
            'nilai_pengganti' => $data['nilai_pengganti'],
            'stok_total' => $stokTotal,
            'stok_tersedia' => max(0, $stokTotal - $stokDisewa - $stokRusak),
            'stok_rusak' => $stokRusak,
        ]);

        $this->lupakanCache();
        $pesan = "Data alat \"{$alat->nama}\" berhasil diperbarui.";

        if ($request->wantsJson()) {
            return $this->responsJson($alat, $request, $pesan);
        }

        return $this->kembaliKeDaftar($request)->with('success', $pesan);
    }

    /**
     * Hapus alat. Alat yang sudah pernah dipakai pada pemesanan tidak dihapus
     * agar riwayat transaksi tetap utuh.
     */
    public function destroy(Request $request, Alat $alat): RedirectResponse|JsonResponse
    {
        $this->authorize('delete', $alat);

        if ($alat->detailPemesanan()->exists()) {
            $pesan = "Alat \"{$alat->nama}\" tidak bisa dihapus karena sudah dipakai pada pemesanan.";

            if ($request->wantsJson()) {
                return response()->json(['message' => $pesan], 422);
            }

            return $this->kembaliKeDaftar($request)->with('error', $pesan);
        }

        $id = $alat->id;
        $nama = $alat->nama;
        $alat->delete();

        $this->lupakanCache();
        $pesan = "Alat \"{$nama}\" berhasil dihapus.";

        if ($request->wantsJson()) {
            return response()->json([
                'message' => $pesan,
                'id' => $id,
                'ringkasan' => $this->ringkasan(),
            ]);
        }

        return $this->kembaliKeDaftar($request)->with('success', $pesan);
    }

    /**
     * Respons JSON setelah menyimpan data. Baris tabel dikirim dalam bentuk
     * HTML supaya browser cukup mengganti satu <tr> tanpa memuat ulang halaman
     * atau meminta query tambahan ke Supabase.
     */
    private function responsJson(Alat $alat, Request $request, string $pesan): JsonResponse
    {
        $alat->loadMissing('kategori');

        return response()->json([
            'message' => $pesan,
            'id' => $alat->id,
            'baris' => view('admin.peralatan._baris', [
                'alat' => $alat,
                'filters' => $this->filter($request, true),
            ])->render(),
            'item' => $this->dataSatuAlat($alat),
            'ringkasan' => $this->ringkasan(),
        ]);
    }

    /**
     * Kembali ke halaman daftar sambil mempertahankan filter yang aktif.
     */
    private function kembaliKeDaftar(Request $request): RedirectResponse
    {
        $filters = array_filter(
            [
                'search' => $request->input('search'),
                'kategori' => $request->input('kategori'),
                'kondisi' => $request->input('kondisi'),
                'per_page' => $request->input('per_page'),
            ],
            fn ($nilai) => filled($nilai) && $nilai !== 'semua',
        );

        return redirect()->route('admin.peralatan.index', $filters);
    }
}
