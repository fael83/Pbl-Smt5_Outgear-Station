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

    {{-- Pop-up detail pemesanan (hanya melihat, tidak ada aksi ubah atau hapus) --}}
    <div x-show="detail" x-cloak x-transition.opacity
         @keydown.escape.window="tutupDetail()"
         @click.self="tutupDetail()"
         class="fixed inset-0 z-[60] flex items-center justify-center p-4 bg-zinc-900/50">
        <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
            <template x-if="detail">
                <div>
                    <div class="flex items-start justify-between gap-4 p-5 border-b border-zinc-100">
                        <div>
                            <p class="text-[11px] font-bold text-zinc-400 uppercase tracking-wider">Detail Pemesanan</p>
                            <h3 class="text-lg font-black text-zinc-900 font-mono" x-text="'#' + detail.kode"></h3>
                            <span class="inline-block mt-1 px-2.5 py-1 rounded-full text-[11px] font-bold border bg-zinc-100 text-zinc-700 border-zinc-200" x-text="detail.status"></span>
                        </div>
                        <button type="button" @click="tutupDetail()" class="p-2 rounded-lg text-zinc-400 hover:bg-zinc-100 hover:text-zinc-700" title="Tutup">✕</button>
                    </div>

                    <div class="p-5 space-y-5 text-xs">
                        <!-- Pelanggan & jadwal -->
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div class="bg-zinc-50 rounded-xl p-4 space-y-1">
                                <p class="text-[10px] font-bold text-zinc-400 uppercase">Pelanggan</p>
                                <p class="font-bold text-zinc-900" x-text="detail.pelanggan"></p>
                                <p class="text-zinc-500" x-text="detail.telepon"></p>
                                <p class="text-zinc-500" x-text="detail.email"></p>
                            </div>
                            <div class="bg-zinc-50 rounded-xl p-4 space-y-1">
                                <p class="text-[10px] font-bold text-zinc-400 uppercase">Jadwal Sewa</p>
                                <p class="font-bold text-zinc-900" x-text="detail.jadwal"></p>
                                <p class="text-zinc-500" x-text="detail.lama + ' hari'"></p>
                                <p class="text-zinc-400">Dibuat: <span x-text="detail.dibuat"></span></p>
                                <p class="text-red-500" x-show="detail.dibatalkan">Dibatalkan: <span x-text="detail.dibatalkan"></span></p>
                            </div>
                        </div>

                        <!-- Alat yang disewa -->
                        <div>
                            <p class="text-[10px] font-bold text-zinc-400 uppercase mb-2">Alat yang Disewa</p>
                            <div class="border border-zinc-200 rounded-xl overflow-hidden">
                                <table class="w-full text-left">
                                    <thead class="bg-zinc-50 text-zinc-400 text-[10px] uppercase">
                                        <tr>
                                            <th class="px-3 py-2">Alat</th>
                                            <th class="px-3 py-2 text-center">Jumlah</th>
                                            <th class="px-3 py-2 text-right">Tarif/Hari</th>
                                            <th class="px-3 py-2 text-right">Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-zinc-100">
                                        <template x-for="(alat, i) in detail.items" :key="i">
                                            <tr>
                                                <td class="px-3 py-2 font-semibold text-zinc-800" x-text="alat.nama"></td>
                                                <td class="px-3 py-2 text-center" x-text="alat.jumlah"></td>
                                                <td class="px-3 py-2 text-right" x-text="rp(alat.tarif)"></td>
                                                <td class="px-3 py-2 text-right font-semibold" x-text="rp(alat.subtotal)"></td>
                                            </tr>
                                        </template>
                                    </tbody>
                                </table>
                            </div>
                        </div>

                        <!-- Ringkasan biaya -->
                        <div class="bg-zinc-50 rounded-xl p-4 space-y-1.5">
                            <div class="flex justify-between"><span class="text-zinc-500">Total Biaya</span><span class="font-black text-zinc-900" x-text="rp(detail.total)"></span></div>
                            <div class="flex justify-between"><span class="text-zinc-500">DP (50%)</span><span class="font-semibold" x-text="rp(detail.dp)"></span></div>
                            <div class="flex justify-between"><span class="text-zinc-500">Sisa Pelunasan</span><span class="font-semibold" x-text="rp(detail.total - detail.dp)"></span></div>
                        </div>

                        <!-- Riwayat pembayaran -->
                        <div>
                            <p class="text-[10px] font-bold text-zinc-400 uppercase mb-2">Riwayat Pembayaran</p>
                            <p class="text-zinc-400" x-show="detail.pembayaran.length === 0">Belum ada transaksi pembayaran.</p>
                            <div class="space-y-2">
                                <template x-for="(bayar, i) in detail.pembayaran" :key="i">
                                    <div class="flex items-center justify-between border border-zinc-200 rounded-xl px-4 py-2.5">
                                        <div>
                                            <p class="font-bold text-zinc-800" x-text="labelJenis(bayar.jenis) + ' · ' + bayar.metode"></p>
                                            <p class="text-[11px] text-zinc-400" x-text="bayar.waktu ? 'Dibayar ' + bayar.waktu : 'Belum dibayar'"></p>
                                        </div>
                                        <div class="text-right">
                                            <p class="font-bold text-zinc-900" x-text="rp(bayar.jumlah)"></p>
                                            <p class="text-[11px] font-semibold"
                                               :class="bayar.status === 'berhasil' ? 'text-emerald-600' : (bayar.status === 'gagal' ? 'text-red-500' : 'text-amber-600')"
                                               x-text="labelBayar(bayar.status)"></p>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
        </div>
    </div>

    @include('partials.status-muat')

</body>
</html>
