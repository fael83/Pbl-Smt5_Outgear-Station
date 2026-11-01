@extends('layouts.owner')

@section('content')
<div class="max-w-3xl mx-auto space-y-3 pt-2 pb-6">

    {{-- Header & Breadcrumb --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <div>
            <div class="flex items-center gap-2 text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                <span>Kelola Data Staff</span>
                <span>/</span>
                <span class="text-zinc-900">Tambah Akun Baru</span>
            </div>

            <h1 class="text-xl font-black text-zinc-900 tracking-tight">
                Tambah Akun Staff
            </h1>

            <p class="text-xs text-zinc-500 mt-0.5">
                Daftarkan akun baru untuk admin atau petugas gudang.
            </p>
        </div>

        <a href="{{ url('/owner/kelola-staff') }}"
           class="px-4 py-2 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-700 rounded-xl text-xs font-bold shadow-sm transition inline-flex items-center justify-center gap-2 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>
    </div>

    {{-- Kartu Form Pendaftaran --}}
    <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">

        {{-- Header Kartu --}}
        <div class="px-5 py-3.5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                </div>

                <div>
                    <h3 class="font-bold text-zinc-900 text-sm">
                        Pendaftaran Akun Baru
                    </h3>
                    <p class="text-xs text-zinc-500 mt-0.5">
                        Lengkapi data akun dan tentukan hak akses staf.
                    </p>
                </div>
            </div>

            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-200 inline-flex items-center gap-1.5 w-fit">
                <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span>
                Akun Baru
            </span>
        </div>

        {{-- Formulir Utama: Backend Tetap --}}
        <form action="{{ url('/owner/kelola-staff') }}"
              method="POST"
              class="p-5 space-y-3"
              autocomplete="off">

            @csrf

            {{-- Error Validasi --}}
            @if ($errors->any())
                <div class="p-3 bg-red-50 border border-red-200 rounded-xl text-red-600 text-xs space-y-1">
                    <p class="font-bold flex items-center gap-1.5">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                        Periksa kembali data berikut:
                    </p>

                    <ul class="list-disc list-inside space-y-0.5 pl-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Nama Lengkap --}}
            <div>
                <label for="name"
                       class="block text-[11px] font-bold text-zinc-700 uppercase tracking-wider mb-1.5">
                    Nama Lengkap
                </label>

                <input type="text"
                       id="name"
                       name="name"
                       value="{{ old('name') }}"
                       required
                       placeholder="Contoh: Budi Santoso"
                       autocomplete="off"
                       class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2.5 text-sm font-medium text-zinc-800 placeholder:text-zinc-400 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition">
            </div>

            {{-- Email & Nomor Telepon --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email"
                           class="block text-[11px] font-bold text-zinc-700 uppercase tracking-wider mb-1.5">
                        Alamat Email
                    </label>

                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ old('email') }}"
                           required
                           placeholder="staf@outgear.test"
                           autocomplete="off"
                           class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2.5 text-sm font-medium text-zinc-800 placeholder:text-zinc-400 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition">
                </div>

                <div>
                    <label for="no_telepon"
                           class="block text-[11px] font-bold text-zinc-700 uppercase tracking-wider mb-1.5">
                        No. Telepon / WhatsApp
                    </label>

                    <input type="text"
                           id="no_telepon"
                           name="no_telepon"
                           value="{{ old('no_telepon') }}"
                           placeholder="0811xxxxxxxx"
                           autocomplete="off"
                           class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2.5 text-sm font-medium text-zinc-800 placeholder:text-zinc-400 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition">
                </div>
            </div>

            {{-- Peran / Role --}}
            <div>
                <label for="role"
                       class="block text-[11px] font-bold text-zinc-700 uppercase tracking-wider mb-1.5">
                    Peran (Role)
                </label>

                <select id="role"
                        name="role"
                        class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2.5 text-sm font-medium text-zinc-700 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition cursor-pointer">
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>
                        Admin Operasional
                    </option>
                    <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>
                        Petugas Gudang
                    </option>
                </select>

                <p class="text-[11px] text-zinc-400 mt-1">
                    Peran menentukan hak akses staf di dalam sistem.
                </p>
            </div>

            {{-- Password Sementara --}}
            <div>
                <label for="password"
                       class="block text-[11px] font-bold text-zinc-700 uppercase tracking-wider mb-1.5">
                    Password Sementara
                    <span class="text-zinc-400 font-medium normal-case">(min. 8 karakter)</span>
                </label>

                <input type="password"
                       id="password"
                       name="password"
                       required
                       placeholder="Minimal 8 karakter"
                       autocomplete="new-password"
                       class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2.5 text-sm font-medium text-zinc-800 placeholder:text-zinc-400 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition">
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2.5 pt-3 border-t border-zinc-100">

                <a href="{{ url('/owner/kelola-staff') }}"
                   class="px-5 py-2.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-700 rounded-xl text-xs font-bold transition text-center">
                    Batal
                </a>

                <button type="submit"
                        class="px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white rounded-xl text-xs font-bold shadow-sm transition inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4v16m8-8H4"/>
                    </svg>
                    Simpan Akun Staff
                </button>
            </div>
        </form>
    </div>
</div>
@endsection