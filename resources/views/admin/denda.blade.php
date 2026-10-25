<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Kelola Denda</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
<body class="admin-theme antialiased font-sans" x-data="{ showSearchInput: {{ $filters['search'] !== '' ? 'true' : 'false' }}, showFilterDropdown: false }">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas (Bersih, Sesuai Referensi) -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-10 py-4 z-30 border-b border-zinc-200/60 flex items-center justify-between">
                <div></div> <!-- Spacer kosong agar sisi kanan tetap rapi -->

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-4 py-2 rounded-full shadow-sm text-xs font-bold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Basecamp Malang • Online
                    </div>
                    <button class="p-2.5 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm transition" title="Refresh">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                    <button class="p-2.5 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm relative transition" title="Notifikasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-portal-orange rounded-full"></span>
                    </button>
                    <div class="w-9 h-9 rounded-full bg-zinc-900 text-white font-bold text-xs flex items-center justify-center border border-zinc-300 shadow-sm">DW</div>
                </div>
            </header>

            <!-- Isi Halaman Kelola Denda (Lega & Tidak Mepet) -->
            <div class="p-10 space-y-8 max-w-[100rem] mx-auto w-full">
                
                <!-- Judul Utama & Badge Informasi di Kanan -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-black text-zinc-900 tracking-tight">Kelola Denda & Kerusakan</h1>
                        <p class="text-xs text-zinc-500 mt-1">Pencatatan denda keterlambatan pengembalian dan klaim penggantian alat berdasarkan ERD.</p>
                    </div>
                    <div class="bg-amber-100 text-amber-800 text-xs font-extrabold px-5 py-2.5 rounded-2xl shadow-sm border border-amber-200/60 self-start md:self-auto flex items-center gap-2">
                        <span class="w-2 h-2 rounded-full bg-amber-600"></span>
                        {{ number_format($jumlahBelumDibayar, 0, ',', '.') }} Tagihan Denda Belum Lunas
                    </div>
                </div>

                <!-- 3 KARTU STATISTIK DENDA -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Denda Keterlambatan</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-amber-600">Rp <span class="font-heading">{{ number_format((float) $totalKeterlambatan, 0, ',', '.') }}</span></div>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-amber-600 font-bold">Aktif</span>
                            <span class="text-zinc-400 text-right">{{ $jumlahBelumDibayar }} tagihan belum lunas</span>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Klaim Kerusakan</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-rose-600">Rp <span class="font-heading">{{ number_format((float) $totalKerusakan, 0, ',', '.') }}</span></div>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-rose-600 font-bold">Perhatian</span>
                            <span class="text-zinc-400 text-right">Kerusakan dan kehilangan belum lunas</span>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Terlunasi</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-emerald-600">Rp <span class="font-heading">{{ number_format((float) $totalLunas, 0, ',', '.') }}</span></div>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-emerald-600 font-bold">Sukses</span>
                            <span class="text-zinc-400 text-right">Total tagihan lunas</span>
                        </div>
                    </div>
                </div>

                <!-- TABEL DATA DENDA -->
                <div class="bg-white rounded-3xl border border-zinc-200/80 shadow-sm overflow-hidden">
                    
                    <!-- Header Action Board (Kaca Pembesar & Icon Filter seperti di Verifikasi) -->
                    <div class="p-5 border-b border-zinc-100 flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="font-bold text-zinc-900 text-base">Daftar Tagihan Denda</h3>
                            <span class="text-xs font-medium text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">
                                Filter: <span class="font-bold text-zinc-800">{{ match ($filters['status']) { 'belum_dibayar' => 'Belum Lunas', 'lunas' => 'Lunas', default => 'Semua Status' } }}</span>
                            </span>
                            <span class="text-xs font-medium text-zinc-400">
                                ({{ number_format($denda->total(), 0, ',', '.') }} data)
                            </span>
                        </div>

                        <div class="flex items-center gap-2 relative">
                            <!-- Input Search Interaktif -->
                            <form method="GET" action="{{ route('admin.denda.index') }}" class="flex items-center gap-2">
                                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                                <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
                                
                                <div x-show="showSearchInput" x-transition class="relative">
                                    <input id="dendaSearchInput" 
                                           type="text" 
                                           name="search"
                                           value="{{ $filters['search'] }}"
                                           placeholder="Cari ID / pemesanan..." 
                                           class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">
                                    <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </div>

                                <!-- Tombol Kaca Pembesar -->
                                <button type="button" 
                                        @click="showSearchInput = !showSearchInput; if(showSearchInput) { $nextTick(() => document.getElementById('dendaSearchInput')?.focus()) }" 
                                        class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" 
                                        title="Cari tagihan denda">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </button>
                            </form>
                            
                            <!-- Tombol Filter Interaktif -->
                            <div class="relative">
                                <button type="button"
                                        @click="showFilterDropdown = !showFilterDropdown" 
                                        class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" 
                                        title="Filter Status Pembayaran Denda">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                </button>

                                <!-- Dropdown Menu Filter -->
                                <div x-show="showFilterDropdown" 
                                     @click.away="showFilterDropdown = false" 
                                     x-transition 
                                     class="absolute right-0 mt-2 w-48 bg-white border border-zinc-200 rounded-xl shadow-lg py-1.5 z-50 text-xs font-medium">
                                    <div class="px-3 py-1.5 font-bold text-zinc-400 uppercase text-[10px] tracking-wider border-b border-zinc-100">Status Pembayaran</div>
                                    <a href="{{ route('admin.denda.index', array_filter(['search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" 
                                       class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                                        <span>Semua Status</span>
                                        @if ($filters['status'] === 'semua')
                                            <span class="text-portal-orange font-bold">✓</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.denda.index', array_filter(['status' => 'belum_dibayar', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" 
                                       class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-amber-700 flex items-center justify-between">
                                        <span>Belum Lunas</span>
                                        @if ($filters['status'] === 'belum_dibayar')
                                            <span class="text-portal-orange font-bold">✓</span>
                                        @endif
                                    </a>
                                    <a href="{{ route('admin.denda.index', array_filter(['status' => 'lunas', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" 
                                       class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-emerald-700 flex items-center justify-between">
                                        <span>Lunas</span>
                                        @if ($filters['status'] === 'lunas')
                                            <span class="text-portal-orange font-bold">✓</span>
                                        @endif
                                    </a>
                                </div>
                            </div>

                            @if ($filters['status'] !== 'semua' || $filters['search'] !== '')
                                <a href="{{ route('admin.denda.index') }}"
                                   class="text-xs font-semibold text-portal-orange hover:underline px-1" title="Reset filter">Reset</a>
                            @endif
                        </div>
                    </div>

                    <!-- Tabel Data Utama dengan Spacing Lega -->
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-zinc-50/80 text-zinc-400 font-black uppercase text-[10px] tracking-wider border-b border-zinc-100">
                                    <th class="py-5 px-6">ID & Relasi (FK)</th>
                                    <th class="py-5 px-4">Jenis Denda (Enum)</th>
                                    <th class="py-5 px-4">Nilai Dasar & Tarif Terpakai</th>
                                    <th class="py-5 px-4">Jumlah Denda</th>
                                    <th class="py-5 px-4">Status</th>
                                    <th class="py-5 px-6 text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                @forelse ($denda as $dataDenda)
                                    @php
                                        $kelasJenis = match ($dataDenda->jenis_denda) {
                                            'keterlambatan' => 'bg-amber-50 text-amber-700 border-amber-200/60',
                                            'kerusakan_ringan', 'kerusakan_berat' => 'bg-rose-50 text-rose-700 border-rose-200/60',
                                            'kehilangan' => 'bg-purple-50 text-purple-700 border-purple-200/60',
                                            default => 'bg-zinc-100 text-zinc-700 border-zinc-200',
                                        };
                                        $kelasStatus = $dataDenda->status === 'lunas'
                                            ? 'bg-emerald-50 text-emerald-700 border-emerald-200/60'
                                            : 'bg-rose-50 text-rose-700 border-rose-200/60';
                                    @endphp
                                    <tr class="hover:bg-zinc-50/60 transition">
                                        <td class="py-5 px-6">
                                            <div class="font-extrabold text-zinc-900 text-sm">#DND-{{ str_pad((string) $dataDenda->id, 2, '0', STR_PAD_LEFT) }}</div>
                                            <div class="text-[11px] text-zinc-400 font-mono mt-0.5">
                                                Pengembalian #{{ $dataDenda->pengembalian_id }} ? Pemesanan #{{ $dataDenda->pemesanan_id }}
                                                @if ($dataDenda->detail_pengembalian_id)
                                                    ? Item #{{ $dataDenda->detail_pengembalian_id }}
                                                @endif
                                            </div>
                                        </td>
                                        <td class="py-5 px-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full border text-[11px] font-extrabold {{ $kelasJenis }}">
                                                {{ str_replace('_', ' ', $dataDenda->jenis_denda) }}
                                            </span>
                                        </td>
                                        <td class="py-5 px-4 text-zinc-600">
                                            <div class="font-bold text-zinc-800">Dasar: Rp {{ number_format((float) $dataDenda->nilai_dasar, 0, ',', '.') }}</div>
                                            <div class="text-[11px] text-zinc-400 mt-0.5">
                                                Tarif terkait: {{ $dataDenda->nilai_tarif_terkait !== null ? 'Rp '.number_format((float) $dataDenda->nilai_tarif_terkait, 0, ',', '.') : '?' }}
                                            </div>
                                        </td>
                                        <td class="py-5 px-4 font-black text-zinc-900 text-sm">Rp {{ number_format((float) $dataDenda->jumlah_denda, 0, ',', '.') }}</td>
                                        <td class="py-5 px-4">
                                            <span class="inline-flex items-center px-3 py-1 rounded-full border text-[11px] font-extrabold {{ $kelasStatus }}">
                                                {{ str_replace('_', ' ', $dataDenda->status) }}
                                            </span>
                                        </td>
                                        <td class="py-5 px-6 text-right">
                                            @if ($dataDenda->status === 'lunas')
                                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 bg-emerald-50 px-3 py-1.5 rounded-xl border border-emerald-200/60">Tervalidasi</span>
                                            @else
                                                <span class="bg-zinc-900 text-white text-xs font-bold py-2 px-3.5 rounded-xl shadow-sm">Menunggu pembayaran</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="p-10 text-center">
                                            <p class="text-3xl">🧾</p>
                                            <p class="mt-2 text-sm font-semibold text-[#1A2024]">
                                                {{ $filters['search'] !== '' || $filters['status'] !== 'semua' ? 'Tidak ada data denda yang cocok dengan filter.' : 'Belum ada data denda.' }}
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500">
                                                {{ $filters['search'] !== '' || $filters['status'] !== 'semua'
                                                    ? 'Coba ubah kata kunci pencarian atau status pembayaran.'
                                                    : 'Data denda akan muncul setelah tercatat pada proses pengembalian.' }}
                                            </p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination Footer -->
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-5 py-4 border-t border-gray-100">
                        <p class="text-xs text-gray-500">
                            @if ($denda->total() > 0)
                                Menampilkan {{ $denda->firstItem() }}–{{ $denda->lastItem() }} dari {{ $denda->total() }} data denda
                            @else
                                Tidak ada data denda untuk ditampilkan
                            @endif
                        </p>
                        <div class="flex flex-wrap items-center gap-3">
                            <form method="GET" action="{{ route('admin.denda.index') }}" class="flex items-center gap-2">
                                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                                <input type="hidden" name="search" value="{{ $filters['search'] }}">
                                <label for="fine_per_page" class="text-xs text-gray-500">Baris per halaman:</label>
                                <select id="fine_per_page" name="per_page" onchange="this.form.submit()"
                                        class="bg-[#F4EFEA]/60 border border-transparent rounded-lg px-2 py-1 text-xs text-[#1A2024] font-semibold focus:ring-2 focus:ring-[#15AEEA]">
                                    @foreach ([10, 25, 50] as $jumlahBaris)
                                        <option value="{{ $jumlahBaris }}" @selected($filters['per_page'] === $jumlahBaris)>{{ $jumlahBaris }}</option>
                                    @endforeach
                                </select>
                            </form>
                            @if ($denda->hasPages())
                                <div data-paginasi>{{ $denda->links() }}</div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>