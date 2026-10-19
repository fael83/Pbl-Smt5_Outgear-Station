@extends('layouts.owner')

@section('content')
<div class="space-y-6">
    
    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                <span>Master Data</span>
                <span>/</span>
                <span class="text-gray-900">Kelola Data Alat & Tarif</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2 py-1 text-[10px] font-bold text-orange-700 bg-orange-100 rounded-md">Owner Authority Only</span>
            </div>
        </div>
        <div class="flex gap-3">
            <button class="px-4 py-2 text-sm font-bold text-white bg-brand hover:bg-brand-hover rounded-xl shadow-sm flex items-center gap-2 transition-all">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Tambah Master Alat
            </button>
        </div>
    </div>

    <!-- 4 Kartu Metrik Ringkasan (Aman dengan Optional) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Unit Keseluruhan</p>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold text-gray-900">{{ optional($alat)->sum('stok_total') ?? 0 }}</span>
                <span class="text-xs text-gray-500 font-medium">Unit Alat</span>
            </div>
            <div class="mt-3 flex items-center gap-2 text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-100 px-2 py-1 rounded-md w-fit">
                Dari {{ optional($kategori_alat)->count() ?? 0 }} Kategori
            </div>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Rata-rata Tarif Sewa</p>
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-bold text-gray-900">Rp</span>
                <span class="text-3xl font-extrabold text-gray-900">{{ number_format(optional($alat)->avg('tarif_per_hari') ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-gray-500 font-medium">/hari</span>
            </div>
        </div>
        <div class="p-5 bg-white rounded-2xl border border-gray-100 shadow-sm">
            <div class="flex justify-between items-start mb-2">
                <p class="text-xs font-bold text-gray-400 uppercase tracking-wider">Unit Sedang Disewa</p>
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
            </div>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold text-gray-900">{{ optional($alat)->sum('stok_disewa') ?? 0 }}</span>
                <span class="text-xs text-gray-500 font-medium">Unit</span>
            </div>
        </div>
        <div class="p-5 bg-[#FAF7F2] rounded-2xl border border-red-100 shadow-inner">
            <p class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-2">Perlu Maintenance</p>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-extrabold text-red-600">{{ optional($alat)->sum('stok_rusak') ?? 0 }}</span>
                <span class="text-xs text-gray-600 font-medium">Unit Rusak</span>
            </div>
            <div class="mt-3 flex items-center gap-2 text-[10px] font-bold text-red-700 bg-red-100/50 px-2 py-1 rounded-md w-fit">
                <span class="w-2 h-2 rounded-full bg-red-500"></span> Tindakan diperlukan
            </div>
        </div>
    </div>

    <!-- Area Tabel Utama -->
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        
        <!-- Header Tabel & Filter -->
        <div class="p-5 border-b border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <button class="flex items-center gap-2 px-4 py-2 bg-brand text-white text-sm font-bold rounded-xl shadow-sm transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                    Daftar Alat
                </button>
            </div>
            
            <div class="flex items-center gap-3">
                <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Filter Kategori:</span>
                <select name="kategori_id" class="bg-[#FAF7F2] border border-gray-200 text-gray-700 px-3 py-2 rounded-xl text-sm font-medium focus:outline-none focus:border-brand focus:ring-1 focus:ring-brand transition-all cursor-pointer shadow-sm">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori_alat as $kategori)
                    <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama }}
                    </option>
                    @endforeach
                </select>
            </div>
        </div>

        <!-- Tabel Data Dinamis dari Database -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-[#FAF7F2]/50 text-[10px] uppercase tracking-wider text-gray-500 font-bold border-b border-gray-100">
                        <th class="p-4 pl-6">Nama Alat</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Tarif per Hari</th>
                        <th class="p-4">Nilai Pengganti</th>
                        <th class="p-4">Rincian Stok</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="text-sm">
                    @forelse($alat as $item)
                    <tr class="border-b border-gray-50 hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 pl-6">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden flex items-center justify-center">
                                    @if($item->foto)
                                        <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}" class="w-full h-full object-cover">
                                    @else
                                        <span class="text-xs">⛺</span>
                                    @endif
                                </div>
                                <div>
                                    <p class="font-bold text-gray-900">{{ $item->nama }}</p>
                                    <p class="text-[10px] text-gray-400 line-clamp-1 max-w-[200px]">{{ $item->deskripsi }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="p-4">
                            <span class="px-2 py-1 bg-green-50 text-green-700 text-xs font-bold rounded-md border border-green-200">
                                {{ $item->kategori->nama ?? 'Tanpa Kategori' }}
                            </span>
                        </td>
                        <td class="p-4">
                            <p class="font-bold text-brand">Rp {{ number_format($item->tarif_per_hari, 0, ',', '.') }}</p>
                        </td>
                        <td class="p-4 font-semibold text-gray-600">Rp {{ number_format($item->nilai_pengganti, 0, ',', '.') }}</td>
                        <td class="p-4">
                            <div class="flex items-center gap-1.5 text-[10px] font-bold">
                                <span class="bg-emerald-50 text-emerald-700 px-1.5 py-0.5 rounded" title="Tersedia">{{ $item->stok_tersedia }} ✔</span>
                                <span class="bg-amber-50 text-amber-700 px-1.5 py-0.5 rounded" title="Disewa">{{ $item->stok_disewa }} 🛒</span>
                                <span class="bg-red-50 text-red-700 px-1.5 py-0.5 rounded" title="Rusak">{{ $item->stok_rusak }} 🔧</span>
                                <span class="text-gray-400 ml-1">/ {{ $item->stok_total }} Total</span>
                            </div>
                        </td>
                        <td class="p-4">
                            <div class="flex gap-2">
                                <button class="text-gray-400 hover:text-brand transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                                <button class="text-gray-400 hover:text-red-500 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="p-8 text-center text-gray-400 text-sm">
                            Belum ada data alat yang tersimpan di database.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection