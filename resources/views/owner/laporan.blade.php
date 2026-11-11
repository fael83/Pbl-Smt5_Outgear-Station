
@extends('layouts.owner')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">
                Manajemen Bisnis / Laporan
            </p>
            <h1 class="text-2xl font-black text-zinc-900 tracking-tight">
                Laporan Penyewaan
            </h1>
            <p class="text-xs text-zinc-500 mt-0.5">
                Pantau transaksi dan ringkasan penyewaan OutGear Station.
            </p>
        </div>

        <!-- Tombol Ekspor -->
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('owner.laporan.export-csv', request()->query()) }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-zinc-200 text-zinc-700 text-xs font-semibold rounded-xl hover:bg-zinc-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v12m0 0l-4-4m4 4l4-4M5 17v4h14v-4" />
                </svg>
                Unduh CSV
            </a>

            <a href="{{ route('owner.laporan.export', request()->query()) }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-zinc-900 text-white text-xs font-semibold rounded-xl hover:bg-zinc-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v12m0 0l-4-4m4 4l4-4M5 17v4h14v-4" />
                </svg>
                Unduh PDF
            </a>
        </div>
    </div>

    <!-- Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <!-- Total Pemesanan -->
        <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-zinc-100 flex items-center justify-center">
                <svg class="w-5 h-5 text-zinc-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 12h6m-6 4h6" />
                </svg>
            </div>
            <p class="text-xs font-semibold text-zinc-400 mt-4">Total Pemesanan</p>
            <h2 class="text-3xl font-black text-zinc-800 mt-1">
                {{ number_format($ringkasan['total_pemesanan'], 0, ',', '.') }}
            </h2>
            <p class="text-xs text-zinc-400 mt-2">Sesuai filter yang dipilih</p>
        </div>

        <!-- Pemesanan Selesai -->
        <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <p class="text-xs font-semibold text-zinc-400 mt-4">Pemesanan Selesai</p>
            <h2 class="text-3xl font-black text-zinc-800 mt-1">
                {{ number_format($ringkasan['pemesanan_selesai'], 0, ',', '.') }}
            </h2>
            <p class="text-xs text-emerald-600 mt-2">Status selesai</p>
        </div>

        <!-- Menunggu Pembayaran -->
        <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs font-semibold text-zinc-400 mt-4">Menunggu Pembayaran</p>
            <h2 class="text-3xl font-black text-zinc-800 mt-1">
                {{ number_format($ringkasan['menunggu_pembayaran'], 0, ',', '.') }}
            </h2>
            <p class="text-xs text-orange-600 mt-2">Perlu ditindaklanjuti</p>
        </div>

        <!-- Pendapatan -->
        <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <p class="text-xs font-semibold text-zinc-400 mt-4">Pembayaran Berhasil</p>
            <h2 class="text-xl font-black text-zinc-800 mt-1">
                Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}
            </h2>
            <p class="text-xs text-zinc-400 mt-2">Akumulasi pembayaran berhasil</p>
        </div>
    </div>

    <!-- Grafik -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-4">

        <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
            <h2 class="text-base font-bold text-zinc-900">Pemesanan per Bulan</h2>
            <p class="text-xs text-zinc-500 mt-1">
                Jumlah pemesanan berdasarkan tanggal mulai sewa.
            </p>
            <div class="mt-5 h-72 relative">
                <canvas id="grafikBulanan"></canvas>
            </div>
        </div>

        <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
            <h2 class="text-base font-bold text-zinc-900">Status Pemesanan</h2>
            <p class="text-xs text-zinc-500 mt-1">
                Perbandingan jumlah pemesanan berdasarkan status.
            </p>
            <div class="mt-5 h-72 relative">
                <canvas id="grafikStatus"></canvas>
            </div>
        </div>
    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
        <div class="mb-4">
            <h2 class="text-base font-bold text-zinc-900">Filter Laporan</h2>
            <p class="text-xs text-zinc-500 mt-1">
                Pilih periode dan status untuk melihat data tertentu.
            </p>
        </div>

        <form action="{{ route('owner.laporan.index') }}" method="GET"
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            <div>
                <label for="tanggal_mulai" class="block text-xs font-semibold text-zinc-700 mb-2">
                    Tanggal Mulai
                </label>
                <input type="date" id="tanggal_mulai" name="tanggal_mulai"
                    value="{{ request('tanggal_mulai') }}"
                    class="w-full rounded-xl border-zinc-200 text-xs focus:border-orange-400 focus:ring-orange-400">
            </div>

            <div>
                <label for="tanggal_selesai" class="block text-xs font-semibold text-zinc-700 mb-2">
                    Tanggal Selesai
                </label>
                <input type="date" id="tanggal_selesai" name="tanggal_selesai"
                    value="{{ request('tanggal_selesai') }}"
                    class="w-full rounded-xl border-zinc-200 text-xs focus:border-orange-400 focus:ring-orange-400">
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold text-zinc-700 mb-2">
                    Status Pemesanan
                </label>
                <select id="status" name="status"
                    class="w-full rounded-xl border-zinc-200 text-xs focus:border-orange-400 focus:ring-orange-400">
                    <option value="">Semua Status</option>
                    <option value="menunggu_pembayaran" @selected(request('status') === 'menunggu_pembayaran')>
                        Menunggu Pembayaran
                    </option>
                    <option value="selesai" @selected(request('status') === 'selesai')>
                        Selesai
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 px-4 py-2.5 rounded-xl bg-zinc-900 text-white text-xs font-semibold hover:bg-zinc-800 transition">
                    Terapkan
                </button>
                <a href="{{ route('owner.laporan.index') }}"
                    class="px-4 py-2.5 rounded-xl border border-zinc-200 text-zinc-600 text-xs font-semibold hover:bg-zinc-50 transition">
                    Reset
                </a>
            </div>
        </form>
    </div>

    <!-- Tabel Laporan -->
    <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">

        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 p-5 border-b border-zinc-100">
            <div>
                <h2 class="text-base font-bold text-zinc-900">Data Penyewaan</h2>
                <p class="text-xs text-zinc-500 mt-1">
                    Daftar pemesanan berdasarkan filter yang dipilih.
                </p>
            </div>
            <span class="text-xs font-semibold text-zinc-500 bg-zinc-50 border border-zinc-200 rounded-lg px-3 py-2">
                {{ $pemesanan->total() }} data
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-xs text-left align-middle">
                <thead>
                    <tr class="bg-zinc-50/50 text-zinc-400 font-bold border-b border-zinc-100 uppercase tracking-wider">
                        <th class="px-5 py-4 font-bold">Kode Pemesanan</th>
                        <th class="px-5 py-4 font-bold">Pelanggan</th>
                        <th class="px-5 py-4 font-bold">Tanggal Sewa</th>
                        <th class="px-5 py-4 font-bold">Total Biaya</th>
                        <th class="px-5 py-4 font-bold">Status</th>
                        <th class="px-5 py-4 font-bold">Pembayaran</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                    @forelse ($pemesanan as $item)
                    <tr class="hover:bg-zinc-50/80 transition-colors">
                        <td class="px-5 py-4">
                            <span class="font-bold text-zinc-900">
                                {{ $item->kode_pemesanan }}
                            </span>
                        </td>

                        <td class="px-5 py-4">
                            <span class="font-medium text-zinc-700">
                                {{ $item->pelanggan?->name ?? '-' }}
                            </span>
                        </td>

                        <td class="px-5 py-4 text-zinc-600 whitespace-nowrap">
                            {{ $item->tanggal_mulai?->format('d M Y') ?? '-' }}
                            <span class="text-zinc-400">s/d</span>
                            {{ $item->tanggal_selesai?->format('d M Y') ?? '-' }}
                        </td>

                        <td class="px-5 py-4 whitespace-nowrap font-bold text-zinc-800">
                            Rp {{ number_format($item->total_biaya ?? 0, 0, ',', '.') }}
                        </td>

                        <td class="px-5 py-4">
                            @if ($item->status === 'selesai')
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                Selesai
                            </span>
                            @elseif ($item->status === 'menunggu_pembayaran')
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-orange-50 text-orange-700 text-xs font-semibold">
                                Menunggu Pembayaran
                            </span>
                            @else
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-zinc-100 text-zinc-600 text-xs font-semibold">
                                {{ ucfirst(str_replace('_', ' ', $item->status ?? '-')) }}
                            </span>
                            @endif
                        </td>

                        <td class="px-5 py-4">
                            @php
                                $pembayaranBerhasil = $item->pembayaran
                                    ->contains('status_transaksi', 'berhasil');
                            @endphp

                            @if ($pembayaranBerhasil)
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-semibold">
                                Berhasil
                            </span>
                            @else
                            <span class="inline-flex px-2.5 py-1 rounded-lg bg-zinc-100 text-zinc-600 text-xs font-semibold">
                                Belum Berhasil
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-5 py-12 text-center">
                            <div class="flex flex-col items-center">
                                <div class="w-12 h-12 rounded-2xl bg-zinc-100 flex items-center justify-center mb-3">
                                    <svg class="w-6 h-6 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a2 2 0 01.707.293l5.414 5.414A2 2 0 0119 8v11a2 2 0 01-2 2z" />
                                    </svg>
                                </div>
                                <p class="font-bold text-zinc-700">
                                    Belum ada data penyewaan
                                </p>
                                <p class="text-xs text-zinc-400 mt-1">
                                    Coba ubah filter tanggal atau status.
                                </p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if ($pemesanan->hasPages())
        <div class="px-5 py-4 border-t border-zinc-100">
            {{ $pemesanan->links() }}
        </div>
        @endif
    </div>

    <!-- Catatan -->
    <div class="flex items-start gap-3 p-4 rounded-xl bg-white border border-zinc-200/80">
        <svg class="w-5 h-5 text-zinc-400 flex-shrink-0 mt-0.5"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>
        <p class="text-xs leading-relaxed text-zinc-500">
            Pendapatan dihitung dari jumlah transaksi pembayaran dengan status
            berhasil pada pemesanan yang sesuai dengan filter. Nilai ini
            merupakan akumulasi pembayaran berhasil, bukan otomatis keuntungan bersih.
        </p>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const canvasBulanan = document.getElementById('grafikBulanan');
        const canvasStatus = document.getElementById('grafikStatus');

        if (!canvasBulanan || !canvasStatus) {
            return;
        }

        if (typeof Chart === 'undefined') {
            console.error('Chart.js gagal dimuat.');
            return;
        }

        const dataBulanan = @json($grafikBulanan);
        const dataStatus = @json($grafikStatus);

        // Grafik jumlah pemesanan per bulan.
        new Chart(canvasBulanan, {
            type: 'bar',
            data: {
                labels: dataBulanan.labels,
                datasets: [{
                    label: 'Jumlah Pemesanan',
                    data: dataBulanan.data,
                    backgroundColor: '#FFB76B',
                    borderRadius: 6,
                    maxBarThickness: 40
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            precision: 0,
                            stepSize: 1
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });

        // Grafik distribusi status pemesanan.
        new Chart(canvasStatus, {
            type: 'doughnut',
            data: {
                labels: dataStatus.labels,
                datasets: [{
                    data: dataStatus.data,
                    backgroundColor: [
                        '#FFB76B',
                        '#c7E9C0',
                        '#D9D9D9',
                        '#A8B9C9',
                        '#B8A6C9',
                        '#E8C9B7'
                    ],
                    borderWidth: 2,
                    borderColor: '#FFFFFF'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    });
</script>
@endsection
