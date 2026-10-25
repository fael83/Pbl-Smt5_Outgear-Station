<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AturanDenda;

class DendaController extends Controller
{
    public function aturanDenda()
    {
        $dendaList = AturanDenda::all(); 
        return view('owner.aturan-denda', compact('dendaList'));
    }

    public function updateAturanDenda(Request $request, $id)
    {
        $request->validate([
            'jenis_denda' => 'required|string|max:255',
            'perhitungan_denda' => 'required|string',
            'nilai_denda' => 'required|numeric|min:0',
        ]);

        $denda = AturanDenda::findOrFail($id);
        $denda->jenis_denda = $request->jenis_denda;
        $denda->perhitungan_denda = $request->perhitungan_denda;
        $denda->nilai_denda = $request->nilai_denda;
        $denda->save();

        return redirect('/owner/aturan-denda')->with('success', 'Aturan denda berhasil diperbarui!');
    }
}