<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class StaffController extends Controller
{
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
}