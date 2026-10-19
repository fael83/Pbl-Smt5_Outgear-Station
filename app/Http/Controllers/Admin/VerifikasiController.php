<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Halaman verifikasi pembayaran pada panel admin.
 */
class VerifikasiController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.verifikasi');
    }
}
