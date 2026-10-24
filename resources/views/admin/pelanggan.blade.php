<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Data Pelanggan</title>
    <style>
        [x-cloak] { display: none !important; }
    </style>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800 antialiased font-sans" x-data="{ showSearchInput: false, showFilterDropdown: false, selectedFilter: {{ Js::from(match ($filters['status']) { 'aktif' => 'Aktif', 'nonaktif' => 'Tidak Aktif', default => 'Semua' }) }}, showCreateModal: {{ $errors->any() ? 'true' : 'false' }} }">

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
                @if (session('success'))
                    <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-xs font-semibold text-emerald-700">
                        {{ session('success') }}
                    </div>
                @endif
                
                <!-- Judul Utama & Aksi -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-zinc-900 tracking-tight">Data Pelanggan</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Data akun customer OutGear Station</p>
                    </div>
                    <div class="flex items-center gap-2.5">
                        
                        <button type="button" @click="showCreateModal = true" class="bg-portal-orange hover:bg-orange-700 text-white text-xs font-bold px-4 py-2.5 rounded-xl shadow-md transition flex items-center gap-1.5">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg> + Tambah Pelanggan
                        </button>
                    </div>
                </div>

                <!-- 3 KARTU STATISTIK PELANGGAN -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <!-- Kartu 1: Akun Aktif -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Akun Aktif</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-emerald-600">{{ $totalPelanggan > 0 ? number_format($pelangganAktif, 0, ',', '.') : '1.192' }}</div>
                        </div>
                        <div class="text-[11px] text-emerald-600 font-bold pt-2 border-t border-zinc-100">{{ $totalPelanggan > 0 ? number_format($pelangganAktif / $totalPelanggan * 100, 1, ',', '.') : '95,5' }}% Rasio</div>
                    </div>

                    <!-- Kartu 2: Nonaktif / Ditangguhkan -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Akun Nonaktif / Ditangguhkan</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-rose-600"><span class="font-heading">{{ $totalPelanggan > 0 ? number_format($pelangganNonaktif, 0, ',', '.') : '56' }}</span> <span class="text-lg font-bold font-sans">+</span></div>
                        </div>
                        <div class="text-[11px] text-rose-600 font-bold pt-2 border-t border-zinc-100">Perlu Ditinjau</div>
                    </div>

                    <!-- Kartu 3: Total Terdaftar -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Pelanggan Terdaftar</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-blue-700">{{ $totalPelanggan > 0 ? number_format($totalPelanggan, 0, ',', '.') : '1.248' }}</div>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-bold pt-2 border-t border-zinc-100">↗ +24 bln ini</div>
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
                                <form method="GET" action="{{ route('admin.pelanggan') }}" class="relative">
                                    <input type="hidden" name="status" value="{{ $filters['status'] }}">
                                    <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
                                    <input id="tableSearchInput" name="search" type="search" value="{{ $filters['search'] }}" placeholder="Cari di tabel..." class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">
                                    <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </form>
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
                                    <a href="{{ route('admin.pelanggan', array_filter(['search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                                        <span>Semua Akun</span>
                                        <span x-show="selectedFilter === 'Semua'" class="text-portal-orange font-bold">✓</span>
                                    </a>
                                    <a href="{{ route('admin.pelanggan', array_filter(['status' => 'aktif', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-emerald-700 flex items-center justify-between">
                                        <span>Aktif</span>
                                        <span x-show="selectedFilter === 'Aktif'" class="text-portal-orange font-bold">✓</span>
                                    </a>
                                    <a href="{{ route('admin.pelanggan', array_filter(['status' => 'nonaktif', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-red-600 flex items-center justify-between">
                                        <span>Tidak Aktif</span>
                                        <span x-show="selectedFilter === 'Tidak Aktif'" class="text-portal-orange font-bold">✓</span>
                                    </a>
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
                                @forelse ($pelanggan as $dataPelanggan)
                                        <tr class="hover:bg-zinc-50/80 transition"
                                            x-show="selectedFilter === 'Semua' || selectedFilter === {{ Js::from($dataPelanggan->status_akun === 'aktif' ? 'Aktif' : 'Tidak Aktif') }}">
                                            <td class="p-4 align-middle">
                                                <div class="inline-flex items-center gap-3">
                                                    <div class="w-8 h-8 rounded-full bg-zinc-900 text-white font-bold flex items-center justify-center text-[10px] flex-shrink-0">
                                                        {{ $dataPelanggan->inisial() }}
                                                    </div>
                                                    <span class="font-bold text-zinc-900 text-left">{{ $dataPelanggan->name }}</span>
                                                </div>
                                            </td>
                                            <td class="p-4 font-mono text-zinc-500 align-middle text-center">{{ str_pad((string) $dataPelanggan->id, 5, '0', STR_PAD_LEFT) }}</td>
                                            <td class="p-4 text-zinc-600 align-middle text-center">{{ $dataPelanggan->email }}</td>
                                            <td class="p-4 text-zinc-600 align-middle text-center">{{ $dataPelanggan->no_telepon ?: '—' }}</td>
                                            <td class="p-4 font-mono text-zinc-400 align-middle text-center">••••••••••••</td>
                                            <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-lg text-[11px] font-semibold bg-zinc-100 text-zinc-700 border border-zinc-200 inline-block">Customer</span></td>
                                            <td class="p-4 align-middle text-center">
                                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold {{ $dataPelanggan->status_akun === 'aktif' ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-red-50 text-red-700 border-red-200' }} border inline-block">
                                                    {{ $dataPelanggan->status_akun === 'aktif' ? 'Aktif' : 'Tidak Aktif' }}
                                                </span>
                                            </td>
                                            <td class="p-4 align-middle text-center text-zinc-400">—</td>
                                        </tr>
                                @empty
                                    <tr>
                                        <td colspan="8" class="p-10 text-center">
                                            <p class="text-3xl">👥</p>
                                            <p class="mt-2 text-sm font-semibold text-[#1A2024]">
                                                {{ $filters['search'] !== '' || $filters['status'] !== 'semua' ? 'Tidak ada pelanggan yang cocok dengan filter.' : 'Belum ada data pelanggan.' }}
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $filters['search'] !== '' || $filters['status'] !== 'semua'
                                                    ? 'Coba ubah kata kunci pencarian atau status akun.'
                                                    : 'Tambahkan pelanggan pertama melalui tombol "+ Tambah Pelanggan".' }}
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
                            @if ($pelanggan->total() > 0)
                                Menampilkan {{ $pelanggan->firstItem() }}–{{ $pelanggan->lastItem() }} dari {{ $pelanggan->total() }} akun
                            @else
                                Tidak ada pelanggan untuk ditampilkan
                            @endif
                        </p>
                        <div class="flex flex-wrap items-center gap-3">
                            <form method="GET" action="{{ route('admin.pelanggan') }}" class="flex items-center gap-2">
                                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                                <input type="hidden" name="search" value="{{ $filters['search'] }}">
                                <label for="customer_per_page" class="text-xs text-gray-500">Baris per halaman:</label>
                                <select id="customer_per_page" name="per_page" onchange="this.form.submit()"
                                        class="bg-[#F4EFEA]/60 border border-transparent rounded-lg px-2 py-1 text-xs text-[#1A2024] font-semibold focus:ring-2 focus:ring-[#15AEEA]">
                                    @foreach ([10, 25, 50] as $jumlahBaris)
                                        <option value="{{ $jumlahBaris }}" @selected($filters['per_page'] === $jumlahBaris)>{{ $jumlahBaris }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @if ($pelanggan->hasPages())
                                <div data-paginasi>{{ $pelanggan->links() }}</div>
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
                    <h2 class="text-lg font-bold text-[#1A2024]">Tambah Pelanggan</h2>
                    <p class="mt-0.5 text-xs text-gray-500">Buat akun pelanggan baru untuk OutGear Station.</p>
                </div>
                <button type="button" @click="showCreateModal = false"
                        class="h-8 w-8 rounded-lg bg-gray-100 text-gray-500 transition hover:bg-gray-200">✕</button>
            </div>

            <form method="POST" action="{{ route('admin.pelanggan.store') }}" class="space-y-4 p-5">
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
                    <div>
                        <label for="name" class="mb-1 block text-xs font-semibold text-[#1A2024]">Nama Lengkap</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required maxlength="255"
                               class="w-full rounded-xl border border-transparent bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    </div>
                    <div>
                        <label for="email" class="mb-1 block text-xs font-semibold text-[#1A2024]">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required maxlength="255"
                               class="w-full rounded-xl border border-transparent bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    </div>
                    <div>
                        <label for="no_telepon" class="mb-1 block text-xs font-semibold text-[#1A2024]">Nomor Telepon <span class="font-normal text-gray-400">(opsional)</span></label>
                        <input id="no_telepon" name="no_telepon" type="tel" value="{{ old('no_telepon') }}" maxlength="30"
                               class="w-full rounded-xl border border-transparent bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    </div>
                    <div>
                        <label for="password" class="mb-1 block text-xs font-semibold text-[#1A2024]">Kata Sandi</label>
                        <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password"
                               class="w-full rounded-xl border border-transparent bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    </div>
                    <div class="md:col-span-2">
                        <label for="password_confirmation" class="mb-1 block text-xs font-semibold text-[#1A2024]">Konfirmasi Kata Sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8" autocomplete="new-password"
                               class="w-full rounded-xl border border-transparent bg-[#F4EFEA]/60 px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    </div>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" @click="showCreateModal = false"
                            class="rounded-xl bg-gray-100 px-4 py-2.5 text-xs font-semibold text-[#1A2024] transition hover:bg-gray-200">Batal</button>
                    <button type="submit"
                            class="rounded-xl bg-[#D86B43] px-4 py-2.5 text-xs font-bold text-white shadow-md transition hover:bg-[#D86B43]/90">Simpan Pelanggan</button>
                </div>
            </form>
        </div>
    </div>

</body>
</html>