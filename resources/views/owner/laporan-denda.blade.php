@extends('layouts.owner')

@section('content')
<div class="max-w-7xl mx-auto space-y-6 pt-6">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <p class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2">
                Manajemen Bisnis / Laporan / Rekap Denda
            </p>

            <h1 class="text-2xl font-black text-zinc-900 tracking-tight">
                Rekap Denda
            </h1>

            <p class="text-xs text-zinc-500 mt-0.5">
                Pantau data denda dan pembayaran denda penyewaan OutGear Station.
            </p>
        </div>

        <a href="{{ route('owner.laporan.index') }}"
            class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-zinc-200 text-zinc-700 text-xs font-semibold rounded-xl hover:bg-zinc-50 transition">

            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                    stroke-width="2" d="M15 19l-7-7 7-7" />
            </svg>

            Kembali ke Laporan
        </a>
    </div>

    <!-- Ringkasan Denda -->
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

        <!-- Total Denda -->
        <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
            <p class="text-xs font-semibold text-zinc-500">
                Total Nominal Denda
            </p>

            <h3 class="text-xl font-black text-zinc-900 mt-2">
                Rp {{ number_format($ringkasan['total_denda'] ?? 0, 0, ',', '.') }}
            </h3>

            <p class="text-xs text-zinc-400 mt-2">
                Akumulasi seluruh denda
            </p>
        </div>

        <!-- Jumlah Data -->
        <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
            <p class="text-xs font-semibold text-zinc-500">
                Jumlah Data Denda
            </p>

            <h3 class="text-xl font-black text-zinc-900 mt-2">
                {{ number_format($ringkasan['jumlah_data'] ?? 0) }}
            </h3>

            <p class="text-xs text-zinc-400 mt-2">
                Total catatan denda
            </p>
        </div>

        <!-- Belum Dibayar -->
        <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
            <p class="text-xs font-semibold text-zinc-500">
                Denda Belum Dibayar
            </p>

            <h3 class="text-xl font-black text-amber-600 mt-2">
                Rp {{ number_format($ringkasan['belum_dibayar'] ?? 0, 0, ',', '.') }}
            </h3>

            <p class="text-xs text-zinc-400 mt-2">
                Menunggu pembayaran
            </p>
        </div>

        <!-- Sudah Dibayar -->
        <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
            <p class="text-xs font-semibold text-zinc-500">
                Denda Sudah Dibayar
            </p>

            <h3 class="text-xl font-black text-green-600 mt-2">
                Rp {{ number_format($ringkasan['sudah_dibayar'] ?? 0, 0, ',', '.') }}
            </h3>

            <p class="text-xs text-zinc-400 mt-2">
                Pembayaran telah diterima
            </p>
        </div>

    </div>

    <!-- Grafik dan Informasi -->
    <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">

        <div>
            <h2 class="text-base font-bold text-zinc-900">
                Grafik Jenis Denda
            </h2>

            <p class="text-xs text-zinc-500 mt-1">
                Perbandingan nominal denda berdasarkan jenisnya.
            </p>
        </div>

        <div class="mt-5 h-72 relative">
            <canvas id="grafikJenisDenda"></canvas>
        </div>

    </div>

    <!-- Filter -->
    <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">

        <div class="mb-4">
            <h2 class="text-base font-bold text-zinc-900">
                Filter Rekap Denda
            </h2>

            <p class="text-xs text-zinc-500 mt-1">
                Pilih periode, status, atau jenis denda untuk melihat data tertentu.
            </p>
        </div>

        <form action="{{ route('owner.laporan.denda') }}"
            method="GET"
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-5 gap-4">

            <!-- Tanggal Mulai -->
            <div>
                <label for="tanggal_mulai"
                    class="block text-xs font-semibold text-zinc-700 mb-2">
                    Tanggal Mulai
                </label>

                <input type="date"
                    id="tanggal_mulai"
                    name="tanggal_mulai"
                    value="{{ request('tanggal_mulai') }}"
                    class="w-full rounded-xl border-zinc-200 text-xs focus:border-orange-400 focus:ring-orange-400">
            </div>

            <!-- Tanggal Selesai -->
            <div>
                <label for="tanggal_selesai"
                    class="block text-xs font-semibold text-zinc-700 mb-2">
                    Tanggal Selesai
                </label>

                <input type="date"
                    id="tanggal_selesai"
                    name="tanggal_selesai"
                    value="{{ request('tanggal_selesai') }}"
                    class="w-full rounded-xl border-zinc-200 text-xs focus:border-orange-400 focus:ring-orange-400">
            </div>

            <!-- Status -->
            <div>
                <label for="status"
                    class="block text-xs font-semibold text-zinc-700 mb-2">
                    Status Denda
                </label>

                <select id="status"
                    name="status"
                    class="w-full rounded-xl border-zinc-200 text-xs focus:border-orange-400 focus:ring-orange-400">

                    <option value="">Semua Status</option>

                    <option value="belum_dibayar"
                        @selected(request('status') === 'belum_dibayar')>
                        Belum Dibayar
                    </option>

                    <option value="dibayar"
                        @selected(request('status') === 'dibayar')>
                        Sudah Dibayar
                    </option>

                </select>
            </div>

            <!-- Jenis Denda -->
            <div>
                <label for="jenis_denda"
                    class="block text-xs font-semibold text-zinc-700 mb-2">
                    Jenis Denda
                </label>

                <select id="jenis_denda"
                    name="jenis_denda"
                    class="w-full rounded-xl border-zinc-200 text-xs focus:border-orange-400 focus:ring-orange-400">

                    <option value="">Semua Jenis</option>

                    @foreach ($jenisDenda as $jenis)
                        <option value="{{ $jenis }}"
                            @selected(request('jenis_denda') === $jenis)>
                            {{ ucfirst(str_replace('_', ' ', $jenis)) }}
                        </option>
                    @endforeach

                </select>
            </div>

            <!-- Tombol -->
            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 px-4 py-2.5 rounded-xl bg-zinc-900 text-white text-xs font-semibold hover:bg-zinc-800 transition">
                    Terapkan
                </button>

                <a href="{{ route('owner.laporan.denda') }}"
                    class="px-4 py-2.5 rounded-xl border border-zinc-200 text-zinc-600 text-xs font-semibold hover:bg-zinc-50 transition">
                    Reset
                </a>
            </div>

        </form>
    </div>

    <!-- Tabel Rekap Denda -->
    <div id="data-rekap-denda">
        @include('owner.partials.laporan-denda-tabel')
    </div>

    <!-- Catatan -->
    <div class="flex items-start gap-3 p-4 rounded-xl bg-white border border-zinc-200/80">

        <svg class="w-5 h-5 text-zinc-400 flex-shrink-0 mt-0.5"
            fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round"
                stroke-width="2"
                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
        </svg>

        <p class="text-xs leading-relaxed text-zinc-500">
            Total denda merupakan akumulasi nominal denda berdasarkan
            data yang sesuai dengan filter. Denda belum dibayar dan
            sudah dibayar ditampilkan secara terpisah.
        </p>

    </div>

</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const canvas = document.getElementById('grafikJenisDenda');

    const grafikData = @json($grafikJenis);

    if (typeof Chart !== 'undefined' && canvas) {

        new Chart(canvas, {
            type: 'bar',

            data: {
                labels: grafikData.labels,

                datasets: [{
                    label: 'Nominal Denda',
                    data: grafikData.data,
                    backgroundColor: '#FFB76B',
                    borderRadius: 6,
                    maxBarThickness: 45
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,

                scales: {
                    y: {
                        beginAtZero: true,

                        ticks: {
                            callback: function (value) {
                                return 'Rp ' + Number(value).toLocaleString('id-ID');
                            }
                        }
                    }
                },

                plugins: {
                    legend: {
                        display: false
                    },

                    tooltip: {
                        callbacks: {
                            label: function (context) {
                                return 'Rp ' +
                                    Number(context.raw).toLocaleString('id-ID');
                            }
                        }
                    }
                }
            }
        });

    } else {
        console.error('Chart.js gagal dimuat.');
    }

});
</script>
@endsection
