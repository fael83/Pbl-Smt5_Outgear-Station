
<div class="bg-white rounded-2xl border border-zinc-200/80 overflow-hidden shadow-sm">

    <!-- Header Tabel -->
    <div class="p-5 border-b border-zinc-100">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-base font-bold text-zinc-900">
                    Data Rekap Denda
                </h3>
                <p class="text-xs text-zinc-500 mt-1">
                    Daftar denda dari transaksi penyewaan OutGear Station.
                </p>
            </div>

            <span class="text-xs font-semibold text-zinc-500 bg-zinc-100 px-3 py-1.5 rounded-lg">
                {{ number_format($dataDenda->total()) }} data
            </span>
        </div>
    </div>

    <!-- Isi Tabel -->
    <div class="overflow-x-auto">
        <table class="w-full text-sm text-left">

            <thead class="bg-zinc-50 text-zinc-500">
                <tr>
                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide whitespace-nowrap">
                        Kode Pemesanan
                    </th>

                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide whitespace-nowrap">
                        Pelanggan
                    </th>

                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide whitespace-nowrap">
                        Tanggal Sewa
                    </th>

                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide whitespace-nowrap">
                        Jenis Denda
                    </th>

                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide whitespace-nowrap">
                        Jumlah Denda
                    </th>

                    <th class="px-5 py-4 text-xs font-bold uppercase tracking-wide whitespace-nowrap">
                        Status
                    </th>
                </tr>
            </thead>

            <tbody class="divide-y divide-zinc-100">

                @forelse ($dataDenda as $item)
                    <tr class="hover:bg-zinc-50/70 transition">

                        <!-- Kode Pemesanan -->
                        <td class="px-5 py-4 font-semibold text-zinc-800 whitespace-nowrap">
                            {{ $item->pemesanan?->kode_pemesanan ?? '-' }}
                        </td>

                        <!-- Pelanggan -->
                        <td class="px-5 py-4 text-zinc-600 whitespace-nowrap">
                            {{ $item->pemesanan?->pelanggan?->name ?? '-' }}
                        </td>

                        <!-- Tanggal Sewa -->
                        <td class="px-5 py-4 text-zinc-600 whitespace-nowrap">
                            {{ $item->pemesanan?->tanggal_mulai?->format('d/m/Y') ?? '-' }}
                        </td>

                        <!-- Jenis Denda -->
                        <td class="px-5 py-4 text-zinc-600 whitespace-nowrap">
                            {{ ucfirst(str_replace('_', ' ', $item->jenis_denda ?? '-')) }}
                        </td>

                        <!-- Jumlah Denda -->
                        <td class="px-5 py-4 text-zinc-700 font-semibold whitespace-nowrap">
                            Rp {{ number_format($item->jumlah_denda ?? 0, 0, ',', '.') }}
                        </td>

                        <!-- Status Denda -->
                        <td class="px-5 py-4 whitespace-nowrap">
                            @if ($item->status === 'dibayar')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-green-50 text-green-700">
                                    Dibayar
                                </span>

                            @elseif ($item->status === 'belum_dibayar')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700">
                                    Belum Dibayar
                                </span>

                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-zinc-100 text-zinc-600">
                                    {{ ucfirst(str_replace('_', ' ', $item->status ?? '-')) }}
                                </span>
                            @endif
                        </td>

                    </tr>

                @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center">

                                <div class="w-12 h-12 rounded-full bg-zinc-100 flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-zinc-400"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round"
                                            stroke-linejoin="round"
                                            stroke-width="1.8"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2" />
                                    </svg>
                                </div>

                                <p class="text-sm font-semibold text-zinc-700">
                                    Belum ada data denda
                                </p>

                                <p class="text-xs text-zinc-400 mt-1">
                                    Data denda akan ditampilkan ketika tersedia.
                                </p>

                            </div>
                        </td>
                    </tr>
                @endforelse

            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if ($dataDenda->hasPages())
        <div class="px-5 py-4 border-t border-zinc-100">
            {{ $dataDenda->links() }}
        </div>
    @endif

</div>
