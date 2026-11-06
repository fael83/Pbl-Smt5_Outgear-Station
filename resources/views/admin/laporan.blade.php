<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OutGear Station - Laporan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-theme antialiased font-sans"
      x-data="halamanLaporan({
          tabelUrl: {{ Js::from(route('admin.laporan.tabel')) }},
          filters: {{ Js::from(['search' => $filters['search'], 'tipe' => $filters['tipe'], 'dari' => $filters['dari'], 'sampai' => $filters['sampai'], 'per_page' => $filters['per_page']]) }},
          bawaan: {{ Js::from($bawaan) }},
          ringkasan: {{ Js::from($ringkasan) }}
      })">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas (Bersih) -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-10 py-4 z-30 border-b border-zinc-200/60 flex items-center justify-between">
                <div></div> <!-- Spacer kosong -->

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-4 py-2 rounded-full shadow-sm text-xs font-bold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Basecamp Malang • Online
                    </div>
                    @include('partials.tombol-muat-ulang')
                    <div class="w-9 h-9 rounded-full bg-zinc-900 text-white font-bold text-xs flex items-center justify-center border border-zinc-300 shadow-sm">DW</div>
                </div>
            </header>

            <!-- Isi Halaman Laporan -->
            <div class="p-10 space-y-8 max-w-[100rem] mx-auto w-full">
                
                <!-- Judul Utama & Aksi Ekspor -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-black text-zinc-900 tracking-tight">Laporan Operasional</h1>
                        <p class="text-xs text-zinc-500 mt-1">Rekapitulasi pendapatan, transaksi sewa, denda, dan penggunaan peralatan basecamp.</p>
                    </div>

                    <!-- Tombol Ekspor PDF & Excel -->
                    <div class="flex items-center gap-2.5 self-start md:self-auto">
                        <!-- Tombol Excel -->
                        <a href="{{ route('admin.laporan.excel') }}" 
                           class="bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-700 text-xs font-bold py-2.5 px-4 rounded-xl transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel
                        </a>

                        <!-- Tombol PDF -->
                        <a href="{{ route('admin.laporan.pdf') }}" 
                           class="bg-zinc-900 hover:bg-portal-orange text-white text-xs font-bold py-2.5 px-4 rounded-xl transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak PDF
                        </a>
                    </div>
                </div> <!-- END / Penutup div judul & tombol ekspor -->

                <!-- 4 KARTU RINGKASAN LAPORAN (ANGKA BERWARNA & TIDAK HITAM) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kartu 1 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Pendapatan</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-emerald-600">Rp <span class="font-heading" x-text="ringkasanData.pendapatan">{{ $ringkasan['pendapatan'] }}</span></div>
                        </div>
                        <div class="text-[11px] font-bold pt-2 border-t border-zinc-100"
                             :class="ringkasanData.pendapatan_tren > 0 ? 'text-emerald-600' : (ringkasanData.pendapatan_tren < 0 ? 'text-rose-600' : 'text-zinc-400')"
                             x-text="ringkasanData.pendapatan_catatan">{{ $ringkasan['pendapatan_catatan'] }}</div>
                    </div>

                    <!-- Kartu 2 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Transaksi</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-blue-600"><span class="font-heading" x-text="ringkasanData.transaksi">{{ $ringkasan['transaksi'] }}</span> <span class="text-xs font-medium font-sans text-zinc-400">sewa</span></div>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-medium pt-2 border-t border-zinc-100" x-text="'Periode ' + ringkasanData.periode">Periode {{ $ringkasan['periode'] }}</div>
                    </div>

                    <!-- Kartu 3 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Denda Terekap</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-amber-600">Rp <span class="font-heading" x-text="ringkasanData.denda">{{ $ringkasan['denda'] }}</span></div>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-medium pt-2 border-t border-zinc-100">Keterlambatan & Kerusakan</div>
                    </div>

                    <!-- Kartu 4 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Utilitas Alat</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-teal-600"><span class="font-heading" x-text="ringkasanData.utilisasi">{{ $ringkasan['utilisasi'] }}</span>%</div>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-medium pt-2 border-t border-zinc-100" x-text="ringkasanData.alat_teratas">{{ $ringkasan['alat_teratas'] }}</div>
                    </div>
                </div>

                <!-- TABEL DATA LAPORAN (diperbarui lewat AJAX, tanpa reload halaman) -->
                <div x-ref="tabel" @click="tangkapKlik($event)" :class="memuat ? 'opacity-60' : ''" class="transition-opacity">
                    @include('admin.laporan._tabel')
                </div>

            </div>
        </main>
    </div>

    @include('partials.status-muat')
</body>
</html>