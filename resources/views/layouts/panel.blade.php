<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'Admin Panel' }} - Outgear Station</title>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="admin-theme font-sans antialiased min-h-screen flex flex-col">
    <div class="flex flex-1 min-h-screen">
        <!-- Sidebar navigasi -->
        @if(auth()->check() && (auth()->user()->role === 'petugas' || optional(auth()->user()->role)->value === 'petugas' || auth()->user()->role == 'petugas'))
            @includeIf('partials.sidebar-petugas')
        @else
            @includeIf('partials.sidebar-admin')
        @endif

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="bg-transparent px-6 py-4 flex items-center justify-between sticky top-0 z-10">
                <div class="flex items-center gap-4">
                    <!-- Kosong atau bisa diisi breadcrumb -->
                </div>
                <div class="flex items-center gap-4">
                    <button class="relative p-2 text-gray-600 hover:text-[#1A2024]">
                        <!-- Notifikasi opsional -->
                    </button>
                <!-- Profil pengguna: foto profil, nama, dan email (partial bersama) -->
                @include('partials.profil-topbar')
                </div>
            </header>

            <!-- Tempat Render Konten Dashboard -->
            <main class="flex-1 p-6 space-y-6 overflow-y-auto">
                @includeIf('partials.flash-message')
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>