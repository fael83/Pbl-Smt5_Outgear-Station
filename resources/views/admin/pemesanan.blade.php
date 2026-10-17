<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Data Pemesanan</title>
    <!-- Alpine.js untuk interaktivitas tombol search & dropdown filter -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'portal-sidebar': '#18181b',
                        'portal-bg': '#f4f2ef',
                        'portal-orange': '#e05a3f',
                        'portal-card': '#ffffff',
                    }
                }
            }
        }
    </script>
    @vite('resources/css/app.css')
</head>
<body class="text-slate-800 antialiased" x-data="{ showSearchInput: false, showFilterDropdown: false, selectedFilter: 'Semua' }">

    <div class="flex min-h-screen">

        <!--SIDEBAR KIRI (Sticky & Full Tinggi) -->
        <aside class="sticky top-0 h-screen w-64 bg-portal-sidebar text-white flex flex-col flex-shrink-0 z-50 select-none">
            
            <!-- Logo & Brand -->
            <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-portal-orange flex items-center justify-center font-black text-white text-sm shadow-md">O</div>
                    <div>
                        <h1 class="font-extrabold text-sm tracking-wide text-white leading-tight">OutGear Station</h1>
                        <span class="text-[9px] tracking-widest text-portal-orange font-bold uppercase">ADMIN PORTAL</span>
                    </div>
                </div>
            </div>

            <!-- Navigasi Menu -->
            <nav class="flex-grow px-4 py-4 space-y-1.5 overflow-y-auto text-sm">
                <!-- Dashboard -->
                <a href="/admin" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin') && !request()->is('admin/*') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>

                <!-- Data Pelanggan -->
                <a href="/admin/pelanggan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/pelanggan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Data Pelanggan
                </a>

                <!-- Data Peralatan -->
                <a href="/admin/peralatan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/peralatan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Data Peralatan
                </a>

                <!-- Data Pemesanan (Aktif) -->
                <a href="/admin/pemesanan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/pemesanan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Data Pemesanan
                </a>

                <!-- Verifikasi Pembayaran -->
                <a href="/admin/verifikasi" class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->is('admin/verifikasi') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Verifikasi Pembayaran
                    </div>
                    <span class="bg-amber-500 text-zinc-950 font-bold text-xs px-2 py-0.5 rounded-full">5</span>
                </a>

                <!-- Kelola Denda -->
                <a href="/admin/denda" class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->is('admin/denda') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Kelola Denda
                    </div>
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                </a>

                <!-- Laporan -->
                <a href="/admin/laporan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/laporan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                    Laporan
                </a>
            </nav>

            <!-- Profil User -->
            <div class="p-3 mx-3 mb-4 rounded-2xl bg-zinc-900/90 border border-zinc-800 flex items-center justify-between shadow-inner">
                <div class="flex items-center gap-2.5">
                    <div class="w-8 h-8 rounded-full bg-portal-orange font-bold text-xs flex items-center justify-center text-white">DW</div>
                    <div class="leading-tight">
                        <div class="font-semibold text-xs text-white">Dimas Wicaksono</div>
                        <div class="text-[10px] text-zinc-400">Staff Operasional</div>
                    </div>
                </div>
                <button class="text-zinc-400 hover:text-white">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                </button>
            </div>
        </aside>

        <!--KONTEN UTAMA (Kanan)-->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-8 py-3.5 z-40 border-b border-zinc-200/60 flex items-center justify-between">
                <div class="relative w-96">
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-3.5 py-1.5 rounded-full shadow-sm text-xs font-semibold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Basecamp Sleman • Online
                    </div>
                    <button class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-600 hover:bg-zinc-50 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                    <button class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-600 hover:bg-zinc-50 shadow-sm relative">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
                    </button>
                    <div class="w-8 h-8 rounded-full bg-zinc-900 text-white font-bold text-xs flex items-center justify-center border border-zinc-300 shadow-sm">DW</div>
                </div>
            </header>

            <!-- Isi Halaman Data Pemesanan -->
            <div class="p-8 space-y-6">
                
                <!-- Judul Utama & Aksi -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-zinc-900 tracking-tight">Data Pemesanan</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Kelola seluruh data booking & penyewaan peralatan outdoor</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button class="bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Ekspor CSV
                        </button>
                        <button class="bg-portal-orange hover:bg-orange-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> + Tambah Pemesanan
                        </button>
                    </div>
                </div>

                <!-- 4 KARTU STATISTIK PEMESANAN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Kartu 1: Booking Aktif -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">38</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Booking Aktif</div>
                        </div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> +14% ↑
                        </span>
                    </div>

                    <!-- Kartu 2: Menunggu Pembayaran -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-amber-600">12</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Menunggu Pembayaran</div>
                        </div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            Perlu Aksi
                        </span>
                    </div>

                    <!-- Kartu 3: Sedang Berlangsung -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">21</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Sedang Berlangsung</div>
                        </div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Aktif
                        </span>
                    </div>

                    <!-- Kartu 4: Selesai Bulan Ini -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">156</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Selesai Bulan Ini</div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-zinc-100 text-zinc-600 border border-zinc-200 flex items-center gap-1">
                            ↗ +32 dari bln lalu
                        </span>
                    </div>
                </div>

                <!-- STATUS BOARD / TABEL DATA PEMESANAN -->
                <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">
                    
                    <!-- Header Action Board -->
                    <div class="p-5 border-b border-zinc-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="font-bold text-zinc-900 text-base">Daftar Pemesanan</h3>
                            <span class="text-xs font-medium text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">Filter: <span class="font-bold text-zinc-800" x-text="selectedFilter"></span></span>
                        </div>

                        <div class="flex items-center gap-2 relative">
                            <!-- Input Search Interaktif -->
                            <div x-show="showSearchInput" x-transition class="relative">
                                <input id="tableSearchInput" type="text" placeholder="Cari pemesanan..." class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">
                                <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>

                            <!-- Tombol Kaca Pembesar -->
                            <button @click="showSearchInput = !showSearchInput; $nextTick(() => document.getElementById('tableSearchInput')?.focus())" class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" title="Cari di tabel">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                            
                            <!-- Tombol Filter Interaktif -->
                            <div class="relative">
                                <button @click="showFilterDropdown = !showFilterDropdown" class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" title="Filter Status Pemesanan">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                </button>

                                <!-- Dropdown Menu Filter -->
                                <div x-show="showFilterDropdown" @click.away="showFilterDropdown = false" x-transition class="absolute right-0 mt-2 w-48 bg-white border border-zinc-200 rounded-xl shadow-lg py-1.5 z-50 text-xs font-medium">
                                    <div class="px-3 py-1.5 font-bold text-zinc-400 uppercase text-[10px] tracking-wider border-b border-zinc-100">Filter Status</div>
                                    <button @click="selectedFilter = 'Semua'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                                        <span>Semua Pemesanan</span>
                                        <span x-show="selectedFilter === 'Semua'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedFilter = 'Menunggu Bayar'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-amber-700 flex items-center justify-between">
                                        <span>Menunggu Bayar</span>
                                        <span x-show="selectedFilter === 'Menunggu Bayar'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedFilter = 'Berlangsung'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-blue-700 flex items-center justify-between">
                                        <span>Berlangsung</span>
                                        <span x-show="selectedFilter === 'Berlangsung'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedFilter = 'Selesai'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-emerald-700 flex items-center justify-between">
                                        <span>Selesai</span>
                                        <span x-show="selectedFilter === 'Selesai'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedFilter = 'Dibatalkan'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-red-600 flex items-center justify-between">
                                        <span>Dibatalkan</span>
                                        <span x-show="selectedFilter === 'Dibatalkan'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABEL PEMESANAN -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-center text-xs align-middle">
                            <thead>
                                <tr class="text-zinc-400 font-bold border-b border-zinc-100 bg-zinc-50/50">
                                    <th class="p-4 uppercase align-middle text-center">ID BOOKING</th>
                                    <th class="p-4 uppercase align-middle text-center">PELANGGAN</th>
                                    <th class="p-4 uppercase align-middle text-center">ITEM SEWA</th>
                                    <th class="p-4 uppercase align-middle text-center">JADWAL SEWA</th>
                                    <th class="p-4 uppercase align-middle text-center">TOTAL</th>
                                    <th class="p-4 uppercase align-middle text-center">STATUS</th>
                                    <th class="p-4 uppercase align-middle text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                
                                <!-- Baris 1: Rian Pratama - Menunggu Bayar -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="selectedFilter === 'Semua' || selectedFilter === 'Menunggu Bayar'">
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">#OG-0891</td>
                                    <td class="p-4 align-middle">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-zinc-200 font-bold text-zinc-700 flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=Rian+Pratama&background=e76f51&color=fff" alt="RP">
                                            </div>
                                            <div class="text-left">
                                                <span class="font-bold text-zinc-900 block">Rian Pratama</span>
                                                <span class="text-[11px] text-zinc-400">+62 812-4491-9921</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">3 item gear</div>
                                        <div class="text-[11px] text-zinc-400">Tenda, Carrier, Sleeping Bag</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">25 Okt - 28 Okt 2024</div>
                                        <div class="text-[11px] text-amber-600">3 Hari Durasi</div>
                                    </td>
                                    <td class="p-4 font-bold text-zinc-900 align-middle text-center">Rp 385.000</td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-block">Menunggu Bayar</span></td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Detail Pemesanan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <button class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Batalkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Baris 2: Nadia Safitri - Berlangsung -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="selectedFilter === 'Semua' || selectedFilter === 'Berlangsung'">
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">#OG-0889</td>
                                    <td class="p-4 align-middle">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-zinc-200 font-bold text-zinc-700 flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=Nadia+Safitri&background=2a9d8f&color=fff" alt="NS">
                                            </div>
                                            <div class="text-left">
                                                <span class="font-bold text-zinc-900 block">Nadia Safitri</span>
                                                <span class="text-[11px] text-zinc-400">+62 819-3321-1200</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">5 item gear</div>
                                        <div class="text-[11px] text-zinc-400">Tenda, Carrier, Kompor, Nesting, Matras</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">24 Okt - 27 Okt 2024</div>
                                        <div class="text-[11px] text-blue-600">Sedang Digunakan</div>
                                    </td>
                                    <td class="p-4 font-bold text-zinc-900 align-middle text-center">Rp 520.000</td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 inline-block">Berlangsung</span></td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Detail Pemesanan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <button class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Batalkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Baris 3: Dimas Anggoro - Berlangsung -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="selectedFilter === 'Semua' || selectedFilter === 'Berlangsung'">
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">#OG-0885</td>
                                    <td class="p-4 align-middle">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-zinc-200 font-bold text-zinc-700 flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=Dimas+Anggoro&background=264653&color=fff" alt="DA">
                                            </div>
                                            <div class="text-left">
                                                <span class="font-bold text-zinc-900 block">Dimas Anggoro</span>
                                                <span class="text-[11px] text-zinc-400">+62 857-1234-5678</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">2 item gear</div>
                                        <div class="text-[11px] text-zinc-400">Carrier, Sleeping Bag</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">23 Okt - 26 Okt 2024</div>
                                        <div class="text-[11px] text-blue-600">Sedang Digunakan</div>
                                    </td>
                                    <td class="p-4 font-bold text-zinc-900 align-middle text-center">Rp 240.000</td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200 inline-block">Berlangsung</span></td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Detail Pemesanan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <button class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Batalkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Baris 4: Maya Sinta - Selesai -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="selectedFilter === 'Semua' || selectedFilter === 'Selesai'">
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">#OG-0881</td>
                                    <td class="p-4 align-middle">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-zinc-200 font-bold text-zinc-700 flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=Maya+Sinta&background=f4a261&color=fff" alt="MS">
                                            </div>
                                            <div class="text-left">
                                                <span class="font-bold text-zinc-900 block">Maya Sinta</span>
                                                <span class="text-[11px] text-zinc-400">+62 878-9012-3456</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">4 item gear</div>
                                        <div class="text-[11px] text-zinc-400">Tenda, Carrier, Kompor, Headlamp</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">21 Okt - 24 Okt 2024</div>
                                        <div class="text-[11px] text-zinc-400">Selesai Normal</div>
                                    </td>
                                    <td class="p-4 font-bold text-zinc-900 align-middle text-center">Rp 460.000</td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-block">Selesai</span></td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Detail Pemesanan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <button class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Baris 5: Lolita Rein - Menunggu Bayar -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="selectedFilter === 'Semua' || selectedFilter === 'Menunggu Bayar'">
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">#OG-0895</td>
                                    <td class="p-4 align-middle">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-zinc-200 font-bold text-zinc-700 flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=Lolita+Rein&background=e9c46a&color=fff" alt="LR">
                                            </div>
                                            <div class="text-left">
                                                <span class="font-bold text-zinc-900 block">Lolita Rein</span>
                                                <span class="text-[11px] text-zinc-400">+62 813-2287-4390</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">2 item gear</div>
                                        <div class="text-[11px] text-zinc-400">Tenda, Matras</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">26 Okt - 29 Okt 2024</div>
                                        <div class="text-[11px] text-amber-600">3 Hari Durasi</div>
                                    </td>
                                    <td class="p-4 font-bold text-zinc-900 align-middle text-center">Rp 195.000</td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-block">Menunggu Bayar</span></td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Detail Pemesanan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <button class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Batalkan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                                <!-- Baris 6: Jack Johnson - Dibatalkan -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="selectedFilter === 'Semua' || selectedFilter === 'Dibatalkan'">
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">#OG-0878</td>
                                    <td class="p-4 align-middle">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-zinc-200 font-bold text-zinc-700 flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=Jack+Johnson&background=e76f51&color=fff" alt="JJ">
                                            </div>
                                            <div class="text-left">
                                                <span class="font-bold text-zinc-900 block">Jack Johnson</span>
                                                <span class="text-[11px] text-zinc-400">+62 821-9901-2311</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">
                                        <div class="font-semibold text-zinc-900">1 item gear</div>
                                        <div class="text-[11px] text-zinc-400">Carrier 50L</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-semibold text-zinc-900 line-through text-zinc-400">20 Okt - 22 Okt 2024</div>
                                        <div class="text-[11px] text-red-500">Dibatalkan Pelanggan</div>
                                    </td>
                                    <td class="p-4 font-bold text-zinc-400 align-middle text-center line-through">Rp 150.000</td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-200 inline-block">Dibatalkan</span></td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="inline-flex items-center gap-1.5">
                                            <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Detail Pemesanan">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                            </button>
                                            <button class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </div>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION FOOTER -->
                    <div class="p-4 border-t border-zinc-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500 font-medium">
                        <div>
                            Menampilkan data ke <span class="font-bold text-zinc-900">1 - 6</span> dari <span class="font-bold text-zinc-900">227</span> pemesanan
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-400 cursor-not-allowed">‹</button>
                            <button class="w-8 h-8 rounded-lg bg-zinc-900 text-white font-bold flex items-center justify-center">1</button>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">2</button>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">3</button>
                            <span class="px-1 text-zinc-400">...</span>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">38</button>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">›</button>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

</body>
</html>