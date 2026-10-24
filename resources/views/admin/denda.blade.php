<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Kelola Denda</title>
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
                        'portal-orange': '#EA580C',
                        'portal-card': '#ffffff',
                    }
                }
            }
        }
    </script>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#F8F6F2] text-slate-800 antialiased font-sans" x-data="{ activeTab: {{ Js::from(match ($filters['status']) { 'belum_dibayar' => 'Belum Lunas', 'lunas' => 'Lunas', default => 'Semua' }) }}, showFilterMenu: false }">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas (Bersih, Sesuai Referensi) -->
            <header class="sticky top-0 bg-[#F8F6F2]/90 backdrop-blur-md px-10 py-4 z-30 border-b border-zinc-200/60 flex items-center justify-between">
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
                    
                    <!-- Search & Filter Bar (Menggunakan Ikon Filter Khas Referensi) -->
                    <div class="p-6 border-b border-zinc-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="font-black text-zinc-900 text-sm">Daftar Tagihan Denda</h3>
                            <span class="text-xs font-bold text-zinc-500 bg-zinc-100 px-3 py-1 rounded-xl">Filter: <span x-text="activeTab" class="text-zinc-900"></span></span>
                        </div>

                        <div class="flex items-center gap-2.5">
                            <!-- Input Search -->
                            <div class="relative">
                                <form method="GET" action="{{ route('admin.denda') }}" class="relative">
                                    <input type="hidden" name="status" value="{{ $filters['status'] }}">
                                    <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
                                    <input type="search" name="search" value="{{ $filters['search'] }}" placeholder="Cari ID denda / referensi..." class="bg-zinc-50 border border-zinc-200 text-xs rounded-xl px-4 py-2.5 pl-9 focus:outline-none focus:border-portal-orange transition w-64">
                                    <svg class="w-4 h-4 text-zinc-400 absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </form>
                            </div>

                            <!-- Tombol Ikon Filter Dropdown -->
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="p-2.5 bg-zinc-50 hover:bg-zinc-100 border border-zinc-200 rounded-xl text-zinc-600 transition flex items-center justify-center shadow-sm" title="Filter Data">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707v4.172a1 1 0 01-1.447.894l-4-2A1 1 0 017 18v-3.586a1 1 0 00-.293-.707L.293 7.293A1 1 0 010 6.586V4z"></path></svg>
                                </button>

                                <!-- Dropdown Menu Filter -->
                                <div x-show="open" @click.away="open = false" x-cloak class="absolute right-0 mt-2 w-48 bg-white border border-zinc-200 rounded-2xl shadow-xl py-2 z-50 text-xs font-medium">
                                    <div class="px-4 py-1.5 text-[10px] font-black text-zinc-400 uppercase tracking-wider">Status Pembayaran</div>
                                    <a href="{{ route('admin.denda', array_filter(['search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="block w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700">Semua Status</a>
                                    <a href="{{ route('admin.denda', array_filter(['status' => 'belum_dibayar', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="block w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700">Belum Lunas</a>
                                    <a href="{{ route('admin.denda', array_filter(['status' => 'lunas', 'search' => $filters['search'], 'per_page' => $filters['per_page'] !== 10 ? $filters['per_page'] : null])) }}" class="block w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700">Lunas</a>
                                </div>
                            </div>
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
                            <form method="GET" action="{{ route('admin.denda') }}" class="flex items-center gap-2">
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