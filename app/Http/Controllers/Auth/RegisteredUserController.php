<?php

namespace App\Http\Controllers\Auth;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     */
    public function store(RegisterRequest $request): RedirectResponse
    {
        // Password otomatis di-hash oleh cast `hashed` pada model User.
        $user = User::create([
            ...$request->validated(),
            'role' => Role::Customer,
            'status_akun' => 'aktif',
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect('/katalog');
    }
}
