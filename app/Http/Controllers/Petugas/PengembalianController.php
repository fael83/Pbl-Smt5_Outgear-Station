<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Pengembalian;
use App\Models\DetailPengembalian;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class PengembalianController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'per_page' => $request->get('per_page', 10),
        ];

        $query = Pengembalian::with([
            'pemesanan.pelanggan',
            'pemesanan.detailPemesanan.alat',
            'petugas',
            'detailPengembalian.detailPemesanan.alat'
        ]);

        if (!empty($filters['search'])) {
            $query->whereHas('pemesanan', function($q) use ($filters) {
                $q->where('id', 'like', '%' . $filters['search'] . '%');
            });
        }

        $daftarPengembalian = $query->latest('waktu_submit')->paginate((int)$filters['per_page'])->withQueryString();

        $ringkasan = [
            'total_pengembalian' => Pengembalian::count(),
            'tepat_waktu' => Pengembalian::where('hari_terlambat', '<=', 0)->count(),
            'terlambat' => Pengembalian::where('hari_terlambat', '>', 0)->count(),
        ];

        return view('petugas.pengembalian', compact('daftarPengembalian', 'filters', 'ringkasan'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pemesanan_id' => 'required|exists:pemesanan,id',
            'waktu_pengambilan' => 'required|date',
            'waktu_submit' => 'required|date',
            'hari_terlambat' => 'required|integer|min:0',
        ]);

        $validated['user_id'] = auth()->id();

        Pengembalian::create($validated);

        return response()->json([
            'pesan' => 'Data pengembalian berhasil dicatat.',
        ]);
    }

    public function updateChecklist(Request $request, $id)
    {
        $pengembalian = Pengembalian::with('pemesanan.detailPemesanan')->findOrFail($id);

        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.detail_pemesanan_id' => 'required|exists:detail_pemesanan,id',
            'items.*.jumlah_baik' => 'required|integer|min:0',
            'items.*.jumlah_rusak' => 'required|integer|min:0',
            'items.*.jumlah_hilang' => 'required|integer|min:0',
            'items.*.alasan_hilang' => 'nullable|string|max:500',
        ]);

        foreach ($validated['items'] as $item) {
            DetailPengembalian::updateOrCreate(
                [
                    'pengembalian_id' => $pengembalian->id,
                    'detail_pemesanan_id' => $item['detail_pemesanan_id'],
                ],
                [
                    'jumlah_baik' => $item['jumlah_baik'],
                    'jumlah_rusak' => $item['jumlah_rusak'],
                    'jumlah_hilang' => $item['jumlah_hilang'],
                    'alasan_hilang' => ($item['jumlah_hilang'] > 0) ? ($item['alasan_hilang'] ?? null) : null,
                ]
            );
        }

        // Jika ada status pemesanan masih 'berlangsung', ubah ke 'selesai'
        if ($pengembalian->pemesanan && $pengembalian->pemesanan->status === 'berlangsung') {
            $pengembalian->pemesanan->update(['status' => 'selesai']);
        }

        return response()->json([
            'pesan' => 'Checklist kondisi fisik barang berhasil disimpan.',
        ]);
    }

    public function destroy($id)
    {
        $pengembalian = Pengembalian::findOrFail($id);
        $pengembalian->delete();

        return response()->json([
            'pesan' => 'Data pengembalian berhasil dihapus.',
        ]);
    }
}