@extends('layouts.customer')

@section('title', 'Profil Saya - OutGear Station')

@section('content')
<div class="max-w-4xl mx-auto px-4 sm:px-6 py-10 w-full space-y-8">
    
        
        <!-- Header Profil -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-sm flex flex-col sm:flex-row items-center gap-6">
            <div class="w-20 h-20 rounded-2xl bg-gray-900 text-white font-black text-2xl flex items-center justify-center shadow-md shrink-0">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </div>
            <div class="text-center sm:text-left space-y-1">
                <h1 class="text-2xl font-black text-gray-900 tracking-tight">{{ auth()->user()->name ?? 'Pengguna' }}</h1>
                <p class="text-xs text-gray-500 font-medium">{{ auth()->user()->email ?? 'user@example.com' }}</p>
                <div class="pt-2">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200">
                        Akun Penyewa Aktif
                    </span>
                </div>
            </div>
        </div>

        <!-- Grid 2 Kolom: Informasi Akun & Password -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            
            <!-- 1. Form Update Informasi Profil -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Informasi Profil</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Perbarui nama dan alamat email akun kamu.</p>
                </div>

                <form method="post" action="{{ route('profile.update') }}" class="space-y-4">
                    @csrf
                    @method('patch')

                    <!-- Nama -->
                    <div>
                        <label for="name" class="block text-xs font-bold text-gray-700 uppercase mb-1">Nama Lengkap</label>
                        <input type="text" id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-900 font-medium focus:ring-2 focus:ring-brand focus:outline-none transition">
                        @error('name')
                            <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-700 uppercase mb-1">Alamat Email</label>
                        <input type="email" id="email" name="email" value="{{ old('email', auth()->user()->email ?? '') }}" required 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-900 font-medium focus:ring-2 focus:ring-brand focus:outline-none transition">
                        @error('email')
                            <p class="text-[11px] text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="pt-2 flex items-center justify-between">
                        <button type="submit" class="bg-gray-900 hover:bg-brand text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-sm">
                            Simpan Perubahan
                        </button>

                        @if (session('status') === 'profile-updated')
                            <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" 
                                  class="text-xs font-bold text-emerald-600">
                                ✓ Tersimpan!
                            </span>
                        @endif
                    </div>
                </form>
            </div>

            <!-- 2. Form Update Password -->
            <div class="bg-white rounded-3xl p-6 sm:p-8 border border-gray-200 shadow-sm space-y-6">
                <div>
                    <h3 class="text-base font-extrabold text-gray-900">Ubah Kata Sandi</h3>
                    <p class="text-xs text-gray-500 mt-0.5">Pastikan akunmu menggunakan kata sandi yang aman.</p>
                </div>

                <form method="post" action="{{ route('password.update') }}" class="space-y-4">
                    @csrf
                    @method('put')

                    <!-- Password Saat Ini -->
                    <div>
                        <label for="current_password" class="block text-xs font-bold text-gray-700 uppercase mb-1">Kata Sandi Saat Ini</label>
                        <input type="password" id="current_password" name="current_password" required 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-900 font-medium focus:ring-2 focus:ring-brand focus:outline-none transition">
                    </div>

                    <!-- Password Baru -->
                    <div>
                        <label for="password" class="block text-xs font-bold text-gray-700 uppercase mb-1">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" required 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-900 font-medium focus:ring-2 focus:ring-brand focus:outline-none transition">
                    </div>

                    <!-- Konfirmasi Password Baru -->
                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-gray-700 uppercase mb-1">Konfirmasi Kata Sandi Baru</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required 
                               class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs text-gray-900 font-medium focus:ring-2 focus:ring-brand focus:outline-none transition">
                    </div>

                    <div class="pt-2 flex items-center justify-between">
                        <button type="submit" class="bg-gray-900 hover:bg-brand text-white text-xs font-bold px-5 py-2.5 rounded-xl transition shadow-sm">
                            Update Kata Sandi
                        </button>

                        @if (session('status') === 'password-updated')
                            <span x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 3000)" 
                                  class="text-xs font-bold text-emerald-600">
                                ✓ Password Diperbarui!
                            </span>
                        @endif
                    </div>
                </form>
            </div>

        </div>

        <!-- 3. Zona Bahaya (Logout / Hapus Akun) -->
        <div class="bg-white rounded-3xl p-6 sm:p-8 border border-red-100 shadow-sm flex flex-col sm:flex-row items-center justify-between gap-4">
            <div>
                <h4 class="text-sm font-extrabold text-gray-900">Keluar dari Sesi</h4>
                <p class="text-xs text-gray-500 mt-0.5">Akhiri sesi penggunaan akun di perangkat ini.</p>
            </div>
            
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 text-xs font-bold px-5 py-2.5 rounded-xl transition">
                    Keluar / Logout
                </button>
            </form>
        </div>

</div>
@endsection
