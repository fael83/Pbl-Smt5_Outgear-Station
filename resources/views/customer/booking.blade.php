@extends('layouts.customer')

@section('title', 'Booking Saya - OutGear Station')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="{ activeTab: 'semua' }">
    
    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex flex-wrap items-center gap-2.5">
                <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Booking & Status Penyewaan</h1>
                @auth
                    <span class="bg-brand/10 text-brand text-xs font-bold px-3 py-1 rounded-full border border-brand/20">
                        Akun: {{ Auth::user()->name }}
                    </span>
                @endauth
            </div>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">
                @auth
                    Halo <strong class="text-gray-800">{{ Auth::user()->name }}</strong>, berikut riwayat jadwal pengambilan dan status perlengkapan outdoor kamu.
                @else
                    Pantau riwayat, jadwal pengambilan, dan status perlengkapan outdoor kamu.
                @endauth
            </p>
        </div>
        
        <!-- Tombol Aksi -->
        <div class="shrink-0">
            <a href="{{ route('katalog') }}" class="bg-brand hover:bg-brand-hover text-white text-xs font-bold px-5 py-3 rounded-2xl transition shadow-lg shadow-brand/20 inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Sewa Alat Baru</span>
            </a>
        </div>
    </div>

    @php
        $countSemua = $bookings->count();
        $countProses = $bookings->whereIn('status', ['menunggu_pembayaran', 'dikonfirmasi', 'berlangsung'])->count();
        $countSelesai = $bookings->where('status', 'selesai')->count();
        $countBatal = $bookings->where('status', 'dibatalkan')->count();
    @endphp

    <!-- Filter Tab Status -->
    <div class="flex items-center gap-2 border-b border-gray-200 pb-4 mb-8 text-xs font-bold overflow-x-auto">
        <button @click="activeTab = 'semua'" 
                :class="activeTab === 'semua' ? 'bg-gray-900 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300'"
                class="px-4 py-2 rounded-xl transition shrink-0">
            Semua ({{ $countSemua }})
        </button>
        <button @click="activeTab = 'proses'" 
                :class="activeTab === 'proses' ? 'bg-gray-900 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300'"
                class="px-4 py-2 rounded-xl transition shrink-0">
            Sedang Disewa / Proses ({{ $countProses }})
        </button>
        <button @click="activeTab = 'selesai'" 
                :class="activeTab === 'selesai' ? 'bg-gray-900 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300'"
                class="px-4 py-2 rounded-xl transition shrink-0">
            Selesai ({{ $countSelesai }})
        </button>
        @if($countBatal > 0)
        <button @click="activeTab = 'dibatalkan'" 
                :class="activeTab === 'dibatalkan' ? 'bg-gray-900 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300'"
                class="px-4 py-2 rounded-xl transition shrink-0">
            Dibatalkan ({{ $countBatal }})
        </button>
        @endif
    </div>

    <!-- Daftar Kartu Booking Dinamis -->
    <div class="space-y-4">
        @forelse($bookings as $booking)
            @php
                $tabCategory = match($booking->status) {
                    'menunggu_pembayaran', 'dikonfirmasi', 'berlangsung' => 'proses',
                    'selesai' => 'selesai',
                    'dibatalkan' => 'dibatalkan',
                    default => 'proses',
                };

                $lamaHari = $booking->lamaSewa();
                $daftarAlat = $booking->detailPemesanan->map(function($detail) {
                    $nama = $detail->alat->nama ?? 'Perlengkapan Outdoor';
                    return "{$nama} ({$detail->jumlah} Unit)";
                })->join(', ');
            @endphp

            <div x-show="activeTab === 'semua' || activeTab === '{{ $tabCategory }}'" 
                 class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200 shadow-sm hover:shadow-md transition duration-300">
                
                <!-- Top Info -->
                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-2">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="font-mono text-xs font-bold text-gray-700 bg-gray-50 px-3 py-1 rounded-lg border border-gray-200/60">
                            #{{ $booking->kode_pemesanan }}
                        </span>
                        
                        @if($booking->status === 'menunggu_pembayaran')
                            <span class="px-3 py-1 bg-amber-50 text-amber-700 rounded-full text-[11px] font-bold flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span>
                                Menunggu Pembayaran
                            </span>
                        @elseif($booking->status === 'dikonfirmasi')
                            <span class="px-3 py-1 bg-indigo-50 text-indigo-700 rounded-full text-[11px] font-bold flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                Dikonfirmasi / Siap Ambil
                            </span>
                        @elseif($booking->status === 'berlangsung')
                            <span class="px-3 py-1 bg-blue-50 text-blue-700 rounded-full text-[11px] font-bold flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full bg-blue-500 animate-ping"></span>
                                Sedang Disewa
                            </span>
                        @elseif($booking->status === 'selesai')
                            <span class="px-3 py-1 bg-emerald-50 text-emerald-700 rounded-full text-[11px] font-bold flex items-center gap-1.5">
                                ✓ Selesai / Dikembalikan
                            </span>
                        @elseif($booking->status === 'dibatalkan')
                            <span class="px-3 py-1 bg-red-50 text-red-700 rounded-full text-[11px] font-bold flex items-center gap-1.5">
                                ✕ Dibatalkan
                            </span>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400 font-medium">
                        Dipesan pada {{ $booking->created_at?->translatedFormat('d M Y, H:i') ?? '-' }}
                    </div>
                </div>

                <!-- Content -->
                <div class="py-5 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2 flex-1">
                        <h3 class="font-extrabold text-gray-900 text-sm sm:text-base leading-snug">
                            {{ $daftarAlat ?: 'Paket Perlengkapan Outdoor' }}
                        </h3>
                        <div class="inline-flex flex-wrap items-center gap-2 text-xs text-gray-600 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100">
                            <span>📅</span>
                            <span>
                                Ambil: <strong class="text-gray-900">{{ $booking->tanggal_mulai?->translatedFormat('d M Y') ?? '-' }}</strong>
                                &nbsp;—&nbsp;
                                Kembali: <strong class="text-gray-900">{{ $booking->tanggal_selesai?->translatedFormat('d M Y') ?? '-' }}</strong>
                                <span class="text-brand font-bold">({{ $lamaHari }} Hari)</span>
                            </span>
                        </div>
                    </div>

                    <div class="text-left md:text-right shrink-0">
                        <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider block">Total Biaya</span>
                        <span class="text-lg sm:text-xl font-black text-brand">
                            Rp {{ number_format($booking->total_biaya, 0, ',', '.') }}
                        </span>
                        @if($booking->jumlah_dp > 0)
                            <span class="text-[11px] text-gray-500 block">
                                (DP: Rp {{ number_format($booking->jumlah_dp, 0, ',', '.') }})
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Footer -->
                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    <p class="text-xs text-amber-700 bg-amber-50/80 px-3.5 py-2 rounded-xl border border-amber-200/60 flex items-center gap-2">
                        <span>⚠️</span>
                        <span>Wajib membawa <strong>E-KTP asli</strong> atas nama <strong>{{ $booking->pelanggan?->name ?? 'Penyewa' }}</strong> saat pengambilan unit di basecamp.</span>
                    </p>
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('katalog') }}" class="px-4 py-2 border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-xs font-bold transition">
                            Sewa Lagi
                        </a>
                        <a href="https://wa.me/?text=Halo%20Admin%20OutGear%20Station,%20saya%20mau%20konfirmasi%20booking%20%23{{ $booking->kode_pemesanan }}" 
                           target="_blank" 
                           class="px-4 py-2 bg-gray-900 hover:bg-brand text-white rounded-xl text-xs font-bold transition shadow-sm inline-flex items-center gap-1.5">
                            <span>Hubungi CS</span>
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-12 text-center border border-gray-200 shadow-sm">
                <div class="w-16 h-16 bg-gray-100 text-gray-400 rounded-full flex items-center justify-center mx-auto text-2xl mb-4">
                    📦
                </div>
                <h3 class="text-base font-bold text-gray-900 mb-1">Belum Ada Riwayat Booking</h3>
                <p class="text-xs text-gray-500 max-w-md mx-auto mb-6">
                    @auth
                        Akun <strong>{{ Auth::user()->name }}</strong> belum memiliki riwayat pemesanan atau persewaan alat aktif.
                    @else
                        Silakan login untuk melihat riwayat sewa Anda, atau mulai pilih alat outdoor di katalog.
                    @endauth
                </p>
                <a href="{{ route('katalog') }}" class="inline-flex items-center gap-2 bg-brand hover:bg-brand-hover text-white text-xs font-bold px-5 py-3 rounded-2xl transition shadow-lg shadow-brand/20">
                    <span>Mulai Sewa Alat di Katalog</span>
                </a>
            </div>
        @endforelse
    </div>
</div>
@endsection
