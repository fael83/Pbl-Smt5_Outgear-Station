<?php

namespace App\Http\Requests\Admin;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StorePemesananRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => [
                'required',
                'integer',
                Rule::exists(User::class, 'id')
                    ->where('role', Role::Customer->value)
                    ->where('status_akun', 'aktif'),
            ],
            'tanggal_mulai' => ['required', 'date', 'after_or_equal:today'],
            'tanggal_selesai' => ['required', 'date', 'after_or_equal:tanggal_mulai'],
            'jumlah_dp' => ['nullable', 'numeric', 'min:0', 'max:9999999999'],
            'items' => ['required', 'array'],
            'items.*' => ['required', 'integer', 'min:0', 'max:100000'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'pelanggan_id' => 'pelanggan',
            'tanggal_mulai' => 'tanggal mulai sewa',
            'tanggal_selesai' => 'tanggal selesai sewa',
            'jumlah_dp' => 'jumlah DP',
            'items' => 'item sewa',
        ];
    }
}
