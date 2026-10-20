<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Halaman laporan pada panel admin.
 */
class LaporanController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.laporan');
    }
}
