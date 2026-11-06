{{--
    Kartu tabel laporan (toolbar + tabel sesuai tab + pagination).

    Dipakai saat halaman dirender penuh dan dikirim ulang lewat AJAX oleh
    halamanLaporan.muatTabel() (GET /admin/laporan/tabel), sehingga berganti
    tab, pencarian, periode, dan pagination tidak memuat ulang halaman.

    Variabel: $data (paginator tab aktif) dan $filters
    (search, tipe, dari, sampai, per_page).
--}}
@php
    $tipeAktif = $filters['tipe'];
    $adaPencarian = $filters['search'] !== '';

    $labelTab = [
        'pendapatan' => 'Ringkasan Pendapatan',
        'penyewaan' => 'Histori Penyewaan',
        'denda' => 'Laporan Denda',
    ];

    $placeholder = [
        'pendapatan' => 'Cari kode sewa / ID transaksi...',
        'penyewaan' => 'Cari kode sewa / pelanggan...',
        'denda' => 'Cari ID / pemesanan / jenis...',
    ][$tipeAktif];

    $kelasStatusSewa = [
        'menunggu_pembayaran' => 'bg-amber-50 text-amber-700 border-amber-200/60',
        'dikonfirmasi' => 'bg-indigo-50 text-indigo-700 border-indigo-200/60',
        'berlangsung' => 'bg-blue-50 text-blue-700 border-blue-200/60',
        'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200/60',
        'dibatalkan' => 'bg-red-50 text-red-700 border-red-200/60',
    ];

    $namaBulan = static fn ($tanggal): string => \Illuminate\Support\Carbon::parse($tanggal)->locale('id')->translatedFormat('d F Y');
    $rupiah = static fn ($angka): string => 'Rp '.number_format((float) $angka, 0, ',', '.');
