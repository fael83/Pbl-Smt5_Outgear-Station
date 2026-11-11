<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OutGear Station - Data Pemesanan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-theme antialiased font-sans"
      x-data="halamanPemesanan({
          tabelUrl: {{ Js::from(route('admin.pemesanan.tabel')) }},
          labelStatus: {{ Js::from($labelStatus) }},
          filters: {{ Js::from(['search' => $kataKunci, 'status' => (string) $statusAktif, 'per_page' => (string) $perPage]) }}
      })">

    <div class="flex min-h-screen">

        @include('partials.sidebar-admin')


        <!--KONTEN UTAMA (Kanan)-->
        <main class="flex-grow flex flex-col min-w-0">

            <!-- Header Atas -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-8 py-3.5 z-40 border-b border-zinc-200/60 flex items-center justify-between">
                <div class="relative w-96">
                </div>

                <div class="flex items-center gap-3">
                    <!-- Profil pengguna: foto profil, nama, dan email (sama seperti halaman data peralatan) -->
                    @include('partials.profil-topbar')
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
                        <div class="text-[11px] text-blue-700 font-bold pt-2 border-t border-zinc-100">Aktif</div>
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

                <!-- Kartu tabel (diperbarui lewat AJAX, tanpa memuat ulang halaman) -->
                <div x-ref="tabel" @click="tangkapKlik($event)" :class="memuat ? 'opacity-60' : ''" class="transition-opacity">
                    @include('admin.pemesanan._tabel')
                </div>


            </div>
        </main>
    </div>

    @include('partials.status-muat')

</body>
</html>
