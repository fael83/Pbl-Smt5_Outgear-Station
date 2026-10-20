{{--
    Kartu tabel data pelanggan (tabel + footer + pagination).

    Dipakai saat halaman dirender penuh dan dikirim ulang lewat AJAX oleh
    halamanPelanggan.muatTabel(), sehingga pencarian/filter/pagination tidak
    perlu memuat ulang seluruh halaman.
--}}
<div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
    <div class="flex flex-wrap items-center justify-between gap-2 px-5 py-4 border-b border-gray-100">
        <div>
            <h2 class="text-sm font-bold font-heading text-[#1A2024]">Daftar Pelanggan</h2>
            <p class="text-[11px] text-gray-400 mt-0.5">
                Akun pelanggan yang terdaftar beserta status dan riwayat booking-nya.
            </p>
        </div>
        <span class="text-[10px] font-bold px-2.5 py-1 rounded-full bg-[#F4EFEA] text-[#1A2024] border border-gray-200">
            {{ $daftarPelanggan->total() }} pelanggan
        </span>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left text-xs text-gray-600">
            <thead class="bg-[#F4EFEA]/60 text-[#1A2024] font-semibold">
                <tr>
                    <th class="p-3.5 whitespace-nowrap">Pelanggan</th>
                    <th class="p-3.5 whitespace-nowrap">ID Pelanggan</th>
                    <th class="p-3.5 whitespace-nowrap">No. Telepon</th>
                    <th class="p-3.5 whitespace-nowrap">Total Booking</th>
                    <th class="p-3.5 whitespace-nowrap">Bergabung</th>
                    <th class="p-3.5 text-center whitespace-nowrap">Status</th>
                    <th class="p-3.5 text-right whitespace-nowrap">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse ($daftarPelanggan as $pelanggan)
                    @include('admin.pelanggan._baris', ['pelanggan' => $pelanggan, 'filters' => $filters])
                @empty
                    <tr>
                        <td colspan="7" class="p-10 text-center">
                            <p class="text-3xl">🧑‍🤝‍🧑</p>
                            <p class="mt-2 text-sm font-semibold text-[#1A2024]">
                                {{ $adaFilter ? 'Tidak ada pelanggan yang cocok dengan filter.' : 'Belum ada pelanggan terdaftar.' }}
                            </p>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ $adaFilter
                                    ? 'Coba ubah kata kunci pencarian atau reset filter.'
                                    : 'Akun pelanggan muncul di sini setelah melakukan pendaftaran.' }}
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
            @if ($daftarPelanggan->total() > 0)
                Menampilkan {{ $daftarPelanggan->firstItem() }}–{{ $daftarPelanggan->lastItem() }}
                dari {{ $daftarPelanggan->total() }} pelanggan
                @if ($adaFilter)
                    <span class="text-gray-400">(terfilter)</span>
                @endif
            @else
                Tidak ada pelanggan untuk ditampilkan
            @endif
        </p>

        <div class="flex flex-wrap items-center gap-3">
            {{-- Tanpa JavaScript form ini masih bisa dikirim lewat tombol "Terapkan" di toolbar. --}}
            <form method="GET" action="{{ route('admin.pelanggan') }}" class="flex items-center gap-2">
                <input type="hidden" name="search" value="{{ $filters['search'] }}">
                <input type="hidden" name="status" value="{{ $filters['status'] }}">
                <label for="per_page" class="text-xs text-gray-500">Baris per halaman:</label>
                <select id="per_page" name="per_page" x-model="perPage" @change="muatTabel()"
                        class="bg-[#F4EFEA]/60 border border-transparent rounded-lg px-2 py-1 text-xs text-[#1A2024] font-semibold focus:ring-2 focus:ring-[#15AEEA]">
                    @foreach ([10, 25, 50] as $jumlahBaris)
                        <option value="{{ $jumlahBaris }}" @selected($filters['per_page'] === $jumlahBaris)>{{ $jumlahBaris }}</option>
                    @endforeach
                </select>
            </form>

            @if ($daftarPelanggan->hasPages())
                <div data-paginasi>
                    {{ $daftarPelanggan->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
