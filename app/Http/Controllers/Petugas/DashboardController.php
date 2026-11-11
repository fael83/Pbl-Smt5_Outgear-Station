<?php

namespace App\Http\Controllers\Petugas;

use App\Http\Controllers\Controller;
use App\Models\Alat;
use App\Models\Pemesanan;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $hariIni = now()->toDateString();
        $petugasId = auth()->id(); // ID petugas yang sedang login

        // 1. Jadwal Ambil Hari Ini (bisa difilter berdasarkan petugas jika diperlukan, atau seluruh jadwal gudang)
        $ambilHariIni = Pemesanan::with('pelanggan')
            ->whereDate('tanggal_mulai', $hariIni)
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal_mulai')
            ->get();

        // 2. Jadwal Kembali Hari Ini
        $kembaliHariIni = Pemesanan::with('pelanggan')
            ->whereDate('tanggal_selesai', $hariIni)
            ->where('status', '!=', 'dibatalkan')
            ->orderBy('tanggal_selesai')
            ->get();

        // 3. Alat Rusak
        $alatRusak = Alat::where('stok_rusak', '>', 0)
            ->orderByDesc('stok_rusak')
            ->get();

        // Ringkasan untuk kotak metrik di atas
        $ringkasan = [
            'total_ambil' => $ambilHariIni->count(),
            'total_kembali' => $kembaliHariIni->count(),
            'total_rusak' => Alat::sum('stok_rusak'),
        ];

        return view('petugas.dashboard', compact(
            'ambilHariIni',
            'kembaliHariIni',
            'alatRusak',
            'ringkasan'
        ));
    }
    
    public function profile(): View
    {
        return view('petugas.profile');
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . auth()->id(),
            'no_telepon' => 'nullable|string|max:20',
        ]);

        $user = auth()->user();
        $user->name = $request->name;
        $user->email = $request->email;
        $user->no_telepon = $request->no_telepon;
        
        if ($request->filled('password')) {
            $request->validate([
                'password' => 'min:8|confirmed',
            ]);
            $user->password = bcrypt($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui!');
    }
}