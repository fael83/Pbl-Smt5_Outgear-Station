<?php

namespace App\Http\Controllers;

use App\Models\Alat;
use Illuminate\View\View;

class KatalogController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(): View
    {
        $peralatan = Alat::query()
            ->with('kategori')
            ->orderBy('nama')
            ->get();

        return view('customer.katalog', compact('peralatan'));
    }
}
