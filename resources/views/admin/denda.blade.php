<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OutGear Station - Kelola Denda</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-theme antialiased font-sans"
      x-data="halamanDenda({
          tabelUrl: {{ Js::from(route('admin.denda.tabel')) }},
          filters: {{ Js::from(['search' => $filters['search'], 'status' => $filters['status'] === 'semua' ? '' : $filters['status'], 'per_page' => (string) $filters['per_page']]) }},
          ringkasan: {{ Js::from($ringkasan) }}
      })">

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
                    <!-- Muat ulang data: tombol, pop-up, dan notifikasinya sama di semua halaman admin -->
                    @include('partials.tombol-muat-ulang', ['fallback' => route('admin.denda.index')])
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
                        <span x-text="ringkasanData.belum_dibayar">{{ $ringkasan['belum_dibayar'] }}</span> Tagihan Denda Belum Lunas
                    </div>
                </div>

                <!-- 3 KARTU STATISTIK DENDA -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Denda Keterlambatan</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-amber-600">Rp <span class="font-heading" x-text="ringkasanData.keterlambatan">{{ $ringkasan['keterlambatan'] }}</span></div>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-amber-600 font-bold">Aktif</span>
                            <span class="text-zinc-400 text-right"><span x-text="ringkasanData.belum_dibayar">{{ $ringkasan['belum_dibayar'] }}</span> tagihan belum lunas</span>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Klaim Kerusakan</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-rose-600">Rp <span class="font-heading" x-text="ringkasanData.kerusakan">{{ $ringkasan['kerusakan'] }}</span></div>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-rose-600 font-bold">Perhatian</span>
                            <span class="text-zinc-400 text-right">Kerusakan dan kehilangan belum lunas</span>
                        </div>
                    </div>

                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Terlunasi</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-emerald-600">Rp <span class="font-heading" x-text="ringkasanData.lunas">{{ $ringkasan['lunas'] }}</span></div>
                        </div>
                        <div class="flex items-center justify-between gap-2 text-[11px] pt-2 border-t border-zinc-100">
                            <span class="text-emerald-600 font-bold">Sukses</span>
                            <span class="text-zinc-400 text-right">Total tagihan lunas</span>
                        </div>
                    </div>
                </div>

                <!-- Kartu tabel (diperbarui lewat AJAX, tanpa memuat ulang halaman) -->
                <div x-ref="tabel" @click="tangkapKlik($event)" :class="memuat ? 'opacity-60' : ''" class="transition-opacity">
                    @include('admin.denda._tabel')
                </div>

            </div>
        </main>
    </div>

    @include('partials.status-muat')

</body>
</html>