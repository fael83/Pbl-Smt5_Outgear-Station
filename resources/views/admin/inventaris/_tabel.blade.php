{{--
    Kartu tabel data peralatan (tabel + footer + pagination).

    Dipakai saat halaman dirender penuh dan dikirim ulang lewat AJAX oleh
    halamanPeralatan.muatTabel(), sehingga pencarian/filter/pagination tidak
    perlu memuat ulang seluruh halaman.
--}}
<div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4 shadow-sm">
    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-gray-600">
            <thead class="bg-[#F4EFEA]/60 text-[#1A2024] font-semibold">
                <tr>
                    <th class="p-3.5 rounded-l-xl whitespace-nowrap">ID Alat</th>
                    <th class="p-3.5">Nama Alat</th>
                    <th class="p-3.5">Kategori</th>
                    <th class="p-3.5 whitespace-nowrap">Tarif / Hari</th>
                    <th class="p-3.5 whitespace-nowrap">Nilai Pengganti</th>
                    <th class="p-3.5 text-center">Total</th>
                    <th class="p-3.5 text-center">Tersedia</th>
                    <th class="p-3.5 text-center">Disewa</th>
                    <th class="p-3.5 text-center">Rusak</th>
                    <th class="p-3.5 rounded-r-xl text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($daftarAlat as $alat)
                    @include('admin.inventaris._baris', ['alat' => $alat, 'filters' => $filters])
                @empty
                    <tr>
                        <td colspan="10" class="p-10 text-center">
                            <p class="text-3xl">📦</p>
                            <p class="mt-2 text-sm font-semibold text-[#1A2024]">
                                {{ $adaFilter ? 'Tidak ada alat yang cocok dengan filter.' : 'Belum ada data alat.' }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $adaFilter
                                    ? 'Coba ubah kata kunci pencarian atau reset filter.'
                                    : 'Tambahkan alat pertama melalui tombol "+ Tambah Alat Baru".' }}
                            </p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination & Baris per Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-3 pt-4 border-t border-gray-100">
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
            {{-- Tanpa JavaScript form ini masih bisa dikirim lewat tombol "Terapkan" di form filter. --}}
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
