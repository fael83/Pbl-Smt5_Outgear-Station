@extends('layouts.owner')

@section('content')
<div class="max-w-2xl mx-auto space-y-2 pt-2 pb-6">
    
    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                <span>Kelola Data Staff</span>
                <span>/</span>
                <span class="text-gray-900">Tambah Akun Baru</span>
            </div>
            <div class="flex items-center gap-3">
                <h1 class="text-xl font-extrabold text-gray-900">Formulir Pendaftaran Staff</h1>
            </div>
            <p class="text-xs text-gray-500 mt-1">Daftarkan akun baru untuk admin atau petugas gudang operasional.</p>
        </div>
        <a href="{{ url('/owner/kelola-staff') }}" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2 w-fit">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
            Kembali
        </a>
    </div>

    <!-- Kartu Utama -->
    <div class="bg-white rounded-3xl border border-gray-100 shadow-sm p-6 space-y-4">
        
        <!-- Sub-Header / Info Banner -->
        <div class="flex items-center justify-between p-4 bg-[#FAF7F2] rounded-2xl border border-gray-200/60">
            <div class="flex items-center gap-3.5">
                <div class="w-10 h-10 rounded-xl bg-brand/10 text-brand flex items-center justify-center font-bold">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                </div>
                <div>
                    <h3 class="font-extrabold text-gray-900 text-sm">Pendaftaran Akun Baru</h3>
                    <p class="text-xs text-gray-500">Lengkapi data kredensial untuk memberikan hak akses.</p>
                </div>
            </div>
            <span class="px-3 py-1 bg-brand/10 text-brand text-[11px] font-bold rounded-full border border-brand/20 flex items-center gap-1.5 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-brand animate-pulse"></span> Registration Mode
            </span>
        </div>

        <!-- Formulir Pendaftaran -->
        <form action="{{ url('/owner/kelola-staff') }}" method="POST" class="space-y-4" autocomplete="off">
            @csrf
            
            <!-- Penangkap Error Validasi-->
            @if ($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 rounded-2xl text-red-600 text-xs font-bold space-y-1 animate-fade-in">
                    <p class="font-black uppercase tracking-wider flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Perhatian, Mohon Periksa Kembali:
                    </p>
                    <ul class="list-disc list-inside space-y-0.5 font-medium pl-4">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Nama Lengkap -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                    Nama Lengkap
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="Contoh: Budi Santoso" autocomplete="off" class="w-full bg-[#FAF7F2] border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition">
            </div>

            <!-- Email & No Telepon -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Alamat Email
                    </label>
                    <input type="email" name="email" value="{{ old('email') }}" required placeholder="staf@outgear.test" autocomplete="off" class="w-full bg-[#FAF7F2] border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition">
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-2">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        No. Telepon / WhatsApp
                    </label>
                    <input type="text" name="no_telepon" value="{{ old('no_telepon') }}" placeholder="0811xxxxxxxx" autocomplete="off" class="w-full bg-[#FAF7F2] border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition">
                </div>
            </div>

            <!-- Peran (Role) -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                    Peran (Role)
                </label>
                <select name="role" class="w-full bg-[#FAF7F2] border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition cursor-pointer">
                    <option value="admin" {{ old('role') == 'admin' ? 'selected' : '' }}>Admin Operasional</option>
                    <option value="petugas" {{ old('role') == 'petugas' ? 'selected' : '' }}>Petugas Gudang</option>
                </select>
            </div>

            <!-- Password Sementara -->
            <div>
                <label class="block text-xs font-bold text-gray-700 uppercase tracking-wider mb-1.5 flex items-center gap-2">
                    <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                    Password Sementara <span class="text-gray-400 font-normal lowercase">(min. 8 karakter)</span>
                </label>
                <input type="password" name="password" required placeholder="Minimal 8 karakter" autocomplete="new-password" class="w-full bg-[#FAF7F2] border border-gray-200 rounded-xl px-4 py-2.5 text-sm font-medium focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition">
            </div>

            <!-- Tombol Aksi Bawah -->
            <div class="flex items-center justify-end gap-3 pt-3 border-t border-gray-100">
                <a href="{{ url('/owner/kelola-staff') }}" class="px-5 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-bold transition shadow-sm">Batal</a>
                <button type="submit" class="px-6 py-2 bg-brand hover:bg-brand-hover text-white rounded-xl text-sm font-bold shadow-sm transition flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Simpan Akun Staff
                </button>
            </div>
        </form>
    </div>
</div>
@endsection