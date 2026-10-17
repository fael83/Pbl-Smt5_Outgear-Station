@extends('layouts.customer')

@section('title', 'Booking Saya - Outgear Station')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    
    <!-- Header Halaman -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl font-bold text-gray-900 font-heading">Riwayat & Status Booking</h1>
            <p class="text-sm text-gray-500 mt-1">Kelola dan pantau status penyewaan alat outdoor Anda di sini.</p>
        </div>
        <div>
            <a href="/katalog" class="px-4 py-2.5 bg-orange-600 hover:bg-orange-700 text-white rounded-xl text-sm font-bold transition shadow-sm inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Sewa Alat Baru
            </a>
        </div>
    </div>

    <!-- Filter Tab Status (Opsional untuk interaktif) -->
    <div class="flex items-center gap-2 overflow-x-auto pb-4 mb-6">
        <button class="px-4 py-2 bg-orange-600 text-white rounded-xl text-xs font-bold shadow-sm">Semua (2)</button>
        <button class="px-4 py-2 bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-xl text-xs font-semibold">Menunggu Pembayaran</button>
        <button class="px-4 py-2 bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-xl text-xs font-semibold">Sedang Disewa</button>
        <button class="px-4 py-2 bg-white text-gray-600 border border-gray-200 hover:bg-gray-50 rounded-xl text-xs font-semibold">Selesai</button>
    </div>

    <!-- Daftar Kartu Booking (Dummy Data) -->
    <div class="space-y-4">
        
        <!-- Kartu 1: Status Aktif / Disewa -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm transition hover:border-orange-200">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-2">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xs font-bold bg-gray-100 px-3 py-1 rounded-lg text-gray-700">#BK-20261013</span>
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 border border-blue-100 rounded-full text-xs font-bold flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full bg-blue-500 animate-pulse"></span>
                        Sedang Disewa
                    </span>
                </div>
                <div class="text-xs text-gray-500">
                    Booking pada: <span class="font-semibold text-gray-700">10 Okt 2026</span>
                </div>
            </div>

            <!-- Detail Barang di dalam Booking -->
            <div class="py-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div class="md:col-span-2 space-y-2">
                    <h4 class="font-bold text-gray-900 text-base">Tenda Consina Magnum 4 (1 Unit) + Carrier 60L</h4>
                    <p class="text-xs text-gray-500 flex items-center gap-1.5">
                        <svg class="w-4 h-4 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Jadwal Ambil: <strong class="text-gray-700">14 Okt 2026</strong> &nbsp;—&nbsp; Kembali: <strong class="text-gray-700">17 Okt 2026</strong> (3 Hari)
                    </p>
                </div>
                <div class="text-left md:text-right">
                    <span class="text-xs text-gray-400 block">Total Biaya</span>
                    <span class="text-lg font-extrabold text-orange-600">Rp 185.000</span>
                </div>
            </div>

            <!-- Footer Kartu: Tombol Aksi -->
            <div class="pt-4 border-t border-gray-100 flex flex-wrap items-center justify-between gap-3">
                <div class="text-xs text-amber-700 bg-amber-50 px-3 py-1.5 rounded-lg border border-amber-200">
                    ⚠️ Harap bawa E-KTP asli saat pengambilan barang di basecamp.
                </div>
                <div class="flex items-center gap-2">
                    <button onclick="alert('Menampilkan detail invoice pesanan')" class="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                        Lihat Invoice
                    </button>
                    <button onclick="alert('Silakan hubungi CS untuk bantuan')" class="px-4 py-2 bg-gray-900 hover:bg-gray-800 text-white rounded-xl text-xs font-bold transition">
                        Bantuan CS
                    </button>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Status Selesai -->
        <div class="bg-white rounded-2xl p-6 border border-gray-200/80 shadow-sm opacity-90">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-2">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xs font-bold bg-gray-100 px-3 py-1 rounded-lg text-gray-700">#BK-20260901</span>
                    <span class="px-3 py-1 bg-green-50 text-green-700 border border-green-100 rounded-full text-xs font-bold flex items-center gap-1.5">
                        ✓ Selesai / Dikembalikan
                    </span>
                </div>
                <div class="text-xs text-gray-500">
                    Booking pada: <span class="font-semibold text-gray-700">1 Sep 2026</span>
                </div>
            </div>

            <div class="py-4 grid grid-cols-1 md:grid-cols-3 gap-4 items-center">
                <div class="md:col-span-2 space-y-2">
                    <h4 class="font-bold text-gray-900 text-base">Kompor Portable + Nesting Cooking Set</h4>
                    <p class="text-xs text-gray-500">
                        Jadwal Ambil: 2 Sep 2026 — Kembali: 4 Sep 2026 (2 Hari)
                    </p>
                </div>
                <div class="text-left md:text-right">
                    <span class="text-xs text-gray-400 block">Total Biaya</span>
                    <span class="text-lg font-extrabold text-gray-700">Rp 75.000</span>
                </div>
            </div>

            <div class="pt-4 border-t border-gray-100 flex items-center justify-between">
                <span class="text-xs text-gray-400">Transaksi telah selesai dan alat sudah dikembalikan dalam kondisi baik.</span>
                <button class="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                    Sewa Lagi
                </button>
            </div>
        </div>

    </div>
</div>
@endsection