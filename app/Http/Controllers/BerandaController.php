<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

/**
 * Halaman depan (landing page) OutGear Station.
 */
class BerandaController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome');
    }
}
