@extends('layouts.owner')

@section('content')
<div class="space-y-6 pt-2 pb-10">

    {{-- Header Dashboard --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-zinc-900 tracking-tight">
                Dashboard Owner
            </h2>
            <p class="text-xs text-zinc-500 mt-0.5">
                Ringkasan dan pemantauan bisnis OutGear Station.
            </p>
        </div>

        <div class="text-xs font-medium text-zinc-500">
            <span>OutGear Station</span>
            <span class="mx-1">/</span>
            <span class="font-semibold text-zinc-800">Dashboard</span>
        </div>
    </div>

    {{-- Kartu Ringkasan --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        {{-- Total Jenis Alat --}}
        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between gap-3">
            <div>
                <div class="text-3xl font-black text-zinc-800">
                    {{ number_format($ringkasan['total_alat'], 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">
                    Total Jenis Alat
                </div>
                <p class="text-[11px] text-zinc-500 mt-2">
                    Jenis alat dalam inventaris
                </p>
            </div>

            <span class="p-3 rounded-xl bg-blue-50 text-blue-700 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                </svg>
            </span>
        </div>

        {{-- Total Pemesanan --}}
        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between gap-3">
            <div>
                <div class="text-3xl font-black text-zinc-800">
                    {{ number_format($ringkasan['total_pemesanan'], 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">
                    Total Pemesanan
                </div>
                <p class="text-[11px] text-zinc-500 mt-2">
                    Seluruh transaksi pemesanan
                </p>
            </div>

            <span class="p-3 rounded-xl bg-purple-50 text-purple-700 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M9 5H7a2 2 0 00-2 2v12h10V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 7h6m-6 4h6"/>
                </svg>
            </span>
        </div>

        {{-- Menunggu Pembayaran --}}
        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between gap-3">
            <div>
                <div class="text-3xl font-black text-orange-600">
                    {{ number_format($ringkasan['menunggu_pembayaran'], 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">
                    Menunggu Pembayaran
                </div>
                <p class="text-[11px] text-zinc-500 mt-2">
                    Pemesanan belum dibayar
                </p>
            </div>

            <span class="p-3 rounded-xl bg-orange-50 text-orange-700 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor"
                     stroke-width="1.8">
                    <circle cx="12" cy="12" r="9"/>
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 7v5l3 2"/>
                </svg>
            </span>
        </div>

        {{-- Total Pembayaran Berhasil --}}
        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between gap-3">
            <div class="min-w-0">
                <div class="text-xl font-black text-zinc-800">
                    Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">
                    Total Pembayaran Berhasil
                </div>
                <p class="text-[11px] text-zinc-500 mt-2">
                    Akumulasi pembayaran berstatus berhasil
                </p>
            </div>

            <span class="p-3 rounded-xl bg-emerald-50 text-emerald-700 flex-shrink-0">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5"
                     fill="none" viewBox="0 0 24 24" stroke="currentColor"
                     stroke-width="1.8">
                    <path stroke-linecap="round" stroke-linejoin="round"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 12v-2m9-4a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </span>
        </div>

    </div>

    {{-- Ringkasan Inventaris --}}
    <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm p-6">

        <div class="mb-5">
            <h3 class="font-bold text-zinc-900 text-base">
                Ringkasan Inventaris
            </h3>
            <p class="text-xs text-zinc-500 mt-0.5">
                Kondisi stok alat outdoor saat ini.
            </p>
        </div>

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-5">

            {{-- Total Stok --}}
            <div class="p-5 rounded-2xl bg-zinc-50 border border-zinc-100">
                <div class="text-xs font-semibold text-zinc-400">
                    Total Stok
                </div>
                <div class="text-2xl font-black text-zinc-800 mt-2">
                    {{ number_format($ringkasan['total_stok'], 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-zinc-500 mt-1">Unit alat</p>
            </div>

            {{-- Stok Tersedia --}}
            <div class="p-5 rounded-2xl bg-emerald-50/80 border border-emerald-100">
                <div class="text-xs font-semibold text-emerald-700">
                    Stok Tersedia
                </div>
                <div class="text-2xl font-black text-emerald-700 mt-2">
                    {{ number_format($ringkasan['stok_tersedia'], 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-emerald-700 mt-1">Unit siap disewa</p>
            </div>

            {{-- Sedang Disewa --}}
            <div class="p-5 rounded-2xl bg-orange-50/80 border border-orange-100">
                <div class="text-xs font-semibold text-orange-700">
                    Sedang Disewa
                </div>
                <div class="text-2xl font-black text-orange-700 mt-2">
                    {{ number_format($ringkasan['stok_disewa'], 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-orange-700 mt-1">Unit disewa pelanggan</p>
            </div>

            {{-- Stok Rusak --}}
            <div class="p-5 rounded-2xl bg-red-50/80 border border-red-100">
                <div class="text-xs font-semibold text-red-700">
                    Stok Rusak
                </div>
                <div class="text-2xl font-black text-red-700 mt-2">
                    {{ number_format($ringkasan['stok_rusak'], 0, ',', '.') }}
                </div>
                <p class="text-[11px] text-red-700 mt-1">Unit perlu diperhatikan</p>
            </div>

        </div>
    </div>

    {{-- Pemesanan Terbaru --}}
    <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">

        <div class="p-5 border-b border-zinc-100 flex flex-col md:flex-row md:items-center justify-between gap-3">
            <div>
                <h3 class="font-bold text-zinc-900 text-base">
                    Pemesanan Terbaru
                </h3>
                <p class="text-xs text-zinc-500 mt-0.5">
                    Lima transaksi pemesanan terbaru dari pelanggan.
                </p>
            </div>

            <span class="text-[11px] font-bold px-3 py-1.5 rounded-full bg-zinc-100 text-zinc-600 border border-zinc-200 w-fit">
                {{ $pemesananTerbaru->count() }} transaksi
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full min-w-[700px] text-left text-xs align-middle">

                <thead>
                    <tr class="text-zinc-400 font-bold border-b border-zinc-100 bg-zinc-50/50">
                        <th class="p-4 uppercase">Kode Pemesanan</th>
                        <th class="p-4 uppercase">Pelanggan</th>
                        <th class="p-4 uppercase">Tanggal Mulai</th>
                        <th class="p-4 uppercase">Total Biaya</th>
                        <th class="p-4 uppercase">Status</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">

                    @forelse ($pemesananTerbaru as $pemesanan)
                        <tr class="hover:bg-zinc-50/80 transition">

                            <td class="p-4 align-middle">
                                <span class="font-bold text-zinc-900">
                                    {{ $pemesanan['kode'] }}
                                </span>
                            </td>

                            <td class="p-4 align-middle">
                                {{ $pemesanan['pelanggan'] }}
                            </td>

                            <td class="p-4 align-middle whitespace-nowrap">
                                {{ $pemesanan['tanggal'] }}
                            </td>

                            <td class="p-4 align-middle whitespace-nowrap">
                                <span class="font-bold text-orange-600">
                                    Rp {{ number_format($pemesanan['total'], 0, ',', '.') }}
                                </span>
                            </td>

                            <td class="p-4 align-middle">
                                @php
                                    $status = $pemesanan['status'];

                                    $statusLabel = match ($status) {
                                        'selesai' => 'Selesai',
                                        'menunggu_pembayaran' => 'Menunggu Pembayaran',
                                        default => ucwords(str_replace('_', ' ', $status ?? '-')),
                                    };

                                    $statusClass = match ($status) {
                                        'selesai' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                        'menunggu_pembayaran' => 'bg-orange-50 text-orange-700 border-orange-200',
                                        default => 'bg-zinc-100 text-zinc-600 border-zinc-200',
                                    };
                                @endphp

                                <span class="inline-flex whitespace-nowrap rounded-full px-3 py-1 text-[11px] font-semibold border {{ $statusClass }}">
                                    {{ $statusLabel }}
                                </span>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-12 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="mb-3 rounded-xl bg-zinc-100 p-3 text-zinc-400">
                                        <svg xmlns="http://www.w3.org/2000/svg"
                                             class="w-6 h-6" fill="none"
                                             viewBox="0 0 24 24" stroke="currentColor"
                                             stroke-width="1.7">
                                            <path stroke-linecap="round"
                                                  stroke-linejoin="round"
                                                  d="M9 5H7a2 2 0 00-2 2v12h10V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5v0"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-zinc-700">
                                        Belum ada pemesanan
                                    </p>
                                    <p class="text-xs text-zinc-400 mt-1">
                                        Data pemesanan akan muncul di sini.
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse

                </tbody>
            </table>
        </div>
    </div>

    {{-- Pemesanan Selesai --}}
    <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm p-5 flex items-center justify-between gap-4">
        <div>
            <h3 class="font-bold text-zinc-900 text-base">
                Pemesanan Selesai
            </h3>
            <p class="text-xs text-zinc-500 mt-0.5">
                Total pemesanan yang telah selesai.
            </p>
        </div>

        <div class="rounded-xl bg-emerald-50 border border-emerald-100 px-4 py-3 text-right">
            <p class="text-2xl font-black text-emerald-700">
                {{ number_format($ringkasan['pemesanan_selesai'], 0, ',', '.') }}
            </p>
            <p class="text-[11px] font-semibold text-emerald-700">
                Transaksi
            </p>
        </div>
    </div>

</div>
@endsection
```