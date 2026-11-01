<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Outgear Station') }} - Owner Panel</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-sans antialiased text-gray-900 bg-[#FAF7F2]">
    <div class="min-h-screen flex w-full">

        <!-- SIDEBAR KIRI -->
        <aside class="w-64 bg-[#FAF7F2] border-r border-gray-200 flex-shrink-0 flex flex-col h-screen sticky top-0">

            <!-- Header Logo -->
            <div class="h-20 flex items-center px-6 mt-2">
                <a href="{{ route('owner.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-8 h-8 bg-gray-900 rounded-lg flex items-center justify-center text-white">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24"
                            xmlns="http://www.w3.org/2000/svg">
                            <path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13h-13L12 6.5z" />
                        </svg>
                    </div>

                    <div>
                        <h1 class="text-sm font-bold tracking-wider leading-tight text-gray-900">
                            OUTGEAR<br>STATION
                        </h1>
                    </div>
                </a>
            </div>

            <!-- Menu Navigasi -->
            <nav class="flex-1 px-4 py-6 space-y-6 overflow-y-auto">

                <!-- Grup Menu: Manajemen Bisnis -->
                <div>
                    <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                        Manajemen Bisnis
                    </p>

                    <div class="space-y-1">

                        <!-- Dashboard -->
                        <a href="{{ route('owner.dashboard') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('owner.dashboard') ? 'bg-white shadow-sm border border-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100/50' }}">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                                </path>
                            </svg>

                            Dashboard
                        </a>

                        <!-- Kelola Harga Sewa -->
                        <a href="{{ route('owner.harga-sewa.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('owner.harga-sewa*') ? 'bg-white shadow-sm border border-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100/50' }}">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg>

                            Kelola Harga Sewa
                        </a>

                        <!-- Kelola Aturan Denda -->
                        <a href="{{ route('owner.aturan-denda.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('owner.aturan-denda*') ? 'bg-white shadow-sm border border-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100/50' }}">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z">
                                </path>
                            </svg>

                            Kelola Aturan Denda
                        </a>

                    </div>
                </div>

                <!-- Grup Menu: SDM & Analitik -->
                <div>
                    <p class="px-3 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">
                        SDM & Analitik
                    </p>

                    <div class="space-y-1">

                        <!-- Kelola Data Admin dan Petugas -->
                        <a href="{{ route('owner.kelola-staff.index') }}"
                            class="flex items-center gap-3 px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('owner.kelola-staff*') ? 'bg-white shadow-sm border border-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100/50' }}">

                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                </path>
                            </svg>

                            Kelola Data Admin dan Petugas
                        </a>

                        <!-- Laporan Penyewaan -->
                        <a href="{{ route('owner.laporan.index') }}"
                            class="flex items-center justify-between px-3 py-2.5 rounded-xl font-medium transition-all {{ request()->routeIs('owner.laporan*') ? 'bg-white shadow-sm border border-gray-100 text-gray-900' : 'text-gray-500 hover:text-gray-900 hover:bg-gray-100/50' }}">

                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                    </path>
                                </svg>

                                Laporan Penyewaan
                            </div>

                            <span class="w-2 h-2 bg-orange-500 rounded-full"></span>
                        </a>

                    </div>
                </div>
            </nav>

            <!-- Tombol Keluar -->
            <div class="p-4 border-t border-gray-200">
                <form method="POST" action="{{ url('/logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex items-center justify-center gap-2 w-full py-2.5 text-sm font-bold text-red-600 bg-red-50 hover:bg-red-100 rounded-xl transition-colors">

                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>

                        Keluar Sistem
                    </button>
                </form>
            </div>
        </aside>

        <!-- KONTEN UTAMA KANAN -->
        <div class="flex-1 flex flex-col min-w-0 h-screen overflow-hidden">

            <!-- Topbar Header -->
            <header class="h-20 flex items-center justify-end px-8 bg-[#FAF7F2]">

                <!-- Profil Owner -->
                <div class="flex items-center gap-4">
                    <div
                        class="flex items-center gap-3 bg-white pl-1 pr-4 py-1 rounded-full shadow-sm border border-gray-100 cursor-pointer hover:shadow transition-all">

                        <div
                            class="w-8 h-8 rounded-full bg-gray-900 text-white flex items-center justify-center text-xs font-bold">
                            {{ substr(Auth::user()->name ?? 'Owner', 0, 1) }}
                        </div>

                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-900 leading-none mb-0.5">
                                {{ Auth::user()->name ?? 'Owner Utama' }}
                            </span>

                            <span
                                class="text-[10px] font-semibold text-emerald-600 bg-emerald-50 px-1.5 py-0.5 rounded-md inline-block max-w-fit leading-none">
                                Authority: Owner
                            </span>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Konten Halaman -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto px-8 pb-10 scroll-smooth">
                @yield('content')
            </main>

        </div>
    </div>
</body>

</html>