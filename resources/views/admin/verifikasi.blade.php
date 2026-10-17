<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Verifikasi Pembayaran</title>
    <!-- Alpine.js -->
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
<body class="text-slate-800 antialiased" x-data="{ activeTab: 'Semua' }">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')


        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-8 py-3.5 z-30 border-b border-zinc-200/60 flex items-center justify-between">
                <div class="relative w-80">
            
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

            <!-- Isi Halaman Verifikasi Pembayaran -->
            <div class="p-8 space-y-6">
                
                <!-- Judul Utama & Aksi -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Verifikasi Pembayaran</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Validasi transfer bank dan bukti pembayaran pelanggan secara real-time.</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-xl border border-amber-200">
                            5 Transaksi Menunggu Validasi
                        </span>
                    </div>
                </div>

                <!-- 3 KARTU STATISTIK PEMBAYARAN -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm">
                        <div class="text-xs font-medium text-zinc-500">Menunggu Verifikasi</div>
                        <div class="text-2xl font-bold text-amber-600 mt-2">5 <span class="text-xs font-normal text-zinc-400">transaksi</span></div>
                        <div class="text-[11px] text-zinc-400 mt-1">Total: Rp 2.450.000</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm">
                        <div class="text-xs font-medium text-zinc-500">Terverifikasi Hari Ini</div>
                        <div class="text-2xl font-bold text-emerald-600 mt-2">18 <span class="text-xs font-normal text-zinc-400">transaksi</span></div>
                        <div class="text-[11px] text-zinc-400 mt-1">Total: Rp 6.820.000</div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/80 shadow-sm">
                        <div class="text-xs font-medium text-zinc-500">Ditolak / Perlu Perbaikan</div>
                        <div class="text-2xl font-bold text-rose-600 mt-2">1 <span class="text-xs font-normal text-zinc-400">transaksi</span></div>
                        <div class="text-[11px] text-zinc-400 mt-1">Bukti transfer buram</div>
                    </div>
                </div>

                <!-- TABEL VERIFIKASI -->
                <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">
                    <div class="p-5 border-b border-zinc-100 flex items-center justify-between">
                        <h3 class="font-bold text-zinc-900 text-sm">Daftar Antrean Pembayaran</h3>
                        <span class="text-xs text-zinc-400">Diperbarui beberapa menit lalu</span>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-zinc-50 text-zinc-400 font-bold border-b border-zinc-100">
                                    <th class="p-4 uppercase">KODE & PELANGGAN</th>
                                    <th class="p-4 uppercase">METODE</th>
                                    <th class="p-4 uppercase">JUMLAH</th>
                                    <th class="p-4 uppercase">STATUS</th>
                                    <th class="p-4 uppercase text-right">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                <tr class="hover:bg-zinc-50/60 transition">
                                    <td class="p-4">
                                        <div class="font-bold text-zinc-900">Fajar Hidayat</div>
                                        <div class="text-[11px] text-zinc-400">#OG-0892</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 font-semibold text-[11px]">BCA Virtual Account</span>
                                    </td>
                                    <td class="p-4 font-bold text-zinc-900">Rp 250.000</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Menunggu</span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3 py-1.5 rounded-lg text-xs shadow-sm transition">
                                            Verifikasi
                                        </button>
                                    </td>
                                </tr>
                                <tr class="hover:bg-zinc-50/60 transition">
                                    <td class="p-4">
                                        <div class="font-bold text-zinc-900">Anisa Tri Utami</div>
                                        <div class="text-[11px] text-zinc-400">#OG-0894</div>
                                    </td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-1 rounded-md bg-purple-50 text-purple-700 font-semibold text-[11px]">QRIS Merchant</span>
                                    </td>
                                    <td class="p-4 font-bold text-zinc-900">Rp 420.000</td>
                                    <td class="p-4">
                                        <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Menunggu</span>
                                    </td>
                                    <td class="p-4 text-right">
                                        <button class="bg-emerald-600 hover:bg-emerald-700 text-white font-semibold px-3 py-1.5 rounded-lg text-xs shadow-sm transition">
                                            Verifikasi
                                        </button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>
