<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Data Pemesanan</title>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800 antialiased font-sans" x-data="{ showSearchInput: false, showFilterDropdown: false, selectedFilter: {{ Js::from(match ($filters['status']) { 'menunggu_pembayaran' => 'Menunggu Bayar', 'berlangsung' => 'Berlangsung', 'selesai' => 'Selesai', 'dibatalkan' => 'Dibatalkan', 'dikonfirmasi' => 'Dikonfirmasi', default => 'Semua' }) }}, showCreateModal: {{ $errors->any() ? 'true' : 'false' }} }">

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

            <!-- Isi Halaman Data Pemesanan -->
            <div class="p-8 space-y-6">
                @if (session('success'))
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif
                
                <!-- Judul Utama & Aksi -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-zinc-900 tracking-tight">Data Pemesanan</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Kelola seluruh data booking & penyewaan peralatan outdoor</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                    
                        <button type="button" @click="showCreateModal = true" class="bg-portal-orange hover:bg-orange-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg> + Tambah Pemesanan
                        </button>
                    </div>
                </div>

                <!-- 4 KARTU STATISTIK PEMESANAN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kartu 1: Booking Aktif -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Booking Aktif</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-blue-700">{{ $pemesanan->isNotEmpty() ? $jumlahAktif : '38' }}</div>
                        </div>
                        <div class="text-[11px] text-emerald-600 font-bold pt-2 border-t border-zinc-100">+14% ↑</div>
                    </div>

                    <!-- Kartu 2: Menunggu Pembayaran -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Menunggu Pembayaran</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-amber-600">{{ $pemesanan->isNotEmpty() ? $jumlahMenungguPembayaran : '12' }}</div>
                        </div>
                        <div class="text-[11px] text-amber-600 font-bold pt-2 border-t border-zinc-100">Perlu Aksi</div>
                    </div>

                    <!-- Kartu 3: Sedang Berlangsung -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Sedang Berlangsung</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-blue-700">{{ $pemesanan->isNotEmpty() ? $jumlahBerlangsung : '21' }}</div>
                        </div>
                        <div class="text-[11px] text-blue-700 font-bold pt-2 border-t border-zinc-100">Aktif</div>
                    </div>

                    <!-- Kartu 4: Selesai Bulan Ini -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Selesai Bulan Ini</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-emerald-600">{{ $pemesanan->isNotEmpty() ? $jumlahSelesaiBulanIni : '156' }}</div>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-bold pt-2 border-t border-zinc-100">↗ +32 dari bln lalu</div>
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
                                <form method="GET" action="{{ route('admin.pemesanan') }}" class="relative">
                                    <input type="hidden" name="status" value="{{ $filters['status'] }}">
                                    <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
                                    <input id="tableSearchInput" name="search" type="search" value="{{ $filters['search'] }}" placeholder="Cari pemesanan..." class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">
                                    <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </form>
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
                                    <a href="{{ route('admin.pemesanan', array_filter(['search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                                        <span>Semua Pemesanan</span>
                                        <span x-show="selectedFilter === 'Semua'" class="text-portal-orange font-bold">✓</span>
                                    </a>
                                    <a href="{{ route('admin.pemesanan', array_filter(['status' => 'menunggu_pembayaran', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-amber-700 flex items-center justify-between">
                                        <span>Menunggu Bayar</span>
                                        <span x-show="selectedFilter === 'Menunggu Bayar'" class="text-portal-orange font-bold">✓</span>
                                    </a>
                                    <a href="{{ route('admin.pemesanan', array_filter(['status' => 'dikonfirmasi', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-blue-700 flex items-center justify-between">
                                        <span>Dikonfirmasi</span>
                                        <span x-show="selectedFilter === 'Dikonfirmasi'" class="text-portal-orange font-bold">✓</span>
                                    </a>
                                    <a href="{{ route('admin.pemesanan', array_filter(['status' => 'berlangsung', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-blue-700 flex items-center justify-between">
                                        <span>Berlangsung</span>
                                        <span x-show="selectedFilter === 'Berlangsung'" class="text-portal-orange font-bold">✓</span>
                                    </a>
                                    <a href="{{ route('admin.pemesanan', array_filter(['status' => 'selesai', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-emerald-700 flex items-center justify-between">
                                        <span>Selesai</span>
                                        <span x-show="selectedFilter === 'Selesai'" class="text-portal-orange font-bold">✓</span>
                                    </a>
                                    <a href="{{ route('admin.pemesanan', array_filter(['status' => 'dibatalkan', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-red-600 flex items-center justify-between">
                                        <span>Dibatalkan</span>
                                        <span x-show="selectedFilter === 'Dibatalkan'" class="text-portal-orange font-bold">✓</span>
                                    </a>
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
                                @forelse ($pemesanan as $dataPemesanan)
                                        @php
                                            $labelStatus = match ($dataPemesanan->status) {
                                                'menunggu_pembayaran' => 'Menunggu Bayar',
                                                'dikonfirmasi' => 'Dikonfirmasi',
                                                'berlangsung' => 'Berlangsung',
                                                'selesai' => 'Selesai',
                                                'dibatalkan' => 'Dibatalkan',
                                                default => ucfirst(str_replace('_', ' ', $dataPemesanan->status)),
                                            };
                                            $kelasStatus = match ($dataPemesanan->status) {
                                                'menunggu_pembayaran' => 'bg-amber-50 text-amber-700 border-amber-200',
                                                'dikonfirmasi', 'berlangsung' => 'bg-blue-50 text-blue-700 border-blue-200',
                                                'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                                'dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
                                                default => 'bg-zinc-100 text-zinc-700 border-zinc-200',
                                            };
                                        @endphp
                                        <tr class="hover:bg-zinc-50/80 transition"
                                            x-show="selectedFilter === 'Semua' || selectedFilter === {{ Js::from($labelStatus) }}">
                                            <td class="p-4 font-mono text-zinc-500 align-middle text-center">#{{ $dataPemesanan->kode_pemesanan }}</td>
                                            <td class="p-4 align-middle">
                                                <div class="inline-flex items-center gap-3">
                                                    <div class="flex h-8 w-8 flex-shrink-0 items-center justify-center rounded-full bg-zinc-900 text-[10px] font-bold text-white">
                                                        {{ $dataPemesanan->pelanggan?->inisial() ?? 'PL' }}
                                                    </div>
                                                    <div class="text-left">
                                                        <span class="block font-bold text-zinc-900">{{ $dataPemesanan->pelanggan?->name ?? 'Pelanggan tidak tersedia' }}</span>
                                                        <span class="text-[11px] text-zinc-400">{{ $dataPemesanan->pelanggan?->no_telepon ?: '—' }}</span>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="p-4 text-center align-middle text-zinc-600">
                                                <div class="font-semibold text-zinc-900">{{ $dataPemesanan->detailPemesanan->sum('jumlah') }} item gear</div>
                                                <div class="text-[11px] text-zinc-400">{{ $dataPemesanan->detailPemesanan->pluck('alat.nama')->filter()->implode(', ') ?: '—' }}</div>
                                            </td>
                                            <td class="p-4 text-center align-middle">
                                                <div class="font-semibold text-zinc-900">{{ $dataPemesanan->tanggal_mulai->format('d/m/Y') }} - {{ $dataPemesanan->tanggal_selesai->format('d/m/Y') }}</div>
                                                <div class="text-[11px] text-zinc-500">{{ $dataPemesanan->lamaSewa() }} Hari Durasi</div>
                                            </td>
                                            <td class="p-4 text-center align-middle font-bold text-zinc-900">Rp {{ number_format((float) $dataPemesanan->total_biaya, 0, ',', '.') }}</td>
                                            <td class="p-4 text-center align-middle">
                                                <span class="inline-block rounded-full border px-2.5 py-1 text-[11px] font-bold {{ $kelasStatus }}">{{ $labelStatus }}</span>
                                            </td>
                                            <td class="p-4 text-center align-middle text-zinc-400">—</td>
                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-10 text-center">
                                            <p class="text-3xl">📋</p>
                                            <p class="mt-2 text-sm font-semibold text-[#1A2024]">
                                                {{ $filters['search'] !== '' || $filters['status'] !== 'semua' ? 'Tidak ada pemesanan yang cocok dengan filter.' : 'Belum ada data pemesanan.' }}
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $filters['search'] !== '' || $filters['status'] !== 'semua'
                                                    ? 'Coba ubah kata kunci pencarian atau status pemesanan.'
                                                    : 'Tambahkan pemesanan pertama melalui tombol "+ Tambah Pemesanan".' }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION FOOTER -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-5 py-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500">
                            @if ($pemesanan->total() > 0)
                                Menampilkan {{ $pemesanan->firstItem() }}–{{ $pemesanan->lastItem() }} dari {{ $pemesanan->total() }} pemesanan
                            @else
                                Tidak ada pemesanan untuk ditampilkan
                            @endif
                        </p>
                        <div class="flex flex-wrap items-center gap-3">
                            <form method="GET" action="{{ route('admin.pemesanan') }}" class="flex items-center gap-2">
                                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                                <input type="hidden" name="search" value="{{ $filters['search'] }}">
                                <label for="booking_per_page" class="text-xs text-gray-500">Baris per halaman:</label>
                                <select id="booking_per_page" name="per_page" onchange="this.form.submit()"
                                        class="bg-[#F4EFEA]/60 border border-transparent rounded-lg px-2 py-1 text-xs text-[#1A2024] font-semibold focus:ring-2 focus:ring-[#15AEEA]">
                                    @foreach ([10, 25, 50] as $jumlahBaris)
                                        <option value="{{ $jumlahBaris }}" @selected($filters['per_page'] === $jumlahBaris)>{{ $jumlahBaris }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @if ($pemesanan->hasPages())
                                <div data-paginasi>{{ $pemesanan->links() }}</div>
                            @endif
                        </div>
                    </div>

                </div>

            </div>
        </main>
    </div>

    <div x-cloak x-show="showCreateModal" x-transition.opacity
         class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-[#1A2024]/50 p-4 md:p-8"
         @keydown.escape.window="showCreateModal = false">
        <div class="w-full max-w-2xl rounded-2xl bg-white shadow-xl" @click.outside="showCreateModal = false">
            <div class="flex items-start justify-between gap-4 border-b border-gray-100 p-5">
                <div>
                    <h2 class="text-lg font-bold text-[#1A2024]">Tambah Pemesanan</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Pilih pelanggan, jadwal, dan jumlah peralatan yang disewa.</p>
                </div>
                <button type="button" @click="showCreateModal = false"
                        class="h-8 w-8 rounded-lg bg-gray-100 text-gray-500 transition hover:bg-gray-200">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.pemesanan.store') }}" class="space-y-4 p-5">
                @csrf
                @if ($errors->any())
                    <div class="space-y-1 rounded-xl border border-red-100 bg-red-50 px-4 py-3 text-xs text-red-700">
                        <p class="font-semibold">Periksa kembali data berikut:</p>
                        @foreach ($errors->all() as $pesanError)
                            <p>{{ $pesanError }}</p>
                        @endforeach
                    </div>
                @endif

                <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                    <div class="md:col-span-2">
                        <label for="pelanggan_id" class="mb-1 block text-xs font-semibold text-[#1A2024]">Pelanggan</label>
                        <select id="pelanggan_id" name="pelanggan_id" required
                                class="w-full rounded-xl border border-transparent bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            <option value="">Pilih pelanggan</option>
                            @foreach ($pelangganAktif as $dataPelanggan)
                                <option value="{{ $dataPelanggan->id }}" @selected(old('pelanggan_id') == $dataPelanggan->id)>
                                    {{ $dataPelanggan->name }} — {{ $dataPelanggan->email }}
                                </option>
                            @endforeach
                        </select>
                        @if ($pelangganAktif->isEmpty())
                            <p class="mt-1 text-[10px] text-amber-700">Belum ada pelanggan aktif. Tambahkan pelanggan terlebih dahulu.</p>
                        @endif
                    </div>
                    <div>
                        <label for="tanggal_mulai" class="mb-1 block text-xs font-semibold text-[#1A2024]">Tanggal Mulai</label>
                        <input id="tanggal_mulai" name="tanggal_mulai" type="date" required min="{{ now()->toDateString() }}"
                               value="{{ old('tanggal_mulai') }}"
                               class="w-full rounded-xl border border-transparent bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    </div>
                    <div>
                        <label for="tanggal_selesai" class="mb-1 block text-xs font-semibold text-[#1A2024]">Tanggal Selesai</label>
                        <input id="tanggal_selesai" name="tanggal_selesai" type="date" required min="{{ now()->toDateString() }}"
                               value="{{ old('tanggal_selesai') }}"
                               class="w-full rounded-xl border border-transparent bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    </div>
                </div>

                <div>
                    <div class="mb-2 flex items-center justify-between">
                        <label class="text-xs font-semibold text-[#1A2024]">Peralatan dan Jumlah</label>
                        <span class="text-[10px] text-gray-400">Kosongkan 0 untuk alat yang tidak dipilih</span>
                    </div>
                    <div class="max-h-56 space-y-2 overflow-y-auto rounded-xl border border-gray-100 p-3">
                        @forelse ($alatTersedia as $dataAlat)
                            <div class="flex items-center justify-between gap-4 rounded-lg bg-[#F4EFEA]/40 px-3 py-2.5">
                                <div class="min-w-0">
                                    <p class="truncate text-xs font-semibold text-[#1A2024]">{{ $dataAlat->nama }}</p>
                                    <p class="text-[10px] text-gray-500">
                                        Rp {{ number_format((float) $dataAlat->tarif_per_hari, 0, ',', '.') }}/hari
                                        · Stok {{ $dataAlat->stok_tersedia }}
                                    </p>
                                </div>
                                <input name="items[{{ $dataAlat->id }}]" type="number" min="0" max="{{ $dataAlat->stok_tersedia }}"
                                       value="{{ old('items.'.$dataAlat->id, 0) }}" aria-label="Jumlah {{ $dataAlat->nama }}"
                                       class="w-24 rounded-lg border border-gray-200 bg-white px-3 py-2 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            </div>
                        @empty
                            <p class="py-3 text-center text-xs text-gray-500">Belum ada peralatan dengan stok tersedia.</p>
                        @endforelse
                    </div>
                </div>

                <div>
                    <label for="jumlah_dp" class="mb-1 block text-xs font-semibold text-[#1A2024]">Jumlah DP (Rp) <span class="font-normal text-gray-400">(opsional)</span></label>
                    <input id="jumlah_dp" name="jumlah_dp" type="number" min="0" step="1000" value="{{ old('jumlah_dp', 0) }}"
                           class="w-full rounded-xl border border-transparent bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    <p class="mt-1 text-[10px] text-gray-400">Total biaya dihitung otomatis dari durasi, jumlah, dan tarif alat.</p>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" @click="showCreateModal = false"
                            class="rounded-xl bg-gray-100 px-4 py-2.5 text-xs font-semibold text-[#1A2024] transition hover:bg-gray-200">Batal</button>
                    <button type="submit" @disabled($pelangganAktif->isEmpty() || $alatTersedia->isEmpty())
                            class="rounded-xl bg-[#D86B43] px-4 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-[#D86B43]/90 disabled:cursor-not-allowed disabled:opacity-50">Simpan Pemesanan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>