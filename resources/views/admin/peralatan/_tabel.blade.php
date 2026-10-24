{{--
    Kartu tabel data peralatan (tabel + footer + pagination).

    Dipakai saat halaman dirender penuh dan dikirim ulang lewat AJAX oleh
    halamanPeralatan.muatTabel(), sehingga pencarian/filter/pagination tidak
    perlu memuat ulang seluruh halaman.
--}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-4 border-b border-gray-100">
        <div>
            <h2 class="text-sm font-bold text-[#1A2024]">Daftar Peralatan</h2>
            <p class="text-[11px] text-gray-400 mt-0.5">
                Katalog alat, tarif sewa, dan posisi stok terkini.
            </p>
        </div>
        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-[#F4EFEA] text-[#1A2024] border border-gray-200">
            {{ $daftarAlat->total() }} alat
        </span>
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
