<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Ringkasan booking milik pelanggan. Halaman tetap dapat dibuka tanpa login
 * agar navigasi katalog tidak menemui error.
 */
class BookingSayaController extends Controller
{
    public function __invoke(): View
    {
        return view('customer.booking');
    }
}
