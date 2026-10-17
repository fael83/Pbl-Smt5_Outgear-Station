<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Data Pelanggan</title>
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

        @include('partials.sidebar-admin')


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

            <!-- Isi Halaman Data Pelanggan -->
            <div class="p-8 space-y-6">
                
                <!-- Judul Utama & Aksi -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-zinc-900 tracking-tight">Data Pelanggan</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Data akun customer OutGear Station</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <button class="bg-white border border-zinc-300 hover:bg-zinc-50 text-zinc-700 text-xs font-semibold px-4 py-2.5 rounded-xl shadow-sm transition flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg> Ekspor CSV
                        </button>
                        <button class="bg-portal-orange hover:bg-orange-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> + Tambah Pelanggan
                        </button>
                    </div>
                </div>

                <!-- 3 KARTU STATISTIK PELANGGAN -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
                    <!-- Kartu 1: Akun Aktif -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">1.192</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Akun Aktif</div>
                        </div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> 95.5% Rasio
                        </span>
                    </div>

                    <!-- Kartu 2: Nonaktif / Ditangguhkan -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">56 <span class="text-lg font-bold">+</span></div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Akun Nonaktif / Ditangguhkan</div>
                        </div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-red-50 text-red-700 border border-red-200">
                            Perlu Ditinjau
                        </span>
                    </div>

                    <!-- Kartu 3: Total Terdaftar -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">1.248</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Total Pelanggan Terdaftar</div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-zinc-100 text-zinc-600 border border-zinc-200 flex items-center gap-1">
                            ↗ +24 bln ini
                        </span>
                    </div>
                </div>

                <!-- STATUS BOARD / TABEL DATA PELANGGAN -->
                <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">
                    
                    <!-- Header Action Board (Kaca Pembesar, Filter, dll) -->
                    <div class="p-5 border-b border-zinc-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="font-bold text-zinc-900 text-base">Status Board</h3>
                            <span class="text-xs font-medium text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">Filter: <span class="font-bold text-zinc-800" x-text="selectedFilter"></span></span>
                        </div>

                        <div class="flex items-center gap-2 relative">
                            <!-- Input Search Interaktif -->
                            <div x-show="showSearchInput" x-transition class="relative">
                                <input id="tableSearchInput" type="text" placeholder="Cari di tabel..." class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">
                                <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>

                            <!-- Tombol Kaca Pembesar -->
                            <button @click="showSearchInput = !showSearchInput; $nextTick(() => document.getElementById('tableSearchInput')?.focus())" class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" title="Cari di tabel">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                            
                            <!-- Tombol Filter Interaktif -->
                            <div class="relative">
                                <button @click="showFilterDropdown = !showFilterDropdown" class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" title="Filter Status Akun">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                </button>

                                <!-- Dropdown Menu Filter -->
                                <div x-show="showFilterDropdown" @click.away="showFilterDropdown = false" x-transition class="absolute right-0 mt-2 w-44 bg-white border border-zinc-200 rounded-xl shadow-lg py-1.5 z-50 text-xs font-medium">
                                    <div class="px-3 py-1.5 font-bold text-zinc-400 uppercase text-[10px] tracking-wider border-b border-zinc-100">Filter Status Akun</div>
                                    <button @click="selectedFilter = 'Semua'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                                        <span>Semua Akun</span>
                                        <span x-show="selectedFilter === 'Semua'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedFilter = 'Aktif'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-emerald-700 flex items-center justify-between">
                                        <span>Aktif</span>
                                        <span x-show="selectedFilter === 'Aktif'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedFilter = 'Tidak Aktif'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-red-600 flex items-center justify-between">
                                        <span>Tidak Aktif</span>
                                        <span x-show="selectedFilter === 'Tidak Aktif'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABEL PELANGGAN (Rata Tengah / Text Center) -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-center text-xs align-middle">
                            <thead>
                                <tr class="text-zinc-400 font-bold border-b border-zinc-100 bg-zinc-50/50">
                                    <th class="p-4 uppercase align-middle text-center">NAMA</th>
                                    <th class="p-4 uppercase align-middle text-center">ID</th>
                                    <th class="p-4 uppercase align-middle text-center">EMAIL</th>
                                    <th class="p-4 uppercase align-middle text-center">NO TELEPON</th>
                                    <th class="p-4 uppercase align-middle text-center">KATA SANDI</th>
                                    <th class="p-4 uppercase align-middle text-center">PERAN</th>
                                    <th class="p-4 uppercase align-middle text-center">STATUS AKUN</th>
                                    <th class="p-4 uppercase align-middle text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                
                                <!-- Baris 1: Rian Pratama -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="selectedFilter === 'Semua' || selectedFilter === 'Aktif'">
                                    <td class="p-4 align-middle">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-zinc-200 font-bold text-zinc-700 flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=Rian+Pratama&background=e76f51&color=fff" alt="RP">
                                            </div>
                                            <span class="font-bold text-zinc-900 text-left">Rian Pratama</span>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">00781</td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">rian.pratama@gmail.com</td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">+62 812-4491-9921</td>
                                    <td class="p-4 font-mono text-zinc-400 align-middle text-center">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            ••••••••••••
                                            <button class="text-zinc-400 hover:text-zinc-600 ml-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200 inline-block">Customer</span></td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-block">Aktif</span></td>
                                    <td class="p-4 align-middle text-center">
                                        <button class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition inline-flex items-center justify-center" title="Hapus Pelanggan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 2: Lolita Rein -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="selectedFilter === 'Semua' || selectedFilter === 'Aktif'">
                                    <td class="p-4 align-middle">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-zinc-200 font-bold text-zinc-700 flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=Lolita+Rein&background=2a9d8f&color=fff" alt="LR">
                                            </div>
                                            <span class="font-bold text-zinc-900 text-left">Lolita Rein</span>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">00782</td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">lolita.rein@expedition.id</td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">+62 813-2287-4390</td>
                                    <td class="p-4 font-mono text-zinc-400 align-middle text-center">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            ••••••••••••
                                            <button class="text-zinc-400 hover:text-zinc-600 ml-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200 inline-block">Customer</span></td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-block">Aktif</span></td>
                                    <td class="p-4 align-middle text-center">
                                        <button class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition inline-flex items-center justify-center" title="Hapus Pelanggan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 3: Jack Johnson -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="selectedFilter === 'Semua' || selectedFilter === 'Tidak Aktif'">
                                    <td class="p-4 align-middle">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-zinc-200 font-bold text-zinc-700 flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=Jack+Johnson&background=e9c46a&color=fff" alt="JJ">
                                            </div>
                                            <span class="font-bold text-zinc-900 text-left">Jack Johnson</span>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">00770</td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">jack.johnson@hiker.org</td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">+62 821-9901-2311</td>
                                    <td class="p-4 font-mono text-zinc-400 align-middle text-center">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            ••••••••••••
                                            <button class="text-zinc-400 hover:text-zinc-600 ml-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200 inline-block">Customer</span></td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-red-50 text-red-700 border border-red-200 inline-block">Tidak Aktif</span></td>
                                    <td class="p-4 align-middle text-center">
                                        <button class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition inline-flex items-center justify-center" title="Hapus Pelanggan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                                <!-- Baris 4: Nadia Safitri -->
                                <tr class="hover:bg-zinc-50/80 transition" x-show="selectedFilter === 'Semua' || selectedFilter === 'Aktif'">
                                    <td class="p-4 align-middle">
                                        <div class="inline-flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-zinc-200 font-bold text-zinc-700 flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">
                                                <img src="https://ui-avatars.com/api/?name=Nadia+Safitri&background=f4a261&color=fff" alt="NS">
                                            </div>
                                            <span class="font-bold text-zinc-900 text-left">Nadia Safitri</span>
                                        </div>
                                    </td>
                                    <td class="p-4 font-mono text-zinc-500 align-middle text-center">00761</td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">nadia.safitri@mount.com</td>
                                    <td class="p-4 text-zinc-600 align-middle text-center">+62 819-3321-1200</td>
                                    <td class="p-4 font-mono text-zinc-400 align-middle text-center">
                                        <div class="inline-flex items-center justify-center gap-1">
                                            ••••••••••••
                                            <button class="text-zinc-400 hover:text-zinc-600 ml-1"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg></button>
                                        </div>
                                    </td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200 inline-block">Customer</span></td>
                                    <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200 inline-block">Aktif</span></td>
                                    <td class="p-4 align-middle text-center">
                                        <button class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition inline-flex items-center justify-center" title="Hapus Pelanggan">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </td>
                                </tr>

                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION FOOTER -->
                    <div class="p-4 border-t border-zinc-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500 font-medium">
                        <div>
                            Menampilkan data ke <span class="font-bold text-zinc-900">1 - 8</span> dari <span class="font-bold text-zinc-900">1.248</span> akun
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-400 cursor-not-allowed">‹</button>
                            <button class="w-8 h-8 rounded-lg bg-zinc-900 text-white font-bold flex items-center justify-center">1</button>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">2</button>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">3</button>
                            <span class="px-1 text-zinc-400">...</span>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">156</button>
                            <button class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">›</button>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

</body>
</html>