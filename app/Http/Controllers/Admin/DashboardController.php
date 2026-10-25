<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Pembayaran;
use App\Models\Pemesanan;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use Illuminate\View\View;

/**
 * Dashboard operasional untuk admin, owner, dan petugas.
 *
 * Mengumpulkan seluruh data yang dipakai resources/views/admin/dashboard.blade.php:
 * statistik, booking terbaru, antrean pembayaran, dan inventaris per kategori.
 */
class DashboardController extends Controller
{
    private const KUNCI_CACHE = 'admin:dashboard:data';

    /** Lama data dashboard disimpan di cache (detik). */
    private const DETIK_CACHE = 30;

    /** Alat dianggap kritis jika stok tersedia <= persentase ini dari stok total. */
    private const BATAS_KRITIS_PERSEN = 20;

    private const JUMLAH_BOOKING_TERBARU = 5;

    private const JUMLAH_ANTREAN_PEMBAYARAN = 4;

    /** Kategori yang ditampilkan di rincian; sisanya digabung menjadi "Lainnya". */
    private const MAKS_KATEGORI = 5;

    public function __invoke(Request $request): View|RedirectResponse
    {
        if ($request->boolean('segarkan')) {
            Cache::forget(self::KUNCI_CACHE);

            return redirect()->route('admin.dashboard');
        }

        $data = Cache::remember(
            self::KUNCI_CACHE,
            now()->addSeconds(self::DETIK_CACHE),
            fn (): array => $this->kumpulkanData(),
        );

        return view('admin.dashboard', $data);
    }

