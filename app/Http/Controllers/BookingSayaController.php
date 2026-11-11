<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use Illuminate\Http\Request;

class BookingSayaController extends Controller
{
    public function __invoke(Request $request)
    {
        $user = auth()->user();

        try {
            // Hanya ambil pemesanan milik akun pengguna yang sedang login
            $bookings = $user
                ? Pemesanan::with(['detailPemesanan.alat', 'pelanggan'])
                    ->where('user_id', $user->id)
                    ->latest()
                    ->get()
                : collect();
        } catch (\Throwable) {
            $bookings = collect();
        }

        return view('customer.booking', compact('bookings', 'user'));
    }
}