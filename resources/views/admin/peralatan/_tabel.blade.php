{{--
    Kartu tabel data peralatan (tabel + footer + pagination).

    Dipakai saat halaman dirender penuh dan dikirim ulang lewat AJAX oleh
    halamanPeralatan.muatTabel(), sehingga pencarian/filter/pagination tidak
    perlu memuat ulang seluruh halaman.
--}}
<div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">
    <!-- Header Action Board (Kaca Pembesar & Icon Filter seperti di Verifikasi) -->
    <div class="p-5 border-b border-zinc-100 flex items-center justify-between gap-4">
        <div class="flex items-center gap-3">
            <h3 class="font-bold text-zinc-900 text-base">Daftar Peralatan</h3>
            <span class="text-xs font-medium text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">
                Filter: <span class="font-bold text-zinc-800" x-text="(kondisi === 'semua' ? 'Semua Kondisi' : (kondisi === 'tersedia' ? 'Siap Sewa' : (kondisi === 'habis' ? 'Stok Habis' : 'Perlu Perbaikan'))) + (namaKategoriAktif ? ' • ' + namaKategoriAktif : '')"></span>
            </span>
            <span class="text-xs font-medium text-zinc-400">
                ({{ $daftarAlat->total() }} alat)
            </span>
        </div>

        <div class="flex items-center gap-2 relative">
            <!-- Input Search Interaktif -->
            <div x-show="showSearchInput" x-transition class="relative">
                <input x-ref="peralatanSearchInput" 
                       type="text" 
                       x-model="cari"
                       @input.debounce.350ms="muatTabel()"
                       @keydown.enter.prevent="muatTabel()"
                       placeholder="Cari nama alat / merk..." 
                       class="bg-zinc-100 border border-zinc-300 text-xs rounded-lg pl-8 pr-3 py-1.5 focus:outline-none focus:ring-1 focus:ring-portal-orange w-48 sm:w-64">
                <svg class="w-3.5 h-3.5 text-zinc-400 absolute left-2.5 top-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <!-- Tombol Kaca Pembesar -->
            <button type="button" 
                    @click="showSearchInput = !showSearchInput; if(showSearchInput) { $nextTick(() => $refs.peralatanSearchInput?.focus()) }" 
                    class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" 
                    title="Cari peralatan">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </button>
            
            <!-- Tombol Filter Interaktif -->
            <div class="relative">
                <button type="button" 
                        @click="showFilterDropdown = !showFilterDropdown" 
                        class="p-2 hover:bg-zinc-100 rounded-lg text-zinc-500 transition" 
                        title="Filter Kondisi & Kategori">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                </button>

                <!-- Dropdown Menu Filter -->
                <div x-show="showFilterDropdown" 
                     @click.away="showFilterDropdown = false" 
                     x-transition 
                     class="absolute right-0 mt-2 w-52 max-h-96 overflow-y-auto bg-white border border-zinc-200 rounded-xl shadow-lg py-1.5 z-50 text-xs font-medium">
                    <div class="px-3 py-1.5 font-bold text-zinc-400 uppercase text-[10px] tracking-wider border-b border-zinc-100">Kondisi Stok</div>
                    <button type="button" @click="pilihKondisi('semua'); showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                        <span>Semua Kondisi</span>
                        <span x-show="kondisi === 'semua'" class="text-portal-orange font-bold">✓</span>
                    </button>
                    <button type="button" @click="pilihKondisi('tersedia'); showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-emerald-700 flex items-center justify-between">
                        <span>Siap Sewa</span>
                        <span x-show="kondisi === 'tersedia'" class="text-portal-orange font-bold">✓</span>
                    </button>
                    <button type="button" @click="pilihKondisi('habis'); showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-amber-700 flex items-center justify-between">
                        <span>Stok Habis</span>
                        <span x-show="kondisi === 'habis'" class="text-portal-orange font-bold">✓</span>
                    </button>
                    <button type="button" @click="pilihKondisi('rusak'); showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-rose-700 flex items-center justify-between">
                        <span>Perlu Perbaikan</span>
                        <span x-show="kondisi === 'rusak'" class="text-portal-orange font-bold">✓</span>
                    </button>

                    <div class="px-3 py-1.5 font-bold text-zinc-400 uppercase text-[10px] tracking-wider border-b border-t border-zinc-100 mt-1">Kategori Alat</div>
                    <button type="button" @click="pilihKategori(''); showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                        <span>Semua Kategori</span>
                        <span x-show="!kategori" class="text-portal-orange font-bold">✓</span>
                    </button>
                    @if (isset($daftarKategori))
                        @foreach ($daftarKategori as $kat)
                            <button type="button" @click="pilihKategori('{{ $kat->id }}'); showFilterDropdown = false" class="w-full text-left px-4 py-2 hover:bg-zinc-50 text-zinc-700 flex items-center justify-between">
                                <span>{{ $kat->nama }}</span>
                                <span x-show="kategori === '{{ $kat->id }}'" class="text-portal-orange font-bold">✓</span>
                            </button>
                        @endforeach
                    @endif
                </div>
            </div>

            <template x-if="adaFilter">
                <button type="button" @click="resetFilter()" class="text-xs font-semibold text-portal-orange hover:underline px-1" title="Reset filter">
                    Reset
                </button>
            </template>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-gray-600">
            <thead class="bg-[#F4EFEA]/60 text-[#1A2024] font-semibold">
                <tr>
                    <th class="p-3.5 whitespace-nowrap">Peralatan</th>
                    <th class="p-3.5 whitespace-nowrap">ID Alat</th>
                    <th class="p-3.5">Kategori</th>
                    <th class="p-3.5 whitespace-nowrap">Tarif / Hari</th>
                    <th class="p-3.5 whitespace-nowrap">Nilai Pengganti</th>
                    <th class="p-3.5 whitespace-nowrap">Stok</th>
                    <th class="p-3.5 text-center whitespace-nowrap">Status</th>
                    <th class="p-3.5 text-right whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($daftarAlat as $alat)
                    @include('admin.peralatan._baris', ['alat' => $alat, 'filters' => $filters])
                @empty
                    <tr>
                        <td colspan="8" class="p-10 text-center">
                            <p class="text-3xl">📦</p>
                            <p class="mt-2 text-sm font-semibold text-[#1A2024]">
                                {{ $adaFilter ? 'Tidak ada alat yang cocok dengan filter.' : 'Belum ada data alat.' }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $adaFilter
                                    ? 'Coba ubah kata kunci pencarian atau reset filter.'
                                    : 'Tambahkan peralatan pertama melalui tombol "+ Tambah Peralatan".' }}
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination & Baris per Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 px-5 py-4 border-t border-gray-100">
        <p class="text-xs text-gray-500">
            @if ($daftarAlat->total() > 0)
                Menampilkan {{ $daftarAlat->firstItem() }}–{{ $daftarAlat->lastItem() }}
                dari {{ $daftarAlat->total() }} alat
                @if ($adaFilter)
                    <span class="text-gray-400">(terfilter)</span>
                @endif
            @else
                Tidak ada alat untuk ditampilkan
            @endif
        </p>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Tanpa JavaScript form ini masih bisa dikirim lewat tombol "Terapkan" di toolbar. --}}
            <form method="GET" action="{{ route('admin.peralatan.index') }}" class="flex items-center gap-2">
                <input type="hidden" name="search" value="{{ $filters['search'] }}">
                <input type="hidden" name="kategori" value="{{ $filters['kategori'] ?: '' }}">
                <input type="hidden" name="kondisi" value="{{ $filters['kondisi'] }}">
                <label for="per_page" class="text-xs text-gray-500">Baris per halaman:</label>
                <select id="per_page" name="per_page" x-model="perPage" @change="muatTabel()"
                        class="bg-[#F4EFEA]/60 border border-transparent rounded-lg px-2 py-1 text-xs text-[#1A2024] font-semibold focus:ring-2 focus:ring-[#15AEEA]">
                    @foreach ([10, 25, 50] as $jumlahBaris)
                        <option value="{{ $jumlahBaris }}" @selected($filters['per_page'] === $jumlahBaris)>{{ $jumlahBaris }}</option>
                    @endforeach
                </select>
            </form>

            @if ($daftarAlat->hasPages())
                <div data-paginasi>
                    {{ $daftarAlat->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
