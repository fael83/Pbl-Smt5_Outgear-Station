<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

/**
 * Titik masuk setelah login: arahkan pengguna ke halaman sesuai perannya.
 */
class DashboardController extends Controller
{
    public function __invoke(Request $request): RedirectResponse
    {
        // Tim internal (admin, owner, petugas) memakai panel admin,
        // sedangkan pelanggan kembali ke katalog.
        if ($request->user()?->isStaff()) {
            return redirect()->route('admin.dashboard');
        }

        return redirect('/katalog');
    }
}
