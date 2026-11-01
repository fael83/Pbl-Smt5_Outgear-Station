@extends('layouts.panel', ['title' => 'Profil Petugas Gudang'])

@section('content')
<div class="space-y-8 max-w-[100rem] mx-auto w-full">

    <!-- Judul Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-zinc-900 tracking-tight">Profil Petugas Gudang</h1>
            <p class="text-xs text-zinc-500 mt-1">Kelola informasi akun, kredensial login, dan pantau aktivitas operasional logistik gudang.</p>
        </div>
        <div class="flex items-center gap-3">
        </div>
    </div>

    @if(session('success'))
        <div class="bg-emerald-50 border border-emerald-200 text-emerald-800 px-4 py-3 rounded-2xl text-xs font-bold flex items-center gap-2 shadow-xs">
            <span class="text-base">✓</span>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    <!-- GRID KONTEN PROFIL (Gaya Modular Card Selaras dengan Halaman Admin) -->
    <!-- Ditambahkan kelas items-start agar card kiri dan kanan sejajar lurus di atas -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- KARTU KIRI (Lebar 4): Avatar & Kontak Singkat -->
        <div class="lg:col-span-4 bg-white p-8 rounded-3xl border border-zinc-200/80 shadow-sm flex flex-col items-center text-center space-y-5">
            <div class="relative">
                <div class="w-28 h-28 rounded-full bg-zinc-900 text-white font-black text-3xl flex items-center justify-center border-4 border-zinc-100 shadow-md">
                    {{ auth()->user()->inisial() ?? strtoupper(substr(auth()->user()->name ?? 'P', 0, 2)) }}
                </div>
                <span class="absolute bottom-1 right-1 w-5 h-5 bg-emerald-500 border-2 border-white rounded-full" title="Status: Aktif"></span>
            </div>

            <div>
                <h2 class="text-xl font-black text-zinc-900">{{ auth()->user()->name ?? 'Petugas Gudang' }}</h2>
                <p class="text-xs font-bold text-portal-orange mt-0.5">Staff Operasional Logistik</p>
            </div>

            <div class="w-full pt-4 border-t border-zinc-100 space-y-3 text-left text-xs">
                <div class="flex items-center justify-between">
                    <span class="text-zinc-400 font-medium">Email</span>
                    <span class="font-bold text-zinc-800 truncate max-w-[180px]">{{ auth()->user()->email ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-400 font-medium">Telepon</span>
                    <span class="font-bold text-zinc-800">{{ auth()->user()->no_telepon ?? '-' }}</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-400 font-medium">Penempatan</span>
                    <span class="font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">Gudang Utama</span>
                </div>
                <div class="flex items-center justify-between">
                    <span class="text-zinc-400 font-medium">Status Akun</span>
                    <span class="font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full capitalize">{{ auth()->user()->status_akun ?? 'aktif' }}</span>
                </div>
            </div>
        </div>

        <!-- KARTU KANAN (Lebar 8): Form Informasi Akun & Keamanan -->
        <div class="lg:col-span-8">
            <form action="{{ route('petugas.profile.update') }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                <!-- 1. Informasi Umum -->
                <div class="bg-white p-8 rounded-3xl border border-zinc-200/80 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-zinc-100">
                        <h3 class="font-black text-zinc-900 text-sm">Informasi Akun & Sistem</h3>
                        <span class="text-xs text-zinc-400 font-mono">ID Petugas: #STF-{{ str_pad(auth()->id(), 3, '0', STR_PAD_LEFT) }}</span>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                        <div>
                            <label class="block text-zinc-400 font-bold mb-1.5">Nama Lengkap</label>
                            <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" required
                                   class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                            @error('name') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-zinc-400 font-bold mb-1.5">Alamat Email</label>
                            <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" required
                                   class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                            @error('email') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-zinc-400 font-bold mb-1.5">Nomor Handphone</label>
                            <input type="text" name="no_telepon" value="{{ old('no_telepon', auth()->user()->no_telepon) }}" placeholder="Contoh: 081234567890"
                                   class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                            @error('no_telepon') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-zinc-400 font-bold mb-1.5">Tanggal Bergabung</label>
                            <input type="text" value="{{ auth()->user()->created_at ? auth()->user()->created_at->translatedFormat('d F Y') : '-' }}" disabled
                                   class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-500 cursor-not-allowed">
                        </div>
                    </div>
                </div>

                <!-- 2. Keamanan & Ubah Password -->
                <div class="bg-white p-8 rounded-3xl border border-zinc-200/80 shadow-sm space-y-6">
                    <div class="flex items-center justify-between pb-4 border-b border-zinc-100">
                        <div>
                            <h3 class="font-black text-zinc-900 text-sm">Keamanan & Sandi</h3>
                            <p class="text-[11px] text-zinc-400 mt-0.5">Kosongkan kolom sandi jika Anda tidak bermaksud mengubah kata sandi.</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                        <div>
                            <label class="block text-zinc-400 font-bold mb-1.5">Kata Sandi Baru</label>
                            <input type="password" name="password" placeholder="••••••••"
                                   class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2.5 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                            @error('password') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-zinc-400 font-bold mb-1.5">Konfirmasi Sandi Baru</label>
                            <input type="password" name="password_confirmation" placeholder="••••••••"
                                   class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2.5 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-2 border-t border-zinc-100">
                        <button type="submit" class="px-6 py-2.5 rounded-xl bg-portal-orange hover:bg-orange-600 text-white text-xs font-bold transition shadow-sm hover:shadow">
                            Perbarui Profil Akun
                        </button>
                    </div>
                </div>
            </form>
        </div>

    </div>
</div>
@endsection