@endphp
<div class="bg-white rounded-3xl border border-zinc-200/80 shadow-sm overflow-hidden">

    <!-- Filter Periode & Kategori Laporan -->
    <div class="p-6 border-b border-zinc-100 flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div class="flex items-center gap-2 overflow-x-auto pb-2 lg:pb-0">
            @foreach ($labelTab as $kunci => $label)
                <a href="{{ route('admin.laporan.index', array_filter(['tipe' => $kunci === 'pendapatan' ? null : $kunci, 'dari' => $filters['dari'], 'sampai' => $filters['sampai']])) }}"
                   @click.prevent="pilihTipe('{{ $kunci }}')"
                   :class="filter.tipe === '{{ $kunci }}' ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200'"
                   class="px-4 py-2 rounded-xl text-xs font-bold transition whitespace-nowrap {{ $tipeAktif === $kunci ? 'bg-zinc-900 text-white' : 'bg-zinc-100 text-zinc-600 hover:bg-zinc-200' }}">
                    {{ $label }}
                </a>
            @endforeach
        </div>

        <!-- Filter Rentang Tanggal & Search Filter -->
        <div class="flex flex-wrap items-center gap-3">
            <div class="flex items-center gap-2">
                <input type="date" x-model="filter.dari" @change="ubahPeriode()" value="{{ $filters['dari'] }}"
                       class="bg-zinc-50 border border-zinc-200 text-xs rounded-xl px-3.5 py-2 font-medium text-zinc-700">
                <span class="text-xs text-zinc-400 font-bold">s/d</span>
                <input type="date" x-model="filter.sampai" @change="ubahPeriode()" value="{{ $filters['sampai'] }}"
                       class="bg-zinc-50 border border-zinc-200 text-xs rounded-xl px-3.5 py-2 font-medium text-zinc-700">
            </div>

            <div class="flex items-center gap-2 relative">
                <!-- Input Search Interaktif -->
                <div x-show="showSearchInput" x-transition class="relative">
                    <input x-ref="laporanSearchInput"
                           data-cari
                           type="text"
                           x-model="cari"
                           @input.debounce.350ms="muatTabel()"
                           @keydown.enter.prevent="muatTabel()"
                           placeholder="{{ $placeholder }}"
                           class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-44 sm:w-60">
                    <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Tombol Kaca Pembesar (instan, tanpa database) -->
                <button type="button"
                        @click="showSearchInput = !showSearchInput; if(showSearchInput) { $nextTick(() => $refs.laporanSearchInput?.focus()) }"
                        class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition"
                        title="Cari di laporan">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>

                <!-- Tombol Filter Interaktif -->
                <div class="relative">
                    <button type="button"
                            @click="showFilterDropdown = !showFilterDropdown"
                            class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition"
                            title="Filter Jenis Laporan">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                    </button>

                    <!-- Dropdown Menu Filter -->
                    <div x-show="showFilterDropdown"
                         @click.away="showFilterDropdown = false"
                         x-transition
                         class="absolute right-0 mt-2 w-52 bg-white border border-zinc-200 rounded-xl shadow-lg py-1.5 z-50 text-xs font-medium">
                        <div class="px-3 py-1.5 font-bold text-zinc-400 uppercase text-[10px] tracking-wider border-b border-zinc-100">Pilih Jenis Laporan</div>
                        @foreach ($labelTab as $kunci => $label)
                            <a href="{{ route('admin.laporan.index', array_filter(['tipe' => $kunci === 'pendapatan' ? null : $kunci, 'dari' => $filters['dari'], 'sampai' => $filters['sampai']])) }}"
                               @click.prevent="pilihTipe('{{ $kunci }}'); showFilterDropdown = false"
                               class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                                <span>{{ $label }}</span>
                                <span x-show="filter.tipe === '{{ $kunci }}'" class="text-portal-orange font-bold">✓</span>
                            </a>
                        @endforeach
                    </div>
                </div>

                <template x-if="adaFilter">
                    <button type="button" @click="resetFilter()" class="text-xs font-semibold text-portal-orange hover:underline px-1" title="Reset filter">
                        Reset
                    </button>
                </template>
            </div>
        </div>
    </div>

    @if ($tipeAktif === 'pendapatan')
        <!-- 1. TABEL RINGKASAN PENDAPATAN -->
        <div class="overflow-x-auto">
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
                    @forelse ($data as $baris)
                        <tr class="hover:bg-zinc-50/60 transition">
                            <td class="py-5 px-6 font-bold text-zinc-900">{{ $namaBulan($baris->tanggal) }}</td>
                            <td class="py-5 px-4">{{ number_format((int) $baris->jumlah_transaksi, 0, ',', '.') }} Transaksi</td>
                            <td class="py-5 px-4 text-zinc-700">{{ $rupiah($baris->pendapatan_sewa) }}</td>
                            <td class="py-5 px-4 {{ (float) $baris->pendapatan_denda > 0 ? 'text-amber-600 font-bold' : 'text-zinc-400' }}">{{ $rupiah($baris->pendapatan_denda) }}</td>
                            <td class="py-5 px-6 text-right font-black text-zinc-900 text-sm">{{ $rupiah($baris->total) }}</td>
                        </tr>
                    @empty
                        @include('admin.laporan._kosong', ['kolom' => 5])
                    @endforelse
                </tbody>
            </table>
        </div>
    @elseif ($tipeAktif === 'penyewaan')
        <!-- 2. TABEL HISTORI PENYEWAAN -->
        <div class="overflow-x-auto">
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
                    @forelse ($data as $sewa)
                        @php
                            $mulai = $sewa->tanggal_mulai->copy()->locale('id');
                            $selesai = $sewa->tanggal_selesai->copy()->locale('id');
                        @endphp
                        <tr class="hover:bg-zinc-50/60 transition">
                            <td class="py-5 px-6 font-mono font-bold text-zinc-900">{{ $sewa->kode_pemesanan }}</td>
                            <td class="py-5 px-4 font-bold text-zinc-800">{{ $sewa->nama_pelanggan }}</td>
                            <td class="py-5 px-4 text-zinc-600">{{ $mulai->translatedFormat('d M Y') }} - {{ $selesai->translatedFormat('d M Y') }} ({{ $sewa->lamaSewa() }} Hari)</td>
                            <td class="py-5 px-4 font-bold text-zinc-900">{{ $rupiah($sewa->total_biaya) }}</td>
                            <td class="py-5 px-6 text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full border text-[10px] font-extrabold {{ $kelasStatusSewa[$sewa->status] ?? 'bg-zinc-100 text-zinc-700 border-zinc-200' }}">
                                    {{ \App\Http\Controllers\Admin\PemesananController::LABEL_STATUS[$sewa->status] ?? $sewa->status }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        @include('admin.laporan._kosong', ['kolom' => 5])
                    @endforelse
                </tbody>
            </table>
        </div>
    @else
        <!-- 3. TABEL LAPORAN DENDA -->
        <div class="overflow-x-auto">
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
                    @forelse ($data as $denda)
                        @php
                            $kelasJenis = match ($denda->jenis_denda) {
                                'keterlambatan' => 'bg-amber-50 text-amber-700',
                                'kerusakan_ringan', 'kerusakan_berat' => 'bg-rose-50 text-rose-700',
                                'kehilangan' => 'bg-purple-50 text-purple-700',
                                default => 'bg-zinc-100 text-zinc-700',
                            };
                        @endphp
                        <tr class="hover:bg-zinc-50/60 transition">
                            <td class="py-5 px-6 font-mono font-bold text-zinc-900">#DND-{{ str_pad((string) $denda->id, 2, '0', STR_PAD_LEFT) }}</td>
                            <td class="py-5 px-4">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold capitalize {{ $kelasJenis }}">
                                    {{ str_replace('_', ' ', $denda->jenis_denda) }}
                                </span>
                            </td>
                            <td class="py-5 px-4 text-zinc-600">Pemesanan #{{ $denda->pemesanan_id }} · Pengembalian #{{ $denda->pengembalian_id }}</td>
                            <td class="py-5 px-4 font-bold text-zinc-900">{{ $rupiah($denda->jumlah_denda) }}</td>
                            <td class="py-5 px-6 text-right">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-extrabold capitalize {{ $denda->status === 'lunas' ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700' }}">
                                    {{ $denda->status === 'lunas' ? 'Lunas' : 'Belum Dibayar' }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        @include('admin.laporan._kosong', ['kolom' => 5])
                    @endforelse
                </tbody>
            </table>
        </div>
    @endif

    <!-- Footer Tabel -->
    <div class="p-6 border-t border-zinc-100 flex flex-col md:flex-row md:items-center justify-between gap-3 text-xs text-zinc-500 font-medium">
        <span>
            Menampilkan rekapitulasi data periode <strong>{{ \App\Http\Controllers\Admin\LaporanController::labelPeriode($filters) }}</strong>
            @if ($data->total() > 0)
                · {{ $data->firstItem() }}–{{ $data->lastItem() }} dari {{ number_format($data->total(), 0, ',', '.') }} data
            @endif
        </span>
        <div class="flex flex-wrap items-center gap-3">
            <label for="laporan_per_page" class="text-xs text-gray-500">Baris per halaman:</label>
            <select id="laporan_per_page" x-model="perPage" @change="muatTabel()"
                    class="bg-[#F4EFEA]/60 border border-transparent rounded-lg px-2 py-1 text-xs text-[#1A2024] font-semibold focus:ring-2 focus:ring-[#15AEEA]">
                @foreach ([10, 25, 50] as $jumlahBaris)
                    <option value="{{ $jumlahBaris }}" @selected($filters['per_page'] === $jumlahBaris)>{{ $jumlahBaris }}</option>
                @endforeach
            </select>
            @if ($data->hasPages())
                <div data-paginasi>{{ $data->links() }}</div>
            @endif
        </div>
    </div>
</div>
