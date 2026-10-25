@extends('layouts.panel', ['title' => 'Dashboard Petugas Gudang'])

@section('content')
@php
    // Data dummy sementara untuk preview tampilan
    $dummyPesanan = collect([
        (object)['kode_pemesanan' => 'OGS-20261025-01', 'pelanggan' => (object)['name' => 'Budi Santoso']],
        (object)['kode_pemesanan' => 'OGS-20261025-02', 'pelanggan' => (object)['name' => 'Siti Rahmawati']],
        (object)['kode_pemesanan' => 'OGS-20261025-03', 'pelanggan' => (object)['name' => 'Ahmad Fauzi']],
    ]);

    // Data dummy untuk tugas penting: Checklist & Persiapan Awal
    $dummyChecklist = collect([
        (object)['kode_pemesanan' => 'OGS-20261025-04', 'alat' => 'Tenda Dome 4 Orang', 'pelanggan' => 'Rian Pratama'],
        (object)['kode_pemesanan' => 'OGS-20261025-05', 'alat' => 'Carrier 60L Consina', 'pelanggan' => 'Dewi Lestari'],
    ]);

    $tampilPesanan = (isset($pesananHariIni) && $pesananHariIni->count() > 0) ? $pesananHariIni : $dummyPesanan;
    $tampilChecklist = isset($checklistAwal) ? $checklistAwal : $dummyChecklist;
@endphp

<div class="space-y-6">
    <!-- Header Sambutan -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-white p-6 rounded-2xl border border-zinc-200/70 shadow-sm">
        <div>
            <h1 class="text-2xl font-black text-[#1A2024] tracking-tight">Halo, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-xs text-zinc-500 mt-1">Selamat datang kembali di Panel Petugas Gudang OutGear Station. Pantau tugas operasional harianmu di sini.</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-amber-50 text-amber-700 text-xs font-bold border border-amber-200/60 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-amber-500 animate-pulse"></span>
                Shift Aktif: Gudang Utama
            </span>
        </div>
    </div>

    <!-- Grid Statistik / Informasi Cepat (Tugas Penting Petugas Gudang) -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        
        <!-- Kotak 1: Pesanan Hari Ini (Ambil / Kembali) -->
        <div class="bg-white p-6 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-extrabold text-[#1A2024] text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-portal-orange" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        Jadwal Ambil & Kembali Hari Ini
                    </h3>
                    <span class="bg-blue-50 text-blue-700 text-xs font-bold px-2.5 py-1 rounded-xl border border-blue-100">
                        {{ $tampilPesanan->count() }} Agenda
                    </span>
                </div>
                
                <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                    @foreach($tampilPesanan as $pesanan)
                        <div class="flex justify-between items-center p-3.5 bg-zinc-50 rounded-xl border border-zinc-200/60 hover:border-zinc-300 transition">
                            <div>
                                <p class="text-xs font-bold text-zinc-900">{{ $pesanan->pelanggan->name ?? $pesanan->pelanggan->nama ?? 'Pelanggan' }}</p>
                                <p class="text-[10px] text-zinc-500 font-mono mt-0.5">Kode: {{ $pesanan->kode_pemesanan ?? $pesanan->id }}</p>
                            </div>
                            <a href="#" class="px-3 py-1.5 rounded-lg bg-white border border-zinc-200 text-[11px] font-bold text-zinc-700 hover:bg-zinc-100 transition shadow-sm">
                                Proses →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-zinc-100 flex justify-between items-center text-xs">
                <span class="text-zinc-400">Pastikan cek kondisi fisik alat sebelum diserahterimakan.</span>
            </div>
        </div>

        <!-- Kotak 2: Perlu Checklist Kondisi Awal (Tugas Penting Gudang) -->
        <div class="bg-white p-6 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="font-extrabold text-[#1A2024] text-sm flex items-center gap-2">
                        <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                        Pending Checklist Kondisi Awal
                    </h3>
                    <span class="bg-amber-50 text-amber-700 text-xs font-bold px-2.5 py-1 rounded-xl border border-amber-100">
                        {{ $tampilChecklist->count() }} Unit
                    </span>
                </div>
                
                <div class="space-y-3 max-h-64 overflow-y-auto pr-1">
                    @foreach($tampilChecklist as $item)
                        <div class="flex justify-between items-center p-3.5 bg-amber-50/40 rounded-xl border border-amber-100">
                            <div>
                                <p class="text-xs font-bold text-zinc-900">{{ $item->alat ?? 'Peralatan Outdoor' }}</p>
                                <p class="text-[10px] text-zinc-500 mt-0.5">Peminjam: <span class="font-semibold text-zinc-700">{{ $item->pelanggan }}</span></p>
                            </div>
                            <a href="#" class="px-3 py-1.5 rounded-lg bg-white border border-amber-200 text-[11px] font-bold text-amber-700 hover:bg-amber-50 transition shadow-sm">
                                Checklist →
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-zinc-100 flex justify-between items-center text-xs">
                <span class="text-zinc-400">Wajib diisi sebelum barang diserahkan ke penyewa.</span>
            </div>
        </div>

    </div>

    <!-- Shortcut Menu / Aksi Cepat Petugas Gudang -->
    <div class="bg-white p-6 rounded-2xl border border-zinc-200/70 shadow-sm">
        <h3 class="font-extrabold text-[#1A2024] text-sm mb-4">Aksi Cepat Petugas Gudang</h3>
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
            <a href="#" class="p-4 rounded-xl bg-zinc-50 hover:bg-portal-orange hover:text-white border border-zinc-200/60 transition group text-center flex flex-col items-center justify-center gap-2">
                <span class="text-xl group-hover:scale-110 transition">📦</span>
                <span class="text-xs font-bold text-zinc-700 group-hover:text-white">Serah Terima</span>
            </a>
            <a href="#" class="p-4 rounded-xl bg-zinc-50 hover:bg-portal-orange hover:text-white border border-zinc-200/60 transition group text-center flex flex-col items-center justify-center gap-2">
                <span class="text-xl group-hover:scale-110 transition">📋</span>
                <span class="text-xs font-bold text-zinc-700 group-hover:text-white">Checklist Awal</span>
            </a>
            <a href="#" class="p-4 rounded-xl bg-zinc-50 hover:bg-portal-orange hover:text-white border border-zinc-200/60 transition group text-center flex flex-col items-center justify-center gap-2">
                <span class="text-xl group-hover:scale-110 transition">🔄</span>
                <span class="text-xs font-bold text-zinc-700 group-hover:text-white">Ubah Status</span>
            </a>
            <a href="#" class="p-4 rounded-xl bg-zinc-50 hover:bg-portal-orange hover:text-white border border-zinc-200/60 transition group text-center flex flex-col items-center justify-center gap-2">
                <span class="text-xl group-hover:scale-110 transition">📥</span>
                <span class="text-xs font-bold text-zinc-700 group-hover:text-white">Pengembalian</span>
            </a>
            <a href="#" class="p-4 rounded-xl bg-zinc-50 hover:bg-portal-orange hover:text-white border border-zinc-200/60 transition group text-center flex flex-col items-center justify-center gap-2">
                <span class="text-xl group-hover:scale-110 transition">⚠️</span>
                <span class="text-xs font-bold text-zinc-700 group-hover:text-white">Catat Rusak</span>
            </a>
        </div>
    </div>
</div>
@endsection