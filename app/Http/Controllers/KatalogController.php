<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Katalog alat yang dapat dilihat publik maupun pelanggan.
 */
class KatalogController extends Controller
{
    public function __invoke(): View
    {
        return view('customer.katalog');
    }
}
