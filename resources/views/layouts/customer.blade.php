
<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Outgear Station')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="bg-[#F4EFEA] text-gray-900 antialiased flex flex-col min-h-screen">

    <!-- Navbar -->
    <nav class="bg-[#1A2024] text-white px-6 py-4 flex items-center justify-between sticky top-0 z-50 shadow-md">

        <!-- Logo -->
        <a href="/katalog" class="flex items-center gap-3 shrink-0">
            <div class="w-10 h-10 bg-brand rounded-xl flex items-center justify-center font-bold text-white shadow">
                OS
            </div>

            <div>
                <span class="font-bold tracking-wide text-lg block leading-tight">
                    OutGear Station
                </span>
                <span class="text-[10px] text-gray-400 tracking-wider uppercase">
                    Rental & Outdoor Equipment
                </span>
            </div>
        </a>

        <!-- Menu Navigasi -->
        <div class="hidden md:flex items-center gap-2">
<<<<<<< HEAD
            <a href="/katalog" class="px-4 py-2 rounded-full {{ request()->is('katalog*') ? 'text-white bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }} transition font-medium text-sm">Katalog</a>
            <a href="{{ route('booking.saya') }}" class="px-4 py-2 rounded-full {{ request()->is('booking-saya*') ? 'text-white bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }} transition font-medium text-sm">Booking Saya</a>
            <a href="#" class="px-4 py-2 rounded-full text-gray-300 hover:text-white hover:bg-white/10 transition font-medium text-sm">Chatbot & Bantuan</a>
            <a href="/panduan-sewa" class="px-4 py-2 rounded-full {{ request()->is('panduan-sewa*') ? 'text-white bg-white/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }} transition font-medium text-sm">Panduan Sewa</a>
        </div>
=======
>>>>>>> 3801615 (perbaikan halaman customer)

            <!-- Katalog -->
            <a href="/katalog"
                class="px-4 py-2 rounded-full transition font-medium text-sm
                {{ request()->is('katalog*') ? 'text-white bg-brand' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                Katalog
            </a>

            @auth
            <!-- Booking Saya -->
            <a href="{{ route('booking.saya') }}"
                class="px-4 py-2 rounded-full transition font-medium text-sm
                {{ request()->is('booking-saya*') ? 'text-white bg-brand' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                Booking Saya
            </a>
            @endauth

            <!-- Chatbot & Bantuan -->
            <a href="#"
                class="px-4 py-2 rounded-full text-gray-300 hover:text-white hover:bg-white/10 transition font-medium text-sm">
                Chatbot & Bantuan
            </a>

            <!-- Panduan Sewa -->
            <a href="{{ route('panduan') }}"
                class="px-4 py-2 rounded-full transition font-medium text-sm
                {{ request()->routeIs('panduan') ? 'text-white bg-brand' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                Panduan Sewa
            </a>

        </div>

        <!-- Bagian Kanan -->
        <div class="flex items-center gap-3 sm:gap-4 shrink-0">

            @auth

            <!-- Keranjang -->
            <a href="/katalog"
                class="h-10 px-4 bg-brand hover:bg-brand-hover text-white rounded-full text-xs font-bold transition shadow inline-flex items-center justify-center">
                🛒 Keranjang
            </a>

            <!-- Logout -->
            <form method="POST" action="{{ route('logout') }}" class="inline">
                @csrf

                <button type="submit"
                    class="h-10 px-4 bg-brand hover:bg-brand-hover text-white rounded-full text-xs font-bold transition shadow inline-flex items-center justify-center">
                    Logout
                </button>
            </form>

            @else

            <!-- Login -->
            <a href="{{ route('login') }}"
                class="px-6 py-3 bg-brand hover:bg-brand-hover text-white rounded-full text-sm font-semibold transition shadow">
                Login
            </a>

            @endauth

        </div>
    </nav>

    <!-- Konten Halaman -->
    <main class="flex-grow pt-8 pb-12">
        @yield('content')
    </main>

</body>

</html>
