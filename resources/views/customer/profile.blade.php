<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - OutGear Station</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F8F6F2] text-gray-800 font-sans min-h-screen flex flex-col justify-between"
      x-data="{ 
          cart: [], 
          isCartOpen: false,
          
          get totalItems() {
              return this.cart.reduce((sum, item) => sum + item.qty, 0);
          }
      }">

    <!-- Main Navigation Bar -->
    <nav class="bg-dark text-white px-8 py-6 flex items-center justify-between shadow-xl sticky top-0 z-50 border-b border-white/10">
        <!-- Logo Brand -->
        <div class="flex items-center gap-4">
            <a href="{{ route('beranda') }}" class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-2xl bg-brand flex items-center justify-center font-black text-white shadow-xl shadow-brand/30 text-base tracking-wider">
                    OS
                </div>
                <div>
                    <h1 class="font-black text-base tracking-wide text-white leading-tight">OutGear Station</h1>
                    <p class="text-[11px] text-gray-400 tracking-widest uppercase font-semibold">Rental & Outdoor Equipment</p>
                </div>
            </a>
        </div>

        <!-- Menu Navigasi Utama -->
        <div class="hidden md:flex items-center gap-10 text-sm font-bold tracking-wide">
            <a href="{{ route('katalog') }}" class="text-gray-300 hover:text-brand transition-colors duration-200">
                Katalog
            </a>
            <a href="{{ route('booking.saya') }}" class="text-gray-300 hover:text-brand transition-colors duration-200 flex items-center gap-2.5 group">
                <span>Booking Saya</span>
                <span class="bg-brand/20 text-brand group-hover:bg-brand group-hover:text-white text-xs px-2.5 py-0.5 rounded-full font-extrabold transition-colors duration-200">2</span>
            </a>
            <a href="{{ route('panduan') }}" class="text-gray-300 hover:text-brand transition-colors duration-200">
                Panduan Sewa
            </a>
        </div>

        <!-- Action Items -->
        <div class="flex items-center gap-4">
            <button @click="isCartOpen = true" class="bg-brand hover:bg-brand-hover text-white text-xs font-black px-5 py-3 rounded-2xl flex items-center gap-3 transition shadow-lg shadow-brand/25 active:scale-95">
                <span class="text-sm">🛒 Keranjang</span>
                <span class="bg-white/20 backdrop-blur text-white text-[11px] font-black px-2.5 py-0.5 rounded-full" x-text="totalItems + ' Item'">0 Item</span>
            </button>

            <!-- Profile Icon (Aktif) -->
            <a href="{{ route('profile.edit') }}" class="w-11 h-11 rounded-2xl bg-brand text-white flex items-center justify-center text-sm font-bold border border-brand shadow-sm">
                {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
            </a>
        </div>
    </nav>

    <!-- Konten Utama Profil -->
    <main class="max-w-4xl mx-auto px-4 sm:px-6 py-10 w-full flex-grow space-y-8">
        
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

    </main>

    <!-- Dark Footer -->
    <footer class="bg-dark text-gray-400 text-xs pt-12 pb-6 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
            <!-- Kolom 1: Tentang Basecamp -->
            <div class="space-y-3">
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-brand flex items-center justify-center font-bold text-white text-xs">OS</div>
                    <span class="font-bold text-white text-sm">OutGear Station</span>
                </div>
                <p class="text-[11px] leading-relaxed text-gray-400">
                    Penyedia persewaan perlengkapan outdoor, mountaineering & camping terpercaya sejak 2021 di Malang.
                </p>
                <div class="pt-2 text-[11px] text-gray-300">
                    <p class="font-bold text-white mb-1">📍 Basecamp Utama:</p>
                    <p>Jl. Soekarno Hatta No. 9, Malang, Jawa Timur</p>
                </div>
            </div>

            <!-- Kolom 2: Jam Operasional -->
            <div class="space-y-3">
                <h4 class="font-bold text-white text-sm tracking-wider uppercase">Jam Operasional</h4>
                <div class="space-y-2 text-[11px]">
                    <p><strong class="text-white">Senin — Jumat:</strong> 08:00 - 21:00 WIB</p>
                    <p><strong class="text-white">Sabtu — Minggu:</strong> 07:00 - 22:00 WIB</p>
                    <p class="text-emerald-400 font-semibold pt-1">✔ Layanan Ambil/Kembali Buka Setiap Hari</p>
                </div>
            </div>

            <!-- Kolom 3: Metode Pembayaran -->
            <div class="space-y-3">
                <h4 class="font-bold text-white text-sm tracking-wider uppercase">Metode Pembayaran</h4>
                <p class="text-[11px] text-gray-400">Kami mendukung pembayaran online praktis via Midtrans Gateway serta pembayaran tunai langsung di tempat.</p>
                <div class="flex flex-wrap gap-2 pt-2">
                    <span class="bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-xl text-[11px] font-bold text-white flex items-center gap-1.5">
                        💳 Midtrans Payment
                    </span>
                    <span class="bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-xl text-[11px] font-bold text-white flex items-center gap-1.5">
                        💵 Tunai / Cash
                    </span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-6 border-t border-gray-800 text-center text-[10px] text-gray-500">
            <p>© 2026 OutGear Station. All rights reserved.</p>
        </div>
    </footer>

</body>

</html>