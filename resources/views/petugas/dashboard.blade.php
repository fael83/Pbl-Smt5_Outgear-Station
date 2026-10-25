@extends('layouts.panel', ['title' => 'Dashboard Petugas'])

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-black text-[#1A2024]">Halo, {{ auth()->user()->name }}! 👋</h1>
            <p class="text-xs text-gray-500 mt-1">Selamat datang di Panel Petugas Outgear Station. Berikut tugas hari ini.</p>
        </div>
    </div>

    <!-- Info Cepat -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Kotak 1: Pesanan Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-[#1A2024]">Pesanan Hari Ini (Ambil/Kembali)</h3>
                <span class="bg-blue-100 text-blue-800 text-xs font-bold px-2.5 py-0.5 rounded-full">
                    {{ $pesananHariIni->count() }}
                </span>
            </div>
            
            @if($pesananHariIni->count() > 0)
                <div class="space-y-3">
                    @foreach($pesananHariIni as $pesanan)
                        <div class="flex justify-between items-center p-3 bg-gray-50 rounded-xl border border-gray-100">
                            <div>
                                <p class="text-xs font-bold text-gray-900">{{ $pesanan->pelanggan->name ?? 'Pelanggan' }}</p>
                                <p class="text-[10px] text-gray-500">Kode: {{ $pesanan->kode_pemesanan }}</p>
                            </div>
                            <a href="{{ route('admin.pemesanan') }}" class="text-[10px] font-semibold text-blue-600 hover:underline">
                                Cek Detail →
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 italic">Tidak ada jadwal ambil/kembali pesanan hari ini.</p>
            @endif
        </div>

        <!-- Kotak 2: Alat Rusak / Perlu Cek -->
        <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-[#1A2024]">Peralatan Perlu Diperbaiki</h3>
                <span class="bg-red-100 text-red-800 text-xs font-bold px-2.5 py-0.5 rounded-full">
                    {{ $alatRusak->count() }}
                </span>
            </div>
            
            @if($alatRusak->count() > 0)
                <div class="space-y-3">
                    @foreach($alatRusak as $alat)
                        <div class="flex justify-between items-center p-3 bg-red-50 rounded-xl border border-red-100">
                            <div>
                                <p class="text-xs font-bold text-gray-900">{{ $alat->nama }}</p>
                                <p class="text-[10px] text-red-600">{{ $alat->stok_rusak }} unit rusak</p>
                            </div>
                            <a href="{{ route('admin.peralatan.index') }}" class="text-[10px] font-semibold text-red-700 hover:underline">
                                Lihat Gudang →
                            </a>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-xs text-gray-400 italic">Semua peralatan dalam kondisi baik.</p>
            @endif
        </div>
    </div>
</div>
@endsection