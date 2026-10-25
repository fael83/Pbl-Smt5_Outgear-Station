@php
    $penggunaPanel = auth()->user();

    // Badge sidebar petugas (bisa disesuaikan dengan kebutuhan query tugas gudang jika ada)
    $badgePetugas = \Illuminate\Support\Facades\Cache::remember(
        'sidebar:badge:petugas',
        now()->addMinutes(5),
        static fn (): array => [
            // Contoh: mengambil data barang perlu dicek atau pemesanan hari ini
            'tugas_masuk' => \App\Models\Pemesanan::query()->whereDate('tanggal_mulai', today())->count(),
        ],
    );
    $jumlahTugasHariIni = $badgePetugas['tugas_masuk'];
@endphp
<aside class="sticky top-0 h-screen w-64 bg-portal-sidebar text-white flex flex-col flex-shrink-0 z-40 select-none">
    
    <!-- Logo & Brand -->
    <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-portal-orange flex items-center justify-center font-black text-white text-sm shadow-md">OS</div>
            <div>
                <h1 class="font-extrabold text-sm tracking-wide text-white leading-tight">OutGear Station</h1>
                <span class="text-[9px] tracking-widest text-portal-orange font-bold uppercase">PETUGAS GUDANG</span>
            </div>
        </div>
    </div>

    <!-- Navigasi Menu (Sesuai Use Case Petugas Gudang) -->
    <nav class="flex-grow px-4 py-4 space-y-1.5 overflow-y-auto text-sm">
        <!-- Dashboard -->
        <a href="{{ route('petugas.dashboard') }}" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('petugas.dashboard') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Dashboard
        </a>

        <!-- 1. Persiapan & Serah Terima Barang -->
        <a href="{{ route('petugas.persiapan') }}" 
   class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->routeIs('petugas.persiapan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7v10l8 4"></path></svg>
    Persiapan & Serah Terima
</a>

        <!-- 2. Checklist Kondisi Awal -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
            Checklist Kondisi Awal
        </a>

        <!-- 3. Ubah Status Barang -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
            Ubah Status Barang
        </a>

        <!-- 4. Pengembalian Barang -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 15l-3-3m0 0l3-3m-3 3h8M3 12a9 9 0 1118 0 9 9 0 01-18 0z"></path></svg>
            Pengembalian Barang
        </a>

        <!-- 5. Kerusakan & Kehilangan -->
        <a href="#" class="flex items-center gap-3 px-4 py-3 rounded-xl font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
            Kerusakan & Kehilangan
        </a>
    </nav>

    <!-- Profil User & Logout (Konsisten dengan Sidebar Admin) -->
    <div class="p-3 mx-3 mb-4 rounded-2xl bg-zinc-900/90 border border-zinc-800 flex items-center justify-between shadow-inner">
        <div class="flex items-center gap-2.5 min-w-0">
            <div class="w-8 h-8 rounded-full bg-portal-orange font-bold text-xs flex items-center justify-center text-white shrink-0">
                {{ $penggunaPanel?->inisial() ?? 'PG' }}
            </div>
            <div class="leading-tight min-w-0">
                <div class="font-semibold text-xs text-white truncate max-w-[120px]">{{ $penggunaPanel?->name ?? 'Petugas Gudang' }}</div>
                <div class="text-[10px] text-zinc-400">Staff Gudang</div>
            </div>
        </div>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" title="Keluar" class="text-zinc-400 hover:text-white">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
            </button>
        </form>
    </div>
</aside>