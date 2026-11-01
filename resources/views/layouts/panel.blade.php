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
                @php
                    $peranPanel = auth()->user()?->role?->value ?? auth()->user()?->role;
                    $urlProfilTop = ($peranPanel === 'petugas') ? route('petugas.profile') : route('admin.profile');
                @endphp
                <a href="{{ $urlProfilTop }}"
                    class="flex items-center gap-3 pl-4 border-l border-gray-200 hover:opacity-80 transition cursor-pointer group">
                        <div class="w-9 h-9 rounded-full bg-[#1A2024] text-white flex items-center justify-center font-bold text-xs group-hover:bg-[#D86B43] transition shrink-0">
                            {{ auth()->user()?->inisial() ?? strtoupper(substr(auth()->user()?->name ?? 'P', 0, 2)) }}
                        </div>
                        <div class="text-xs text-left">
                            <p class="font-semibold text-[#1A2024] group-hover:text-[#D86B43] transition leading-tight">{{ auth()->user()?->name ?? 'Petugas Gudang' }}</p>
                            <p class="text-zinc-400 text-[11px] leading-tight mt-0.5">{{ auth()->user()?->email ?? '-' }}</p>
                        </div>
                    </a>
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