@extends('layouts.panel', ['title' => 'Dashboard Petugas Gudang'])

@section('content')
<div class="space-y-6">

    <!-- Header Judul & Tanggal Real-time -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-black text-[#1A2024] tracking-tight">Dashboard Operasional Gudang</h1>
            <p class="text-xs text-zinc-500 mt-0.5">
                {{ \Carbon\Carbon::now()->translatedFormat('l, d F Y') }} · data pukul {{ date('H:i') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
        </div>
    </div>

    <!-- 3 Kotak Ringkasan / Kartu Statistik di Atas -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        
        <!-- Kotak 1: Ambil Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Jadwal Ambil Hari Ini</span>
            <div class="my-2">
                <p class="text-3xl font-black font-heading text-[#D86B43]">{{ $ringkasan['total_ambil'] }}</p>
            </div>
            <div class="text-[11px] text-zinc-500 font-medium pt-2 border-t border-zinc-100">
                Penyewa mengambil alat hari ini
            </div>
        </div>

        <!-- Kotak 2: Kembali Hari Ini -->
        <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Jadwal Kembali Hari Ini</span>
            <div class="my-2">
                <p class="text-3xl font-black font-heading text-blue-600">{{ $ringkasan['total_kembali'] }}</p>
            </div>
            <div class="text-[11px] text-zinc-500 font-medium pt-2 border-t border-zinc-100">
                Batas pengembalian alat hari ini
            </div>
        </div>

        <!-- Kotak 3: Total Rusak -->
        <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
            <span class="text-[10px] font-bold uppercase tracking-wider text-zinc-400">Total Unit Rusak</span>
            <div class="my-2">
                <p class="text-3xl font-black font-heading text-rose-600">{{ $ringkasan['total_rusak'] }}</p>
            </div>
            <div class="text-[11px] text-zinc-500 font-medium pt-2 border-t border-zinc-100">
                Unit butuh perawatan/perbaikan
            </div>
        </div>

    </div>

    <!-- Grid Utama: Data Dipisah di Kiri, Panel Aktivitas di Kanan -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Kiri (2 Kolom): Tabel Terpisah (Jadwal Ambil & Jadwal Kembali) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- 1. Tabel Jadwal Ambil Hari Ini -->
            <div class="bg-white rounded-2xl border border-zinc-200/70 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                    <div>
                        <h2 class="text-xs font-black text-zinc-900 uppercase tracking-wider">Jadwal Pengambilan Alat Hari Ini</h2>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Daftar pelanggan yang dijadwalkan mengambil alat dari gudang.</p>
                    </div>
                    <a href="{{ route('petugas.persiapan') }}" class="text-xs font-bold text-[#15AEEA] hover:underline">Kelola Persiapan →</a>
                </div>

                <div class="divide-y divide-zinc-100">
                    @forelse($ambilHariIni as $pesanan)
                        <div class="p-4 flex items-center justify-between gap-4 hover:bg-zinc-50/50 transition">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-extrabold text-[#D86B43]">{{ $pesanan->kode_pemesanan }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-orange-50 text-orange-700 border border-orange-200/60">Ambil Hari Ini</span>
                                </div>
                                <p class="text-xs font-bold text-zinc-900 mt-1">{{ $pesanan->pelanggan->name ?? 'Pelanggan' }}</p>
                                <p class="text-[11px] text-zinc-400">Periode Sewa: {{ $pesanan->tanggal_mulai }} s/d {{ $pesanan->tanggal_selesai }}</p>
                            </div>
                            <div>
                                <a href="{{ route('petugas.persiapan') }}" class="px-3 py-1.5 rounded-xl bg-zinc-100 text-zinc-700 hover:bg-zinc-200 text-xs font-bold transition shadow-2xs">
                                    Detail →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-zinc-400 italic text-xs">
                            Tidak ada jadwal pengambilan alat untuk hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

            <!-- 2. Tabel Jadwal Pengembalian Hari Ini -->
            <div class="bg-white rounded-2xl border border-zinc-200/70 shadow-sm overflow-hidden">
                <div class="p-5 border-b border-zinc-100 flex items-center justify-between bg-zinc-50/50">
                    <div>
                        <h2 class="text-xs font-black text-zinc-900 uppercase tracking-wider">Jadwal Pengembalian Alat Hari Ini</h2>
                        <p class="text-[11px] text-zinc-500 mt-0.5">Daftar pelanggan yang harus mengembalikan alat ke gudang hari ini.</p>
                    </div>
                    <a href="{{ route('petugas.pengembalian') }}" class="text-xs font-bold text-[#15AEEA] hover:underline">Kelola Pengembalian →</a>
                </div>

                <div class="divide-y divide-zinc-100">
                    @forelse($kembaliHariIni as $pesanan)
                        <div class="p-4 flex items-center justify-between gap-4 hover:bg-zinc-50/50 transition">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="font-mono text-xs font-extrabold text-blue-600">{{ $pesanan->kode_pemesanan }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 text-blue-700 border border-blue-200/60">Kembali Hari Ini</span>
                                </div>
                                <p class="text-xs font-bold text-zinc-900 mt-1">{{ $pesanan->pelanggan->name ?? 'Pelanggan' }}</p>
                                <p class="text-[11px] text-zinc-400">Periode Sewa: {{ $pesanan->tanggal_mulai }} s/d {{ $pesanan->tanggal_selesai }}</p>
                            </div>
                            <div>
                                <a href="{{ route('petugas.pengembalian') }}" class="px-3 py-1.5 rounded-xl bg-zinc-100 text-zinc-700 hover:bg-zinc-200 text-xs font-bold transition shadow-2xs">
                                    Proses →
                                </a>
                            </div>
                        </div>
                    @empty
                        <div class="py-8 text-center text-zinc-400 italic text-xs">
                            Tidak ada jadwal pengembalian alat untuk hari ini.
                        </div>
                    @endforelse
                </div>
            </div>

        </div>

        <!-- Kanan (1 Kolom): Panel Aktivitas Lapangan -->
        <div class="bg-white rounded-2xl border border-zinc-200/70 shadow-sm p-5 space-y-4 flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between border-b border-zinc-100 pb-3">
                    <div>
                        <h3 class="text-xs font-black text-zinc-900 uppercase tracking-wider">Status Aktivitas Lapangan</h3>
                        <p class="text-[11px] text-zinc-400 mt-0.5">Monitoring peminjaman aktif hari ini</p>
                    </div>
                    <a href="{{ route('petugas.pengembalian') }}" class="text-[11px] font-bold text-[#15AEEA] hover:underline">Kelola →</a>
                </div>

                <!-- Ringkasan Mini Angka -->
                <div class="grid grid-cols-2 gap-3 my-4">
                    <div class="bg-orange-50/60 p-3 rounded-xl border border-orange-100 text-center">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-orange-600 block">Total Ambil</span>
                        <span class="text-xl font-black text-orange-700 font-heading">{{ $ringkasan['total_ambil'] }}</span>
                    </div>
                    <div class="bg-blue-50/60 p-3 rounded-xl border border-blue-100 text-center">
                        <span class="text-[10px] font-bold uppercase tracking-wider text-blue-600 block">Total Kembali</span>
                        <span class="text-xl font-black text-blue-700 font-heading">{{ $ringkasan['total_kembali'] }}</span>
                    </div>
                </div>

                <!-- Daftar Ringkas Agenda Hari Ini -->
                <div class="space-y-2 pt-1">
                    <p class="text-[11px] font-bold text-zinc-500 uppercase tracking-wider">Daftar Agenda Terdekat:</p>
                    
                    @if($ambilHariIni->count() > 0)
                        @foreach($ambilHariIni->take(3) as $agenda)
                            <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-zinc-50 border border-zinc-100">
                                <div>
                                    <span class="font-bold text-zinc-800 block truncate max-w-[130px]">{{ $agenda->pelanggan->name ?? 'Pelanggan' }}</span>
                                    <span class="text-[10px] font-mono text-[#D86B43]">{{ $agenda->kode_pemesanan }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-md bg-orange-100 text-orange-800 text-[10px] font-bold">Ambil</span>
                            </div>
                        @endforeach
                    @elseif($kembaliHariIni->count() > 0)
                        @foreach($kembaliHariIni->take(3) as $agenda)
                            <div class="flex items-center justify-between text-xs p-2.5 rounded-xl bg-zinc-50 border border-zinc-100">
                                <div>
                                    <span class="font-bold text-zinc-800 block truncate max-w-[130px]">{{ $agenda->pelanggan->name ?? 'Pelanggan' }}</span>
                                    <span class="text-[10px] font-mono text-blue-600">{{ $agenda->kode_pemesanan }}</span>
                                </div>
                                <span class="px-2 py-0.5 rounded-md bg-blue-100 text-blue-800 text-[10px] font-bold">Kembali</span>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center py-6 text-zinc-400 text-xs italic bg-zinc-50/50 rounded-xl border border-dashed border-zinc-200">
                            Tidak ada agenda aktif saat ini.
                        </div>
                    @endif
                </div>
            </div>

    </div>
</div>
@endsection