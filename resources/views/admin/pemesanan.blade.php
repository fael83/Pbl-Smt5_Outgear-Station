@use('Illuminate\Support\Str')
@php
    // Inisial pengguna yang sedang login untuk avatar di header.
    $penggunaLogin = auth()->user();
    $inisialLogin = $penggunaLogin
        ? Str::upper(Str::of($penggunaLogin->name)->explode(' ')->filter()->take(2)->map(fn ($kata) => Str::substr($kata, 0, 1))->implode(''))
        : '?';

    // Kelas badge per status (ditulis lengkap supaya terbaca oleh Tailwind).
    $kelasBadge = [
        'menunggu_pembayaran' => 'bg-amber-50 text-amber-700 border-amber-200',
        'dikonfirmasi' => 'bg-indigo-50 text-indigo-700 border-indigo-200',
        'berlangsung' => 'bg-blue-50 text-blue-700 border-blue-200',
        'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
        'dibatalkan' => 'bg-red-50 text-red-700 border-red-200',
    ];

    // Warna avatar pelanggan (dipilih berdasarkan id supaya konsisten).
    $warnaAvatar = ['bg-orange-100 text-orange-700', 'bg-teal-100 text-teal-700', 'bg-slate-200 text-slate-700', 'bg-amber-100 text-amber-700', 'bg-indigo-100 text-indigo-700'];

    // Parameter query yang dipertahankan saat pindah filter / halaman.
    $parameterCari = $kataKunci !== '' ? ['q' => $kataKunci] : [];
