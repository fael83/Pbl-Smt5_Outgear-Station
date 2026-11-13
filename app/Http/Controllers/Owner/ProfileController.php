<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    // Menampilkan profil Owner
    public function index(Request $request): View
    {
        return view('owner.profile', [
            'user' => $request->user(),
        ]);
    }

    // Mengubah informasi profil Owner
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'no_telepon' => [
                'required',
                'string',
                'regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/',
            ],
        ], [
            'no_telepon.regex' => 'Nomor telepon harus menggunakan format nomor seluler Indonesia yang valid.',
        ]);

        $request->user()->update($validated);

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    // Mengubah kata sandi Owner
    public function updatePassword(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'current_password' => ['required', 'current_password'],
            'password' => ['required', 'confirmed', 'min:8'],
        ], [
            'current_password.current_password' => 'Kata sandi lama tidak sesuai.',
            'password.confirmed' => 'Konfirmasi kata sandi baru tidak cocok.',
        ]);

        $request->user()->update([
            'password' => $validated['password'],
        ]);

        return back()->with('success', 'Kata sandi berhasil diperbarui.');
    }
}