<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Admin Portal</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-theme antialiased font-sans"
      x-data="halamanDashboard({
        dataUrl: {{ Js::from(route('admin.dashboard.data')) }},
        jumlahVerifikasi: {{ (int) $statistik['perlu_verifikasi'] }}
      })">

    <div class="flex min-h-screen">

        <!-- Sidebar Admin -->
        @include('partials.sidebar-admin')

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas: judul, status basecamp, aksi cepat, dan profil pengguna -->
            <header class="sticky top-0 bg-portal-bg/90 backdrop-blur-md px-8 py-3 z-40 border-b border-zinc-200/60 flex items-center justify-between gap-4">
                <div>
                    <h2 class="text-2xl font-bold text-zinc-900 tracking-tight">Dashboard Operasional</h2>
                    <p class="text-xs text-zinc-500 mt-0.5">
                        {{ \Carbon\Carbon::now()->locale('id')->translatedFormat('l, d F Y') }}
                    </p>
                </div>

                <!-- Status & User Action -->
                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-3.5 py-1.5 rounded-full shadow-sm text-xs font-medium text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Basecamp Malang • Online
                    </div>

                    <!-- Muat ulang data: isi dashboard diambil ulang lewat AJAX, tanpa reload -->
                    <a href="{{ route('admin.dashboard', ['segarkan' => 1]) }}"
                       @click.prevent="muatUlang()"
                       :class="memuat ? 'pointer-events-none opacity-60' : ''"
                       class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm transition flex items-center justify-center"
                       :title="memuat ? 'Memuat data…' : 'Muat ulang data'">
                        <svg class="w-4 h-4" :class="memuat ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                    <a href="/admin/verifikasi"
                       class="p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm relative transition"
                       :title="judulVerifikasi">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <span x-cloak x-show="jumlahVerifikasi > 0" class="absolute top-1 right-1 w-2 h-2 bg-portal-orange rounded-full"></span>
                    </a>

                    <!-- Profil pengguna, tampilannya sama dengan topbar halaman data peralatan -->
                    <a href="{{ route('admin.profile') }}" title="Buka profil admin"
                       class="flex items-center gap-3 pl-4 border-l border-zinc-200 hover:opacity-80 transition">
                        <div class="w-8 h-8 rounded-full bg-[#1A2024] text-white flex items-center justify-center font-bold text-xs">
                            {{ auth()->user()?->inisial() ?? 'AD' }}
                        </div>
                        <div class="text-xs">
                            <p class="font-semibold text-[#1A2024]">{{ auth()->user()?->name ?? 'Admin Toko' }}</p>
                            <p class="text-gray-500">{{ auth()->user()?->email ?? '-' }}</p>
                        </div>
                    </a>
                </div>
            </header>

            <!-- Isi halaman (diperbarui lewat AJAX, tanpa memuat ulang halaman) -->
            <div class="relative flex-1">
                <div x-ref="isi" :class="memuat ? 'opacity-60' : ''" class="p-8 space-y-6 transition-opacity">
                    @include('admin.dashboard._isi')
                </div>

                <!-- Penanda proses saat data diambil ulang -->
                <div x-cloak x-show="memuat" class="absolute inset-x-0 top-0 flex justify-center pt-10 pointer-events-none">
                    <div class="flex items-center gap-2 bg-[#1A2024] text-white text-xs font-semibold px-4 py-2 rounded-full shadow-lg">
                        <span class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                        Memuat data…
                    </div>
                </div>
            </div>
        </main>
    </div>

    <!-- Notifikasi ringan (mis. gagal memuat ulang data) -->
    <div x-cloak x-show="pesan" x-transition.opacity
         class="fixed bottom-6 right-6 z-50 max-w-sm px-4 py-3 rounded-xl shadow-lg text-xs font-semibold border"
         :class="pesanGagal ? 'bg-red-50 text-red-700 border-red-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100'">
        <span x-text="pesan"></span>
    </div>

</body>
</html>
