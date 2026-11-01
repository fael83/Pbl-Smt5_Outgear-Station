<?php

namespace App\Http\Controllers;

use App\Models\Pemesanan;
use Illuminate\Http\Request;

class BookingSayaController extends Controller
{
    public function __invoke(Request $request)
    {
        // Panggil relasi detailPemesanan.alat (sesuai method di model Alat)
        $bookings = Pemesanan::with(['detailPemesanan.alat'])
            ->where('user_id', auth()->id())
            ->latest()
            ->get();

        return view('customer.booking', compact('bookings'));
    }
}