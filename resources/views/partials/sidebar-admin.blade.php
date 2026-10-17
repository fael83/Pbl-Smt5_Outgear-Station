<aside class="sticky top-0 h-screen w-64 bg-portal-sidebar text-white flex flex-col flex-shrink-0 z-40 select-none">
    
    <!-- Logo & Brand -->
    <div class="p-6 border-b border-zinc-800 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-portal-orange flex items-center justify-center font-black text-white text-sm shadow-md">O</div>
            <div>
                <h1 class="font-extrabold text-sm tracking-wide text-white leading-tight">OutGear Station</h1>
                <span class="text-[9px] tracking-widest text-portal-orange font-bold uppercase">ADMIN PORTAL</span>
            </div>
        </div>
    </div>

    <!-- Navigasi Menu -->
    <nav class="flex-grow px-4 py-4 space-y-1.5 overflow-y-auto text-sm">
        <!-- Dashboard -->
        <a href="/admin" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin') && !request()->is('admin/*') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
            Dashboard
        </a>

        <!-- Data Pelanggan -->
        <a href="/admin/pelanggan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/pelanggan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
            Data Pelanggan
        </a>

        <!-- Data Peralatan -->
        <a href="/admin/peralatan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/peralatan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
            Data Peralatan
        </a>

        <!-- Data Booking -->
        <a href="/admin/pemesanan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/pemesanan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            Data Booking
        </a>

        <!-- Verifikasi Pembayaran -->
        <a href="/admin/verifikasi" class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->is('admin/verifikasi') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Verifikasi Pembayaran
            </div>
            <span class="bg-amber-500 text-zinc-950 font-bold text-xs px-2 py-0.5 rounded-full">5</span>
        </a>

        <!-- Kelola Denda -->
        <a href="/admin/denda" class="flex items-center justify-between px-4 py-3 rounded-xl {{ request()->is('admin/denda') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
            <div class="flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Kelola Denda
            </div>
            <span class="w-2 h-2 bg-red-500 rounded-full"></span>
        </a>

        <!-- Laporan -->
        <a href="/admin/laporan" class="flex items-center gap-3 px-4 py-3 rounded-xl {{ request()->is('admin/laporan') ? 'font-semibold bg-portal-orange text-white shadow-md shadow-orange-900/20' : 'font-medium text-zinc-400 hover:text-white hover:bg-zinc-800/60 transition' }}">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
            Laporan
        </a>
    </nav>

    <!-- Profil User -->
    <div class="p-3 mx-3 mb-4 rounded-2xl bg-zinc-900/90 border border-zinc-800 flex items-center justify-between shadow-inner">
        <div class="flex items-center gap-2.5">
            <div class="w-8 h-8 rounded-full bg-portal-orange font-bold text-xs flex items-center justify-center text-white">DW</div>
            <div class="leading-tight">
                <div class="font-semibold text-xs text-white">Dimas Wicaksono</div>
                <div class="text-[10px] text-zinc-400">Staff Operasional</div>
            </div>
        </div>
        <button class="text-zinc-400 hover:text-white">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
        </button>
    </div>
</aside>