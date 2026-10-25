<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class LaporanController extends Controller
{
    public function __invoke(Request $request)
    {
        // Logika tampilan laporan di sini
        return view('admin.laporan');
    }

    public function exportExcel(Request $request)
    {
        // Logika export Excel...
    }

    public function cetakPdf(Request $request)
    {
        // Logika cetak PDF...
    }
}