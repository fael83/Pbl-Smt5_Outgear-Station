<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

/**
 * Halaman pengelolaan denda pada panel admin.
 */
class DendaController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.denda');
    }
}
