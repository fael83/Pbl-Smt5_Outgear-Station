<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Outgear Station')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F6F2] text-gray-900 antialiased flex flex-col min-h-screen"
      x-data="{ 
          cart: [], 
          isCartOpen: false,
          get totalItems() {
              return this.cart.reduce((sum, item) => sum + item.qty, 0);
          }
      }">

    <!--  NAVBAR GELAP KUSTOM MODERN (Lebih Lebar, Gagah & Clean Text) -->
    <nav class="bg-dark text-white px-8 py-6 flex items-center justify-between sticky top-0 z-50 shadow-xl border-b border-white/10">
        
        <!-- Logo Brand (Diperbesar) -->
        <a href="/katalog" class="flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-brand flex items-center justify-center font-black text-white shadow-xl shadow-brand/30 text-base tracking-wider group-hover:scale-105 transition-transform duration-200">
                OS
            </div>
            <div>
                <span class="font-black text-base tracking-wide text-white leading-tight block">OutGear Station</span>
                <span class="text-[11px] text-gray-400 tracking-widest uppercase font-semibold block">Rental & Outdoor Equipment</span>
            </div>
        </a>

        <!-- Menu Navigasi Utama (Hanya Teks Warna Oranye Saat Aktif) -->
        <div class="hidden md:flex items-center gap-10 text-sm font-bold tracking-wide">
            <!-- Katalog -->
            <a href="/katalog" 
               class="{{ request()->is('katalog*') ? 'text-brand font-extrabold' : 'text-gray-300 hover:text-brand' }} transition-colors duration-200">
                Katalog
            </a>

            <!-- Booking Saya -->
            <a href="{{ route('booking.saya') }}" 
               class="{{ request()->is('booking-saya*') ? 'text-brand font-extrabold' : 'text-gray-300 hover:text-brand' }} transition-colors duration-200 flex items-center gap-2.5 group">
                <span>Booking Saya</span>
                <span class="bg-brand/20 text-brand group-hover:bg-brand group-hover:text-white text-xs px-2.5 py-0.5 rounded-full font-extrabold transition-colors duration-200">2</span>
            </a>

            <!-- Chatbot & Bantuan -->
            <a href="#" 
               class="text-gray-300 hover:text-brand transition-colors duration-200">
                Chatbot & Bantuan
            </a>

            <!-- Panduan Sewa -->
            <a href="/panduan-sewa" 
               class="{{ request()->is('panduan-sewa*') ? 'text-brand font-extrabold' : 'text-gray-300 hover:text-brand' }} transition-colors duration-200">
                Panduan Sewa
            </a>
        </div>

        <!-- Bagian Kanan (Tombol Keranjang & Cek Status Login) -->
        <div class="flex items-center gap-4">
            <!-- Tombol Keranjang -->
            <button @click="isCartOpen = true" class="bg-brand hover:bg-brand-hover text-white text-xs font-black px-5 py-3 rounded-2xl flex items-center gap-3 transition shadow-lg shadow-brand/25 active:scale-95">
                <span class="text-sm">🛒 Keranjang</span>
                <span class="bg-white/20 backdrop-blur text-white text-[11px] font-black px-2.5 py-0.5 rounded-full" x-text="typeof totalItems !== 'undefined' ? totalItems + ' Item' : '0 Item'">0 Item</span>
            </button>

            @auth
                <!-- Jika Sudah Login: Tampilkan Nama User & Logout -->
                <div class="flex items-center gap-3 bg-gray-800/90 backdrop-blur px-4 py-2 rounded-2xl border border-gray-700/80 shadow-sm">
                    <span class="text-xs font-bold text-gray-200">👤 {{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 font-bold transition border-l border-gray-700 pl-3">Keluar</button>
                    </form>
                </div>
            @else
                <!-- Jika Belum Login -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="text-xs font-bold text-gray-300 hover:text-white px-3.5 py-2 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="text-xs font-black bg-brand hover:bg-brand-hover text-white px-4 py-2.5 rounded-xl transition shadow-md shadow-brand/20">Daftar</a>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Area Render Konten Utama (Disi oleh Katalog, Panduan, dll) -->
    <main class="flex-grow pt-6 pb-12">
        @yield('content')
    </main>

</body>
</html>