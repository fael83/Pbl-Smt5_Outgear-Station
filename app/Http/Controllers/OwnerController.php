<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kategori;
use App\Models\Alat;
use App\Models\User;

class OwnerController extends Controller
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

    public function kelolaStaff(Request $request)
    {
        $query = User::whereIn('role', ['admin', 'petugas']);

        if ($request->has('peran') && $request->peran != '') {
            $query->where('role', $request->peran);
        }

        $stafList = $query->get();

        return view('owner.kelola-staff', compact('stafList'));
    }

    public function createStaff()
    {
        return view('owner.create-staff');
    }

    public function storeStaff(Request $request)
    {
        // Validasi dan simpan data staf baru
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'no_telepon' => 'nullable|string|max:15',
            'role' => 'required|string',
            'password' => 'required|min:8',
        ]);

        $staf = new User();
        $staf->name = $request->name;
        $staf->email = $request->email;
        $staf->no_telepon = $request->no_telepon;
        $staf->role = $request->role;
        $staf->password = bcrypt($request->password);
        $staf->save();

        return redirect('/owner/kelola-staff')->with('success', 'Akun staff baru berhasil ditambahkan!');
    }

    public function editStaff($id)
    {
        $staf = User::findOrFail($id);
        return view('owner.edit-staff', compact('staf'));
    }

    public function updateStaff(Request $request, $id)
    {
        $staf = User::findOrFail($id);

        $staf->name = $request->name;
        $staf->email = $request->email;
        $staf->no_telepon = $request->no_telepon;
        $staf->role = $request->role;

        // Jika password diisi, maka update passwordnya
        if ($request->filled('password')) {
            $staf->password = bcrypt($request->password);
        }

        $staf->save();
        return redirect('/owner/kelola-staff')->with('success', 'Data staff berhasil diperbarui!');
    }

    public function destroyStaff($id)
    {
        $staf = User::findOrFail($id);
        $staf->delete();

        return redirect('/owner/kelola-staff')->with('success', 'Akun staff berhasil dihapus!');
    }

public function aturanDenda()
    {
        $dendaList = \App\Models\AturanDenda::all(); // Sesuaikan nama Model jika berbeda
        return view('owner.aturan-denda', compact('dendaList'));
    }

    public function updateAturanDenda(Request $request, $id)
    {
        $request->validate([
            'jenis_denda' => 'required|string|max:255',
            'perhitungan_denda' => 'required|string',
            'nilai_denda' => 'required|numeric|min:0',
        ]);

        $denda = \App\Models\AturanDenda::findOrFail($id);
        $denda->jenis_denda = $request->jenis_denda;
        $denda->perhitungan_denda = $request->perhitungan_denda;
        $denda->nilai_denda = $request->nilai_denda;
        $denda->save();

        return redirect('/owner/aturan-denda')->with('success', 'Aturan denda berhasil diperbarui!');
    }
}