@endphp
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Data Pemesanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="text-slate-800 antialiased">

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
                    <a href="{{ route('admin.pemesanan', array_filter(['status' => $statusAktif]) + $parameterCari) }}" class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-600 hover:bg-zinc-50 shadow-sm" title="Muat ulang data">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </a>
                    <div class="w-8 h-8 rounded-full bg-zinc-900 text-white font-bold text-xs flex items-center justify-center border border-zinc-300 shadow-sm" title="{{ $penggunaLogin?->name }}">{{ $inisialLogin }}</div>
                </div>
            </header>

            <!-- Isi Halaman Data Pemesanan -->
            <div class="p-8 space-y-6">

                <!-- Judul Utama & Aksi -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h2 class="text-2xl font-black text-zinc-900 tracking-tight">Data Pemesanan</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Kelola seluruh data booking & penyewaan peralatan outdoor</p>
                    </div>
                </div>

                <!-- 4 KARTU STATISTIK PEMESANAN (data dari database) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
                    <!-- Kartu 1: Booking Aktif -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">{{ number_format($statistik['aktif'], 0, ',', '.') }}</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Booking Aktif</div>
                        </div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Belum selesai
                        </span>
                    </div>

                    <!-- Kartu 2: Menunggu Pembayaran -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-amber-600">{{ number_format($statistik['menunggu_pembayaran'], 0, ',', '.') }}</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Menunggu Pembayaran</div>
                        </div>
                        @if ($statistik['menunggu_pembayaran'] > 0)
                            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-amber-50 text-amber-700 border border-amber-200">
                                Perlu Aksi
                            </span>
                        @else
                            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-zinc-100 text-zinc-600 border border-zinc-200">
                                Tidak ada
                            </span>
                        @endif
                    </div>

                    <!-- Kartu 3: Sedang Berlangsung -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">{{ number_format($statistik['berlangsung'], 0, ',', '.') }}</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Sedang Berlangsung</div>
                        </div>
                        <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-blue-50 text-blue-700 border border-blue-200 flex items-center gap-1">
                            <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-pulse"></span> Aktif
                        </span>
                    </div>

                    <!-- Kartu 4: Selesai Bulan Ini -->
                    <div class="bg-white p-6 rounded-2xl border border-zinc-200/80 shadow-sm flex items-center justify-between">
                        <div>
                            <div class="text-3xl font-black text-blue-700">{{ number_format($statistik['selesai_bulan_ini'], 0, ',', '.') }}</div>
                            <div class="text-xs font-semibold text-zinc-400 mt-1">Selesai Bulan Ini</div>
                        </div>
                        <span class="text-[11px] font-bold px-2.5 py-1 rounded-full bg-zinc-100 text-zinc-600 border border-zinc-200 flex items-center gap-1">
                            @if ($statistik['selisih_selesai'] > 0)
                                ↗ +{{ $statistik['selisih_selesai'] }} dari bln lalu
                            @elseif ($statistik['selisih_selesai'] < 0)
                                ↘ {{ $statistik['selisih_selesai'] }} dari bln lalu
                            @else
                                = sama dgn bln lalu
                            @endif
                        </span>
                    </div>
                </div>

                <!-- STATUS BOARD / TABEL DATA PEMESANAN -->
                <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">

                    <!-- Header Action Board: pencarian + filter status -->
                    <form method="GET" action="{{ route('admin.pemesanan') }}"
                          class="p-5 border-b border-zinc-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <h3 class="font-bold text-zinc-900 text-base">Daftar Pemesanan</h3>
                            <span class="text-xs font-medium text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">
                                {{ number_format($pemesanan->total(), 0, ',', '.') }} data
                            </span>
                        </div>

                        <div class="flex flex-wrap items-center gap-2">
                            <input type="text" name="q" value="{{ $kataKunci }}" maxlength="100"
                                   placeholder="Cari kode, pelanggan, alat..."
                                   class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">

                            <select name="status" onchange="this.form.submit()"
                                    class="bg-white border border-zinc-300 text-xs font-medium text-zinc-700 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-portal-orange">
                                <option value="">Semua Status</option>
                                @foreach ($labelStatus as $kunciStatus => $namaStatus)
                                    <option value="{{ $kunciStatus }}" @selected($statusAktif === $kunciStatus)>{{ $namaStatus }}</option>
                                @endforeach
                            </select>

                            <button type="submit"
                                    class="bg-zinc-900 hover:bg-zinc-700 text-white text-xs font-bold px-4 py-2 rounded-lg transition">
                                Cari
                            </button>

                            @if ($statusAktif || $kataKunci !== '')
                                <a href="{{ route('admin.pemesanan') }}"
                                   class="text-xs font-semibold text-portal-orange hover:underline px-1">Reset</a>
                            @endif
                        </div>
                    </form>

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
                                @forelse ($pemesanan as $item)
                                    @php
                                        $dibatalkan = $item->status === 'dibatalkan';
                                        $namaPelanggan = $item->pelanggan?->name ?? 'Pelanggan dihapus';
                                        $inisialPelanggan = Str::upper(Str::of($namaPelanggan)->explode(' ')->filter()->take(2)->map(fn ($kata) => Str::substr($kata, 0, 1))->implode(''));

                                        // Item sewa: total unit, lalu 3 nama alat pertama.
                                        $totalUnit = $item->detailPemesanan->sum('jumlah');
                                        $daftarAlat = $item->detailPemesanan->map(fn ($detail) => ($detail->alat?->nama ?? 'Alat dihapus') . ($detail->jumlah > 1 ? ' ×' . $detail->jumlah : ''));
                                        $ringkasAlat = $daftarAlat->take(3)->implode(', ') . ($daftarAlat->count() > 3 ? ' +' . ($daftarAlat->count() - 3) . ' lainnya' : '');

                                        // Jadwal sewa; lama sewa dihitung inklusif (hari mulai dan hari selesai sama-sama dihitung).
                                        $mulai = $item->tanggal_mulai->copy()->locale('id');
                                        $selesai = $item->tanggal_selesai->copy()->locale('id');
                                        $lamaSewa = max(1, (int) $mulai->diffInDays($selesai) + 1);
                                        $teksJadwal = $mulai->translatedFormat('d M') . ' - ' . $selesai->translatedFormat('d M Y');
                                    @endphp
                                    <tr class="hover:bg-zinc-50/80 transition">
                                        <td class="p-4 font-mono text-zinc-500 align-middle text-center">#{{ $item->kode_pemesanan }}</td>
                                        <td class="p-4 align-middle">
                                            <div class="inline-flex items-center gap-3">
                                                <div class="w-8 h-8 rounded-full {{ $warnaAvatar[$item->user_id % count($warnaAvatar)] }} font-bold flex items-center justify-center text-[10px] overflow-hidden flex-shrink-0">{{ $inisialPelanggan }}</div>
                                                <div class="text-left">
                                                    <span class="font-bold text-zinc-900 block">{{ $namaPelanggan }}</span>
                                                    <span class="text-[11px] text-zinc-400">{{ $item->pelanggan?->no_telepon ?: '-' }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="p-4 text-zinc-600 align-middle text-center">
                                            <div class="font-semibold text-zinc-900">{{ $totalUnit }} item gear</div>
                                            <div class="text-[11px] text-zinc-400">{{ $ringkasAlat !== '' ? $ringkasAlat : '-' }}</div>
                                        </td>
                                        <td class="p-4 align-middle text-center">
                                            <div class="font-semibold {{ $dibatalkan ? 'line-through text-zinc-400' : 'text-zinc-900' }}">{{ $teksJadwal }}</div>
                                            @if ($item->status === 'menunggu_pembayaran')
                                                <div class="text-[11px] text-amber-600">{{ $lamaSewa }} Hari Durasi</div>
                                            @elseif ($item->status === 'dikonfirmasi')
                                                <div class="text-[11px] text-indigo-600">{{ $lamaSewa }} Hari · Siap diambil</div>
                                            @elseif ($item->status === 'berlangsung')
                                                <div class="text-[11px] text-blue-600">Sedang Digunakan</div>
                                            @elseif ($item->status === 'selesai')
                                                <div class="text-[11px] text-zinc-400">{{ $lamaSewa }} Hari · Selesai</div>
                                            @else
                                                <div class="text-[11px] text-red-500">Dibatalkan{{ $item->waktu_pembatalan ? ' ' . $item->waktu_pembatalan->copy()->locale('id')->translatedFormat('d M Y') : '' }}</div>
                                            @endif
                                        </td>
                                        <td class="p-4 font-bold align-middle text-center {{ $dibatalkan ? 'text-zinc-400 line-through' : 'text-zinc-900' }}">Rp {{ number_format((float) $item->total_biaya, 0, ',', '.') }}</td>
                                        <td class="p-4 align-middle text-center"><span class="px-2.5 py-1 rounded-full text-[11px] font-bold border inline-block {{ $kelasBadge[$item->status] ?? 'bg-zinc-100 text-zinc-600 border-zinc-200' }}">{{ $labelStatus[$item->status] ?? $item->status }}</span></td>
                                        <td class="p-4 align-middle text-center">
                                            <div class="inline-flex items-center gap-1.5">
                                                <button type="button" class="p-1.5 text-zinc-400 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition" title="Detail Pemesanan">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </button>
                                                @if (in_array($item->status, ['selesai', 'dibatalkan'], true))
                                                    <button type="button" class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Hapus">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                @else
                                                    <button type="button" class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition" title="Batalkan">
                                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="p-10 text-center text-zinc-400">
                                            @if ($statusAktif || $kataKunci !== '')
                                                Tidak ada pemesanan yang cocok dengan filter atau pencarian.
                                                <a href="{{ route('admin.pemesanan') }}" class="text-portal-orange font-semibold hover:underline">Reset</a>
                                            @else
                                                Belum ada data pemesanan.
                                            @endif
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- PAGINATION FOOTER -->
                    <div class="p-4 border-t border-zinc-100 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500 font-medium">
                        <div>
                            Menampilkan data ke <span class="font-bold text-zinc-900">{{ $pemesanan->firstItem() ?? 0 }} - {{ $pemesanan->lastItem() ?? 0 }}</span> dari <span class="font-bold text-zinc-900">{{ number_format($pemesanan->total(), 0, ',', '.') }}</span> pemesanan
                        </div>
                        @if ($pemesanan->hasPages())
                            @php
                                $halamanSekarang = $pemesanan->currentPage();
                                $halamanTerakhir = $pemesanan->lastPage();
                                $daftarHalaman = collect(range(1, $halamanTerakhir))
                                    ->filter(fn ($h) => $h === 1 || $h === $halamanTerakhir || abs($h - $halamanSekarang) <= 1)
                                    ->values();
                            @endphp
                            <div class="flex items-center gap-1.5">
                                @if ($pemesanan->onFirstPage())
                                    <span class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center text-zinc-400 cursor-not-allowed">‹</span>
                                @else
                                    <a href="{{ $pemesanan->previousPageUrl() }}" class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">‹</a>
                                @endif

                                @foreach ($daftarHalaman as $indeks => $nomor)
                                    @if ($indeks > 0 && $nomor - $daftarHalaman[$indeks - 1] > 1)
                                        <span class="px-1 text-zinc-400">...</span>
                                    @endif
                                    @if ($nomor === $halamanSekarang)
                                        <span class="w-8 h-8 rounded-lg bg-zinc-900 text-white font-bold flex items-center justify-center">{{ $nomor }}</span>
                                    @else
                                        <a href="{{ $pemesanan->url($nomor) }}" class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">{{ $nomor }}</a>
                                    @endif
                                @endforeach

                                @if ($pemesanan->hasMorePages())
                                    <a href="{{ $pemesanan->nextPageUrl() }}" class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center hover:bg-zinc-100 text-zinc-700">›</a>
                                @else
                                    <span class="w-8 h-8 rounded-lg border border-zinc-200 flex items-center justify-center text-zinc-400 cursor-not-allowed">›</span>
                                @endif
                            </div>
                        @endif
                    </div>

                </div>

            </div>
        </main>
    </div>

</body>
</html>
