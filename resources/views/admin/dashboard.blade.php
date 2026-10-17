<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Admin Portal</title>
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

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-8 py-3.5 z-40 border-b border-zinc-200/60 flex items-center justify-between">
                <!-- Search Bar Ringkas -->
                <div class="relative w-80">
                </div>

                <!-- Status & User Action -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-3.5 py-1.5 rounded-full shadow-sm text-xs font-medium text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Basecamp Malang • Online
                    </div>
                    <button class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm transition" title="Refresh">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                    <button class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm relative transition" title="Notifikasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-portal-orange rounded-full"></span>
                    </button>
                    <div class="w-8 h-8 rounded-full bg-zinc-900 text-white font-bold text-xs flex items-center justify-center border border-zinc-300 shadow-sm">DW</div>
                </div>
            </header>

            <!-- Isi Halaman Dashboard -->
            <div class="p-8 space-y-6">
                
                <!-- Judul & Aksi Utama (Clean & Minimal) -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Dashboard Operasional</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Jumat, 25 Oktober 2024 • Ringkasan aktivitas harian basecamp</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button class="bg-white border border-zinc-200/80 hover:bg-zinc-50 text-zinc-700 text-xs font-medium px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-2">
                            <svg class="w-4 h-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Ekspor Data
                        </button>
                        <button class="bg-portal-orange hover:bg-orange-600 text-white text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm shadow-orange-900/10 transition flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Sewa Baru (Walk-in)
                        </button>
                    </div>
                </div>

                <!-- 4 KARTU STATISTIK (Clean & Easy to Read) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Kartu 1: Booking Aktif -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-zinc-500">Booking Aktif</span>
                            <div class="p-2 rounded-xl bg-emerald-50 text-emerald-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl font-bold text-zinc-900">38</div>
                            <div class="flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium mt-1">
                                <span>↑ +14%</span>
                                <span class="text-zinc-400">• 8 ambil hari ini</span>
                            </div>
                        </div>
                    </div>

                    <!-- Kartu 2: Verifikasi Pembayaran -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-zinc-500">Perlu Verifikasi</span>
                            <div class="p-2 rounded-xl bg-amber-50 text-amber-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl font-bold text-zinc-900">5</div>
                            <div class="text-[11px] text-amber-600 font-medium mt-1">
                                Rp 2.450.000 menunggu validasi
                            </div>
                        </div>
                    </div>

                    <!-- Kartu 3: Pendapatan Hari Ini -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-zinc-500">Pendapatan Hari Ini</span>
                            <div class="p-2 rounded-xl bg-orange-50 text-portal-orange">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl font-bold text-zinc-900">Rp 3.820.000</div>
                            <div class="flex items-center gap-1 text-[11px] text-emerald-600 font-medium mt-1">
                                <span>✓</span> 112% dari target harian
                            </div>
                        </div>
                    </div>

                    <!-- Kartu 4: Stok Kritis -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-medium text-zinc-500">Stok Alat Kritis</span>
                            <div class="p-2 rounded-xl bg-rose-50 text-rose-600">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                        </div>
                        <div class="mt-3">
                            <div class="text-2xl font-bold text-zinc-900">4 <span class="text-xs font-normal text-zinc-400">item</span></div>
                            <div class="text-[11px] text-rose-600 font-medium mt-1">
                                Tenda & Cookset menipis
                            </div>
                        </div>
                    </div>
                </div>

                <!-- GRID KONTEN UTAMA (8 Kiri, 4 Kanan) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                    
                    <!-- KOLOM KIRI (Lebar 8) -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- TABEL AKTIVITAS BOOKING TERBARU -->
                        <div class="bg-white rounded-2xl border border-zinc-200/70 shadow-sm overflow-hidden">
                            <div class="p-5 border-b border-zinc-100 flex items-center justify-between">
                                <div>
                                    <h3 class="font-bold text-zinc-900 text-sm">Aktivitas Booking Terbaru</h3>
                                    <p class="text-xs text-zinc-400 mt-0.5">Pantau status sewa dan jadwal pengambilan</p>
                                </div>
                                <a href="/admin/pemesanan" class="text-xs font-semibold text-portal-orange hover:underline flex items-center gap-1 transition">
                                    Lihat Semua →
                                </a>
                            </div>

                            <div class="overflow-x-auto">
                                <table class="w-full text-left text-xs">
                                    <thead>
                                        <tr class="bg-zinc-50/70 text-zinc-400 font-semibold uppercase text-[10px] tracking-wider border-b border-zinc-100">
                                            <th class="py-3 px-5">Pelanggan</th>
                                            <th class="py-3 px-4">Jadwal Sewa</th>
                                            <th class="py-3 px-4">Status</th>
                                            <th class="py-3 px-4">Total</th>
                                            <th class="py-3 px-5 text-right">Aksi</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100">
                                        <!-- Row 1 -->
                                        <tr class="hover:bg-zinc-50/50 transition">
                                            <td class="py-3.5 px-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-amber-100 text-amber-800 font-bold flex items-center justify-center text-[11px] flex-shrink-0">RP</div>
                                                    <div>
                                                        <div class="font-semibold text-zinc-900">Rian Pratama</div>
                                                        <div class="text-[11px] text-zinc-400">#OG-0891 • 3 item</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-medium text-zinc-800">25 - 28 Okt 2024</div>
                                                <div class="text-[11px] text-zinc-400">3 Hari</div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-amber-50 text-amber-700">
                                                    Menunggu Bayar
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 font-semibold text-zinc-900">
                                                Rp 385.000
                                            </td>
                                            <td class="py-3.5 px-5 text-right">
                                                <a href="/admin/pemesanan" class="text-xs font-semibold text-zinc-500 hover:text-portal-orange transition">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Row 2 -->
                                        <tr class="hover:bg-zinc-50/50 transition">
                                            <td class="py-3.5 px-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-blue-100 text-blue-800 font-bold flex items-center justify-center text-[11px] flex-shrink-0">NS</div>
                                                    <div>
                                                        <div class="font-semibold text-zinc-900">Nadia Safitri</div>
                                                        <div class="text-[11px] text-zinc-400">#OG-0889 • 5 item</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-medium text-zinc-800">24 - 27 Okt 2024</div>
                                                <div class="text-[11px] text-zinc-400">4 Hari</div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-blue-50 text-blue-700">
                                                    DP 50% Masuk
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 font-semibold text-zinc-900">
                                                Rp 520.000
                                            </td>
                                            <td class="py-3.5 px-5 text-right">
                                                <a href="/admin/pemesanan" class="text-xs font-semibold text-zinc-500 hover:text-portal-orange transition">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Row 3 -->
                                        <tr class="hover:bg-zinc-50/50 transition">
                                            <td class="py-3.5 px-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[11px] flex-shrink-0">DA</div>
                                                    <div>
                                                        <div class="font-semibold text-zinc-900">Dimas Anggoro</div>
                                                        <div class="text-[11px] text-zinc-400">#OG-0885 • 2 item</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-medium text-zinc-800">23 - 26 Okt 2024</div>
                                                <div class="text-[11px] text-emerald-600">Berjalan</div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-emerald-50 text-emerald-700">
                                                    Sedang Disewa
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 font-semibold text-zinc-900">
                                                Rp 240.000
                                            </td>
                                            <td class="py-3.5 px-5 text-right">
                                                <a href="/admin/pemesanan" class="text-xs font-semibold text-zinc-500 hover:text-portal-orange transition">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>

                                        <!-- Row 4 -->
                                        <tr class="hover:bg-zinc-50/50 transition">
                                            <td class="py-3.5 px-5">
                                                <div class="flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-zinc-200 text-zinc-700 font-bold flex items-center justify-center text-[11px] flex-shrink-0">MS</div>
                                                    <div>
                                                        <div class="font-semibold text-zinc-900">Maya Sinta</div>
                                                        <div class="text-[11px] text-zinc-400">#OG-0881 • 4 item</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <div class="font-medium text-zinc-800">21 - 24 Okt 2024</div>
                                                <div class="text-[11px] text-zinc-400">Selesai</div>
                                            </td>
                                            <td class="py-3.5 px-4">
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium bg-zinc-100 text-zinc-600">
                                                    Selesai
                                                </span>
                                            </td>
                                            <td class="py-3.5 px-4 font-semibold text-zinc-900">
                                                Rp 460.000
                                            </td>
                                            <td class="py-3.5 px-5 text-right">
                                                <a href="/admin/pemesanan" class="text-xs font-semibold text-zinc-500 hover:text-portal-orange transition">
                                                    Detail
                                                </a>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- VERIFIKASI PEMBAYARAN CEPAT -->
                        <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <h3 class="font-bold text-zinc-900 text-sm">Verifikasi Pembayaran</h3>
                                    <span class="bg-amber-100 text-amber-800 text-[11px] font-bold px-2 py-0.5 rounded-full">5 Menunggu</span>
                                </div>
                                <a href="/admin/verifikasi" class="text-xs font-semibold text-portal-orange hover:underline">
                                    Kelola Semua →
                                </a>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                <!-- Card 1 -->
                                <div class="p-3.5 rounded-xl bg-zinc-50/80 border border-zinc-200/60 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-zinc-900 text-xs truncate">Fajar Hidayat</div>
                                        <div class="text-[11px] text-zinc-400 mt-0.5">BCA VA • <span class="font-bold text-zinc-900">Rp 250.000</span></div>
                                    </div>
                                    <button class="flex-shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold py-1.5 px-3 rounded-lg shadow-sm transition flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                        Konfirmasi
                                    </button>
                                </div>

                                <!-- Card 2 -->
                                <div class="p-3.5 rounded-xl bg-zinc-50/80 border border-zinc-200/60 flex items-center justify-between gap-3">
                                    <div class="min-w-0">
                                        <div class="font-semibold text-zinc-900 text-xs truncate">Anisa Tri Utami</div>
                                        <div class="text-[11px] text-zinc-400 mt-0.5">QRIS • <span class="font-bold text-zinc-900">Rp 420.000</span></div>
                                    </div>
                                    <button class="flex-shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold py-1.5 px-3 rounded-lg shadow-sm transition flex items-center gap-1">
                                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"></path></svg>
                                        Konfirmasi
                                    </button>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- KOLOM KANAN (Lebar 4) -->
                    <div class="lg:col-span-4 space-y-6">
                        
                        <!-- PERINGATAN STOK ALAT -->
                        <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm space-y-4">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-zinc-900 text-sm">Peringatan Stok</h3>
                                <a href="/admin/peralatan" class="text-xs font-semibold text-zinc-400 hover:text-portal-orange transition">Inventaris →</a>
                            </div>

                            <div class="space-y-4 text-xs">
                                <!-- Item 1 -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="font-medium text-zinc-800">Tenda Eiger Shina 4P</span>
                                        <span class="text-[11px] font-bold text-rose-600 bg-rose-50 px-2 py-0.5 rounded">Sisa 1</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-rose-500 h-full rounded-full" style="width: 8%;"></div>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-zinc-400 mt-1">
                                        <span>Tersedia: 1 dari 12 unit</span>
                                        <span>11 Disewa</span>
                                    </div>
                                </div>

                                <!-- Item 2 -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="font-medium text-zinc-800">Carrier Deuter 50L</span>
                                        <span class="text-[11px] font-bold text-amber-600 bg-amber-50 px-2 py-0.5 rounded">Sisa 2</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-amber-500 h-full rounded-full" style="width: 15%;"></div>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-zinc-400 mt-1">
                                        <span>Tersedia: 2 dari 15 unit</span>
                                        <span>13 Disewa</span>
                                    </div>
                                </div>

                                <!-- Item 3 -->
                                <div>
                                    <div class="flex items-center justify-between mb-1.5">
                                        <span class="font-medium text-zinc-800">Nesting DS-308 Cookset</span>
                                        <span class="text-[11px] font-bold text-rose-700 bg-rose-100 px-2 py-0.5 rounded">Habis</span>
                                    </div>
                                    <div class="w-full bg-zinc-100 h-1.5 rounded-full overflow-hidden">
                                        <div class="bg-rose-600 h-full rounded-full" style="width: 0%;"></div>
                                    </div>
                                    <div class="flex justify-between text-[11px] text-zinc-400 mt-1">
                                        <span>Tersedia: 0 dari 8 unit</span>
                                        <span>8 Disewa</span>
                                    </div>
                                </div>
                            </div>

                            <a href="/admin/peralatan" class="w-full bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 text-zinc-700 font-semibold text-xs py-2 rounded-xl transition flex items-center justify-center gap-2 shadow-sm">
                                <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path></svg>
                                Tarik Unit / Restok
                            </a>
                        </div>

                        <!-- JADWAL PENGEMBALIAN HARI INI -->
                        <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm space-y-3.5">
                            <div class="flex items-center justify-between">
                                <h3 class="font-bold text-zinc-900 text-sm">Pengembalian Hari Ini</h3>
                                <span class="text-xs font-bold text-zinc-700 bg-zinc-100 px-2.5 py-0.5 rounded-full">4 / 6 Unit</span>
                            </div>
                            <p class="text-xs text-zinc-400">2 booking belum mengembalikan alat sebelum pukul 20:00 WIB.</p>
                            
                            <div class="space-y-2.5">
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-zinc-50 border border-zinc-100 text-xs">
                                    <div>
                                        <div class="font-semibold text-zinc-800">Dimas Anggoro</div>
                                        <div class="text-[11px] text-zinc-400">Tenda 4P, Carrier 50L</div>
                                    </div>
                                    <span class="text-[10px] font-bold text-emerald-700 bg-emerald-50 px-2 py-0.5 rounded">Tepat Waktu</span>
                                </div>
                                <div class="flex items-center justify-between p-2.5 rounded-xl bg-amber-50/60 border border-amber-200/60 text-xs">
                                    <div>
                                        <div class="font-semibold text-zinc-800">Budi Santoso</div>
                                        <div class="text-[11px] text-zinc-400">Flysheet 3x4, Matras</div>
                                    </div>
                                    <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">Belum Kembali</span>
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