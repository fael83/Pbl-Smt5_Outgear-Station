<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Admin Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800 antialiased">

    <div class="flex min-h-screen">

        <!-- =========================================================
             SIDEBAR KIRI (Sticky & Full Tinggi)
             ========================================================= -->
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
                <a href="/admin" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin') && !request()->is('admin/*') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                    Dashboard
                </a>
                <a href="/admin/pelanggan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/pelanggan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    Data Pelanggan
                </a>
                <a href="/admin/peralatan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/peralatan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                    Data Peralatan
                </a>
                <a href="/admin/pemesanan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/pemesanan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    Data Booking
                </a>
                <a href="/admin/verifikasi" class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->is('admin/verifikasi') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Verifikasi Pembayaran
                    </div>
                    <span class="bg-amber-500 text-zinc-950 font-bold text-xs px-2 py-0.5 rounded-full">5</span>
                </a>
                <a href="/admin/denda" class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->is('admin/denda') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
                    <div class="flex items-center gap-3">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Kelola Denda
                    </div>
                    <span class="w-2 h-2 bg-red-500 rounded-full"></span>
                </a>
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

        <!-- =========================================================
             KONTEN UTAMA (Kanan)
             ========================================================= -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-8 py-3.5 z-40 border-b border-zinc-200/60 flex items-center justify-between">
                <div class="relative w-96">
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-3.5 py-1.5 rounded-full shadow-sm text-xs font-semibold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Basecamp Malang • Online
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

            <!-- Isi Halaman Dashboard -->
            <div class="p-8 space-y-6">
                
                <!-- Judul & Aksi Utama -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="text-[11px] font-bold text-zinc-400 tracking-wider uppercase mb-1 flex items-center gap-1.5">
                            RINGKASAN HARIAN <span class="text-zinc-300">•</span> <span class="text-zinc-700 font-semibold">Jumat, 25 Oktober 2024</span>
                        </div>
                        <h2 class="text-2xl font-black text-zinc-900 tracking-tight">Operasional OutGear Station</h2>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button class="bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Ekspor
                        </button>
                        <button class="bg-portal-orange hover:bg-orange-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> Sewa Baru (Walk-in)
                        </button>
                    </div>
                </div>

                <!-- 4 KARTU STATISTIK ATAS -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Kartu 1 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2.5 rounded-xl bg-emerald-50 text-emerald-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-800">+14%</span>
                            </div>
                            <div class="text-[11px] font-bold tracking-wider text-zinc-400 uppercase">TOTAL BOOKING AKTIF</div>
                            <div class="text-2xl font-black text-zinc-900 mt-0.5">38 <span class="text-xs font-medium text-zinc-500">booking aktif</span></div>
                        </div>
                        <div class="text-[11px] text-zinc-500 font-medium mt-3 pt-2.5 border-t border-zinc-100">
                            8 unit dijadwalkan ambil hari ini
                        </div>
                    </div>

                    <!-- Kartu 2 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2.5 rounded-xl bg-amber-50 text-amber-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-amber-100 text-amber-800">Perlu Aksi</span>
                            </div>
                            <div class="text-[11px] font-bold tracking-wider text-zinc-400 uppercase">MENUNGGU VERIFIKASI</div>
                            <div class="text-2xl font-black text-zinc-900 mt-0.5">5 <span class="text-xs font-medium text-zinc-500">transaksi mutasi</span></div>
                        </div>
                        <div class="text-[11px] text-zinc-600 font-medium mt-3 pt-2.5 border-t border-zinc-100">
                            Total nominal: <span class="font-bold text-zinc-900">Rp 2.450.000</span>
                        </div>
                    </div>

                    <!-- Kartu 3 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2.5 rounded-xl bg-orange-50 text-portal-orange">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-orange-100 text-orange-800">Target Harian</span>
                            </div>
                            <div class="text-[11px] font-bold tracking-wider text-zinc-400 uppercase">PENDAPATAN SEWA HARI INI</div>
                            <div class="text-2xl font-black text-zinc-900 mt-0.5">Rp 3.820.000</div>
                        </div>
                        <div class="text-[11px] text-emerald-600 font-semibold mt-3 pt-2.5 border-t border-zinc-100 flex items-center gap-1">
                            <span>✓</span> 112% dari target harian
                        </div>
                    </div>

                    <!-- Kartu 4 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex flex-col justify-between">
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <div class="p-2.5 rounded-xl bg-red-50 text-red-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                </div>
                                <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-red-100 text-red-800">Perhatian</span>
                            </div>
                            <div class="text-[11px] font-bold tracking-wider text-zinc-400 uppercase">STOK ALAT KRITIS</div>
                            <div class="text-2xl font-black text-zinc-900 mt-0.5">4 <span class="text-xs font-medium text-zinc-500">kategori rendah</span></div>
                        </div>
                        <div class="text-[11px] text-red-600 font-semibold mt-3 pt-2.5 border-t border-zinc-100 truncate">
                            Tenda 4P & Cookset menipis
                        </div>
                    </div>
                </div>

                <!-- GRID UTAMA (12 Kolom: 8 Kiri untuk Aktivitas Booking & Banner, 4 Kanan untuk Peringatan Stok) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- KOLOM KIRI (Lebar 8) -->
                    <div class="lg:col-span-8 space-y-6 min-w-0">
                        
                        <!-- TABEL AKTIVITAS BOOKING TERBARU -->
                        <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm">
                            <div class="flex items-center justify-between mb-5">
                                <div>
                                    <h3 class="font-bold text-zinc-900 text-base">Aktivitas Booking Terbaru</h3>
                                    <p class="text-xs text-zinc-500">Daftar pemesanan dan jadwal pengambilan perlengkapan terkini.</p>
                                </div>
                                <a href="#" class="text-xs font-bold text-zinc-900 hover:text-portal-orange flex items-center gap-1 transition">
                                    Lihat Semua →
                                </a>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="text-zinc-400 font-bold border-b border-zinc-100">
                                            <th class="pb-3 uppercase">PELANGGAN</th>
                                            <th class="pb-3 uppercase">JADWAL SEWA</th>
                                            <th class="pb-3 uppercase">STATUS</th>
                                            <th class="pb-3 uppercase">TOTAL</th>
                                            <th class="pb-3 uppercase">AKSI</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                        <tr>
                                            <td class="py-4">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[10px] flex-shrink-0">RP</div>
                                                    <div>
                                                        <div class="font-bold text-zinc-900">Rian Pratama</div>
                                                        <div class="text-[11px] text-zinc-400">#OG-0891 • 3 item gear</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                <div class="font-semibold text-zinc-900">25 Okt - 28 Okt 2024</div>
                                                <div class="text-[11px] text-amber-600">3 Hari Durasi</div>
                                            </td>
                                            <td class="py-4">
                                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Bayar</span>
                                            </td>
                                            <td class="py-4 font-bold text-zinc-900">Rp 385.000</td>
                                            <td class="py-4">
                                                <a href="#" class="text-xs font-bold text-zinc-500 hover:text-portal-orange">Detail</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-4">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-[10px] flex-shrink-0">NS</div>
                                                    <div>
                                                        <div class="font-bold text-zinc-900">Nadia Safitri</div>
                                                        <div class="text-[11px] text-zinc-400">#OG-0889 • 5 item gear</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                <div class="font-semibold text-zinc-900">24 Okt - 27 Okt 2024</div>
                                                <div class="text-[11px] text-amber-600">4 Hari Durasi</div>
                                            </td>
                                            <td class="py-4">
                                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-blue-50 text-blue-700 border border-blue-200">DP Terbayar 50%</span>
                                            </td>
                                            <td class="py-4 font-bold text-zinc-900">Rp 520.000</td>
                                            <td class="py-4">
                                                <a href="#" class="text-xs font-bold text-zinc-500 hover:text-portal-orange">Detail</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-4">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[10px] flex-shrink-0">DA</div>
                                                    <div>
                                                        <div class="font-bold text-zinc-900">Dimas Anggoro</div>
                                                        <div class="text-[11px] text-zinc-400">#OG-0885 • 2 item gear</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                <div class="font-semibold text-zinc-900">23 Okt - 26 Okt 2024</div>
                                                <div class="text-[11px] text-emerald-600 font-medium">Sedang Digunakan</div>
                                            </td>
                                            <td class="py-4">
                                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Berlangsung</span>
                                            </td>
                                            <td class="py-4 font-bold text-zinc-900">Rp 240.000</td>
                                            <td class="py-4">
                                                <a href="#" class="text-xs font-bold text-zinc-500 hover:text-portal-orange">Detail</a>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="py-4">
                                                <div class="flex items-center gap-2.5">
                                                    <div class="w-8 h-8 rounded-full bg-zinc-200 text-zinc-700 font-bold flex items-center justify-center text-[10px] flex-shrink-0">MS</div>
                                                    <div>
                                                        <div class="font-bold text-zinc-900">Maya Sinta</div>
                                                        <div class="text-[11px] text-zinc-400">#OG-0881 • 4 item gear</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-4">
                                                <div class="font-semibold text-zinc-900">21 Okt - 24 Okt 2024</div>
                                                <div class="text-[11px] text-zinc-400">Selesai Normal</div>
                                            </td>
                                            <td class="py-4">
                                                <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-zinc-100 text-zinc-700 border border-zinc-200">Selesai</span>
                                            </td>
                                            <td class="py-4 font-bold text-zinc-900">Rp 460.000</td>
                                            <td class="py-4">
                                                <a href="#" class="text-xs font-bold text-zinc-500 hover:text-portal-orange">Detail</a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM KANAN (Lebar 4): PERINGATAN STOK -->
                    <div class="lg:col-span-4 space-y-6 w-full">
                        <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-zinc-900 text-base">Peringatan Stok</h3>
                                <a href="#" class="text-xs font-bold text-zinc-500 hover:text-portal-orange">Inventaris</a>
                            </div>
                            <p class="text-xs text-zinc-500 leading-relaxed">Unit berada di ambang batas minimum untuk jadwal sewa pekan ini.</p>

                            <div class="space-y-4 text-xs font-medium">
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="font-bold text-zinc-900">Tenda Eiger Shina 4P</span>
                                        <span class="font-bold text-red-600 bg-red-50 px-2.5 py-0.5 rounded border border-red-200">Sisa 1 Unit</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-red-500 h-full rounded-full" style="width: 8%;"></div>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-zinc-400 mt-1">
                                        <span>Tersedia: 1 dari 12</span>
                                        <span>11 Sedang Disewa</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="font-bold text-zinc-900">Carrier Deuter 50L</span>
                                        <span class="font-bold text-red-600 bg-red-50 px-2.5 py-0.5 rounded border border-red-200">Sisa 2 Unit</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-red-500 h-full rounded-full" style="width: 15%;"></div>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-zinc-400 mt-1">
                                        <span>Tersedia: 2 dari 15</span>
                                        <span>13 Sedang Disewa</span>
                                    </div>
                                </div>

                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="font-bold text-zinc-900">Nesting DS-308 Cookset</span>
                                        <span class="font-bold text-red-600 bg-red-100 px-2.5 py-0.5 rounded border border-red-200">Habis Total</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 h-2 rounded-full overflow-hidden">
                                        <div class="bg-red-600 h-full rounded-full" style="width: 0%;"></div>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-zinc-400 mt-1">
                                        <span>Tersedia: 0 dari 8</span>
                                        <span>8 Sedang Dipakai</span>
                                    </div>
                                </div>
                            </div>

                            <button class="w-full mt-2 bg-white hover:bg-zinc-50 border border-zinc-300 text-zinc-700 font-bold text-xs py-2.5 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                                <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg> Tarik Unit Antar-Basecamp
                            </button>
                        </div>
                    </div>
                </div>

                <!-- BAGIAN BAWAH: VERIFIKASI PEMBAYARAN (PINDAH KE BAWAH) -->
                <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm space-y-4">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <h3 class="font-bold text-zinc-900 text-base">Verifikasi Pembayaran</h3>
                            <span class="bg-amber-500 text-zinc-950 text-[11px] font-extrabold px-2.5 py-1 rounded-full">5 Antrean</span>
                        </div>
                        <a href="#" class="text-xs font-bold text-zinc-900 hover:text-portal-orange">Semua →</a>
                    </div>
                    <p class="text-xs text-zinc-500">Validasi mutasi bank pelanggan sebelum serah terima unit.</p>

                    <!-- Grid List Verifikasi Pembayaran (Bisa jadi 2 kolom ke samping agar rapi) -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <!-- Kartu Verifikasi 1 -->
                        <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-200/60 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="font-bold text-zinc-900 text-sm">Fajar Hidayat</div>
                                <div class="text-sm font-black text-zinc-900">Rp 250.000</div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-zinc-400 font-medium">
                                <span>#OG-0892 • BCA VA</span>
                                <span class="text-blue-700 font-bold bg-blue-50 px-2 py-0.5 rounded border border-blue-200">DP 50% Masuk</span>
                            </div>
                           <div class="flex items-center justify-end gap-2 pt-1">
                                <button class="w-auto bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow-sm transition flex items-center justify-center gap-1">
                                    <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> Verifikasi
                                </button>
                            </div>
                        </div>

                        <!-- Kartu Verifikasi 2 -->
                        <div class="bg-zinc-50 p-4 rounded-xl border border-zinc-200/60 space-y-3">
                            <div class="flex items-center justify-between">
                                <div class="font-bold text-zinc-900 text-sm">Anisa Tri Utami</div>
                                <div class="text-sm font-black text-zinc-900">Rp 420.000</div>
                            </div>
                            <div class="flex items-center justify-between text-[11px] text-zinc-400 font-medium">
                                <span>#OG-0894 • QRIS</span>
                                <span class="text-emerald-700 font-bold bg-emerald-50 px-2 py-0.5 rounded border border-emerald-200">Pelunasan Penuh</span>
                            </div>
                            <div class="flex items-center justify-end gap-2 pt-1">
                                    <button class="w-auto bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-bold py-2 px-4 rounded-lg shadow-sm transition flex items-center justify-center gap-1">
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg> Verifikasi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>