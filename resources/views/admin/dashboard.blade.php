<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Admin Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Chart.js untuk Donut Ring Chart -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-[#F8F6F2] text-slate-800 antialiased font-sans" x-data="{ showModalSewa: false }">

    <div class="flex min-h-screen">

        <!-- Sidebar Admin -->
        @include('partials.sidebar-admin')

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas (Bersih Tanpa Search Bar) -->
            <header class="sticky top-0 bg-[#F8F6F2]/90 backdrop-blur-md px-8 py-3.5 z-40 border-b border-zinc-200/60 flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Dashboard Operasional</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">
                        {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }}
                    </p>
                </div>

                <!-- Status & User Action -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-3.5 py-1.5 rounded-full shadow-sm text-xs font-medium text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Basecamp Malang • Online
                    </div>
                    <div x-data="{ isRefreshing: false }">
                        <button @click="isRefreshing = true; window.location.reload();" 
                                class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm transition flex items-center justify-center" 
                                title="Refresh Data">
                            <svg class="w-4 h-4" :class="{ 'animate-spin': isRefreshing }" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                    <button class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm relative transition" title="Notifikasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1 right-1 w-2 h-2 bg-portal-orange rounded-full"></span>
                    </button>
                    <a href="{{ route('admin.profile') }}" aria-label="Buka profil admin" title="Profil Admin"
                       class="w-8 h-8 rounded-full bg-zinc-900 text-white font-bold text-xs flex items-center justify-center border border-zinc-300 shadow-sm hover:ring-2 hover:ring-portal-orange focus:outline-none focus:ring-2 focus:ring-portal-orange transition">
                        DW
                    </a>
                </div>
            </header>

            <!-- Isi Halaman Dashboard -->
            <div class="p-8 space-y-6">
                
                

                <!-- 4 KARTU STATISTIK (Semua Kartu Putih Bersih, Proporsional, Tanpa Highlight Hitam) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    
                    <!-- Kartu 1: Booking Aktif -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Booking Aktif</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-blue-700 tracking-tight">38</div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-emerald-600 font-bold">8 ambil hari ini</span>
                        </div>
                    </div>

                    <!-- Kartu 2: Verifikasi Pembayaran -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Perlu Verifikasi</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-amber-600 tracking-tight"><span class="font-heading">5</span> <span class="text-xs font-semibold font-sans text-zinc-400">booking</span></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-amber-600 font-bold">Menunggu Validasi</span>
                        </div>
                    </div>

                    <!-- Kartu 3: Pendapatan Hari Ini -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Pendapatan Hari Ini</span>
                        <div class="my-2">
                            <div class="text-2xl font-black text-emerald-600 tracking-tight">Rp <span class="font-heading">3.820.000</span></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-zinc-400 font-medium">Target Harian</span>
                        </div>
                    </div>

                    <!-- Kartu 4: Stok Alat Kritis -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Stok Alat Kritis</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-rose-600 tracking-tight"><span class="font-heading">4</span> <span class="text-xs font-semibold font-sans text-zinc-400">item</span></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-rose-600 font-bold">Menipis / Habis</span>
                        </div>
                    </div>

                </div>

                <!-- GRID KONTEN UTAMA (8 Kiri, 4 Kanan) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-stretch">
                    
                    <!-- KOLOM KIRI (Lebar 8): TABEL BOOKING & VERIFIKASI PEMBAYARAN -->
                    <div class="lg:col-span-8 space-y-6 flex flex-col justify-between">
                        
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
                                                    <div class="font-semibold text-zinc-900">Rian Pratama</div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 font-medium text-zinc-800">
                                                25 - 28 Okt 2024
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
                                                    <div class="font-semibold text-zinc-900">Nadia Safitri</div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 font-medium text-zinc-800">
                                                24 - 27 Okt 2024
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
                                                    <div class="font-semibold text-zinc-900">Dimas Anggoro</div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 font-medium text-zinc-800">
                                                23 - 26 Okt 2024
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
                                                    <div class="font-semibold text-zinc-900">Maya Sinta</div>
                                                </div>
                                            </td>
                                            <td class="py-3.5 px-4 font-medium text-zinc-800">
                                                21 - 24 Okt 2024
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

                    <!-- KOLOM KANAN (Lebar 4): STATUS INVENTARIS ALAT DIBUAT PAS DI TENGAH BERSIH -->
                    <div class="lg:col-span-4 flex flex-col h-full">
                        <div class="bg-white p-6 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between h-full">
                            
                            <!-- Header Widget -->
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                                <div>
                                    <h3 class="font-bold text-zinc-900 text-sm">Status Inventaris Alat</h3>
                                    <p class="text-[11px] text-zinc-400">Unit terdistribusi hari ini</p>
                                </div>
                                <a href="/admin/peralatan" class="text-xs font-semibold text-portal-orange hover:underline">Detail →</a>
                            </div>

                            <!-- Area Donut Chart Presisi Pas di Tengah Vertikal & Horisontal -->
                            <div class="my-auto py-6 flex flex-col items-center justify-center">
                                <div class="relative flex items-center justify-center">
                                    <div class="w-44 h-44">
                                        <canvas id="inventoryChart"></canvas>
                                    </div>
                                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
                                        <span class="text-3xl font-black font-heading text-blue-700 leading-none">38</span>
                                        <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mt-1">UNIT DISEWA</span>
                                        <span class="text-[9px] text-zinc-400 font-medium mt-0.5">(dari 50 total)</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Breakdown Per Kategori Alat -->
                            <div class="space-y-2.5 pt-3 border-t border-zinc-100 text-xs">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-portal-orange"></span>
                                        <span class="font-medium text-zinc-700">Tenda Camping</span>
                                    </div>
                                    <span class="font-bold text-zinc-900">11 / 12 Unit</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-zinc-900"></span>
                                        <span class="font-medium text-zinc-700">Carrier & Ransel</span>
                                    </div>
                                    <span class="font-bold text-zinc-900">13 / 15 Unit</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                        <span class="font-medium text-zinc-700">Cookset & Kompor</span>
                                    </div>
                                    <span class="font-bold text-zinc-900">8 / 8 Unit</span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <span class="w-2.5 h-2.5 rounded-full bg-zinc-300"></span>
                                        <span class="font-medium text-zinc-700">Sleeping Bag & Matras</span>
                                    </div>
                                    <span class="font-bold text-zinc-900">6 / 15 Unit</span>
                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- MODAL FORM: SEWA BARU (WALK-IN) -->
    <div x-show="showModalSewa" class="fixed inset-0 z-50 overflow-y-auto bg-black/50 backdrop-blur-sm flex items-center justify-center p-4" x-cloak>
        <div class="bg-white rounded-3xl max-w-xl w-full p-6 space-y-6 shadow-2xl border border-zinc-100 relative animate-in fade-in zoom-in duration-200">
            
            <!-- Header Modal -->
            <div class="flex items-center justify-between border-b border-zinc-100 pb-4">
                <div>
                    <h3 class="text-lg font-black text-zinc-900">Pencatatan Sewa Baru (Walk-in)</h3>
                    <p class="text-xs text-zinc-400 mt-0.5">Input data pelanggan langsung dan pilih paket alat sewa basecamp.</p>
                </div>
                <button @click="showModalSewa = false" class="w-8 h-8 rounded-full bg-zinc-100 text-zinc-500 hover:bg-zinc-200 flex items-center justify-center transition">
                    ✕
                </button>
            </div>

            <!-- Form Inputs -->
            <form class="space-y-4 text-xs font-medium">
                <div>
                    <label class="block text-zinc-700 font-bold mb-1.5">Nama Lengkap Pelanggan</label>
                    <input type="text" placeholder="Masukkan nama sesuai identitas (KTP/KTM)" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-zinc-700 font-bold mb-1.5">Nomor WhatsApp</label>
                        <input type="text" placeholder="08xxxxxxxxxx" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                    </div>
                    <div>
                        <label class="block text-zinc-700 font-bold mb-1.5">Pilih Kategori Alat</label>
                        <select class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                            <option value="">Pilih paket / kategori...</option>
                            <option value="tenda">Tenda Camping (kap. 4)</option>
                            <option value="carrier">Carrier & Ransel 60L</option>
                            <option value="cookset">Paket Cookset & Kompor</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-zinc-700 font-bold mb-1.5">Tanggal Ambil</label>
                        <input type="date" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                    </div>
                    <div>
                        <label class="block text-zinc-700 font-bold mb-1.5">Tanggal Kembali</label>
                        <input type="date" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                    </div>
                </div>

                <div>
                    <label class="block text-zinc-700 font-bold mb-1.5">Catatan Tambahan (Opsional)</label>
                    <textarea rows="3" placeholder="Contoh: Titip KTP asli sebagai jaminan..." class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 text-zinc-800 focus:outline-none focus:border-portal-orange transition"></textarea>
                </div>

                <!-- Footer Aksi Modal -->
                <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                    <button type="button" @click="showModalSewa = false" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-bold px-5 py-2.5 rounded-xl transition">
                        Batal
                    </button>
                    <button type="submit" class="bg-portal-orange hover:bg-orange-600 text-white font-bold px-5 py-2.5 rounded-xl transition shadow-sm">
                        Simpan & Proses Sewa
                    </button>
                </div>
            </form>

        </div>
    </div>

    <!-- SCRIPT CHART.JS UNTUK DONUT CHART JUMLAH UNIT -->
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const ctx = document.getElementById('inventoryChart').getContext('2d');
            new Chart(ctx, {
                type: 'doughnut',
                data: {
                    labels: ['Sedang Disewa', 'Tersedia di Basecamp'],
                    datasets: [{
                        data: [38, 12], // 38 Unit Disewa, 12 Unit Tersedia
                        backgroundColor: [
                            '#EA580C', // portal-orange
                            '#E4E4E7'  // zinc-200
                        ],
                        borderWidth: 0,
                        hoverOffset: 4
                    }]
                },
                options: {
                    cutout: '76%',
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return context.label + ': ' + context.raw + ' Unit';
                                }
                            }
                        }
                    }
                }
            });
        });
    </script>

</body>
</html>