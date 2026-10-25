<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\SerahTerima;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class PersiapanController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'per_page' => $request->get('per_page', 10),
        ];

        $query = SerahTerima::with(['pemesanan.pelanggan', 'petugas']);

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_ktp', 'ilike', '%' . $filters['search'] . '%')
                  ->orWhere('nomor_ktp', 'ilike', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status_serah_terima', $filters['status']);
        }

        $daftarPersiapan = $query->latest('waktu_serah_terima')->paginate((int)$filters['per_page'])->withQueryString();

        $ringkasan = [
            'total_agenda' => SerahTerima::count(),
            'siap_diserahkan' => SerahTerima::where('status_serah_terima', 'diambil')->count(), // <-- Diubah jadi 'diambil'
            'sudah_dikembalikan' => SerahTerima::where('status_serah_terima', 'dikembalikan')->count(),
        ];

        return view('petugas.persiapan', compact('daftarPersiapan', 'filters', 'ringkasan'));
    }

    public function tabel(Request $request)
    {
        $filters = [
            'search' => $request->get('search'),
            'status' => $request->get('status'),
            'per_page' => $request->get('per_page', 10),
        ];

        $query = SerahTerima::with(['pemesanan.pelanggan', 'petugas']);

        if (!empty($filters['search'])) {
            $query->where(function($q) use ($filters) {
                $q->where('nama_ktp', 'ilike', '%' . $filters['search'] . '%')
                  ->orWhere('nomor_ktp', 'ilike', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['status'])) {
            $query->where('status_serah_terima', $filters['status']);
        }

        $daftarPersiapan = $query->latest('waktu_serah_terima')->paginate((int)$filters['per_page'])->withQueryString();

        return view('petugas._tabel-persiapan', compact('daftarPersiapan', 'filters'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'pemesanan_id' => 'required|exists:pemesanan,id',
            'nama_ktp' => 'required|string|max:255',
            'nomor_ktp' => 'required|string|max:50',
            'waktu_serah_terima' => 'required|date',
            'status_serah_terima' => 'required|in:diambil,dikembalikan',
        ]);

        $validated['user_id'] = auth()->id();

        SerahTerima::create($validated);

        $ringkasan = [
            'total_agenda' => SerahTerima::count(),
            'siap_diserahkan' => SerahTerima::where('status_serah_terima', 'diambil')->count(), // <-- Diubah jadi 'diambil'
            'sudah_dikembalikan' => SerahTerima::where('status_serah_terima', 'dikembalikan')->count(),
        ];

        return response()->json([
            'pesan' => 'Data serah terima berhasil ditambahkan.',
            'ringkasan' => $ringkasan
        ]);
    }

    public function update(Request $request, $id)
    {
        $serahTerima = SerahTerima::findOrFail($id);

        $validated = $request->validate([
            'pemesanan_id' => 'required|exists:pemesanan,id',
            'nama_ktp' => 'required|string|max:255',
            'nomor_ktp' => 'required|string|max:50',
            'waktu_serah_terima' => 'required|date',
            'status_serah_terima' => 'required|in:diambil,dikembalikan',
        ]);

        $serahTerima->update($validated);

        $ringkasan = [
            'total_agenda' => SerahTerima::count(),
            'siap_diserahkan' => SerahTerima::where('status_serah_terima', 'diambil')->count(),
            'sudah_dikembalikan' => SerahTerima::where('status_serah_terima', 'dikembalikan')->count(),
        ];

        return response()->json([
            'pesan' => 'Data berhasil diproses.',
            'ringkasan' => $ringkasan
        ]);
    }

    public function destroy($id)
    {
        $serahTerima = SerahTerima::findOrFail($id);
        $serahTerima->delete();

        $ringkasan = [
            'total_agenda' => SerahTerima::count(),
            'siap_diserahkan' => SerahTerima::where('status_serah_terima', 'diambil')->count(),
            'sudah_dikembalikan' => SerahTerima::where('status_serah_terima', 'dikembalikan')->count(),
        ];

        return response()->json([
            'pesan' => 'Data serah terima berhasil dihapus.',
            'ringkasan' => $ringkasan
        ]);
    }
}