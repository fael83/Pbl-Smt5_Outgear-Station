@extends('layouts.panel', ['title' => 'Kerusakan & Kehilangan'])

@section('content')
    <div class="space-y-6">

        <!-- Judul Halaman -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-[#1A2024] tracking-tight">Kerusakan & Kehilangan Alat</h1>
                <p class="text-xs text-gray-500 mt-1 max-w-2xl">
                    Pemantauan daftar alat outdoor yang dilaporkan mengalami kerusakan atau hilang berdasarkan hasil checklist pengembalian barang.
                </p>
            </div>
        </div>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Kasus Tercatat</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-zinc-900">{{ $ringkasan['total_kasus'] }}</p>
                </div>
                <div class="text-[11px] text-zinc-500 font-medium pt-2 border-t border-zinc-100">
                    Insiden pada pengembalian
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Unit Rusak</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-amber-600">{{ $ringkasan['total_rusak'] }}</p>
                </div>
                <div class="text-[11px] text-amber-700 font-bold pt-2 border-t border-zinc-100">
                    Memerlukan perbaikan/maintenance
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Unit Hilang</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-rose-600">{{ $ringkasan['total_hilang'] }}</p>
                </div>
                <div class="text-[11px] text-rose-700 font-bold pt-2 border-t border-zinc-100">
                    Dalam proses penanganan denda
                </div>
            </div>
        </div>

        <!-- Tabel Data Kerusakan & Kehilangan -->
        <div class="bg-white rounded-2xl border border-zinc-200/70 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50 text-zinc-400 text-[10px] uppercase tracking-wider font-bold border-b border-zinc-200/60">
                            <th class="py-3.5 px-6">ID Detail</th>
                            <th class="py-3.5 px-6">Kode Booking</th>
                            <th class="py-3.5 px-6">Pelanggan</th>
                            <th class="py-3.5 px-6">Nama Alat</th>
                            <th class="py-3.5 px-6 text-center text-amber-600">Jumlah Rusak</th>
                            <th class="py-3.5 px-6 text-center text-rose-600">Jumlah Hilang</th>
                            <th class="py-3.5 px-6">Alasan Hilang / Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-xs text-zinc-700 font-medium">
                        @forelse($daftarKasus as $kasus)
                            <tr class="hover:bg-zinc-50/50 transition">
                                <td class="py-4 px-6 font-mono text-zinc-400 font-bold">#{{ $kasus->id }}</td>
                                <td class="py-4 px-6 font-bold text-[#D86B43] font-mono">
                                    {{ $kasus->pengembalian->pemesanan->kode_pemesanan ?? '#'.$kasus->pengembalian->pemesanan_id }}
                                </td>
                                <td class="py-4 px-6 font-semibold text-zinc-800">
                                    {{ $kasus->pengembalian->pemesanan->pelanggan->name ?? 'Pelanggan' }}
                                </td>
                                <td class="py-4 px-6 font-bold text-zinc-900">
                                    {{ $kasus->detailPemesanan->alat->nama_alat ?? 'Alat tidak ditemukan' }}
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($kasus->jumlah_rusak > 0)
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                            {{ $kasus->jumlah_rusak }} Unit Rusak
                                        </span>
                                    @else
                                        <span class="text-zinc-300">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-center">
                                    @if($kasus->jumlah_hilang > 0)
                                        <span class="px-3 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-700 border border-rose-200">
                                            {{ $kasus->jumlah_hilang }} Unit Hilang
                                        </span>
                                    @else
                                        <span class="text-zinc-300">-</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-zinc-600">
                                    @if($kasus->jumlah_hilang > 0 && !empty($kasus->alasan_hilang))
                                        <div class="inline-flex items-start gap-1.5 bg-rose-50 border border-rose-200/80 px-2.5 py-1.5 rounded-xl text-rose-800 text-[11px] leading-tight max-w-xs">
                                            <span class="font-bold shrink-0">Alasan:</span>
                                            <span>{{ $kasus->alasan_hilang }}</span>
                                        </div>
                                    @elseif($kasus->jumlah_hilang > 0)
                                        <span class="text-zinc-400 italic text-[11px]">Belum ada alasan dicatat</span>
                                    @else
                                        <span class="text-zinc-300">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-12 text-center text-zinc-400 italic">Belum ada catatan kerusakan atau kehilangan alat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($daftarKasus->hasPages())
                <div class="p-4 border-t border-zinc-100">
                    {{ $daftarKasus->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection