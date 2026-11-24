<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OutGear Station - Pembayaran</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if ($midtransAktif)
        <script src="{{ $snapUrl }}" data-client-key="{{ $clientKey }}"></script>
    @endif
</head>
<body class="admin-theme antialiased min-h-screen"
      x-data="halamanPembayaran({
          tab: {{ Js::from(request('tab', 'dp')) }},
          dp: {{ Js::from($dataAwal['dp']) }},
          pelunasan: {{ Js::from($dataAwal['pelunasan']) }},
          dataUrl: {{ Js::from($dataUrl) }},
          sinkronUrl: {{ Js::from(route('admin.pembayaran.sinkron', ['pembayaran' => '__ID__'])) }}
      })"
      x-effect="window.dispatchEvent(new CustomEvent('badge-verifikasi', { detail: dpMenunggu }))">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')

        <main class="flex-grow flex flex-col min-w-0">

            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-8 py-3.5 z-30 border-b border-zinc-200/60 flex items-center justify-between">
                <div></div>
                <div class="flex items-center gap-3">
                    @include('partials.tombol-muat-ulang')
                    @include('partials.profil-topbar')
                </div>
            </header>

            <div class="p-8 space-y-6">

                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Pembayaran</h2>
                    <p class="text-xs text-zinc-500 mt-1">
                        Pantau DP dari customer dan catat pelunasan (sisa sewa + denda) saat barang dikembalikan. Semua pembayaran bisa lewat Midtrans atau tunai.
                    </p>
                </div>

                @unless ($midtransAktif)
                    <div class="bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium rounded-xl px-4 py-3">
                        Midtrans belum dikonfigurasi di <span class="font-mono">.env</span>, jadi hanya pembayaran tunai yang bisa dipakai.
                    </div>
                @endunless

                <!-- Kartu ringkasan -->
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">DP Menunggu</span>
                        <div class="text-3xl font-black font-heading text-amber-600 my-2" x-text="dpMenunggu"></div>
                        <div class="text-[11px] text-amber-600 font-bold pt-2 border-t border-zinc-100">Perlu ditinjau</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">DP Berhasil</span>
                        <div class="text-3xl font-black font-heading text-emerald-600 my-2" x-text="dpBerhasil"></div>
                        <div class="text-[11px] text-emerald-600 font-bold pt-2 border-t border-zinc-100">Sudah masuk</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Pelunasan Belum Dibayar</span>
                        <div class="text-3xl font-black font-heading text-rose-600 my-2" x-text="pelunasanPerluBayar"></div>
                        <div class="text-[11px] text-rose-600 font-bold pt-2 border-t border-zinc-100">Barang sudah dikembalikan</div>
                    </div>
                    <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Tagihan Pelunasan</span>
                        <div class="text-2xl font-black font-heading text-zinc-900 my-2.5" x-text="formatRupiah(totalTagihan)"></div>
                        <div class="text-[11px] text-zinc-500 font-bold pt-2 border-t border-zinc-100">Sisa sewa + denda</div>
                    </div>
                </div>

                <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">

                    <!-- Tab + pencarian + filter -->
                    <div class="p-5 border-b border-zinc-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                        <div class="flex flex-wrap items-center gap-3">
                            <div class="inline-flex bg-zinc-100 rounded-xl p-1 text-xs font-bold">
                                <button type="button" @click="pindahTab('dp')"
                                        :class="tab === 'dp' ? 'bg-white text-zinc-900 shadow-sm' : 'text-zinc-500 hover:text-zinc-800'"
                                        class="px-4 py-2 rounded-lg transition">Pembayaran DP</button>
                                <button type="button" @click="pindahTab('pelunasan')"
                                        :class="tab === 'pelunasan' ? 'bg-white text-zinc-900 shadow-sm' : 'text-zinc-500 hover:text-zinc-800'"
                                        class="px-4 py-2 rounded-lg transition">Pelunasan &amp; Denda</button>
                            </div>
                            <span class="text-xs font-medium text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">
                                Filter:
                                <span class="font-bold text-zinc-800"
                                      x-text="filterStatus === 'Semua' ? 'Semua Status' : (filterStatus === 'pending' ? 'Pending' : (filterStatus === 'berhasil' ? 'Berhasil' : (filterStatus === 'gagal' ? 'Gagal' : (filterStatus === 'belum_bayar' ? 'Belum Dibayar' : (filterStatus === 'menunggu' ? 'Menunggu Midtrans' : 'Lunas')))))"></span>
                            </span>
                        </div>

                        <div class="flex items-center gap-2 relative">
                            <!-- Input Search Interaktif -->
                            <div x-show="showSearchInput" x-transition class="relative">
                                <input x-ref="pembayaranSearchInput"
                                       type="text"
                                       x-model="search"
                                       placeholder="Cari kode / pelanggan..."
                                       class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">
                                <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>

                            <!-- Tombol Kaca Pembesar -->
                            <button type="button"
                                    @click="showSearchInput = !showSearchInput; if(showSearchInput) { $nextTick(() => $refs.pembayaranSearchInput?.focus()) }"
                                    class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition"
                                    title="Cari pembayaran">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </button>

                            <!-- Tombol Filter Interaktif -->
                            <div class="relative">
                                <button type="button"
                                        @click="showFilterDropdown = !showFilterDropdown"
                                        class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition"
                                        title="Filter Status Pembayaran">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                                </button>

                                <div x-show="showFilterDropdown"
                                     @click.away="showFilterDropdown = false"
                                     x-transition
                                     class="absolute right-0 mt-2 w-48 bg-white border border-zinc-200 rounded-xl shadow-lg py-1.5 z-50 text-xs font-medium">
                                    <div class="px-3 py-1.5 font-bold text-zinc-400 uppercase text-[10px] tracking-wider border-b border-zinc-100">Status Pembayaran</div>
                                    <button type="button" @click="filterStatus = 'Semua'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                                        <span>Semua Status</span>
                                        <span x-show="filterStatus === 'Semua'" class="text-portal-orange font-bold">✓</span>
                                    </button>
                                    <template x-if="tab === 'dp'">
                                        <div>
                                            <button type="button" @click="filterStatus = 'pending'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-amber-700 flex items-center justify-between">
                                                <span>Pending</span>
                                                <span x-show="filterStatus === 'pending'" class="text-portal-orange font-bold">✓</span>
                                            </button>
                                            <button type="button" @click="filterStatus = 'berhasil'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-emerald-700 flex items-center justify-between">
                                                <span>Berhasil</span>
                                                <span x-show="filterStatus === 'berhasil'" class="text-portal-orange font-bold">✓</span>
                                            </button>
                                            <button type="button" @click="filterStatus = 'gagal'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-rose-700 flex items-center justify-between">
                                                <span>Gagal</span>
                                                <span x-show="filterStatus === 'gagal'" class="text-portal-orange font-bold">✓</span>
                                            </button>
                                        </div>
                                    </template>
                                    <template x-if="tab === 'pelunasan'">
                                        <div>
                                            <button type="button" @click="filterStatus = 'belum_bayar'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-rose-700 flex items-center justify-between">
                                                <span>Belum Dibayar</span>
                                                <span x-show="filterStatus === 'belum_bayar'" class="text-portal-orange font-bold">✓</span>
                                            </button>
                                            <button type="button" @click="filterStatus = 'menunggu'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-amber-700 flex items-center justify-between">
                                                <span>Menunggu Midtrans</span>
                                                <span x-show="filterStatus === 'menunggu'" class="text-portal-orange font-bold">✓</span>
                                            </button>
                                            <button type="button" @click="filterStatus = 'lunas'; showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-emerald-700 flex items-center justify-between">
                                                <span>Lunas</span>
                                                <span x-show="filterStatus === 'lunas'" class="text-portal-orange font-bold">✓</span>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <button type="button" x-show="adaFilter" @click="resetFilter()" class="text-xs font-semibold text-portal-orange hover:underline" title="Reset pencarian & filter">Reset</button>
                        </div>
                    </div>

                    <!-- TAB PELUNASAN -->
                    <div x-show="tab === 'pelunasan'" class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-zinc-50/80 text-zinc-400 font-bold border-b border-zinc-200 uppercase tracking-wider text-[10px]">
                                    <th class="p-4">Pemesanan</th>
                                    <th class="p-4">Total Sewa</th>
                                    <th class="p-4">DP</th>
                                    <th class="p-4">Sisa Sewa</th>
                                    <th class="p-4">Denda</th>
                                    <th class="p-4">Harus Dibayar</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                <template x-for="item in pelunasanTersaring" :key="item.id">
                                    <tr class="hover:bg-zinc-50/80 transition align-top">
                                        <td class="p-4">
                                            <div class="font-bold text-zinc-900" x-text="item.pelanggan_nama"></div>
                                            <div class="text-[11px] text-portal-orange font-mono font-semibold mt-0.5" x-text="item.kode_pemesanan"></div>
                                            <div class="text-[10px] text-zinc-400 mt-0.5"
                                                 x-text="item.dikembalikan ? 'Barang sudah dikembalikan' + (item.hari_terlambat > 0 ? ' (terlambat ' + item.hari_terlambat + ' hari)' : '') : 'Belum dikembalikan'"></div>
                                        </td>
                                        <td class="p-4" x-text="formatRupiah(item.total_biaya)"></td>
                                        <td class="p-4" x-text="formatRupiah(item.dp)"></td>
                                        <td class="p-4 font-semibold" x-text="formatRupiah(item.sisa_sewa)"></td>
                                        <td class="p-4">
                                            <div class="font-semibold" :class="item.denda > 0 ? 'text-rose-600' : 'text-zinc-400'" x-text="formatRupiah(item.denda)"></div>
                                            <template x-for="(d, i) in item.rincian_denda" :key="i">
                                                <div class="text-[10px] text-zinc-500 mt-0.5" x-text="d.jenis + ': ' + formatRupiah(d.jumlah)"></div>
                                            </template>
                                        </td>
                                        <td class="p-4">
                                            <span class="font-extrabold text-sm text-zinc-900" x-text="formatRupiah(item.total_tagihan)"></span>
                                        </td>
                                        <td class="p-4">
                                            <template x-if="item.status === 'lunas'">
                                                <div>
                                                    <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-50 text-emerald-700 border border-emerald-200">Lunas</span>
                                                    <div class="text-[10px] text-zinc-400 mt-1" x-show="item.metode" x-text="item.metode + ' · ' + (item.waktu_bayar ?? '')"></div>
                                                </div>
                                            </template>
                                            <template x-if="item.status === 'menunggu'">
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-amber-50 text-amber-700 border border-amber-200">Menunggu Midtrans</span>
                                            </template>
                                            <template x-if="item.status === 'belum_bayar'">
                                                <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold bg-rose-50 text-rose-700 border border-rose-200">Belum dibayar</span>
                                            </template>
                                        </td>
                                        <td class="p-4 text-center">
                                            <template x-if="item.status === 'menunggu'">
                                                <div class="inline-flex gap-1.5">
                                                    <button @click="lanjutkanMidtrans(item)" class="bg-portal-orange hover:opacity-90 text-white font-bold px-2.5 py-1 rounded-lg text-xs shadow-sm transition">Buka Midtrans</button>
                                                    <button @click="cekStatus(item.pembayaran_menunggu_id)" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-bold px-2.5 py-1 rounded-lg text-xs transition">Cek Status</button>
                                                    <button @click="bukaBayar(item)" class="bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-bold px-2.5 py-1 rounded-lg text-xs transition">Ganti Cara</button>
                                                </div>
                                            </template>
                                            <template x-if="item.status === 'belum_bayar' && item.dikembalikan">
                                                <button @click="bukaBayar(item)" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-3 py-1.5 rounded-lg text-xs shadow-sm transition">Bayar Pelunasan</button>
                                            </template>
                                            <template x-if="item.status === 'belum_bayar' && !item.dikembalikan">
                                                <span class="text-[11px] text-zinc-400">Menunggu pengembalian</span>
                                            </template>
                                            <template x-if="item.status === 'lunas'">
                                                <span class="text-zinc-300">—</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="pelunasanTersaring.length === 0">
                                    <tr><td colspan="8" class="p-10 text-center">
                                        <p class="text-3xl">💳</p>
                                        <p class="mt-2 text-sm font-semibold text-zinc-800" x-text="adaFilter ? 'Tidak ada data yang cocok dengan filter.' : 'Belum ada pemesanan yang berlangsung atau selesai.'"></p>
                                    </td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- TAB DP -->
                    <div x-show="tab === 'dp'" class="overflow-x-auto">
                        <table class="w-full text-left text-xs">
                            <thead>
                                <tr class="bg-zinc-50/80 text-zinc-400 font-bold border-b border-zinc-200 uppercase tracking-wider text-[10px]">
                                    <th class="p-4">Pemesanan</th>
                                    <th class="p-4">Transaction ID</th>
                                    <th class="p-4">Metode</th>
                                    <th class="p-4">Jumlah DP</th>
                                    <th class="p-4">Status</th>
                                    <th class="p-4">Waktu Bayar</th>
                                    <th class="p-4 text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                                <template x-for="item in dpTersaring" :key="item.id">
                                    <tr class="hover:bg-zinc-50/80 transition">
                                        <td class="p-4">
                                            <div class="font-bold text-zinc-900" x-text="item.pelanggan_nama"></div>
                                            <div class="text-[11px] text-portal-orange font-mono font-semibold mt-0.5" x-text="item.kode_pemesanan"></div>
                                        </td>
                                        <td class="p-4"><span class="font-mono text-[11px] bg-zinc-100 px-2 py-1 rounded border border-zinc-200/60" x-text="item.transaction_id"></span></td>
                                        <td class="p-4"><span class="px-2.5 py-1 rounded-md bg-purple-50 text-purple-700 font-bold text-[10px] uppercase border border-purple-200" x-text="item.metode_label"></span></td>
                                        <td class="p-4 font-bold text-zinc-900" x-text="formatRupiah(item.jumlah)"></td>
                                        <td class="p-4">
                                            <span class="inline-flex px-2.5 py-1 rounded-full text-[11px] font-bold border"
                                                  :class="{
                                                      'bg-amber-50 text-amber-700 border-amber-200': item.status_transaksi === 'pending',
                                                      'bg-emerald-50 text-emerald-700 border-emerald-200': item.status_transaksi === 'berhasil',
                                                      'bg-rose-50 text-rose-700 border-rose-200': item.status_transaksi === 'gagal'
                                                  }"
                                                  x-text="item.status_transaksi === 'pending' ? 'Pending' : (item.status_transaksi === 'berhasil' ? 'Berhasil' : 'Gagal')"></span>
                                        </td>
                                        <td class="p-4 text-zinc-500 text-[11px]" x-text="item.waktu_bayar"></td>
                                        <td class="p-4 text-center">
                                            <template x-if="item.status_transaksi === 'pending'">
                                                <div class="inline-flex gap-1.5">
                                                    <button @click="aksiDp(item, 'url_verifikasi', 'tunai')" class="bg-emerald-600 hover:bg-emerald-700 text-white font-bold px-2.5 py-1 rounded-lg text-xs shadow-sm transition">Terima Tunai</button>
                                                    <button @click="aksiDp(item, 'url_verifikasi')" class="bg-portal-orange hover:opacity-90 text-white font-bold px-2.5 py-1 rounded-lg text-xs shadow-sm transition">Cek Midtrans</button>
                                                    <button @click="aksiDp(item, 'url_tolak')" class="bg-rose-50 hover:bg-rose-100 text-rose-700 border border-rose-200 font-bold px-2 py-1 rounded-lg text-xs transition">Tolak</button>
                                                </div>
                                            </template>
                                            <template x-if="item.status_transaksi !== 'pending'">
                                                <span class="text-zinc-300">—</span>
                                            </template>
                                        </td>
                                    </tr>
                                </template>
                                <template x-if="dpTersaring.length === 0">
                                    <tr><td colspan="7" class="p-10 text-center">
                                        <p class="text-3xl">💳</p>
                                        <p class="mt-2 text-sm font-semibold text-zinc-800" x-text="adaFilter ? 'Tidak ada data yang cocok dengan filter.' : 'Belum ada pembayaran DP.'"></p>
                                    </td></tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- MODAL BAYAR PELUNASAN -->
    <div x-cloak x-show="modalBayar" x-transition.opacity
         class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-center justify-center p-4" style="display: none;">
        <div @click.away="modalBayar = false" class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden border border-zinc-200">
            <div class="bg-zinc-900 text-white p-5 flex items-center justify-between">
                <div>
                    <span class="text-[10px] font-bold text-portal-orange tracking-widest uppercase">Pelunasan</span>
                    <h3 class="font-bold text-base mt-0.5" x-text="itemBayar ? itemBayar.kode_pemesanan + ' · ' + itemBayar.pelanggan_nama : ''"></h3>
                </div>
                <button @click="modalBayar = false" class="text-zinc-400 hover:text-white p-1 rounded-lg transition">✕</button>
            </div>

            <template x-if="itemBayar">
                <div class="p-6 space-y-4 text-xs">
                    <div class="space-y-2 bg-zinc-50 p-4 rounded-xl border border-zinc-200/80">
                        <div class="flex justify-between"><span class="text-zinc-500">Total sewa</span><span class="font-semibold" x-text="formatRupiah(itemBayar.total_biaya)"></span></div>
                        <div class="flex justify-between"><span class="text-zinc-500">DP sudah dibayar</span><span class="font-semibold" x-text="'- ' + formatRupiah(itemBayar.dp)"></span></div>
                        <div class="flex justify-between border-t border-zinc-200 pt-2"><span class="text-zinc-500">Sisa sewa</span><span class="font-semibold" x-text="formatRupiah(itemBayar.sisa_sewa)"></span></div>
                        <div class="flex justify-between"><span class="text-zinc-500">Denda</span><span class="font-semibold text-rose-600" x-text="formatRupiah(itemBayar.denda)"></span></div>
                        <template x-for="(d, i) in itemBayar.rincian_denda" :key="i">
                            <div class="flex justify-between pl-3 text-[11px] text-zinc-500"><span x-text="d.jenis"></span><span x-text="formatRupiah(d.jumlah)"></span></div>
                        </template>
                        <div class="flex justify-between border-t border-zinc-300 pt-2 text-sm">
                            <span class="font-bold text-zinc-900">Harus dibayar</span>
                            <span class="font-extrabold text-zinc-900" x-text="formatRupiah(itemBayar.total_tagihan)"></span>
                        </div>
                    </div>

                    <div>
                        <p class="font-bold text-zinc-700 mb-2">Pilih cara bayar</p>
                        <div class="grid grid-cols-2 gap-2">
                            <label class="cursor-pointer border rounded-xl p-3 flex items-center gap-2 transition"
                                   :class="cara === 'tunai' ? 'border-portal-orange bg-orange-50' : 'border-zinc-200'">
                                <input type="radio" value="tunai" x-model="cara" class="accent-orange-500">
                                <span class="font-bold">Tunai (Cash)</span>
                            </label>
                            @if ($midtransAktif)
                                <label class="cursor-pointer border rounded-xl p-3 flex items-center gap-2 transition"
                                       :class="cara === 'midtrans' ? 'border-portal-orange bg-orange-50' : 'border-zinc-200'">
                                    <input type="radio" value="midtrans" x-model="cara" class="accent-orange-500">
                                    <span class="font-bold">Midtrans</span>
                                </label>
                            @else
                                <div class="border border-zinc-200 rounded-xl p-3 text-zinc-400 opacity-60">Midtrans (belum aktif)</div>
                            @endif
                        </div>
                        <p class="text-[11px] text-zinc-400 mt-2" x-text="cara === 'tunai' ? 'Pembayaran langsung dicatat lunas dan denda ikut dilunasi.' : 'Pop-up Midtrans (QRIS, VA, e-wallet) akan terbuka. Status lunas otomatis setelah dibayar.'"></p>
                    </div>

                    <div class="flex gap-2 pt-1">
                        <button @click="modalBayar = false" class="w-1/3 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 font-bold py-2 rounded-xl transition">Batal</button>
                        <button @click="prosesBayar()" class="w-2/3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold py-2 rounded-xl shadow transition"
                                x-text="cara === 'tunai' ? 'Catat Lunas (Tunai)' : 'Bayar via Midtrans'"></button>
                    </div>
                </div>
            </template>
        </div>
    </div>

    @include('partials.status-muat')
</body>
</html>
