<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Laporan</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F6F2] text-slate-800 antialiased font-sans" x-data="{ tipeLaporan: 'pendapatan' }">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas (Bersih) -->
            <header class="sticky top-0 bg-[#F8F6F2]/90 backdrop-blur-md px-10 py-4 z-30 border-b border-zinc-200/60 flex items-center justify-between">
                <div></div> <!-- Spacer kosong -->

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-4 py-2 rounded-full shadow-sm text-xs font-bold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Basecamp Malang • Online
                    </div>
                    <button class="p-2.5 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm transition" title="Refresh">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
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
                        <button class="bg-white hover:bg-zinc-50 border border-zinc-200 text-zinc-700 text-xs font-bold py-2.5 px-4 rounded-xl transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            Export Excel
                        </button>
                        <button class="bg-zinc-900 hover:bg-portal-orange text-white text-xs font-bold py-2.5 px-4 rounded-xl transition shadow-sm flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                            Cetak PDF
                        </button>
                    </div>
                </div>

                <!-- 4 KARTU RINGKASAN LAPORAN (ANGKA BERWARNA & TIDAK HITAM) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <!-- Kartu 1 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Pendapatan</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-emerald-600">Rp <span class="font-heading">14.850.000</span></div>
                        </div>
                        <div class="text-[11px] text-emerald-600 font-bold pt-2 border-t border-zinc-100">↑ +12% dari bulan lalu</div>
                    </div>

                    <!-- Kartu 2 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Transaksi</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-blue-600"><span class="font-heading">124</span> <span class="text-xs font-medium font-sans text-zinc-400">sewa</span></div>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-medium pt-2 border-t border-zinc-100">Bulan Oktober 2026</div>
                    </div>

                    <!-- Kartu 3 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Denda Terekap</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-amber-600">Rp <span class="font-heading">1.310.000</span></div>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-medium pt-2 border-t border-zinc-100">Keterlambatan & Kerusakan</div>
                    </div>

                    <!-- Kartu 4 -->
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Utilitas Alat</span>
                        <div class="my-2">
                            <div class="text-3xl font-black text-teal-600"><span class="font-heading">82</span>%</div>
                        </div>
                        <div class="text-[11px] text-zinc-400 font-medium pt-2 border-t border-zinc-100">Tenda Camping Paling Sering Sewa</div>
                    </div>
                </div>

                <!-- TABEL DATA LAPORAN -->
                <div class="bg-white rounded-3xl border border-zinc-200/80 shadow-sm overflow-hidden">
                    
                    <!-- Filter Periode & Kategori Laporan (TABEL TAB DIBERSIHKAN DARI TYPO) -->
                    <div class="p-6 border-b border-zinc-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                        <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0">
                            <button @click="tipeLaporan = 'pendapatan'" :class="tipeLaporan === 'pendapatan' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition">
                                Ringkasan Pendapatan
                            </button>
                            <button @click="tipeLaporan = 'penyewaan'" :class="tipeLaporan === 'penyewaan' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition">
                                Histori Penyewaan
                            </button>
                            <button @click="tipeLaporan = 'denda'" :class="tipeLaporan === 'denda' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200'" class="px-4 py-2 rounded-xl text-xs font-bold transition">
                                Laporan Denda
                            </button>
                        </div>

                        <!-- Filter Rentang Tanggal -->
                        <div class="flex items-center gap-2">
                            <input type="date" value="2026-10-01" class="bg-zinc-50 border border-zinc-200 text-xs rounded-xl px-3.5 py-2 font-medium text-zinc-700">
                            <span class="text-xs text-zinc-400 font-bold">s/d</span>
                            <input type="date" value="2026-10-31" class="bg-zinc-50 border border-zinc-200 text-xs rounded-xl px-3.5 py-2 font-medium text-zinc-700">
                        </div>
                    </div>

                    <!-- 1. TABEL RINGKASAN PENDAPATAN -->
                    <div x-show="tipeLaporan === 'pendapatan'" class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-zinc-50/80 text-zinc-400 font-black uppercase text-[10px] tracking-wider border-b border-zinc-100">
                                    <th class="py-5 px-6">Tanggal / Periode</th>
                                    <th class="py-5 px-4">Jumlah Transaksi</th>
                                    <th class="py-5 px-4">Pendapatan Sewa</th>
                                    <th class="py-5 px-4">Pendapatan Denda</th>
                                    <th class="py-5 px-6 text-right">Total Pemasukan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                <tr class="hover:bg-zinc-50/60 transition">
                                    <td class="py-5 px-6 font-bold text-zinc-900">18 Oktober 2026</td>
                                    <td class="py-5 px-4">8 Transaksi</td>
                                    <td class="py-5 px-4 text-zinc-700">Rp 1.450.000</td>
                                    <td class="py-5 px-4 text-amber-600 font-bold">Rp 50.000</td>
                                    <td class="py-5 px-6 text-right font-black text-zinc-900 text-sm">Rp 1.500.000</td>
                                </tr>
                                <tr class="hover:bg-zinc-50/60 transition">
                                    <td class="py-5 px-6 font-bold text-zinc-900">17 Oktober 2026</td>
                                    <td class="py-5 px-4">12 Transaksi</td>
                                    <td class="py-5 px-4 text-zinc-700">Rp 2.300.000</td>
                                    <td class="py-5 px-4 text-amber-600 font-bold">Rp 120.000</td>
                                    <td class="py-5 px-6 text-right font-black text-zinc-900 text-sm">Rp 2.420.000</td>
                                </tr>
                                <tr class="hover:bg-zinc-50/60 transition">
                                    <td class="py-5 px-6 font-bold text-zinc-900">16 Oktober 2026</td>
                                    <td class="py-5 px-4">5 Transaksi</td>
                                    <td class="py-5 px-4 text-zinc-700">Rp 980.000</td>
                                    <td class="py-5 px-4 text-zinc-400">Rp 0</td>
                                    <td class="py-5 px-6 text-right font-black text-zinc-900 text-sm">Rp 980.000</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 2. TABEL HISTORI PENYEWAAN -->
                    <div x-show="tipeLaporan === 'penyewaan'" class="overflow-x-auto" x-cloak>
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-zinc-50/80 text-zinc-400 font-black uppercase text-[10px] tracking-wider border-b border-zinc-100">
                                    <th class="py-5 px-6">ID Kode Sewa</th>
                                    <th class="py-5 px-4">Nama Pelanggan</th>
                                    <th class="py-5 px-4">Durasi Sewa</th>
                                    <th class="py-5 px-4">Total Biaya</th>
                                    <th class="py-5 px-6 text-right">Status Sewa</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                <tr class="hover:bg-zinc-50/60 transition">
                                    <td class="py-5 px-6 font-mono font-bold text-zinc-900">#ORD-0894</td>
                                    <td class="py-5 px-4 font-bold text-zinc-800">Anisa Tri Utami</td>
                                    <td class="py-5 px-4 text-zinc-600">18 - 20 Okt 2026 (2 Hari)</td>
                                    <td class="py-5 px-4 font-bold text-zinc-900">Rp 420.000</td>
                                    <td class="py-5 px-6 text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700 border border-emerald-200/60">
                                            Selesai
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-zinc-50/60 transition">
                                    <td class="py-5 px-6 font-mono font-bold text-zinc-900">#ORD-0892</td>
                                    <td class="py-5 px-4 font-bold text-zinc-800">Fajar Hidayat</td>
                                    <td class="py-5 px-4 text-zinc-600">17 - 19 Okt 2026 (2 Hari)</td>
                                    <td class="py-5 px-4 font-bold text-zinc-900">Rp 250.000</td>
                                    <td class="py-5 px-6 text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-blue-50 text-blue-700 border border-blue-200/60">
                                            Sedang Disewa
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 3. TABEL LAPORAN DENDA -->
                    <div x-show="tipeLaporan === 'denda'" class="overflow-x-auto" x-cloak>
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-zinc-50/80 text-zinc-400 font-black uppercase text-[10px] tracking-wider border-b border-zinc-100">
                                    <th class="py-5 px-6">Ref Denda ID</th>
                                    <th class="py-5 px-4">Jenis Denda</th>
                                    <th class="py-5 px-4">Rincian Keterangan</th>
                                    <th class="py-5 px-4">Total Denda</th>
                                    <th class="py-5 px-6 text-right">Status Pelunasan</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                <tr class="hover:bg-zinc-50/60 transition">
                                    <td class="py-5 px-6 font-mono font-bold text-zinc-900">#DND-01</td>
                                    <td class="py-5 px-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-amber-50 text-amber-700">
                                            Keterlambatan
                                        </span>
                                    </td>
                                    <td class="py-5 px-4 text-zinc-600">Terlambat pengembalian 2 hari</td>
                                    <td class="py-5 px-4 font-bold text-zinc-900">Rp 50.000</td>
                                    <td class="py-5 px-6 text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700">
                                            Belum Dibayar
                                        </span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-zinc-50/60 transition">
                                    <td class="py-5 px-6 font-mono font-bold text-zinc-900">#DND-02</td>
                                    <td class="py-5 px-4">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-rose-50 text-rose-700">
                                            Kerusakan Ringan
                                        </span>
                                    </td>
                                    <td class="py-5 px-4 text-zinc-600">Ganti pasak tenda bengkok</td>
                                    <td class="py-5 px-4 font-bold text-zinc-900">Rp 50.000</td>
                                    <td class="py-5 px-6 text-right">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold bg-emerald-50 text-emerald-700">
                                            Lunas
                                        </span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Footer Tabel -->
                    <div class="p-6 border-t border-zinc-100 flex items-center justify-between text-xs text-zinc-500 font-medium">
                        <span>Menampilkan rekapitulasi data periode <strong>Oktober 2026</strong></span>
                        <div class="flex items-center gap-1.5">
                            <button class="px-4 py-2 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-50 transition font-semibold disabled:opacity-50" disabled>Sebelumnya</button>
                            <button class="px-4 py-2 rounded-xl bg-zinc-900 text-white font-bold">1</button>
                            <button class="px-4 py-2 rounded-xl border border-zinc-200 bg-white hover:bg-zinc-50 transition font-semibold">Berikutnya</button>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>