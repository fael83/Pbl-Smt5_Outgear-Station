@extends('layouts.owner')

@section('content')
<div class="pt-1 pb-4">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
        <div>
            <div class="flex items-center gap-2 text-[11px] font-bold text-zinc-400 uppercase tracking-wider mb-1">
                <span>Kelola Data Staff</span>
                <span>/</span>
                <span class="text-zinc-900">Edit Akun</span>
            </div>

            <h2 class="text-2xl font-black text-zinc-900 tracking-tight">
                Edit Akun Staff
            </h2>

            <p class="text-xs text-zinc-500 mt-1">
                Perbarui data kredensial dan hak akses staf dengan aman.
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

    {{-- Kartu Form: Lebar Dibatasi dan Posisi di Tengah --}}
    <div class="max-w-5xl mx-auto bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">

        {{-- Header Kartu --}}
        <div class="px-6 py-4 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center shrink-0">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8"
                              d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                    </svg>
                </div>

                <div>
                    <h3 class="font-bold text-zinc-900 text-sm">
                        Informasi Staff
                    </h3>
                    <p class="text-xs text-zinc-500 mt-0.5">
                        Lengkapi dan perbarui data akun staf.
                    </p>
                </div>
            </div>

            <span class="text-[11px] font-bold px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 inline-flex items-center gap-1.5 w-fit">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Akun Aktif
            </span>
        </div>

        {{-- Formulir: Backend Tidak Diubah --}}
        <form action="{{ url('/owner/kelola-staff/' . $staf->id) }}"
              method="POST"
              class="p-6 space-y-3">

            @csrf
            @method('PUT')

            {{-- Nama Lengkap --}}
            <div>
                <label for="name"
                       class="block text-[11px] font-bold text-zinc-700 uppercase tracking-wider mb-1.5">
                    Nama Lengkap
                </label>

                <input type="text"
                       id="name"
                       name="name"
                       value="{{ $staf->name }}"
                       required
                       class="w-full h-10 bg-zinc-50 border border-zinc-200 rounded-lg px-3.5 text-sm font-medium text-zinc-800 placeholder:text-zinc-400 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition">
            </div>

            {{-- Email dan Nomor Telepon --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="email"
                           class="block text-[11px] font-bold text-zinc-700 uppercase tracking-wider mb-1.5">
                        Alamat Email
                    </label>

                    <input type="email"
                           id="email"
                           name="email"
                           value="{{ $staf->email }}"
                           required
                           class="w-full h-10 bg-zinc-50 border border-zinc-200 rounded-lg px-3.5 text-sm font-medium text-zinc-800 placeholder:text-zinc-400 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition">
                </div>

                <div>
                    <label for="no_telepon"
                           class="block text-[11px] font-bold text-zinc-700 uppercase tracking-wider mb-1.5">
                        No. Telepon / WhatsApp
                    </label>

                    <input type="text"
                           id="no_telepon"
                           name="no_telepon"
                           value="{{ $staf->no_telepon }}"
                           class="w-full h-10 bg-zinc-50 border border-zinc-200 rounded-lg px-3.5 text-sm font-medium text-zinc-800 placeholder:text-zinc-400 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition">
                </div>
            </div>

            {{-- Peran --}}
            <div>
                <label for="role"
                       class="block text-[11px] font-bold text-zinc-700 uppercase tracking-wider mb-1.5">
                    Peran (Role)
                </label>

                <select id="role"
                        name="role"
                        class="w-full h-10 bg-zinc-50 border border-zinc-200 rounded-lg px-3.5 text-sm font-medium text-zinc-700 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition cursor-pointer">
                    <option value="admin" {{ $staf->role == 'admin' ? 'selected' : '' }}>
                        Admin Operasional
                    </option>

                    <option value="petugas" {{ $staf->role == 'petugas' ? 'selected' : '' }}>
                        Petugas Gudang
                    </option>
                </select>

                <p class="text-[10px] text-zinc-400 mt-1">
                    Peran menentukan hak akses staf di dalam sistem.
                </p>
            </div>

            {{-- Password Baru --}}
            <div>
                <label for="password"
                       class="block text-[11px] font-bold text-zinc-700 uppercase tracking-wider mb-1.5">
                    Password Baru
                    <span class="text-zinc-400 font-medium normal-case">(opsional)</span>
                </label>

                <input type="password"
                       id="password"
                       name="password"
                       autocomplete="new-password"
                       placeholder="Kosongkan jika tidak ingin mengubah password"
                       class="w-full h-10 bg-zinc-50 border border-zinc-200 rounded-lg px-3.5 text-sm font-medium text-zinc-800 placeholder:text-zinc-400 focus:outline-none focus:border-orange-400 focus:ring-2 focus:ring-orange-100 transition">

                <p class="text-[10px] text-zinc-400 mt-1">
                    Password lama tetap digunakan jika kolom ini dikosongkan.
                </p>
            </div>

            {{-- Tombol Aksi --}}
            <div class="flex flex-col-reverse sm:flex-row items-stretch sm:items-center justify-end gap-2 pt-4 border-t border-zinc-100">

                <a href="{{ url('/owner/kelola-staff') }}"
                   class="px-5 py-2.5 bg-white border border-zinc-200 hover:bg-zinc-50 text-zinc-700 rounded-lg text-xs font-bold transition text-center">
                    Batal
                </a>

                <button type="submit"
                        class="px-5 py-2.5 bg-zinc-900 hover:bg-zinc-800 text-white rounded-lg text-xs font-bold shadow-sm transition inline-flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M5 13l4 4L19 7"/>
                    </svg>
                    Perbarui Akun
                </button>
            </div>

        </form>
    </div>
</div>
@endsection