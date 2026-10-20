<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Panduan penyewaan alat yang dapat dilihat tanpa login.
 */
class PanduanController extends Controller
{
    public function __invoke(): View
    {
        return view('customer.panduan');
    }
}
