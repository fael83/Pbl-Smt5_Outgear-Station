<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Laporan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800 antialiased">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')


        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-8 py-3.5 z-30 border-b border-zinc-200/60 flex items-center justify-between">
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-3.5 py-1.5 rounded-full shadow-sm text-xs font-semibold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        BASECAMP SLEMAN - AKTIF
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

            <!-- Isi Halaman Laporan -->
            <div class="p-8 space-y-6">
                
                <!-- Judul Utama & Aksi -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Laporan & Rekapitulasi</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Rekapitulasi pendapatan sewa, statistik barang sering disewa, dan performa operasional basecamp.</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button class="bg-portal-orange hover:bg-orange-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Unduh Laporan Bulanan (PDF/Excel)
                        </button>
                    </div>
                </div>

                <!-- 3 KARTU STATISTIK LAPORAN -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm">
                        <div class="text-xs font-medium text-zinc-500">Total Pendapatan Bulan Ini</div>
                        <div class="text-2xl font-bold text-zinc-900 mt-2">Rp 48.520.000</div>
                        <div class="text-[11px] text-emerald-600 font-semibold mt-1">↑ +18.4% dibanding bulan lalu</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm">
                        <div class="text-xs font-medium text-zinc-500">Total Transaksi Booking</div>
                        <div class="text-2xl font-bold text-zinc-900 mt-2">142 Selesai</div>
                        <div class="text-[11px] text-zinc-400 mt-1">Rata-rata 4.7 booking / hari</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm">
                        <div class="text-xs font-medium text-zinc-500">Alat Terfavorit</div>
                        <div class="text-2xl font-bold text-zinc-900 mt-2">Tenda 4P</div>
                        <div class="text-[11px] text-zinc-400 mt-1">Tingkat utilisasi 92%</div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
