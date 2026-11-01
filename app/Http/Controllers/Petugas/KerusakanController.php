<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\DetailPengembalian;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class KerusakanController extends Controller
{
    public function index(Request $request): View
    {
        $filters = [
            'search' => $request->get('search'),
            'per_page' => $request->get('per_page', 10),
        ];

        $query = DetailPengembalian::with(['pengembalian.pemesanan.pelanggan', 'detailPemesanan.alat'])
            ->where(function($q) {
                $q->where('jumlah_rusak', '>', 0)
                  ->orWhere('jumlah_hilang', '>', 0);
            });

        $daftarKasus = $query->latest()->paginate((int)$filters['per_page'])->withQueryString();

        $ringkasan = [
            'total_kasus' => DetailPengembalian::where('jumlah_rusak', '>', 0)->orWhere('jumlah_hilang', '>', 0)->count(),
            'total_rusak' => DetailPengembalian::sum('jumlah_rusak'),
            'total_hilang' => DetailPengembalian::sum('jumlah_hilang'),
        ];

        return view('petugas.kerusakan', compact('daftarKasus', 'filters', 'ringkasan'));
    }
}