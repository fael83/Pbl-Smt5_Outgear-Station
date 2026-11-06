<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

/**
 * Halaman profil pada panel admin (juga dipakai owner).
 *
 * Seluruh isi halaman dibaca dari akun yang sedang login (tabel `users`);
 * perubahan data diri dan kata sandi disimpan kembali ke tabel yang sama.
 */
class ProfilController extends Controller
{
    public function show(Request $request): View|RedirectResponse
    {
        $pengguna = $request->user();

        // Petugas punya halaman profil sendiri.
        if (($pengguna->role?->value ?? $pengguna->role) === 'petugas') {
            return redirect()->route('petugas.profile');
        }

        return view('admin.profile.index', ['pengguna' => $pengguna]);
    }

    /**
     * Simpan nama, email, dan nomor telepon.
     */
    public function update(Request $request): RedirectResponse
    {
        $pengguna = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email,'.$pengguna->id],
            'no_telepon' => ['nullable', 'string', 'max:20'],
        ]);

        $pengguna->fill($data);

        if ($pengguna->isDirty('email')) {
            $pengguna->email_verified_at = null;
        }

        $pengguna->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Ganti kata sandi (wajib menyebut sandi saat ini).
     */
    public function ubahSandi(Request $request): RedirectResponse
    {
        $data = $request->validateWithBag('sandi', [
            'sandi_saat_ini' => ['required', 'current_password'],
            'sandi_baru' => ['required', 'confirmed', Password::min(8)],
        ]);

        $request->user()->forceFill([
            'password' => Hash::make($data['sandi_baru']),
        ])->save();

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}
