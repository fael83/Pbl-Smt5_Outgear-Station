<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Alat;

class OwnerController extends Controller
{
    public function hargaSewa(Request $request) // <-- Tambahkan Request $request di sini
    {
        // Ambil data kategori untuk dropdown filter
        $kategori_alat = Kategori::all();
        
        $query = Alat::with('kategori');

        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Ambil data alat
        $alat = $query->get();

        // <-- PERHATIAN: Masukkan 'alat' ke dalam compact() agar terbaca di view
        return view('owner.harga-sewa', compact('kategori_alat', 'alat'));
    }
}