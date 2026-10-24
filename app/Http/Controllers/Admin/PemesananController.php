<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePemesananRequest;
use App\Models\Alat;
use App\Models\Pemesanan;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Halaman data booking/pemesanan pada panel admin.
 */
class PemesananController extends Controller
{
    public function __invoke(Request $request): View
    {
        $barisPerHalaman = $request->integer('per_page', 10);
        $barisPerHalaman = in_array($barisPerHalaman, [10, 25, 50], true) ? $barisPerHalaman : 10;
        $status = $request->query('status', 'semua');
        $status = in_array($status, [
            'menunggu_pembayaran',
            'dikonfirmasi',
            'berlangsung',
            'selesai',
            'dibatalkan',
        ], true) ? $status : 'semua';
        $cari = trim((string) $request->query('search', ''));

        $query = Pemesanan::query()->with(['pelanggan', 'detailPemesanan.alat']);

        if ($status !== 'semua') {
            $query->where('status', $status);
        }

        if ($cari !== '') {
            $query->where(function ($query) use ($cari): void {
                $query->where('kode_pemesanan', 'like', "%{$cari}%")
                    ->orWhereHas('pelanggan', function ($pelangganQuery) use ($cari): void {
                        $pelangganQuery->where('name', 'like', "%{$cari}%")
                            ->orWhere('no_telepon', 'like', "%{$cari}%")
                            ->orWhere('email', 'like', "%{$cari}%");
                    })
                    ->orWhereHas('detailPemesanan.alat', function ($alatQuery) use ($cari): void {
                        $alatQuery->where('nama', 'like', "%{$cari}%");
                    });
            });
        }

        return view('admin.pemesanan', [
            'pemesanan' => $query->latest()->paginate($barisPerHalaman)->withQueryString(),
            'pelangganAktif' => User::query()
                ->where('role', 'customer')
                ->where('status_akun', 'aktif')
                ->orderBy('name')
                ->get(),
            'alatTersedia' => Alat::query()
                ->where('stok_tersedia', '>', 0)
                ->orderBy('nama')
                ->get(),
            'jumlahAktif' => Pemesanan::query()
                ->whereIn('status', ['dikonfirmasi', 'berlangsung'])
                ->count(),
            'jumlahMenungguPembayaran' => Pemesanan::query()
                ->where('status', 'menunggu_pembayaran')
                ->count(),
            'jumlahBerlangsung' => Pemesanan::query()
                ->where('status', 'berlangsung')
                ->count(),
            'jumlahSelesaiBulanIni' => Pemesanan::query()
                ->where('status', 'selesai')
                ->whereMonth('tanggal_selesai', now()->month)
                ->whereYear('tanggal_selesai', now()->year)
                ->count(),
            'filters' => [
                'search' => $cari,
                'status' => $status,
                'per_page' => $barisPerHalaman,
            ],
        ]);
    }

    public function store(StorePemesananRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $jumlahItem = collect($data['items'])
            ->map(fn (string|int $jumlah): int => (int) $jumlah)
            ->filter(fn (int $jumlah): bool => $jumlah > 0);

        if ($jumlahItem->isEmpty()) {
            throw ValidationException::withMessages([
                'items' => 'Pilih setidaknya satu peralatan untuk pemesanan.',
            ]);
        }

        $alatTerpilih = DB::transaction(function () use ($data, $jumlahItem): Pemesanan {
            $alat = Alat::query()
                ->whereKey($jumlahItem->keys())
                ->orderBy('id')
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            foreach ($jumlahItem as $alatId => $jumlah) {
                $itemAlat = $alat->get($alatId);

                if ($itemAlat === null || $jumlah > $itemAlat->stok_tersedia) {
                    throw ValidationException::withMessages([
                        "items.{$alatId}" => 'Jumlah peralatan melebihi stok yang tersedia.',
                    ]);
                }
            }

            $lamaSewa = (int) Carbon::parse($data['tanggal_mulai'])
                ->diffInDays(Carbon::parse($data['tanggal_selesai'])) + 1;
            $totalBiaya = $jumlahItem->sum(
                fn (int $jumlah, int|string $alatId): float => $alat->get($alatId)->tarif_per_hari * $jumlah * $lamaSewa,
            );
            $jumlahDp = (float) ($data['jumlah_dp'] ?? 0);

            if ($jumlahDp > $totalBiaya) {
                throw ValidationException::withMessages([
                    'jumlah_dp' => 'Jumlah DP tidak boleh melebihi total biaya pemesanan.',
                ]);
            }

            $pemesanan = Pemesanan::create([
                'kode_pemesanan' => 'OG-'.Str::upper((string) Str::ulid()),
                'user_id' => $data['pelanggan_id'],
                'tanggal_mulai' => $data['tanggal_mulai'],
                'tanggal_selesai' => $data['tanggal_selesai'],
                'total_biaya' => $totalBiaya,
                'jumlah_dp' => $jumlahDp,
                'status' => 'menunggu_pembayaran',
            ]);

            foreach ($jumlahItem as $alatId => $jumlah) {
                $itemAlat = $alat->get($alatId);

                $pemesanan->detailPemesanan()->create([
                    'alat_id' => $itemAlat->id,
                    'jumlah' => $jumlah,
                    'tarif_per_hari' => $itemAlat->tarif_per_hari,
                    'subtotal' => $jumlah * $itemAlat->tarif_per_hari * $lamaSewa,
                ]);
            }

            return $pemesanan;
        });

        return redirect()
            ->route('admin.pemesanan')
            ->with('success', "Pemesanan {$alatTerpilih->kode_pemesanan} berhasil ditambahkan.");
    }
}
