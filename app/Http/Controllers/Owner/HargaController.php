<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Alat;

class HargaController extends Controller
{
    public function hargaSewa(Request $request)
    {
        // Ambil data kategori untuk dropdown filter
        $kategori_alat = Kategori::all();

        $query = Alat::with('kategori');

        if ($request->has('kategori_id') && $request->kategori_id != '') {
            $query->where('kategori_id', $request->kategori_id);
        }

        // Ambil data alat
        $alat = $query->get();
        return view('owner.harga-sewa', compact('kategori_alat', 'alat'));
    }

    public function updateHargaSewa(Request $request, $id)
    {
        $request->validate([
            'tarif_sewa' => 'required|numeric|min:0',
            'nilai_pengganti' => 'required|numeric|min:0',
        ]);

        $alat = Alat::findOrFail($id);
        // Sesuaikan dengan nama kolom asli di database kamu: tarif_per_hari
        $alat->tarif_per_hari = $request->tarif_sewa;
        $alat->nilai_pengganti = $request->nilai_pengganti;
        $alat->save();

        return redirect('/owner/harga-sewa')->with('success', 'Harga sewa dan tarif alat berhasil diperbarui!');
    }
}