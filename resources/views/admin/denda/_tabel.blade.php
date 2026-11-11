{{--
    Kartu tabel denda (toolbar + tabel + pagination).

    Dipakai saat halaman dirender penuh dan dikirim ulang lewat AJAX oleh
    halamanDenda.muatTabel() (GET /admin/denda/tabel), sehingga pencarian,
    filter, dan pagination tidak memuat ulang seluruh halaman.

    Variabel: $denda (paginator) dan $filters (search, status, per_page).
--}}
<div class="bg-white rounded-3xl border border-zinc-200/80 shadow-sm overflow-hidden">

    <!-- Header Action Board (Kaca Pembesar & Icon Filter) -->
    <div class="p-5 border-b border-zinc-100 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h3 class="font-bold text-zinc-900 text-base">Daftar Tagihan Denda</h3>
            <span class="text-xs font-medium text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">
                Filter: <span class="font-bold text-zinc-800" x-text="labelFilter">{{ match ($filters['status']) { 'belum_dibayar' => 'Belum Lunas', 'lunas' => 'Lunas', default => 'Semua Status' } }}</span>
            </span>
            <span class="text-xs font-medium text-zinc-400">
                ({{ number_format($denda->total(), 0, ',', '.') }} data)
            </span>
        </div>

        <div class="flex items-center gap-2 relative">
            <!-- Input Search Interaktif (tanpa JavaScript tetap terkirim sebagai form GET) -->
            <form method="GET" action="{{ route('admin.denda.index') }}" @submit.prevent="muatTabel()" class="flex items-center gap-2">
                @if ($filters['status'] !== 'semua')
                    <input type="hidden" name="status" value="{{ $filters['status'] }}">
                @endif
                <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">

                <div x-show="showSearchInput" x-transition class="relative">
                    <input x-ref="dendaSearchInput"
                           data-cari
                           type="text"
                           name="search"
                           x-model="cari"
                           @input.debounce.350ms="muatTabel()"
                           @keydown.enter.prevent="muatTabel()"
                           placeholder="Cari ID / pemesanan..."
                           class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">
                    <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </div>

                <!-- Tombol Kaca Pembesar -->
                <button type="button"
                        @click="showSearchInput = !showSearchInput; if(showSearchInput) { $nextTick(() => $refs.dendaSearchInput?.focus()) }"
                        class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition"
                        title="Cari tagihan denda">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </button>
            </form>

            <!-- Tombol Filter Interaktif -->
            <div class="relative">
                <button type="button"
                        @click="showFilterDropdown = !showFilterDropdown"
                        class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition"
                        title="Filter Status Pembayaran Denda">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </button>

                <!-- Dropdown Menu Filter -->
                <div x-show="showFilterDropdown"
                     @click.away="showFilterDropdown = false"
                     x-transition
                     class="absolute right-0 mt-2 w-48 bg-white border border-zinc-200 rounded-xl shadow-lg py-1.5 z-50 text-xs font-medium">
                    <div class="px-3 py-1.5 font-bold text-zinc-400 uppercase text-[10px] tracking-wider border-b border-zinc-100">Status Pembayaran</div>
                    <a href="{{ route('admin.denda.index', array_filter(['search' => $filters['search']])) }}"
                       @click.prevent="pilihStatus(''); showFilterDropdown = false"
                       class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                        <span>Semua Status</span>
                        <span x-show="filter.status === ''" class="text-portal-orange font-bold">✓</span>
                    </a>
                    <a href="{{ route('admin.denda.index', array_filter(['status' => 'belum_dibayar', 'search' => $filters['search']])) }}"
                       @click.prevent="pilihStatus('belum_dibayar'); showFilterDropdown = false"
                       class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-amber-700 flex items-center justify-between">
                        <span>Belum Lunas</span>
                        <span x-show="filter.status === 'belum_dibayar'" class="text-portal-orange font-bold">✓</span>
                    </a>
                    <a href="{{ route('admin.denda.index', array_filter(['status' => 'lunas', 'search' => $filters['search']])) }}"
                       @click.prevent="pilihStatus('lunas'); showFilterDropdown = false"
                       class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-emerald-700 flex items-center justify-between">
                        <span>Lunas</span>
                        <span x-show="filter.status === 'lunas'" class="text-portal-orange font-bold">✓</span>
                    </a>
                </div>
            </div>

            <template x-if="adaFilter">
                <button type="button" @click="resetFilter()" class="text-xs font-semibold text-portal-orange hover:underline px-1" title="Reset filter">
                    Reset
                </button>
            </template>
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
            {{-- Tanpa JavaScript form ini tetap terkirim lewat onchange biasa. --}}
            <form method="GET" action="{{ route('admin.denda.index') }}" class="flex items-center gap-2">
                @if ($filters['status'] !== 'semua')
                    <input type="hidden" name="status" value="{{ $filters['status'] }}">
                @endif
                <input type="hidden" name="search" value="{{ $filters['search'] }}">
                <label for="fine_per_page" class="text-xs text-gray-500">Baris per halaman:</label>
                <select id="fine_per_page" name="per_page" x-model="perPage" @change="muatTabel()"
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
