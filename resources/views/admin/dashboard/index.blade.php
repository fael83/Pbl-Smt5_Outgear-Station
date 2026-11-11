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
        dataUrl: {{ Js::from(\Illuminate\Support\Facades\Route::has('admin.dashboard.data') ? route('admin.dashboard.data') : route('admin.dashboard')) }},
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
                    @include('partials.tombol-muat-ulang', ['fallback' => route('admin.dashboard', ['segarkan' => 1])])
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
            </div>
        </main>
    </div>

    @include('partials.status-muat')

</body>
</html>