    /**
     * @return array<string, mixed>
     */
    private function kumpulkanData(): array
    {
        $hariIni = Carbon::today();
        $besok = $hariIni->copy()->addDay();

        // Kolom bertipe date dibandingkan dengan 'Y-m-d', kolom datetime dengan 'Y-m-d H:i:s'.
        $tanggalHariIni = $hariIni->toDateString();
        $tanggalBesok = $besok->toDateString();
        $waktuHariIni = $hariIni->toDateTimeString();
        $waktuBesok = $besok->toDateTimeString();

        // Query 1: hitungan pemesanan.
        $pemesanan = Pemesanan::query()
            ->selectRaw("
                sum(case when status in ('menunggu_pembayaran', 'dikonfirmasi', 'berlangsung') then 1 else 0 end) as aktif,
                sum(case when status in ('dikonfirmasi', 'berlangsung') and tanggal_mulai >= ? and tanggal_mulai < ? then 1 else 0 end) as ambil_hari_ini
            ", [$tanggalHariIni, $tanggalBesok])
            ->first();

        // Query 2: pembayaran (antrean verifikasi dan pendapatan hari ini).
        $pembayaran = Pembayaran::query()
            ->selectRaw("
                sum(case when status_transaksi = 'pending' then 1 else 0 end) as menunggu,
                sum(case when status_transaksi = 'berhasil' and waktu_bayar >= ? and waktu_bayar < ? then 1 else 0 end) as berhasil_hari_ini,
                sum(case when status_transaksi = 'berhasil' and waktu_bayar >= ? and waktu_bayar < ? then jumlah else 0 end) as pendapatan_hari_ini
            ", [$waktuHariIni, $waktuBesok, $waktuHariIni, $waktuBesok])
            ->first();

        // Query 3: booking terbaru (nama pelanggan lewat join, tanpa query tambahan).
        $bookingTerbaru = Pemesanan::query()
            ->join('users', 'users.id', '=', 'pemesanan.user_id')
            ->select([
                'pemesanan.id',
                'pemesanan.user_id',
                'pemesanan.kode_pemesanan',
                'pemesanan.tanggal_mulai',
                'pemesanan.tanggal_selesai',
                'pemesanan.status',
                'pemesanan.total_biaya',
                'users.name as nama_pelanggan',
            ])
            ->orderByDesc('pemesanan.created_at')
            ->orderByDesc('pemesanan.id')
            ->limit(self::JUMLAH_BOOKING_TERBARU)
            ->get()
            ->map(fn (Pemesanan $item): array => [
                'user_id' => (int) $item->user_id,
                'kode' => $item->kode_pemesanan,
                'pelanggan' => $item->nama_pelanggan,
                'inisial' => $this->inisial($item->nama_pelanggan),
                'jadwal' => $this->teksJadwal($item->tanggal_mulai, $item->tanggal_selesai),
                'status' => $item->status,
                'label_status' => PemesananController::LABEL_STATUS[$item->status] ?? $item->status,
                'total' => (float) $item->total_biaya,
            ])
            ->all();

        // Query 4: antrean pembayaran yang menunggu verifikasi (yang paling lama di atas).
        $antreanPembayaran = Pembayaran::query()
            ->join('pemesanan', 'pemesanan.id', '=', 'pembayaran.pemesanan_id')
            ->join('users', 'users.id', '=', 'pemesanan.user_id')
            ->where('pembayaran.status_transaksi', 'pending')
            ->select([
                'pembayaran.id',
                'pembayaran.jenis_pembayaran',
                'pembayaran.metode_pembayaran',
                'pembayaran.jumlah',
                'users.name as nama_pelanggan',
            ])
            ->orderBy('pembayaran.created_at')
            ->orderBy('pembayaran.id')
            ->limit(self::JUMLAH_ANTREAN_PEMBAYARAN)
            ->get()
            ->map(fn (Pembayaran $item): array => [
                'pelanggan' => $item->nama_pelanggan,
                'jenis' => ['dp' => 'DP', 'pelunasan' => 'Pelunasan', 'denda' => 'Denda'][$item->jenis_pembayaran] ?? $item->jenis_pembayaran,
                'metode' => ['qris' => 'QRIS', 'card' => 'Kartu'][$item->metode_pembayaran] ?? strtoupper((string) $item->metode_pembayaran),
                'jumlah' => (float) $item->jumlah,
            ])
            ->all();

        // Query 5: stok alat per kategori (juga dipakai untuk donut dan kartu stok kritis).
        $perKategori = Alat::query()
            ->join('kategori', 'kategori.id', '=', 'alat.kategori_id')
            ->selectRaw('
                kategori.nama as nama_kategori,
                sum(alat.stok_total) as total,
                sum(alat.stok_disewa) as disewa,
                sum(alat.stok_tersedia) as tersedia,
                sum(alat.stok_rusak) as rusak,
                sum(case when alat.stok_total > 0 and alat.stok_tersedia * 100 <= alat.stok_total * ? then 1 else 0 end) as kritis
            ', [self::BATAS_KRITIS_PERSEN])
            ->groupBy('kategori.id', 'kategori.nama')
            ->orderByDesc('total')
            ->get();

        return [
            'statistik' => [
                'booking_aktif' => (int) $pemesanan->aktif,
                'ambil_hari_ini' => (int) $pemesanan->ambil_hari_ini,
                'perlu_verifikasi' => (int) $pembayaran->menunggu,
                'pendapatan_hari_ini' => (float) $pembayaran->pendapatan_hari_ini,
                'transaksi_hari_ini' => (int) $pembayaran->berhasil_hari_ini,
                'stok_kritis' => (int) $perKategori->sum('kritis'),
                'batas_kritis_persen' => self::BATAS_KRITIS_PERSEN,
            ],
            'bookingTerbaru' => $bookingTerbaru,
            'antreanPembayaran' => $antreanPembayaran,
            'inventaris' => $this->susunInventaris($perKategori),
            'diperbaruiPada' => Carbon::now()->format('H:i'),
        ];
    }

    /**
     * Menyusun data donut (segmen) dan rincian per kategori.
     * @param  Collection<int, Alat>  $perKategori
     * @return array<string, mixed>
     */
    private function susunInventaris(Collection $perKategori): array
    {
        $total = (int) $perKategori->sum('total');
        $disewa = (int) $perKategori->sum('disewa');
        $tersedia = (int) $perKategori->sum('tersedia');
        $rusak = (int) $perKategori->sum('rusak');

        // Segmen donut SVG: panjang dan posisi dihitung dalam persen dari keliling lingkaran.
        $segmen = [];
        $geser = 0.0;

        foreach ([
            ['label' => 'Sedang Disewa', 'nilai' => $disewa, 'warna' => '#EA580C'],
            ['label' => 'Tersedia di Basecamp', 'nilai' => $tersedia, 'warna' => '#E4E4E7'],
            ['label' => 'Rusak', 'nilai' => $rusak, 'warna' => '#E11D48'],
        ] as $bagian) {
            if ($bagian['nilai'] <= 0 || $total <= 0) {
                continue;
            }

            $persen = round($bagian['nilai'] / $total * 100, 2);
            $segmen[] = $bagian + ['persen' => $persen, 'geser' => round($geser, 2)];
            $geser += $persen;
        }

        $kategori = $perKategori->map(fn ($item): array => [
            'nama' => $item->nama_kategori,
            'disewa' => (int) $item->disewa,
            'total' => (int) $item->total,
        ]);

        if ($kategori->count() > self::MAKS_KATEGORI) {
            $sisa = $kategori->slice(self::MAKS_KATEGORI);

            $kategori = $kategori
                ->take(self::MAKS_KATEGORI)
                ->push([
                    'nama' => 'Lainnya',
                    'disewa' => (int) $sisa->sum('disewa'),
                    'total' => (int) $sisa->sum('total'),
                ]);
        }

        return [
            'total' => $total,
            'disewa' => $disewa,
            'tersedia' => $tersedia,
            'rusak' => $rusak,
            'segmen' => $segmen,
            'kategori' => $kategori->values()->all(),
        ];
    }

    private function inisial(string $nama): string
    {
        return Str::of($nama)
            ->explode(' ')
            ->filter()
            ->take(2)
            ->map(fn (string $kata): string => Str::upper(Str::substr($kata, 0, 1)))
            ->implode('');
    }

    private function teksJadwal(Carbon $mulai, Carbon $selesai): string
    {
        $mulai = $mulai->copy()->locale('id');
        $selesai = $selesai->copy()->locale('id');

        return $mulai->translatedFormat('d M').' - '.$selesai->translatedFormat('d M Y');
    }
}