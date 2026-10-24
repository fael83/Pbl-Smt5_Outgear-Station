<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
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
<body class="text-slate-800 antialiased bg-portal-bg min-h-screen"
    x-data="{
        search: '',
        showSearchInput: false,
        showFilterDropdown: false,
        selectedFilter: 'Semua',
        selectedJenis: 'Semua',
        modalDetail: false,
        selectedItem: null,
        memproses: null,
        page: 1,
        perPage: 10,

        pembayaranList: {{ Js::from($pembayaranList) }},

        init() {
            // Kembali ke halaman 1 setiap kali pencarian atau filter berubah.
            this.$watch('search', () => this.page = 1);
            this.$watch('selectedFilter', () => this.page = 1);
            this.$watch('selectedJenis', () => this.page = 1);
        },

        formatRupiah(val) {
            return 'Rp ' + Number(val).toLocaleString('id-ID');
        },

        labelMetode(metode) {
            return { qris: 'QRIS', card: 'Kartu', cash: 'Cash' }[metode] ?? String(metode ?? '-').toUpperCase();
        },

        tokenCsrf() {
            return document.querySelector('meta[name=csrf-token]')?.content ?? '';
        },

        async kirimAksi(url) {
            try {
                const respons = await fetch(url, {
                    method: 'PATCH',
                    credentials: 'same-origin',
                    headers: {
                        Accept: 'application/json',
                        'X-CSRF-TOKEN': this.tokenCsrf(),
                    },
                });

                const data = await respons.json().catch(() => ({}));

                return { ok: respons.ok, data };
            } catch (galat) {
                return { ok: false, data: { message: 'Koneksi ke server gagal. Coba lagi.' } };
            }
        },

        async verifikasi(id) {
            const item = this.pembayaranList.find(p => p.id === id);
            if (!item || this.memproses) return;

            this.memproses = id;
            const { ok, data } = await this.kirimAksi(item.url_verifikasi);
            this.memproses = null;

            if (!ok) {
                alert(data.message ?? 'Gagal memverifikasi pembayaran.');
                return;
            }

            item.status_transaksi = data.status_transaksi;
            item.waktu_bayar = data.waktu_bayar;

            if (this.selectedItem && this.selectedItem.id === id) {
                this.selectedItem.status_transaksi = data.status_transaksi;
                this.selectedItem.waktu_bayar = data.waktu_bayar;
            }
        },

        async tolak(id) {
            const item = this.pembayaranList.find(p => p.id === id);
            if (!item || this.memproses) return;

            this.memproses = id;
            const { ok, data } = await this.kirimAksi(item.url_tolak);
            this.memproses = null;

            if (!ok) {
                alert(data.message ?? 'Gagal menolak pembayaran.');
                return;
            }

            item.status_transaksi = data.status_transaksi;

            if (this.selectedItem && this.selectedItem.id === id) {
                this.selectedItem.status_transaksi = data.status_transaksi;
            }
        },

        openDetail(item) {
            this.selectedItem = item;
            this.modalDetail = true;
        },

        get filteredList() {
            const query = this.search.toLowerCase().trim();
            const teks = (nilai) => String(nilai ?? '').toLowerCase();

            return this.pembayaranList.filter(item => {
                // Yang berhasil tidak ditampilkan di tabel verifikasi
                if (item.status_transaksi === 'berhasil') {
                    return false;
                }

                const matchStatus = this.selectedFilter === 'Semua' ||
                    (this.selectedFilter === 'Pending' && item.status_transaksi === 'pending') ||
                    (this.selectedFilter === 'Gagal' && item.status_transaksi === 'gagal');

                const matchJenis = this.selectedJenis === 'Semua' || item.jenis_pembayaran === this.selectedJenis;

                const matchSearch = query === '' ||
                    teks(item.id).includes(query) ||
                    teks(item.pemesanan_id).includes(query) ||
                    teks(item.kode_pemesanan).includes(query) ||
                    teks(item.pelanggan_nama).includes(query) ||
                    teks(item.transaction_id_midtrans).includes(query);

                return matchStatus && matchJenis && matchSearch;
            });
        },

        get totalPages() {
            return Math.max(1, Math.ceil(this.filteredList.length / this.perPage));
        },

        get halamanAktif() {
            return Math.min(this.page, this.totalPages);
        },

        get pagedList() {
            const awal = (this.halamanAktif - 1) * this.perPage;
            return this.filteredList.slice(awal, awal + this.perPage);
        },

        get awalData() {
            return this.filteredList.length === 0 ? 0 : (this.halamanAktif - 1) * this.perPage + 1;
        },

        get akhirData() {
            return Math.min(this.halamanAktif * this.perPage, this.filteredList.length);
        },

        get countPending() {
            return this.pembayaranList.filter(p => p.status_transaksi === 'pending').length;
        },

        get countBerhasil() {
            return this.pembayaranList.filter(p => p.status_transaksi === 'berhasil').length;
        },

        get countGagal() {
            return this.pembayaranList.filter(p => p.status_transaksi === 'gagal').length;
        }
    }">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-8 py-3.5 z-30 border-b border-zinc-200/60 flex items-center justify-between">
                <div class="flex items-center gap-2">
                </div>

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-3.5 py-1.5 rounded-full shadow-sm text-xs font-semibold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        BASECAMP SLEMAN - AKTIF
                    </div>
                    <button type="button" onclick="window.location.reload()" title="Muat ulang data" class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-600 hover:bg-zinc-50 shadow-sm transition">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                    <div class="w-8 h-8 rounded-full bg-zinc-900 text-white font-bold text-xs flex items-center justify-center border border-zinc-300 shadow-sm" title="{{ auth()->user()?->name }}">{{ auth()->user()?->inisial() ?? '?' }}</div>
                </div>
            </header>

            <!-- Isi Halaman Verifikasi Pembayaran -->
            <div class="p-8 space-y-6">
                
                <!-- Judul Utama & Aksi -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <div class="flex items-center gap-2.5">
                            <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Verifikasi Pembayaran</h2>
                        </div>
                        <p class="text-xs text-zinc-500 mt-1">
                            Validasi transfer bank dan bukti pembayaran pelanggan secara real-time.
                        </p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        <span class="bg-amber-100 text-amber-800 text-xs font-bold px-3 py-1.5 rounded-xl border border-amber-200 flex items-center gap-1.5">
                            <span class="w-2 h-2 rounded-full bg-amber-500 animate-ping"></span>
                            <span x-text="countPending + ' Transaksi Menunggu Validasi'"></span>
                        </span>
                    </div>
                </div>

                <!-- 3 KARTU STATISTIK RINGKASAN ATAS (MENUNGGU, SUKSES, GAGAL) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Kartu 1: Menunggu Verifikasi -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Menunggu Verifikasi</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-amber-600" x-text="countPending"></div>
                        </div>
                        <div class="text-[11px] text-amber-600 font-bold pt-2 border-t border-zinc-100">Perlu Ditinjau</div>
                    </div>

                    <!-- Kartu 2: Terverifikasi (Sukses) -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Terverifikasi (Sukses)</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-emerald-600" x-text="countBerhasil"></div>
                        </div>
                        <div class="text-[11px] text-emerald-600 font-bold pt-2 border-t border-zinc-100">Selesai</div>
                    </div>

                    <!-- Kartu 3: Ditolak / Gagal -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Ditolak / Gagal</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-rose-600" x-text="countGagal"></div>
                        </div>
                        <div class="text-[11px] text-rose-600 font-bold pt-2 border-t border-zinc-100">Dibatalkan</div>
                    </div>
                </div>

                <!-- STATUS BOARD / TABEL DATA PEMBAYARAN SESUAI ERD -->
                <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">
                    
                    <!-- Header Action Board (Kaca Pembesar & Icon Filter seperti di Data Pelanggan) -->
                    <div class="p-5 border-b border-zinc-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="font-bold text-zinc-900 text-base">Daftar Antrean Pembayaran</h3>
                            <span class="text-xs font-medium text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">
                                Filter: <span class="font-bold text-zinc-800" x-text="selectedFilter + (selectedJenis !== 'Semua' ? ' • ' + selectedJenis.toUpperCase() : '')"></span>
                            </span>
                        </div>

                        <div class="flex items-center gap-2 relative">
                            <!-- Input Search Interaktif -->
                            <div x-show="showSearchInput" x-transition class="relative">
                                <input id="tableSearchInput" 
                                       type="text" 
                                       x-model="search"
                                       placeholder="Cari ID / Transaksi / Pemesan..." 
                                       class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">
                                <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>

                            <!-- Tombol Kaca Pembesar -->
                            <button @click="showSearchInput = !showSearchInput; $nextTick(() => document.getElementById('tableSearchInput')?.focus())" 
                                    class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" 
                                    title="Cari di tabel">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>
                            
                            <!-- Tombol Filter Interaktif -->
                            <div class="relative">
                                <button @click="showFilterDropdown = !showFilterDropdown" 
                                        class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" 
                                        title="Filter Status & Jenis">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                </button>

                                <!-- Dropdown Menu Filter -->
                                <div x-show="showFilterDropdown" 
                                     @click.away="showFilterDropdown = false" 
                                     x-transition 
                                     class="absolute right-0 mt-2 w-48 bg-white border border-zinc-200 rounded-xl shadow-lg py-1.5 z-50 text-xs font-medium">
                                    <div class="px-3 py-1.5 font-bold text-zinc-400 uppercase text-[10px] tracking-wider border-b border-zinc-100">Filter Status Antrean</div>
                                    <button @click="selectedFilter = 'Semua'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                                        <span>Semua Antrean</span>
                                        <span x-show="selectedFilter === 'Semua'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedFilter = 'Pending'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-amber-700 flex items-center justify-between">
                                        <span>Menunggu (Pending)</span>
                                        <span x-show="selectedFilter === 'Pending'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedFilter = 'Gagal'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-rose-700 flex items-center justify-between">
                                        <span>Ditolak (Gagal)</span>
                                        <span x-show="selectedFilter === 'Gagal'" class="text-portal-orange font-bold">✓</span>
                                    </button>

                                    <div class="px-3 py-1.5 font-bold text-zinc-400 uppercase text-[10px] tracking-wider border-b border-t border-zinc-100 mt-1">Filter Jenis Pembayaran</div>
                                    <button @click="selectedJenis = 'Semua'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                                        <span>Semua Jenis</span>
                                        <span x-show="selectedJenis === 'Semua'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedJenis = 'dp'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-blue-700 flex items-center justify-between">
                                        <span>DP (Uang Muka)</span>
                                        <span x-show="selectedJenis === 'dp'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedJenis = 'pelunasan'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-emerald-700 flex items-center justify-between">
                                        <span>Pelunasan</span>
                                        <span x-show="selectedJenis === 'pelunasan'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <button @click="selectedJenis = 'denda'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-amber-700 flex items-center justify-between">
                                        <span>Denda</span>
                                        <span x-show="selectedJenis === 'denda'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- TABEL UTAMA (STRUKTUR SESUAI ERD PEMBAYARAN) -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-zinc-50/80 text-zinc-400 font-bold border-b border-zinc-200 uppercase tracking-wider text-[10px]">
                                    <th class="p-4 text-center">ID</th>
                                    <th class="p-4">PEMESANAN</th>
                                    <th class="p-4">TRANSACTION ID MIDTRANS</th>
                                    <th class="p-4">JENIS</th>
                                    <th class="p-4">METODE</th>
                                    <th class="p-4">JUMLAH</th>
                                    <th class="p-4">STATUS</th>
                                    <th class="p-4">WAKTU BAYAR</th>
                                    <th class="p-4 text-center">AKSI</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                <template x-for="item in pagedList" :key="item.id">
                                    <tr class="hover:bg-zinc-50/80 transition">
                                        <!-- id (bigint PK - clean text tanpa kotak) -->
                                        <td class="p-4 text-center">
                                            <span class="font-mono text-zinc-500 font-semibold text-xs" 
                                                  x-text="'#' + item.id"></span>
                                        </td>

                                        <!-- pemesanan_id (bigint FK) -->
                                        <td class="p-4">
                                            <div class="font-bold text-zinc-900" x-text="item.pelanggan_nama"></div>
                                            <div class="text-[11px] text-zinc-400 font-mono flex items-center gap-1 mt-0.5">
                                                <span class="text-portal-orange font-semibold" x-text="item.kode_pemesanan"></span>
                                                <span class="text-zinc-300">•</span>
                                                <span x-text="'FK: ' + item.pemesanan_id"></span>
                                            </div>
                                        </td>

                                        <!-- transaction_id_midtrans (string) -->
                                        <td class="p-4">
                                            <div class="font-mono text-[11px] font-semibold text-zinc-700 bg-zinc-100/80 px-2 py-1 rounded inline-block border border-zinc-200/60"
                                                 x-text="item.transaction_id_midtrans"></div>
                                        </td>

                                        <!-- jenis_pembayaran (enum: 'dp', 'pelunasan', 'denda') -->
                                        <td class="p-4">
                                            <template x-if="item.jenis_pembayaran === 'dp'">
                                                <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 font-bold text-[10px] uppercase border border-blue-200">
                                                    DP (Uang Muka)
                                                </span>
                                            </template>
                                            <template x-if="item.jenis_pembayaran === 'pelunasan'">
                                                <span class="px-2.5 py-1 rounded-md bg-emerald-50 text-emerald-700 font-bold text-[10px] uppercase border border-emerald-200">
                                                    Pelunasan
                                                </span>
                                            </template>
                                            <template x-if="item.jenis_pembayaran === 'denda'">
                                                <span class="px-2.5 py-1 rounded-md bg-amber-50 text-amber-700 font-bold text-[10px] uppercase border border-amber-200">
                                                    Denda
                                                </span>
                                            </template>
                                        </td>

                                        <!-- metode_pembayaran -->
                                        <td class="p-4">
                                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md font-bold text-[10px] uppercase border"
                                                  :class="item.metode_pembayaran === 'qris' ? 'bg-purple-50 text-purple-700 border-purple-200' : 'bg-zinc-100 text-zinc-700 border-zinc-300'">
                                                <span class="w-1.5 h-1.5 rounded-full" :class="item.metode_pembayaran === 'qris' ? 'bg-purple-500' : 'bg-zinc-400'"></span>
                                                <span x-text="labelMetode(item.metode_pembayaran)"></span>
                                            </span>
                                        </td>

                                        <!-- jumlah (decimal) -->
                                        <td class="p-4">
                                            <span class="font-bold text-zinc-900" x-text="formatRupiah(item.jumlah)"></span>
                                        </td>

                                        <!-- status_transaksi (enum: 'pending', 'gagal') -->
                                        <td class="p-4">
                                            <template x-if="item.status_transaksi === 'pending'">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Pending
                                                </span>
                                            </template>
                                            <template x-if="item.status_transaksi === 'gagal'">
                                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                                    <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span> Gagal
                                                </span>
                                            </template>
                                        </td>

                                        <!-- waktu_bayar (datetime) -->
                                        <td class="p-4 text-zinc-500 text-[11px]">
                                            <span x-text="item.waktu_bayar || '-'"></span>
                                        </td>

                                        <!-- AKSI -->
                                        <td class="p-4 text-center">
                                            <div class="inline-flex items-center gap-1.5">
                                                <!-- Jika pending, tampilkan tombol Verifikasi & Tolak -->
                                                <template x-if="item.status_transaksi === 'pending'">
                                                    <div class="flex items-center gap-1">
                                                        <button @click="verifikasi(item.id)" 
                                                                title="Verifikasi Pembayaran"
                                                                class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-2.5 py-1 rounded-lg text-xs shadow-sm transition">
                                                            Verifikasi
                                                        </button>
                                                        <button @click="tolak(item.id)" 
                                                                title="Tolak Pembayaran"
                                                                class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold px-2 py-1 rounded-lg text-xs transition">
                                                            Tolak
                                                        </button>
                                                    </div>
                                                </template>

                                                <!-- Tombol Detail / Inspect ERD -->
                                                <button @click="openDetail(item)" 
                                                        title="Lihat Detail Transaksi"
                                                        class="p-1.5 text-zinc-400 hover:text-zinc-700 hover:bg-zinc-100 rounded-lg transition">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                </template>

                                <template x-if="filteredList.length === 0">
                                    <tr>
                                        <td colspan="9" class="p-10 text-center">
                                            <p class="text-3xl">💳</p>
                                            <p class="mt-2 text-sm font-semibold text-[#1A2024]"
                                               x-text="search || selectedFilter !== 'Semua' || selectedJenis !== 'Semua' ? 'Tidak ada pembayaran yang cocok dengan filter.' : 'Tidak ada antrean pembayaran untuk diverifikasi.'"></p>
                                            <p class="mt-1 text-xs text-gray-500"
                                               x-text="search || selectedFilter !== 'Semua' || selectedJenis !== 'Semua' ? 'Coba ubah kata kunci pencarian, status, atau jenis pembayaran.' : 'Transaksi yang menunggu validasi akan muncul di sini.'"></p>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION FOOTER (Seperti Halaman Pelanggan) -->
                    <div class="p-4 border-t border-zinc-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500 font-medium">
                        <div>
                            Menampilkan data ke <span class="font-bold text-zinc-900" x-text="awalData + ' - ' + akhirData"></span> dari <span class="font-bold text-zinc-900" x-text="filteredList.length"></span> antrean
                        </div>
                        <div class="flex items-center gap-1.5">
                            <button type="button" @click="page = Math.max(1, halamanAktif - 1)" :disabled="halamanAktif <= 1"
                                    class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center"
                                    :class="halamanAktif <= 1 ? 'text-zinc-400 cursor-not-allowed' : 'hover:bg-zinc-100 text-zinc-700'">‹</button>
                            <span class="px-2 text-zinc-600" x-text="'Halaman ' + halamanAktif + ' dari ' + totalPages"></span>
                            <button type="button" @click="page = Math.min(totalPages, halamanAktif + 1)" :disabled="halamanAktif >= totalPages"
                                    class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center"
                                    :class="halamanAktif >= totalPages ? 'text-zinc-400 cursor-not-allowed' : 'hover:bg-zinc-100 text-zinc-700'">›</button>
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <!-- MODAL DETAIL PEMBAYARAN (ERD FIELDS INSPECTOR) -->
    <div x-show="modalDetail" 
         x-transition.opacity 
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4"
         style="display: none;">
        
        <div @click.away="modalDetail = false" 
             x-show="modalDetail"
             x-transition.scale.95
             class="bg-white rounded-2xl shadow-2xl max-w-lg w-full overflow-hidden border border-zinc-200">
            
            <!-- Modal Header -->
            <div class="bg-zinc-900 text-white p-5 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-portal-orange tracking-widest uppercase">DETAIL PEMBAYARAN</span>
                    <h3 class="font-bold text-base text-white mt-0.5">
                        Rincian Transaksi <span x-text="'#PAY-' + (selectedItem ? selectedItem.id : '')"></span>
                    </h3>
                </div>
                <button @click="modalDetail = false" class="text-zinc-400 hover:text-white p-1 rounded-lg transition">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Modal Content (All ERD Attributes) -->
            <template x-if="selectedItem">
          <div class="p-6 space-y-4 text-xs">
                <div class="grid grid-cols-2 gap-3 bg-zinc-50 p-4 rounded-xl border border-zinc-200/80">
                    <div>
                        <span class="text-zinc-400 font-semibold block text-[10px] uppercase">ID Pembayaran (PK)</span>
                        <span class="font-mono font-bold text-zinc-800 text-sm" x-text="'#' + selectedItem.id"></span>
                    </div>
                    <div>
                        <span class="text-zinc-400 font-semibold block text-[10px] uppercase">ID Pemesanan (FK)</span>
                        <span class="font-mono font-bold text-portal-orange text-sm" x-text="selectedItem.kode_pemesanan + ' (ID: ' + selectedItem.pemesanan_id + ')'"></span>
                    </div>
                </div>

                <div class="space-y-3">
                    <div class="flex justify-between items-center py-1.5 border-b border-zinc-100">
                        <span class="text-zinc-500 font-medium">Pelanggan</span>
                        <div class="text-right">
                            <span class="font-bold text-zinc-900" x-text="selectedItem.pelanggan_nama"></span>
                            <span class="text-zinc-400 text-[11px] block" x-text="selectedItem.pelanggan_telp"></span>
                        </div>
                    </div>

                    <div class="flex justify-between items-center py-1.5 border-b border-zinc-100">
                        <span class="text-zinc-500 font-medium">Midtrans Transaction ID</span>
                        <span class="font-mono text-zinc-700 font-semibold bg-zinc-100 px-2 py-0.5 rounded border border-zinc-200" 
                              x-text="selectedItem.transaction_id_midtrans"></span>
                    </div>

                    <div class="flex justify-between items-center py-1.5 border-b border-zinc-100">
                        <span class="text-zinc-500 font-medium">Jenis Pembayaran</span>
                        <span class="font-bold uppercase tracking-wider text-[11px] px-2.5 py-0.5 rounded"
                              :class="{
                                  'bg-blue-100 text-blue-800': selectedItem.jenis_pembayaran === 'dp',
                                  'bg-emerald-100 text-emerald-800': selectedItem.jenis_pembayaran === 'pelunasan',
                                  'bg-amber-100 text-amber-800': selectedItem.jenis_pembayaran === 'denda'
                              }"
                              x-text="selectedItem.jenis_pembayaran"></span>
                    </div>

                    <div class="flex justify-between items-center py-1.5 border-b border-zinc-100">
                        <span class="text-zinc-500 font-medium">Metode Pembayaran</span>
                        <span class="font-bold uppercase tracking-wider text-[11px] px-2.5 py-0.5 rounded"
                              :class="selectedItem.metode_pembayaran === 'qris' ? 'bg-purple-100 text-purple-800' : 'bg-zinc-200 text-zinc-800'"
                              x-text="selectedItem.metode_pembayaran"></span>
                    </div>

                    <div class="flex justify-between items-center py-1.5 border-b border-zinc-100">
                        <span class="text-zinc-500 font-medium">Jumlah Tagihan</span>
                        <span class="font-extrabold text-base text-zinc-900" x-text="formatRupiah(selectedItem.jumlah)"></span>
                    </div>

                    <div class="flex justify-between items-center py-1.5 border-b border-zinc-100">
                        <span class="text-zinc-500 font-medium">Status Transaksi</span>
                        <span class="font-bold uppercase tracking-wider text-[11px] px-2.5 py-0.5 rounded-full"
                              :class="{
                                  'bg-amber-100 text-amber-800': selectedItem.status_transaksi === 'pending',
                                  'bg-emerald-100 text-emerald-800': selectedItem.status_transaksi === 'berhasil',
                                  'bg-rose-100 text-rose-800': selectedItem.status_transaksi === 'gagal'
                              }"
                              x-text="selectedItem.status_transaksi"></span>
                    </div>

                    <div class="flex justify-between items-center py-1.5 border-b border-zinc-100">
                        <span class="text-zinc-500 font-medium">Waktu Pembayaran</span>
                        <span class="font-semibold text-zinc-700" x-text="selectedItem.waktu_bayar || '-'"></span>
                    </div>
                </div>

                <!-- Modal Actions -->
                <div class="pt-4 flex items-center justify-end gap-2">
                    <template x-if="selectedItem.status_transaksi === 'pending'">
                        <div class="flex items-center gap-2 w-full">
                            <button @click="tolak(selectedItem.id); modalDetail = false;" 
                                    class="w-1/2 bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold py-2 rounded-xl transition">
                                Tolak Pembayaran
                            </button>
                            <button @click="verifikasi(selectedItem.id); modalDetail = false;" 
                                    class="w-1/2 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-xl shadow transition">
                                Verifikasi Sukses
                            </button>
                        </div>
                    </template>
                    <template x-if="selectedItem.status_transaksi !== 'pending'">
                        <button @click="modalDetail = false" 
                                class="bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-bold px-4 py-2 rounded-xl transition">
                            Tutup
                        </button>
                    </template>
                </div>
            </div>
            </template>
        </div>
    </div>

</body>
</html>
