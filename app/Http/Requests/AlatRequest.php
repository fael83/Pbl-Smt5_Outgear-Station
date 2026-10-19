<?php

namespace App\Http\Requests;

use App\Models\Alat;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

class AlatRequest extends FormRequest
{
    /**
     * Tentukan apakah pengguna berhak menjalankan aksi ini.
     *
     * Store memakai policy `create`, sedangkan update memakai policy `update`
     * pada alat yang sedang diubah. Route middleware `role:admin` menjadi
     * lapis pertama, policy menjadi lapis kedua.
     *
     * Tamu (belum login) tetap diizinkan pada mode demo agar panel admin bisa
     * dipakai tanpa login.
     */
    public function authorize(): bool
    {
        $user = $this->user();

        if ($user === null) {
            return true;
        }

        if ($this->isMethod('post')) {
            return $user->can('create', Alat::class);
        }

        /** @var Alat|null $alat */
        $alat = $this->route('alat');

        return $alat instanceof Alat && $user->can('update', $alat);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        /** @var Alat|null $alat */
        $alat = $this->route('alat');

        return [
            'nama' => [
                'required',
                'string',
                'max:255',
                Rule::unique(Alat::class, 'nama')->ignore($alat?->id),
            ],
            'kategori_id' => ['required', 'integer', 'exists:kategori,id'],
            'deskripsi' => ['nullable', 'string', 'max:1000'],
            'tarif_per_hari' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'nilai_pengganti' => ['required', 'numeric', 'min:0', 'max:999999999'],
            'stok_total' => ['required', 'integer', 'min:0', 'max:100000'],
            'stok_rusak' => ['nullable', 'integer', 'min:0', 'max:100000'],
        ];
    }

    /**
     * Validasi tambahan: stok total tidak boleh lebih kecil dari jumlah unit
     * yang sedang disewa + unit rusak, supaya stok tersedia tidak negatif.
     *
     * @return array<int, callable>
     */
    public function after(): array
    {
        return [
            function (Validator $validator): void {
                if ($validator->errors()->isNotEmpty()) {
                    return;
                }

                /** @var Alat|null $alat */
                $alat = $this->route('alat');

                $stokTotal = (int) $this->input('stok_total', 0);
                $stokRusak = (int) $this->input('stok_rusak', 0);
                $stokDisewa = (int) ($alat?->stok_disewa ?? 0);
                $minimal = $stokDisewa + $stokRusak;

                if ($stokTotal < $minimal) {
                    $validator->errors()->add(
                        'stok_total',
                        'Stok total minimal '.number_format($minimal, 0, ',', '.')
                        .' unit (unit disewa + unit rusak).',
                    );
                }
            },
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
            'nama' => 'nama alat',
            'kategori_id' => 'kategori',
            'deskripsi' => 'deskripsi',
            'tarif_per_hari' => 'tarif sewa per hari',
            'nilai_pengganti' => 'nilai pengganti',
            'stok_total' => 'stok total',
            'stok_rusak' => 'stok rusak',
        ];
    }
}
