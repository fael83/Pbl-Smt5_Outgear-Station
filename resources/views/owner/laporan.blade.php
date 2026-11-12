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
            <a id="exportCsv"
                href="{{ route('owner.laporan.export-csv', request()->query()) }}"
                class="inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-white border border-zinc-200 text-zinc-700 text-xs font-semibold rounded-xl hover:bg-zinc-50 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 3v12m0 0l-4-4m4 4l4-4M5 17v4h14v-4" />
                </svg>
                Unduh CSV
            </a>

            <a id="exportPdf"
                href="{{ route('owner.laporan.export', request()->query()) }}"
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
    <div id="laporan-ringkasan">
        @include('owner.partials.laporan-ringkasan')
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
                Pilih periode pembayaran dan status untuk melihat data tertentu.
            </p>
        </div>

        <form id="filterLaporan"
            action="{{ route('owner.laporan.index') }}"
            method="GET"
            class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

            <div>
                <label for="tanggal_mulai" class="block text-xs font-semibold text-zinc-700 mb-2">
                    Tanggal Pembayaran Mulai
                </label>
                <input type="date"
                    id="tanggal_mulai"
                    name="tanggal_mulai"
                    value="{{ request('tanggal_mulai') }}"
                    class="w-full rounded-xl border-zinc-200 text-xs focus:border-orange-400 focus:ring-orange-400">
            </div>

            <div>
                <label for="tanggal_selesai" class="block text-xs font-semibold text-zinc-700 mb-2">
                    Tanggal Pembayaran Selesai
                </label>
                <input type="date"
                    id="tanggal_selesai"
                    name="tanggal_selesai"
                    value="{{ request('tanggal_selesai') }}"
                    class="w-full rounded-xl border-zinc-200 text-xs focus:border-orange-400 focus:ring-orange-400">
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold text-zinc-700 mb-2">
                    Status Pemesanan
                </label>
                <select id="status"
                    name="status"
                    class="w-full rounded-xl border-zinc-200 text-xs focus:border-orange-400 focus:ring-orange-400">

                    <option value="">Semua Status</option>

                    <option value="menunggu_pembayaran"
                        @selected(request('status') === 'menunggu_pembayaran')>
                        Menunggu Pembayaran
                    </option>

                    <option value="dikonfirmasi"
                        @selected(request('status') === 'dikonfirmasi')>
                        Dikonfirmasi
                    </option>

                    <option value="selesai"
                        @selected(request('status') === 'selesai')>
                        Selesai
                    </option>

                    <option value="dibatalkan"
                        @selected(request('status') === 'dibatalkan')>
                        Dibatalkan
                    </option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit"
                    class="flex-1 px-4 py-2.5 rounded-xl bg-zinc-900 text-white text-xs font-semibold hover:bg-zinc-800 transition">
                    Terapkan
                </button>

                <button type="reset"
                    id="resetFilter"
                    class="px-4 py-2.5 rounded-xl border border-zinc-200 text-zinc-600 text-xs font-semibold hover:bg-zinc-50 transition">
                    Reset
                </button>
            </div>
        </form>
    </div>

    <!-- Tabel Laporan -->
    <div id="data-penyewaan">
        @include('owner.partials.laporan-tabel')
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
document.addEventListener('DOMContentLoaded', function () {

    const form = document.getElementById('filterLaporan');
    const ringkasanContainer = document.getElementById('laporan-ringkasan');
    const tabelContainer = document.getElementById('data-penyewaan');

    const canvasBulanan = document.getElementById('grafikBulanan');
    const canvasStatus = document.getElementById('grafikStatus');

    const exportCsv = document.getElementById('exportCsv');
    const exportPdf = document.getElementById('exportPdf');
    const resetButton = document.getElementById('resetFilter');

    let chartBulanan = null;
    let chartStatus = null;

    // Data awal grafik dari controller.
    const dataBulananAwal = @json($grafikBulanan);
    const dataStatusAwal = @json($grafikStatus);

    // Warna grafik berdasarkan nama status.
    function getStatusColors(labels) {
        const colors = {
            'Menunggu Pembayaran': '#F5D76E',
            'Dikonfirmasi': '#8DB7E8',
            'Selesai': '#22C55E',
            'Dibatalkan': '#EF4444'
        };

        return labels.map(label => colors[label] ?? '#B8A6C9');
    }

    // Inisialisasi grafik jika Chart.js berhasil dimuat.
    if (typeof Chart !== 'undefined') {

        // Grafik pemesanan per bulan.
        chartBulanan = new Chart(canvasBulanan, {
            type: 'bar',
            data: {
                labels: dataBulananAwal.labels,
                datasets: [{
                    label: 'Jumlah Pemesanan',
                    data: dataBulananAwal.data,
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

        // Grafik status pemesanan.
        chartStatus = new Chart(canvasStatus, {
            type: 'doughnut',
            data: {
                labels: dataStatusAwal.labels,
                datasets: [{
                    data: dataStatusAwal.data,
                    backgroundColor: getStatusColors(dataStatusAwal.labels),
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

    } else {
        console.error('Chart.js gagal dimuat.');
    }

    // Mengambil parameter filter dari form.
    function getFilterParams() {
        const params = new URLSearchParams(new FormData(form));

        for (const [key, value] of [...params.entries()]) {
            if (!value) {
                params.delete(key);
            }
        }

        return params;
    }

    // Memperbarui link ekspor sesuai filter aktif.
    function updateExportLinks(params) {
        const queryString = params.toString();

        exportCsv.href = "{{ route('owner.laporan.export-csv') }}" +
            (queryString ? '?' + queryString : '');

        exportPdf.href = "{{ route('owner.laporan.export') }}" +
            (queryString ? '?' + queryString : '');
    }

    // Memperbarui grafik setelah data dari server diterima.
    function updateCharts(dataBulanan, dataStatus) {

        if (chartBulanan && chartStatus) {

            chartBulanan.data.labels = dataBulanan.labels;
            chartBulanan.data.datasets[0].data = dataBulanan.data;
            chartBulanan.update();

            chartStatus.data.labels = dataStatus.labels;
            chartStatus.data.datasets[0].data = dataStatus.data;
            chartStatus.data.datasets[0].backgroundColor =
                getStatusColors(dataStatus.labels);

            chartStatus.update();
        }
    }

    // Memuat data laporan menggunakan AJAX.
    async function loadLaporan(url, pushHistory = true) {

        try {

            ringkasanContainer.classList.add('opacity-50');
            tabelContainer.classList.add('opacity-50');

            const response = await fetch(url, {
                method: 'GET',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                throw new Error('Gagal mengambil data laporan.');
            }

            const result = await response.json();

            // Memperbarui ringkasan.
            ringkasanContainer.innerHTML = result.ringkasan_html;

            // Memperbarui tabel.
            tabelContainer.innerHTML = result.tabel_html;

            // Memperbarui grafik.
            updateCharts(
                result.grafikBulanan,
                result.grafikStatus
            );

            // Memperbarui URL browser.
            const currentUrl = new URL(url, window.location.origin);

            if (pushHistory) {
                window.history.pushState({}, '', currentUrl);
            }

            // Memperbarui link ekspor.
            const params = new URLSearchParams(currentUrl.search);
            updateExportLinks(params);

        } catch (error) {

            console.error(error);

            alert('Terjadi kesalahan saat memuat laporan. Silakan coba lagi.');

        } finally {

            ringkasanContainer.classList.remove('opacity-50');
            tabelContainer.classList.remove('opacity-50');

        }
    }

    // Ketika filter diterapkan.
    form.addEventListener('submit', function (event) {

        event.preventDefault();

        const params = getFilterParams();

        const url = form.action +
            (params.toString() ? '?' + params.toString() : '');

        loadLaporan(url);

    });

    // Ketika tombol reset ditekan.
    resetButton.addEventListener('click', function (event) {

        event.preventDefault();

        form.reset();

        loadLaporan(form.action);

    });

    // Menangani klik pagination tanpa reload halaman.
    tabelContainer.addEventListener('click', function (event) {

        const link = event.target.closest('a');

        if (!link) {
            return;
        }

        const href = link.getAttribute('href');

        if (!href || href === '#') {
            return;
        }

        const url = new URL(href, window.location.origin);

        if (url.origin !== window.location.origin) {
            return;
        }

        event.preventDefault();

        loadLaporan(url.href);

    });

    // Menangani tombol Back dan Forward browser.
    window.addEventListener('popstate', function () {

        loadLaporan(window.location.href, false);

    });

});
</script>
@endsection
