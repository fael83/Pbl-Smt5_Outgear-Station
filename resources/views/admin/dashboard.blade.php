@php
    // Kelas badge status pemesanan (ditulis lengkap supaya terbaca oleh Tailwind).
    $kelasBadge = [
        'menunggu_pembayaran' => 'bg-amber-50 text-amber-700',
        'dikonfirmasi' => 'bg-indigo-50 text-indigo-700',
        'berlangsung' => 'bg-blue-50 text-blue-700',
        'selesai' => 'bg-emerald-50 text-emerald-700',
        'dibatalkan' => 'bg-red-50 text-red-700',
    ];

    // Warna avatar pelanggan (dipilih berdasarkan id supaya konsisten).
    $warnaAvatar = ['bg-amber-100 text-amber-800', 'bg-blue-100 text-blue-800', 'bg-emerald-100 text-emerald-800', 'bg-zinc-200 text-zinc-700', 'bg-indigo-100 text-indigo-800'];

    // Warna titik di rincian kategori.
    $warnaKategori = ['bg-portal-orange', 'bg-zinc-900', 'bg-amber-500', 'bg-blue-600', 'bg-emerald-600', 'bg-zinc-400'];
@endphp

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Admin Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F6F2] text-slate-800 antialiased font-sans">

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
                        {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                        <span class="text-zinc-400">· data pukul {{ $diperbaruiPada }}</span>
                    </p>
                </div>

                <!-- Status & User Action -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-3.5 py-1.5 rounded-full shadow-sm text-xs font-medium text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Basecamp Malang • Online
                    </div>
                    <a href="{{ route('admin.dashboard', ['segarkan' => 1]) }}"
                       onclick="this.querySelector('svg').classList.add('animate-spin')"
                       class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm transition flex items-center justify-center"
                       title="Muat ulang data">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                    <a href="/admin/verifikasi"
                       class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm relative transition"
                       title="{{ $statistik['perlu_verifikasi'] > 0 ? $statistik['perlu_verifikasi'].' pembayaran menunggu verifikasi' : 'Tidak ada pembayaran yang menunggu' }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        @if ($statistik['perlu_verifikasi'] > 0)
                            <span class="absolute top-1 right-1 w-2 h-2 bg-portal-orange rounded-full"></span>
                        @endif
                    </a>
                    <a href="{{ route('profile.edit') }}" aria-label="Buka profil admin" title="{{ auth()->user()?->name ?? 'Profil Admin' }}"
                       class="w-8 h-8 rounded-full bg-zinc-900 text-white font-bold text-xs flex items-center justify-center border border-zinc-300 shadow-sm hover:ring-2 hover:ring-portal-orange focus:outline-none focus:ring-2 focus:ring-portal-orange transition">
                        {{ auth()->user()?->inisial() ?? '?' }}
                    </a>
                </div>
            </header>

            <!-- Isi Halaman Dashboard -->
            <div class="p-8 space-y-6">
                
                

                <!-- 4 KARTU STATISTIK (Semua Kartu Putih Bersih, Proporsional, Tanpa Highlight Hitam) -->
                <!-- 4 KARTU STATISTIK (data dari database) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">

                    <!-- Kartu 1: Booking Aktif -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Booking Aktif</span>
                        <div class="my-2">
                            <div class="text-3xl font-black font-heading text-blue-700 tracking-tight">{{ number_format($statistik['booking_aktif'], 0, ',', '.') }}</div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-2 border-t border-zinc-100">
                            @if ($statistik['ambil_hari_ini'] > 0)
                                <span class="text-emerald-600 font-bold">{{ $statistik['ambil_hari_ini'] }} ambil hari ini</span>
                            @else
                                <span class="text-zinc-400 font-medium">Tidak ada jadwal ambil hari ini</span>
                            @endif
                        </div>
                    </div>

                    <!-- Kartu 2: Verifikasi Pembayaran -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Perlu Verifikasi</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-amber-600 tracking-tight"><span class="font-heading">{{ number_format($statistik['perlu_verifikasi'], 0, ',', '.') }}</span> <span class="text-xs font-semibold font-sans text-zinc-400">pembayaran</span></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-2 border-t border-zinc-100">
                            @if ($statistik['perlu_verifikasi'] > 0)
                                <span class="text-amber-600 font-bold">Menunggu Validasi</span>
                            @else
                                <span class="text-zinc-400 font-medium">Tidak ada antrean</span>
                            @endif
                        </div>
                    </div>

                    <!-- Kartu 3: Pendapatan Hari Ini -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Pendapatan Hari Ini</span>
                        <div class="my-2">
                            <div class="text-2xl font-black text-emerald-600 tracking-tight">Rp <span class="font-heading">{{ number_format($statistik['pendapatan_hari_ini'], 0, ',', '.') }}</span></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-zinc-400 font-medium">{{ $statistik['transaksi_hari_ini'] }} transaksi berhasil</span>
                        </div>
                    </div>

                    <!-- Kartu 4: Stok Alat Kritis -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Stok Alat Kritis</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-rose-600 tracking-tight"><span class="font-heading">{{ number_format($statistik['stok_kritis'], 0, ',', '.') }}</span> <span class="text-xs font-semibold font-sans text-zinc-400">item</span></div>
                        </div>
                        <div class="flex items-center justify-between text-[11px] pt-2 border-t border-zinc-100" title="Stok tersedia kurang dari atau sama dengan {{ $statistik['batas_kritis_persen'] }}% dari stok total">
                            @if ($statistik['stok_kritis'] > 0)
                                <span class="text-rose-600 font-bold">Menipis / Habis</span>
                            @else
                                <span class="text-zinc-400 font-medium">Semua stok aman</span>
                            @endif
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
                                        @forelse ($bookingTerbaru as $item)
                                            <tr class="hover:bg-zinc-50/50 transition">
                                                <td class="py-3.5 px-5">
                                                    <div class="flex items-center gap-3">
                                                        <div class="w-8 h-8 rounded-full {{ $warnaAvatar[$item['user_id'] % count($warnaAvatar)] }} font-bold flex items-center justify-center text-[11px] flex-shrink-0">{{ $item['inisial'] }}</div>
                                                        <div>
                                                            <div class="font-semibold text-zinc-900">{{ $item['pelanggan'] }}</div>
                                                            <div class="text-[11px] text-zinc-400 font-mono">#{{ $item['kode'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3.5 px-4 font-medium text-zinc-800">
                                                    {{ $item['jadwal'] }}
                                                </td>
                                                <td class="py-3.5 px-4">
                                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-medium {{ $kelasBadge[$item['status']] ?? 'bg-zinc-100 text-zinc-600' }}">
                                                        {{ $item['label_status'] }}
                                                    </span>
                                                </td>
                                                <td class="py-3.5 px-4 font-semibold text-zinc-900">
                                                    Rp {{ number_format($item['total'], 0, ',', '.') }}
                                                </td>
                                                <td class="py-3.5 px-5 text-right">
                                                    <a href="/admin/pemesanan" class="text-xs font-semibold text-zinc-500 hover:text-portal-orange transition">
                                                        Detail
                                                    </a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="py-10 px-5 text-center text-zinc-400">Belum ada pemesanan.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- VERIFIKASI PEMBAYARAN CEPAT -->
                        <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm space-y-4">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <h3 class="font-bold text-zinc-900 text-sm">Verifikasi Pembayaran</h3>
                                    @if ($statistik['perlu_verifikasi'] > 0)
                                        <span class="bg-amber-100 text-amber-800 text-[11px] font-bold px-2 py-0.5 rounded-full">{{ $statistik['perlu_verifikasi'] }} Menunggu</span>
                                    @endif
                                </div>
                                <a href="/admin/verifikasi" class="text-xs font-semibold text-portal-orange hover:underline">
                                    Kelola Semua →
                                </a>
                            </div>

                            @if (count($antreanPembayaran) > 0)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3.5">
                                    @foreach ($antreanPembayaran as $antrean)
                                        <div class="p-3.5 rounded-xl bg-zinc-50/80 border border-zinc-200/60 flex items-center justify-between gap-3">
                                            <div class="min-w-0">
                                                <div class="font-semibold text-zinc-900 text-xs truncate">{{ $antrean['pelanggan'] }}</div>
                                                <div class="text-[11px] text-zinc-400 mt-0.5">{{ $antrean['jenis'] }} • {{ $antrean['metode'] }} • <span class="font-bold text-zinc-900">Rp {{ number_format($antrean['jumlah'], 0, ',', '.') }}</span></div>
                                            </div>
                                            <a href="/admin/verifikasi" class="flex-shrink-0 bg-emerald-600 hover:bg-emerald-700 text-white text-[11px] font-semibold py-1.5 px-3 rounded-lg shadow-sm transition">
                                                Tinjau
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="py-6 text-center text-xs text-zinc-400">Tidak ada pembayaran yang menunggu verifikasi.</div>
                            @endif
                        </div>

                    </div>

                    <!-- KOLOM KANAN (Lebar 4): STATUS INVENTARIS ALAT DIBUAT PAS DI TENGAH BERSIH -->
                    <!-- KOLOM KANAN (Lebar 4): STATUS INVENTARIS ALAT -->
                    <div class="lg:col-span-4 flex flex-col h-full">
                        <div class="bg-white p-6 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between h-full">

                            <!-- Header Widget -->
                            <div class="flex items-center justify-between pb-3 border-b border-zinc-100">
                                <div>
                                    <h3 class="font-bold text-zinc-900 text-sm">Status Inventaris Alat</h3>
                                    <p class="text-[11px] text-zinc-400">Unit terdistribusi saat ini</p>
                                </div>
                                <a href="/admin/peralatan" class="text-xs font-semibold text-portal-orange hover:underline">Detail →</a>
                            </div>

                            <!-- Donut (SVG, dihitung dari stok di database) -->
                            <div class="my-auto py-6 flex flex-col items-center justify-center">
                                @if ($inventaris['total'] > 0)
                                    <div class="relative flex items-center justify-center">
                                        <svg class="w-44 h-44 -rotate-90" viewBox="0 0 40 40" role="img" aria-label="Komposisi stok alat">
                                            <circle cx="20" cy="20" r="15.9155" fill="none" stroke="#F4F4F5" stroke-width="4.2"></circle>
                                            @foreach ($inventaris['segmen'] as $segmen)
                                                <circle cx="20" cy="20" r="15.9155" fill="none"
                                                        stroke="{{ $segmen['warna'] }}" stroke-width="4.2"
                                                        stroke-dasharray="{{ $segmen['persen'] }} {{ round(100 - $segmen['persen'], 2) }}"
                                                        stroke-dashoffset="-{{ $segmen['geser'] }}">
                                                    <title>{{ $segmen['label'] }}: {{ $segmen['nilai'] }} unit</title>
                                                </circle>
                                            @endforeach
                                        </svg>
                                        <div class="absolute inset-0 flex flex-col items-center justify-center text-center pointer-events-none">
                                            <span class="text-3xl font-black font-heading text-blue-700 leading-none">{{ number_format($inventaris['disewa'], 0, ',', '.') }}</span>
                                            <span class="text-[10px] font-bold text-zinc-400 uppercase tracking-wider mt-1">UNIT DISEWA</span>
                                            <span class="text-[9px] text-zinc-400 font-medium mt-0.5">(dari {{ number_format($inventaris['total'], 0, ',', '.') }} total)</span>
                                        </div>
                                    </div>
                                    <div class="mt-4 flex flex-wrap items-center justify-center gap-x-4 gap-y-1 text-[10px] text-zinc-500">
                                        @foreach ($inventaris['segmen'] as $segmen)
                                            <span class="inline-flex items-center gap-1.5"><span class="w-2 h-2 rounded-full" style="background-color: {{ $segmen['warna'] }}"></span>{{ $segmen['label'] }} ({{ $segmen['nilai'] }})</span>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="text-xs text-zinc-400 text-center">Belum ada data alat.</div>
                                @endif
                            </div>

                            <!-- Breakdown Per Kategori Alat (disewa / total unit) -->
                            @if (count($inventaris['kategori']) > 0)
                                <div class="space-y-2.5 pt-3 border-t border-zinc-100 text-xs">
                                    @foreach ($inventaris['kategori'] as $indeks => $kategori)
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-2 min-w-0">
                                                <span class="w-2.5 h-2.5 rounded-full flex-shrink-0 {{ $warnaKategori[$indeks] ?? 'bg-zinc-300' }}"></span>
                                                <span class="font-medium text-zinc-700 truncate">{{ $kategori['nama'] }}</span>
                                            </div>
                                            <span class="font-bold text-zinc-900 flex-shrink-0">{{ $kategori['disewa'] }} / {{ $kategori['total'] }} Unit</span>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>    

</body>
</html>