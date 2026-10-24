<?php

namespace App\Http\Controllers\Admin;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StorePelangganRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Halaman data pelanggan pada panel admin.
 */
class PelangganController extends Controller
{
    public function __invoke(Request $request): View
    {
        $barisPerHalaman = $request->integer('per_page', 10);
        $barisPerHalaman = in_array($barisPerHalaman, [10, 25, 50], true) ? $barisPerHalaman : 10;
        $status = $request->query('status', 'semua');
        $status = in_array($status, ['aktif', 'nonaktif'], true) ? $status : 'semua';
        $cari = trim((string) $request->query('search', ''));

        $query = User::query()->where('role', Role::Customer);

        if ($status !== 'semua') {
            $query->where('status_akun', $status);
        }

        if ($cari !== '') {
            $query->where(function ($query) use ($cari): void {
                $query->where('name', 'like', "%{$cari}%")
                    ->orWhere('email', 'like', "%{$cari}%")
                    ->orWhere('no_telepon', 'like', "%{$cari}%");
            });
        }

        $totalPelanggan = User::query()->where('role', Role::Customer)->count();

        return view('admin.pelanggan', [
            'pelanggan' => $query->latest()->paginate($barisPerHalaman)->withQueryString(),
            'totalPelanggan' => $totalPelanggan,
            'pelangganAktif' => User::query()->where('role', Role::Customer)->where('status_akun', 'aktif')->count(),
            'pelangganNonaktif' => User::query()->where('role', Role::Customer)->where('status_akun', 'nonaktif')->count(),
            'filters' => [
                'search' => $cari,
                'status' => $status,
                'per_page' => $barisPerHalaman,
            ],
        ]);
    }

    public function store(StorePelangganRequest $request): RedirectResponse
    {
        $data = $request->validated();

        User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'no_telepon' => $data['no_telepon'] ?? null,
            'password' => $data['password'],
            'role' => Role::Customer,
            'status_akun' => 'aktif',
        ]);

        return redirect()
            ->route('admin.pelanggan')
            ->with('success', 'Pelanggan berhasil ditambahkan.');
    }
}
