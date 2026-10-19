<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Halaman data booking/pemesanan pada panel admin.
 */
class PemesananController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.pemesanan');
    }
}
