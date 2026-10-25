@extends('layouts.panel', ['title' => 'Data Pelanggan'])

@php
    $filterAwal = [
        'search' => $filters['search'],
        'status' => $filters['status'],
        'per_page' => (string) $filters['per_page'],
    ];

    // Tab status di atas tabel. `url` menjadi fallback saat JavaScript mati,
    // sedangkan klik normal ditangani Alpine lewat pilihStatus().
    $tabStatus = collect([
        ['kunci' => 'semua', 'label' => 'Semua', 'kunci_ringkasan' => 'total_pelanggan'],
        ['kunci' => 'aktif', 'label' => 'Aktif', 'kunci_ringkasan' => 'pelanggan_aktif'],
        ['kunci' => 'nonaktif', 'label' => 'Nonaktif', 'kunci_ringkasan' => 'pelanggan_nonaktif'],
    ])->map(function (array $tab) use ($filters): array {
        $tab['url'] = route('admin.pelanggan', array_filter([
            'search' => $filters['search'],
            'status' => $tab['kunci'] === 'semua' ? null : $tab['kunci'],
            'per_page' => $filters['per_page'],
        ], static fn ($nilai): bool => $nilai !== null && $nilai !== ''));

        return $tab;
    })->all();

    // Tautan ekspor mengikuti filter yang sedang aktif (fallback tanpa JS).
    $tautanEkspor = route('admin.pelanggan.ekspor', array_filter([
        'search' => $filters['search'],
        'status' => $filters['status'] !== 'semua' ? $filters['status'] : null,
    ], static fn ($nilai): bool => filled($nilai)));
@endphp

