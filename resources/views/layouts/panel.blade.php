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
        @includeIf('partials.sidebar-admin')

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0">
            <!-- Topbar -->
            <header class="bg-transparent px-6 py-4 flex items-center justify-between sticky top-0 z-10">
                <div class="flex items-center gap-4 w-1/3">
                </div>
                <div class="flex items-center gap-4">
                    <button class="relative p-2 text-gray-600 hover:text-[#1A2024]">
                    </button>
                    <div class="flex items-center gap-3 pl-4 border-l border-gray-200">
                        <div class="w-8 h-8 rounded-full bg-[#1A2024] text-white flex items-center justify-center font-bold text-xs">
                            {{ auth()->user()?->inisial() ?? 'AD' }}
                        </div>
                        <div class="text-xs">
                            <p class="font-semibold text-[#1A2024]">{{ auth()->user()?->name ?? 'Admin Toko' }}</p>
                            <p class="text-gray-500">{{ auth()->user()?->email ?? '-' }}</p>
                        </div>
                    </div>
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