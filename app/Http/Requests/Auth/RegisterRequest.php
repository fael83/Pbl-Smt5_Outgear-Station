<?php

namespace App\Http\Requests\Auth;

use App\Models\User;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules;

class RegisterRequest extends FormRequest
{
    /**
     * Pendaftaran terbuka untuk umum.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Aturan validasi pendaftaran akun pelanggan.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)],
            // Nomor Indonesia: +62/62/0 lalu 8 dan 7-12 digit berikutnya.
            'no_telepon' => ['required', 'string', 'regex:/^(\+62|62|0)8[1-9][0-9]{6,11}$/'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ];
    }

    /**
     * Pesan validasi dalam bahasa Indonesia.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'no_telepon.regex' => 'Nomor telepon harus diawali +62, 62, atau 0 dan berupa nomor seluler Indonesia yang valid.',
        ];
    }

    /**
     * Nama atribut yang dipakai pada pesan validasi.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'name' => 'nama',
            'email' => 'email',
            'no_telepon' => 'nomor telepon',
            'password' => 'kata sandi',
        ];
    }
}
