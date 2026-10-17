<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Outgear Station')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F4EFEA] text-gray-900 antialiased flex flex-col min-h-screen">

    <!-- NAVBAR GELAP KUSTOM (Sama persis dengan halaman Katalog) -->
    <nav class="bg-[#1A2024] text-white px-6 py-4 flex items-center justify-between sticky top-0 z-50 shadow-md">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-orange-600 rounded-xl flex items-center justify-center font-bold text-white shadow">
                OS
            </div>
            <div>
                <span class="font-bold tracking-wide text-lg block leading-tight">OutGear Station</span>
                <span class="text-[10px] text-gray-400 tracking-wider uppercase">Rental & Outdoor Equipment</span>
            </div>
        </div>

        <!-- Menu Tengah -->
        <div class="hidden md:flex items-center gap-2">
            <a href="/katalog" class="px-4 py-2 rounded-full {{ request()->is('katalog*') ? 'text-white bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }} transition font-medium text-sm">Katalog</a>
            <a href="{{ route('booking.saya') }}" class="px-4 py-2 rounded-full {{ request()->is('booking-saya*') ? 'text-white bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }} transition font-medium text-sm">Booking Saya</a>
            <a href="#" class="px-4 py-2 rounded-full text-gray-300 hover:text-white hover:bg-white/10 transition font-medium text-sm">Chatbot & Bantuan</a>
            <a shref="/panduan-sewa" class="px-4 py-2 rounded-full {{ request()->is('panduan-sewa*') ? 'text-white bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }} transition font-medium text-sm">Panduan Sewa</a>
        </div>

        <!-- Bagian Kanan (Dinamis: Cek Status Login) -->
        <div class="flex items-center gap-4">
            <a href="/katalog" class="px-4 py-2 bg-orange-600 hover:bg-orange-700 text-white rounded-full text-xs font-bold transition shadow">
                🛒 Keranjang
            </a>

            @auth
                <!-- Jika Sudah Login, Tampilkan Nama/Dropdown Sederhana -->
                <div class="flex items-center gap-2 bg-gray-800 px-3 py-1.5 rounded-full border border-gray-700">
                    <span class="text-xs font-semibold text-gray-200">{{ Auth::user()->name }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 ml-2 font-bold">Keluar</button>
                    </form>
                </div>
            @else
                <!-- Jika Belum Login -->
                <div class="flex items-center gap-2">
                    <a href="{{ route('login') }}" class="text-xs font-semibold text-gray-300 hover:text-white px-3 py-1.5 transition">Masuk</a>
                    <a href="{{ route('register') }}" class="text-xs font-bold bg-orange-600 hover:bg-orange-700 text-white px-3 py-1.5 rounded-lg transition shadow">Daftar</a>
                </div>
            @endauth
        </div>
    </nav>

    <!-- Area Render Konten Utama -->
    <main class="flex-grow pt-8 pb-12">
        @yield('content')
    </main>

</body>
</html>