@section('content')
    <div class="space-y-6"
         x-data="halamanPelanggan({
            tabelUrl: {{ Js::from(route('admin.pelanggan.tabel')) }},
            eksporUrl: {{ Js::from(route('admin.pelanggan.ekspor')) }},
            createUrl: {{ Js::from(route('admin.pelanggan.store')) }},
            updateUrlTemplate: {{ Js::from(route('admin.pelanggan.update', ['pelanggan' => '__ID__'])) }},
            items: {{ Js::from($dataPelanggan) }},
            filters: {{ Js::from($filterAwal) }},
            ringkasan: {{ Js::from($ringkasan) }},
            modalTerbuka: {{ $errors->hasAny(['name', 'email', 'no_telepon', 'password']) ? 'true' : 'false' }},
            formMode: {{ Js::from(old('form_mode', 'create')) }},
            oldValues: {{ Js::from([
                'id' => old('pelanggan_id'),
                'name' => old('name', ''),
                'email' => old('email', ''),
                'no_telepon' => old('no_telepon', ''),
            ]) }}
         })">

        <!-- Judul & Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black font-heading text-[#1A2024] tracking-tight">Data Pelanggan</h1>
                <p class="text-xs text-gray-500 mt-1 max-w-2xl">
                    Daftar akun pelanggan OutGear Station beserta status akun dan jumlah booking.
                    Semua angka di halaman ini dibaca langsung dari database.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="bukaTambah()"
                        class="px-4 py-2.5 bg-[#D86B43] text-white text-xs font-bold rounded-xl hover:bg-[#D86B43]/90 transition shadow-sm">
                    + Tambah Pelanggan
                </button>
            </div>
        </div>

        @if (session('success'))
            <div role="status" class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
                {{ session('success') }}
            </div>
        @endif
        @if (session('error'))
            <div role="alert" class="rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-xs font-semibold text-red-700">
                {{ session('error') }}
            </div>
        @endif

        <!-- Kartu Statistik (dihitung dari tabel users) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-3xl font-black font-heading text-[#1A2024]"
                           x-text="ringkasanData.total_pelanggan">{{ $ringkasan['total_pelanggan'] }}</p>
                        <p class="text-[11px] font-semibold text-gray-400 mt-1">Total Pelanggan</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-gray-100 text-gray-600 border border-gray-200 whitespace-nowrap">
                        +<span x-text="ringkasanData.pelanggan_baru">{{ $ringkasan['pelanggan_baru'] }}</span> bulan ini
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-3xl font-black font-heading text-emerald-600"
                           x-text="ringkasanData.pelanggan_aktif">{{ $ringkasan['pelanggan_aktif'] }}</p>
                        <p class="text-[11px] font-semibold text-gray-400 mt-1">Akun Aktif</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 whitespace-nowrap"
                          x-text="rasio(ringkasanData.pelanggan_aktif, ringkasanData.total_pelanggan) + '% aktif'">
                        {{ $ringkasan['total_pelanggan'] > 0 ? round($ringkasan['pelanggan_aktif'] / $ringkasan['total_pelanggan'] * 100, 1) : 0 }}% aktif
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-3xl font-black font-heading text-[#D86B43]"
                           x-text="ringkasanData.pelanggan_nonaktif">{{ $ringkasan['pelanggan_nonaktif'] }}</p>
                        <p class="text-[11px] font-semibold text-gray-400 mt-1">Akun Nonaktif</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-red-50 text-red-600 border border-red-200 whitespace-nowrap"
                          x-text="rasio(ringkasanData.pelanggan_nonaktif, ringkasanData.total_pelanggan) + '% dari total'">
                        {{ $ringkasan['total_pelanggan'] > 0 ? round($ringkasan['pelanggan_nonaktif'] / $ringkasan['total_pelanggan'] * 100, 1) : 0 }}% dari total
                    </span>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 p-5 shadow-sm">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <p class="text-3xl font-black font-heading text-[#15AEEA]"
                           x-text="ringkasanData.pelanggan_baru">{{ $ringkasan['pelanggan_baru'] }}</p>
                        <p class="text-[11px] font-semibold text-gray-400 mt-1">Pelanggan Baru</p>
                    </div>
                    <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-sky-50 text-sky-700 border border-sky-200 whitespace-nowrap">
                        Bulan berjalan
                    </span>
                </div>
            </div>
        </div>


        <!-- Tabel Data Pelanggan (diperbarui lewat AJAX, tanpa reload halaman) -->
        <div class="relative">
            <div x-ref="tabel" @click="tangkapKlik($event)" :class="memuat ? 'opacity-60' : ''" class="transition-opacity">
                @include('admin.pelanggan._tabel')
            </div>
        <!-- Notifikasi ringan setelah status akun diubah -->
        <div x-cloak x-show="pesan" x-transition.opacity
             class="fixed bottom-6 right-6 z-[60] max-w-sm px-4 py-3 rounded-xl shadow-lg text-xs font-semibold"
             :class="pesanGagal ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100'">
            <span x-text="pesan"></span>
        </div>

        <!-- Form pelanggan baru -->
        <div x-cloak x-show="modalTerbuka" x-transition.opacity
             class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-[#1A2024]/50 p-4 md:p-8"
             @keydown.escape.window="modalTerbuka = false">
            <div class="w-full max-w-xl rounded-2xl bg-white shadow-xl" @click.outside="modalTerbuka = false">
                <div class="flex items-start justify-between gap-4 border-b border-gray-100 p-5">
                    <div>
                        <h2 class="text-lg font-bold text-[#1A2024]"
                            x-text="formMode === 'edit' ? 'Edit Pelanggan' : 'Tambah Pelanggan'">Tambah Pelanggan</h2>
                        <p class="mt-0.5 text-xs text-gray-500"
                           x-text="formMode === 'edit' ? 'Perbarui data akun pelanggan.' : 'Buat akun pelanggan baru yang tersimpan di database.'">
                            Buat akun pelanggan baru yang tersimpan di database.
                        </p>
                    </div>
                    <button type="button" @click="modalTerbuka = false"
                            class="h-8 w-8 rounded-lg bg-gray-100 text-gray-500 transition hover:bg-gray-200"
                            aria-label="Tutup form">✕</button>
                </div>

                <form method="POST" action="{{ route('admin.pelanggan.store') }}" x-bind:action="formAction" class="space-y-4 p-5">
                    @csrf
                    <input type="hidden" name="_method" value="PUT" x-bind:disabled="formMode !== 'edit'">
                    <input type="hidden" name="form_mode" x-bind:value="formMode">
                    <input type="hidden" name="pelanggan_id" x-bind:value="nilaiForm.id">
                    <input type="hidden" name="search" value="{{ $filters['search'] }}">
                    <input type="hidden" name="status" value="{{ $filters['status'] }}">
                    <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">

                    @if ($errors->hasAny(['name', 'email', 'no_telepon', 'password']))
                        <div class="space-y-1 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-xs text-red-700">
                            <p class="font-semibold">Periksa kembali data pada kolom yang ditandai.</p>
                        </div>
                    @endif

                    <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                        <div class="md:col-span-2">
                            <label for="name" class="mb-1 block text-xs font-semibold text-[#1A2024]">Nama pelanggan</label>
                            <input id="name" name="name" type="text" value="{{ old('name') }}" x-model="nilaiForm.name" required maxlength="255" autocomplete="name"
                                   class="w-full rounded-xl border border-gray-200 bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            @error('name')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="email" class="mb-1 block text-xs font-semibold text-[#1A2024]">Email</label>
                            <input id="email" name="email" type="email" value="{{ old('email') }}" x-model="nilaiForm.email" required maxlength="255" autocomplete="email"
                                   class="w-full rounded-xl border border-gray-200 bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            @error('email')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="no_telepon" class="mb-1 block text-xs font-semibold text-[#1A2024]">Nomor telepon <span class="font-normal text-gray-400">(opsional)</span></label>
                            <input id="no_telepon" name="no_telepon" type="tel" value="{{ old('no_telepon') }}" x-model="nilaiForm.no_telepon" maxlength="30" autocomplete="tel"
                                   class="w-full rounded-xl border border-gray-200 bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            @error('no_telepon')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="formMode === 'create'">
                            <label for="password" class="mb-1 block text-xs font-semibold text-[#1A2024]">Kata sandi</label>
                            <input id="password" name="password" type="password" required minlength="8" x-bind:disabled="formMode === 'edit'" autocomplete="new-password"
                                   class="w-full rounded-xl border border-gray-200 bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            @error('password')
                                <p class="mt-1 text-[10px] text-red-600">{{ $message }}</p>
                            @enderror
                        </div>

                        <div x-show="formMode === 'create'">
                            <label for="password_confirmation" class="mb-1 block text-xs font-semibold text-[#1A2024]">Konfirmasi kata sandi</label>
                            <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" x-bind:disabled="formMode === 'edit'" autocomplete="new-password"
                                   class="w-full rounded-xl border border-gray-200 bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                        </div>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                        <button type="button" @click="modalTerbuka = false"
                                class="rounded-xl bg-gray-100 px-4 py-2.5 text-xs font-semibold text-[#1A2024] transition hover:bg-gray-200">Batal</button>
                        <button type="submit"
                                class="rounded-xl bg-[#D86B43] px-4 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#D86B43]/90"
                                x-text="formMode === 'edit' ? 'Simpan Perubahan' : 'Simpan Pelanggan'">Simpan Pelanggan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
