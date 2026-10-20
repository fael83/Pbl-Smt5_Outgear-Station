@extends('layouts.customer')

@section('title', 'Booking Saya - Outgear Station')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="{ activeTab: 'semua' }">
    
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Booking & Status Penyewaan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau riwayat, jadwal pengambilan, dan status perlengkapan outdoor kamu.</p>
        </div>
        <div>
            <a href="/katalog" class="bg-brand hover:bg-brand-hover text-white text-xs font-bold px-4.5 py-3 rounded-2xl transition shadow-lg shadow-brand/20 inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Sewa Alat Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Tab Status (Minimalis Modern) -->
    <div class="flex items-center gap-2 border-b border-gray-200 pb-4 mb-8 text-xs font-bold">
        <button @click="activeTab = 'semua'" 
                :class="activeTab === 'semua' ? 'bg-gray-900 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300'"
                class="px-4 py-2 rounded-xl transition">
            Semua (2)
        </button>
        <button @click="activeTab = 'proses'" 
                :class="activeTab === 'proses' ? 'bg-gray-900 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300'"
                class="px-4 py-2 rounded-xl transition">
            Sedang Disewa (1)
        </button>
        <button @click="activeTab = 'selesai'" 
                :class="activeTab === 'selesai' ? 'bg-gray-900 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300'"
                class="px-4 py-2 rounded-xl transition">
            Selesai (1)
        </button>
    </div>

    <!-- Daftar Kartu Booking -->
    <div class="space-y-4">
        
        <!-- Kartu 1: Sedang Disewa -->
        <div x-show="activeTab === 'semua' || activeTab === 'proses'" 
             class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200 shadow-sm hover:shadow-md transition duration-300">
            
            <!-- Top Info: Kode Booking & Status -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-2">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xs font-bold text-gray-500 bg-gray-50 px-3 py-1 rounded-lg border border-gray-200/60">#BK-20261013</span>
                    <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[11px] font-bold flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-ping"></span>
                        Sedang Disewa
                    </span>
                </div>
                <div class="text-xs text-gray-400 font-medium">
                    Dipesan pada 10 Okt 2026
                </div>
            </div>

            <!-- Main Content: Nama Barang & Jadwal -->
            <div class="py-5 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 flex-1">
                    <h3 class="font-extrabold text-gray-900 text-sm sm:text-base leading-snug">
                        Tenda Consina Magnum 4 (1 Unit) + Carrier 60L
                    </h3>
                    <div class="inline-flex items-center gap-2 text-xs text-gray-600 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100">
                        <span>📅</span>
                        <span>Ambil: <strong class="text-gray-900">14 Okt 2026</strong> &nbsp;—&nbsp; Kembali: <strong class="text-gray-900">17 Okt 2026</strong> <span class="text-brand font-bold">(3 Hari)</span></span>
                    </div>
                </div>

                <div class="text-left md:text-right shrink-0">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider block">Total Biaya</span>
                    <span class="text-lg sm:text-xl font-black text-brand">Rp 185.000</span>
                </div>
            </div>

            <!-- Footer: Catatan & Tombol Aksi -->
            <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <p class="text-xs text-amber-700 bg-amber-50/80 px-3.5 py-2 rounded-xl border border-amber-200/60 flex items-center gap-2">
                    <span>⚠️</span>
                    <span>Wajib membawa <strong>E-KTP asli</strong> saat pengambilan unit di basecamp.</span>
                </p>
                <div class="flex items-center gap-2 shrink-0">
                    <button onclick="alert('Menampilkan detail invoice pesanan')" class="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                        Lihat Invoice
                    </button>
                    <button onclick="alert('Silakan hubungi CS untuk bantuan')" class="px-4 py-2 bg-gray-900 hover:bg-brand text-white rounded-xl text-xs font-bold transition shadow-sm">
                        Bantuan CS
                    </button>
                </div>
            </div>
        </div>

        <!-- Kartu 2: Selesai -->
        <div x-show="activeTab === 'semua' || activeTab === 'selesai'" 
             class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200 shadow-sm opacity-9ains hover:opacity-100 transition duration-300">
            
            <!-- Top Info -->
            <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-2">
                <div class="flex items-center gap-3">
                    <span class="font-mono text-xs font-bold text-gray-500 bg-gray-50 px-3 py-1 rounded-lg border border-gray-200/60">#BK-20260901</span>
                    <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[11px] font-bold">
                        ✓ Selesai / Dikembalikan
                    </span>
                </div>
                <div class="text-xs text-gray-400 font-medium">
                    Dipesan pada 1 Sep 2026
                </div>
            </div>

            <!-- Main Content -->
            <div class="py-5 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-2 flex-1">
                    <h3 class="font-extrabold text-gray-900 text-sm sm:text-base leading-snug">
                        Kompor Portable + Nesting Cooking Set
                    </h3>
                    <p class="text-xs text-gray-500">
                        Jadwal Ambil: <strong class="text-gray-800">2 Sep 2026</strong> — Kembali: <strong class="text-gray-800">4 Sep 2026</strong> (2 Hari)
                    </p>
                </div>

                <div class="text-left md:text-right shrink-0">
                    <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider block">Total Biaya</span>
                    <span class="text-lg sm:text-xl font-black text-gray-700">Rp 75.000</span>
                </div>
            </div>

            <!-- Footer -->
            <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <span class="text-xs text-gray-400">Seluruh unit telah dikembalikan dalam kondisi baik.</span>
                <a href="/katalog" class="px-4 py-2 border border-gray-200 hover:bg-brand hover:text-white hover:border-brand text-gray-700 rounded-xl text-xs font-bold transition">
                    Sewa Lagi
                </a>
            </div>
        </div>

    </div>
</div>
@endsection