<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Profil Admin</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-theme antialiased font-sans">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">

            <!-- Header Atas -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-10 py-4 z-30 border-b border-zinc-200/60 flex items-center justify-between">
                <div></div>
                <div class="flex items-center gap-3">
                    @include('partials.profil-topbar')
                </div>
            </header>

            <!-- Isi Halaman Profil -->
            <div class="p-10 space-y-8 max-w-[100rem] mx-auto w-full">

                <div>
                    <h1 class="text-3xl font-black text-zinc-900 tracking-tight">Profil {{ $pengguna->labelPeran() }}</h1>
                    <p class="text-xs text-zinc-500 mt-1">Kelola informasi akun dan kata sandi Anda.</p>
                </div>

                @if (session('success'))
                    <div class="rounded-xl border border-emerald-100 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">{{ session('success') }}</div>
                @endif

                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

                    <!-- KARTU KIRI: Avatar & kontak singkat -->
                    <div class="lg:col-span-4 bg-white p-8 rounded-3xl border border-zinc-200/80 shadow-sm flex flex-col items-center text-center space-y-5">
                        <div class="w-28 h-28 rounded-full bg-zinc-900 text-white font-black text-3xl flex items-center justify-center border-4 border-zinc-100 shadow-md">
                            {{ $pengguna->inisial() }}
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-zinc-900">{{ $pengguna->name }}</h2>
                            <p class="text-xs font-bold text-portal-orange mt-0.5">{{ $pengguna->labelPeran() }}</p>
                        </div>

                        <div class="w-full pt-4 border-t border-zinc-100 space-y-3 text-left text-xs">
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-zinc-400 font-medium">Email</span>
                                <span class="font-bold text-zinc-800 truncate">{{ $pengguna->email }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-zinc-400 font-medium">Telepon</span>
                                <span class="font-bold text-zinc-800">{{ $pengguna->no_telepon ?: '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-3">
                                <span class="text-zinc-400 font-medium">Status Akun</span>
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full capitalize">{{ $pengguna->status_akun ?? 'aktif' }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU KANAN: Informasi akun & keamanan -->
                    <div class="lg:col-span-8 space-y-6">

                        <form method="POST" action="{{ route('admin.profile.update') }}" class="bg-white p-8 rounded-3xl border border-zinc-200/80 shadow-sm space-y-6">
                            @csrf
                            @method('PUT')
                            <div class="flex items-center justify-between pb-4 border-b border-zinc-100">
                                <h3 class="font-black text-zinc-900 text-sm">Informasi Akun & Sistem</h3>
                                <span class="text-xs text-zinc-400 font-mono">ID Pengguna: #{{ str_pad((string) $pengguna->id, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5" for="name">Nama Lengkap</label>
                                    <input id="name" type="text" name="name" value="{{ old('name', $pengguna->name) }}" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                                    @error('name') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5" for="email">Alamat Email</label>
                                    <input id="email" type="email" name="email" value="{{ old('email', $pengguna->email) }}" required class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                                    @error('email') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5" for="no_telepon">Nomor Handphone</label>
                                    <input id="no_telepon" type="text" name="no_telepon" value="{{ old('no_telepon', $pengguna->no_telepon) }}" placeholder="Contoh: 081234567890" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                                    @error('no_telepon') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5">Tanggal Bergabung</label>
                                    <input type="text" value="{{ $pengguna->created_at?->locale('id')->translatedFormat('d F Y') ?? '-' }}" disabled
                                           class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-500 cursor-not-allowed">
                                </div>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="bg-zinc-900 hover:bg-portal-orange text-white text-xs font-bold py-2.5 px-5 rounded-xl transition shadow-sm">
                                    Simpan Perubahan
                                </button>
                            </div>
                        </form>

                        <form method="POST" action="{{ route('admin.profile.sandi') }}" class="bg-white p-8 rounded-3xl border border-zinc-200/80 shadow-sm space-y-6">
                            @csrf
                            @method('PUT')
                            <div class="pb-4 border-b border-zinc-100">
                                <h3 class="font-black text-zinc-900 text-sm">Keamanan & Sandi</h3>
                                <p class="text-[11px] text-zinc-400 mt-0.5">Perbarui kata sandi secara berkala untuk menjaga keamanan akun.</p>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5" for="sandi_saat_ini">Sandi Saat Ini</label>
                                    <input id="sandi_saat_ini" type="password" name="sandi_saat_ini" placeholder="••••••••" autocomplete="current-password" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                                    @error('sandi_saat_ini', 'sandi') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5" for="sandi_baru">Sandi Baru</label>
                                    <input id="sandi_baru" type="password" name="sandi_baru" placeholder="Minimal 8 karakter" autocomplete="new-password" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                                    @error('sandi_baru', 'sandi') <span class="text-[10px] text-rose-600 mt-1 block font-bold">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5" for="sandi_baru_confirmation">Konfirmasi Sandi Baru</label>
                                    <input id="sandi_baru_confirmation" type="password" name="sandi_baru_confirmation" placeholder="••••••••" autocomplete="new-password" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                                </div>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button type="submit" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold py-2.5 px-5 rounded-xl transition shadow-sm">
                                    Perbarui Kata Sandi
                                </button>
                            </div>
                        </form>

                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
