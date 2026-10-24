<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OutGear Station - Profil Admin</title>
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'portal-sidebar': '#18181b',
                        'portal-bg': '#f4f2ef',
                        'portal-orange': '#EA580C',
                        'portal-card': '#ffffff',
                    }
                }
            }
        }
    </script>
    @vite('resources/css/app.css')
</head>
<body class="bg-[#F8F6F2] text-slate-800 antialiased font-sans" x-data="{ editMode: false }">

    <div class="flex min-h-screen relative">

        @include('partials.sidebar-admin')

        <!-- KONTEN UTAMA (Kanan) -->
        <main class="flex-grow flex flex-col min-w-0">
            
            <!-- Header Atas (Bersih, Sesuai Standar Portal) -->
            <header class="sticky top-0 bg-[#F8F6F2]/90 backdrop-blur-md px-10 py-4 z-30 border-b border-zinc-200/60 flex items-center justify-between">
                <div></div> <!-- Spacer kosong -->

                <div class="flex items-center gap-3">
                    <div class="flex items-center gap-2 bg-white border border-zinc-200/80 px-4 py-2 rounded-full shadow-sm text-xs font-bold text-emerald-700">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        Basecamp Malang • Online
                    </div>
                    <button class="p-2.5 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm transition" title="Refresh">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                    <div class="w-9 h-9 rounded-full bg-zinc-900 text-white font-bold text-xs flex items-center justify-center border border-zinc-300 shadow-sm">DW</div>
                </div>
            </header>

            <!-- Isi Halaman Profil -->
            <div class="p-10 space-y-8 max-w-[100rem] mx-auto w-full">
                
                <!-- Judul Halaman & Tombol Aksi Edit -->
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-black text-zinc-900 tracking-tight">Profil Administrator</h1>
                        <p class="text-xs text-zinc-500 mt-1">Kelola informasi akun, kredensial login, dan pantau aktivitas operasional admin.</p>
                    </div>

                </div>

                <!-- GRID KONTEN PROFIL (Gaya Modular Card Sesuai Referensi) -->
                <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
                    
                    <!-- KARTU KIRI (Lebar 4): Avatar & Kontak Singkat -->
                    <div class="lg:col-span-4 bg-white p-8 rounded-3xl border border-zinc-200/80 shadow-sm flex flex-col items-center text-center space-y-5">
                        <div class="relative">
                            <div class="w-28 h-28 rounded-full bg-zinc-900 text-white font-black text-3xl flex items-center justify-center border-4 border-zinc-100 shadow-md">
                                DW
                            </div>
                            <span class="absolute bottom-1 right-1 w-5 h-5 bg-emerald-500 border-2 border-white rounded-full" title="Online"></span>
                        </div>

                        <div>
                            <h2 class="text-xl font-black text-zinc-900">Dewi Wahyuni</h2>
                            <p class="text-xs font-bold text-portal-orange mt-0.5">Super Administrator</p>
                        </div>

                        <div class="w-full pt-4 border-t border-zinc-100 space-y-3 text-left text-xs">
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-400 font-medium">Email</span>
                                <span class="font-bold text-zinc-800">dewi.admin@outgear.com</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-400 font-medium">Telepon</span>
                                <span class="font-bold text-zinc-800">+62 812-3456-7890</span>
                            </div>
                            <div class="flex items-center justify-between">
                                <span class="text-zinc-400 font-medium">Basecamp</span>
                                <span class="font-bold text-emerald-700 bg-emerald-50 px-2.5 py-0.5 rounded-full">Malang Pusat</span>
                            </div>
                        </div>
                    </div>

                    <!-- KARTU KANAN (Lebar 8): Informasi Detail & Keamanan -->
                    <div class="lg:col-span-8 space-y-6">
                        
                        <!-- Informasi Umum -->
                        <div class="bg-white p-8 rounded-3xl border border-zinc-200/80 shadow-sm space-y-6">
                            <div class="flex items-center justify-between pb-4 border-b border-zinc-100">
                                <h3 class="font-black text-zinc-900 text-sm">Informasi Akun & Sistem</h3>
                                <span class="text-xs text-zinc-400">ID Admin: #ADM-001</span>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 text-xs">
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5">Nama Lengkap</label>
                                    <input type="text" value="Dewi Wahyuni" :disabled="!editMode" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition disabled:bg-zinc-50/50">
                                </div>
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5">Alamat Email</label>
                                    <input type="email" value="dewi.admin@outgear.com" :disabled="!editMode" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition disabled:bg-zinc-50/50">
                                </div>
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5">Nomor Handphone</label>
                                    <input type="text" value="+62 812-3456-7890" :disabled="!editMode" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition disabled:bg-zinc-50/50">
                                </div>
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5">Tanggal Bergabung</label>
                                    <input type="text" value="15 Januari 2025" disabled class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-3 font-semibold text-zinc-500 cursor-not-allowed">
                                </div>
                            </div>
                        </div>

                        <!-- Keamanan & Ubah Password -->
                        <div class="bg-white p-8 rounded-3xl border border-zinc-200/80 shadow-sm space-y-6">
                            <div class="flex items-center justify-between pb-4 border-b border-zinc-100">
                                <div>
                                    <h3 class="font-black text-zinc-900 text-sm">Keamanan & Sandi</h3>
                                    <p class="text-[11px] text-zinc-400 mt-0.5">Perbarui kata sandi secara berkala untuk menjaga keamanan portal admin.</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-xs">
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5">Sandi Saat Ini</label>
                                    <input type="password" placeholder="••••••••" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2.5 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                                </div>
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5">Sandi Baru</label>
                                    <input type="password" placeholder="••••••••" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2.5 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                                </div>
                                <div>
                                    <label class="block text-zinc-400 font-bold mb-1.5">Konfirmasi Sandi Baru</label>
                                    <input type="password" placeholder="••••••••" class="w-full bg-zinc-50 border border-zinc-200 rounded-xl px-4 py-2.5 font-semibold text-zinc-800 focus:outline-none focus:border-portal-orange transition">
                                </div>
                            </div>

                            <div class="flex justify-end pt-2">
                                <button class="bg-zinc-100 hover:bg-zinc-200 text-zinc-800 text-xs font-bold py-2.5 px-5 rounded-xl transition shadow-sm">
                                    Perbarui Kata Sandi
                                </button>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </main>
    </div>

</body>
</html>