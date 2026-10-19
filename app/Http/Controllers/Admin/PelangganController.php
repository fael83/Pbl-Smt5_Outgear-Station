<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Halaman data pelanggan pada panel admin.
 */
class PelangganController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.pelanggan');
    }
}
