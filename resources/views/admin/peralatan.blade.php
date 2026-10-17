<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Data Peralatan</title>
    <!-- Alpine.js untuk interaktivitas modal, search, & filter tab -->
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
<body class="text-slate-800 antialiased" x-data="{ 
    showSearchInput: false, 
    showAddModal: false, 
    activeTab: 'Semua' 
}">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')


        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-8 py-3.5 z-30 border-b border-zinc-200/60 flex items-center justify-between">
                <div class="relative w-96"></div>

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

            <!-- Isi Halaman Data Peralatan -->
            <div class="p-8 space-y-6">
                
                <!-- Judul Utama & Aksi -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-zinc-900 tracking-tight">Data Peralatan</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Manajemen katalog master alat outdoor, tarif sewa harian, nilai pengganti, dan status ketersediaan unit.</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button class="bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Ekspor CSV
                        </button>
                        <button @click="showAddModal = true" class="bg-portal-orange hover:bg-orange-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> + Tambah Peralatan
                        </button>
                    </div>
                </div>

                <!-- 3 KARTU STATISTIK PERALATAN -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">112</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Tersedia</div>
                        </div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 60.2% Rasio Siap
                        </span>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">58</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Sedang Disewa</div>
                        </div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                            31.2% Aktif Lapangan
                        </span>
                    </div>

                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">186</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Total Unit Terdaftar</div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-zinc-100 text-zinc-600 border border-zinc-200 flex items-center gap-1">
                            ↗ +28 SKU Aktif
                        </span>
                    </div>
                </div>

                <!-- STATUS BOARD / TABEL DATA PERALATAN -->
                <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">
                    
                    <!-- Header Action Board & Filter Tab -->
                    <div class="p-5 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-2 overflow-x-auto pb-1 sm:pb-0">
                            <button @click="activeTab = 'Semua'" :class="activeTab === 'Semua' ? 'bg-zinc-900 text-white font-bold' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 font-medium'" class="px-3.5 py-1.5 rounded-xl text-xs transition">Semua (28)</button>
                            <button @click="activeTab = 'Siap Sewa'" :class="activeTab === 'Siap Sewa' ? 'bg-zinc-900 text-white font-bold' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 font-medium'" class="px-3.5 py-1.5 rounded-xl text-xs transition">Siap Sewa (18)</button>
                            <button @click="activeTab = 'Sedang Disewa'" :class="activeTab === 'Sedang Disewa' ? 'bg-zinc-900 text-white font-bold' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 font-medium'" class="px-3.5 py-1.5 rounded-xl text-xs transition">Sedang Disewa (7)</button>
                            <button @click="activeTab = 'Cuci & QC'" :class="activeTab === 'Cuci & QC' ? 'bg-zinc-900 text-white font-bold' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 font-medium'" class="px-3.5 py-1.5 rounded-xl text-xs transition">Cuci & QC (2)</button>
                            <button @click="activeTab = 'Perlu Perbaikan'" :class="activeTab === 'Perlu Perbaikan' ? 'bg-zinc-900 text-white font-bold' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200 font-medium'" class="px-3.5 py-1.5 rounded-xl text-xs transition">Perlu Perbaikan (1)</button>
                        </div>

                        <div class="flex items-center gap-2 relative">
                            <div x-show="showSearchInput" x-transition class="relative">
                                <input id="tableSearchInput" type="text" placeholder="Cari alat / SKU..." class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">
                                <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <button @click="showSearchInput = !showSearchInput; $nextTick(() => document.getElementById('tableSearchInput')?.focus())" class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" title="Cari peralatan">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                        </div>
                    </div>

                    <!-- TABEL DATA PERALATAN (Rata Tengah) -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-center text-xs align-middle">
                            <thead>
                                <tr class="text-zinc-400 font-bold border-b border-zinc-100 bg-zinc-50/50">
                                    <th class="p-4 uppercase align-middle text-center">FOTO & NAMA ALAT</th>
                                    <th class="p-4 uppercase align-middle text-center">ID & SKU</th>
                                    <th class="p-4 uppercase align-middle text-center">KATEGORI</th>
                                    <th class="p-4 uppercase align-middle text-center">TARIF & DEPOSIT</th>
                                    <th class="p-4 uppercase align-middle text-center">NILAI PENGGANTI</th>
                                    <th class="p-4 uppercase align-middle text-center">STOK UNIT</th>
                                    <th class="p-4 uppercase align-middle text-center">STATUS</th>
                                    <th class="p-4 uppercase align-middle text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                
                                <!-- Baris 1: Tersedia -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="activeTab === 'Semua' || activeTab === 'Siap Sewa'">
                                    <td class="p-4 align-middle text-center">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                                <img src="https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=100&q=80" alt="Tenda" class="w-full h-full object-cover">
                                            </div>
                                            <div class="text-left">
                                                <div class="font-bold text-zinc-900">Tenda Dome Eiger Shira 4P</div>
                                                <div class="text-[10px] text-zinc-400">Alu-frame • 4 Orang</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">
                                        <div>EQ-001</div>
                                        <div class="text-[10px] text-zinc-400">TND-EIG-001</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <span class="px-2 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">Tenda & Shelter</span>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-bold text-zinc-900">Rp 45.000 <span class="text-[10px] text-zinc-400 font-normal">/hari</span></div>
                                        <div class="text-[10px] text-zinc-500">Deposit Rp 100.000</div>
                                    </td>
                                    <td class="p-4 font-mono font-bold text-zinc-800 align-middle text-center">Rp 1.250.000</td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-bold text-zinc-900">15 Unit Total</div>
                                        <div class="text-[10px] text-emerald-600 font-semibold">11 Tersedia • 4 Sewa</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-block">Tersedia</span>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition inline-flex items-center justify-center" title="Detail / Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 2: Kritis / Perlu Perbaikan -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="activeTab === 'Semua' || activeTab === 'Perlu Perbaikan'">
                                    <td class="p-4 align-middle text-center">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                                <img src="https://images.unsplash.com/photo-1622543925917-763c33d1a86b?auto=format&fit=crop&w=100&q=80" alt="Carrier" class="w-full h-full object-cover">
                                            </div>
                                            <div class="text-left">
                                                <div class="font-bold text-zinc-900">Carrier Osprey Atmos AG 65L</div>
                                                <div class="text-[10px] text-zinc-400">Anti-Gravity • Torso M/L</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">
                                        <div>EQ-002</div>
                                        <div class="text-[10px] text-zinc-400">CAR-OSP-065</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <span class="px-2 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">Tas Carrier</span>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-bold text-zinc-900">Rp 50.000 <span class="text-[10px] text-zinc-400 font-normal">/hari</span></div>
                                        <div class="text-[10px] text-zinc-500">Deposit Rp 150.000</div>
                                    </td>
                                    <td class="p-4 font-mono font-bold text-zinc-800 align-middle text-center">Rp 3.800.000</td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-bold text-zinc-900">12 Unit Total</div>
                                        <div class="text-[10px] text-red-600 font-semibold">1 Tersedia • 10 Sewa</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-200 inline-block">Kritis</span>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition inline-flex items-center justify-center" title="Detail / Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 3: Tersedia -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="activeTab === 'Semua' || activeTab === 'Siap Sewa'">
                                    <td class="p-4 align-middle text-center">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                                <img src="https://images.unsplash.com/photo-1510312305653-8ed496efae75?auto=format&fit=crop&w=100&q=80" alt="Kompor" class="w-full h-full object-cover">
                                            </div>
                                            <div class="text-left">
                                                <div class="font-bold text-zinc-900">Kompor Windproof Lotus Kovar</div>
                                                <div class="text-[10px] text-zinc-400">Piezo Ignition • Windshield</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">
                                        <div>EQ-003</div>
                                        <div class="text-[10px] text-zinc-400">CKG-KVR-001</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <span class="px-2 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">Alat Masak</span>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-bold text-zinc-900">Rp 15.000 <span class="text-[10px] text-zinc-400 font-normal">/hari</span></div>
                                        <div class="text-[10px] text-zinc-500">Deposit Rp 30.000</div>
                                    </td>
                                    <td class="p-4 font-mono font-bold text-zinc-800 align-middle text-center">Rp 185.000</td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-bold text-zinc-900">24 Unit Total</div>
                                        <div class="text-[10px] text-emerald-600 font-semibold">16 Tersedia • 8 Sewa</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-block">Tersedia</span>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition inline-flex items-center justify-center" title="Detail / Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 4: Cuci & QC -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="activeTab === 'Semua' || activeTab === 'Cuci & QC'">
                                    <td class="p-4 align-middle text-center">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-zinc-100 border border-zinc-200 overflow-hidden flex-shrink-0 flex items-center justify-center">
                                                <img src="https://images.unsplash.com/photo-1526772662000-3f88f10405ff?auto=format&fit=crop&w=100&q=80" alt="Sleeping Bag" class="w-full h-full object-cover">
                                            </div>
                                            <div class="text-left">
                                                <div class="font-bold text-zinc-900">Sleeping Bag Consina Snug 10°C</div>
                                                <div class="text-[10px] text-zinc-400">Mummy Type • Ripstop Nylon</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">
                                        <div>EQ-004</div>
                                        <div class="text-[10px] text-zinc-400">SLP-CNS-010</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <span class="px-2 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200">Sleeping Gear</span>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-bold text-zinc-900">Rp 12.000 <span class="text-[10px] text-zinc-400 font-normal">/hari</span></div>
                                        <div class="text-[10px] text-zinc-500">Deposit Rp 25.000</div>
                                    </td>
                                    <td class="p-4 font-mono font-bold text-zinc-800 align-middle text-center">Rp 245.000</td>
                                    <td class="p-4 align-middle text-center">
                                        <div class="font-bold text-zinc-900">30 Unit Total</div>
                                        <div class="text-[10px] text-amber-600 font-semibold">18 Tersedia • 12 Sewa</div>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200 inline-block">Cuci & QC</span>
                                    </td>
                                    <td class="p-4 align-middle text-center">
                                        <button class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition inline-flex items-center justify-center" title="Detail / Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION FOOTER -->
                    <div class="p-4 border-t border-zinc-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500 font-medium">
                        <div>
                            Menampilkan data ke <span class="font-bold text-zinc-900">1 - 4</span> dari <span class="font-bold text-zinc-900">28</span> SKU alat
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-400 cursor-not-allowed">‹</button>
                            <button class="w-8 h-8 rounded-lg bg-zinc-900 text-white font-bold flex items-center justify-center">1</button>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">2</button>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">3</button>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">4</button>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">›</button>
                        </div>
                    </div>

                </div>

            </div>
        </main>

        <!-- MODAL TAMBAH PERALATAN (Pop-up Interaktif) -->
        <div x-show="showAddModal" class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4" x-transition.opacity>
            <div @click.away="showAddModal = false" class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden border border-zinc-100" x-transition.scale>
                
                <!-- Modal Header -->
                <div class="px-6 py-4 border-b border-zinc-100 flex items-center justify-between bg-zinc-50">
                    <h3 class="font-bold text-zinc-900 text-sm">Tambah Katalog Peralatan Baru</h3>
                    <button @click="showAddModal = false" class="text-zinc-400 hover:text-zinc-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>

                <!-- Modal Body Form -->
                <form @submit.prevent="showAddModal = false" class="p-6 space-y-4 text-xs">
                    <div>
                        <label class="block font-bold text-zinc-700 mb-1">Nama Alat</label>
                        <input type="text" placeholder="Contoh: Tenda Dome Eiger..." class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-portal-orange text-zinc-800" required>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">ID & SKU</label>
                            <input type="text" placeholder="EQ-005 / TND-XXX" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-portal-orange text-zinc-800" required>
                        </div>
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Kategori</label>
                            <select class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-portal-orange text-zinc-800">
                                <option>Tenda & Shelter</option>
                                <option>Tas Carrier</option>
                                <option>Alat Masak</option>
                                <option>Sleeping Gear</option>
                                <option>Elektronik</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Tarif Sewa (Rp / Hari)</label>
                            <input type="number" placeholder="45000" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-portal-orange text-zinc-800" required>
                        </div>
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Nilai Pengganti (Rp)</label>
                            <input type="number" placeholder="1250000" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-portal-orange text-zinc-800" required>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Total Stok Unit</label>
                            <input type="number" placeholder="15" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-portal-orange text-zinc-800" required>
                        </div>
                        <div>
                            <label class="block font-bold text-zinc-700 mb-1">Status Awal</label>
                            <select class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-3 py-2.5 focus:outline-none focus:ring-1 focus:ring-portal-orange text-zinc-800">
                                <option>Tersedia</option>
                                <option>Cuci & QC</option>
                                <option>Kritis / Perbaikan</option>
                            </select>
                        </div>
                    </div>

                    <!-- Modal Footer Actions -->
                    <div class="pt-4 border-t border-zinc-100 flex items-center justify-end gap-2.5">
                        <button type="button" @click="showAddModal = false" class="px-4 py-2.5 rounded-xl border border-zinc-200 text-zinc-600 hover:bg-zinc-50 font-semibold transition">Batal</button>
                        <button type="submit" class="px-4 py-2.5 rounded-xl bg-portal-orange hover:bg-orange-700 text-white font-bold shadow-md transition">Simpan Alat</button>
                    </div>
                </form>

            </div>
        </div>

    </div>

</body>
</html>