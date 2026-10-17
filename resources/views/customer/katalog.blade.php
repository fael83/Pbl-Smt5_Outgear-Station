<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Sewa Alat Outdoor & Pendakian - OutGear Station</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F6F2] text-gray-800 font-sans min-h-screen flex flex-col justify-between">

    <!-- Main Navigation Bar -->
    <nav class="bg-dark text-white px-6 py-3.5 flex items-center justify-between shadow-lg sticky top-0 z-50">
        <!-- Logo Brand -->
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-xl bg-brand flex items-center justify-center font-bold text-white shadow-md text-sm">
                OS
            </div>
            <div>
                <h1 class="font-bold text-sm tracking-wide text-white">OutGear Station</h1>
                <p class="text-[10px] text-gray-400 tracking-wider uppercase">Rental & Outdoor Equipment</p>
            </div>
        </div>

        <!-- Menu Navigasi Utama -->
        <div class="hidden md:flex items-center gap-2 text-xs font-medium">
            <a href="#" class="px-4 py-2 rounded-full bg-brand text-white font-semibold shadow-sm">Katalog</a>
            <a href="#" class="px-3.5 py-2 rounded-full text-gray-300 hover:text-white hover:bg-white/5 transition flex items-center gap-1.5">
                Booking Saya <span class="bg-brand/80 text-white text-[10px] px-1.5 py-0.5 rounded-full font-bold">2</span>
            </a>
            <a href="#" class="px-3.5 py-2 rounded-full text-gray-300 hover:text-white hover:bg-white/5 transition">Chatbot & Bantuan</a>
           <a href="{{ route('panduan') }}" class="px-3.5 py-2 rounded-full text-gray-300 hover:text-white hover:bg-white/5 transition">Panduan Sewa</a>
        </div>

            <!-- Tombol Keranjang -->
            <button class="bg-brand hover:bg-brand-hover text-white text-xs px-3.5 py-2 rounded-full flex items-center gap-2 transition shadow-md">
                <span>🛒 Keranjang</span>
                <span class="bg-white text-brand text-[10px] font-bold px-1.5 py-0.5 rounded-full">3 Item</span>
            </button>

            <!-- Profile Icon -->
            <button class="w-8 h-8 rounded-full bg-gray-700 text-gray-300 flex items-center justify-center text-xs hover:bg-gray-600 transition">
                👤
            </button>
        </div>
    </nav>

    <!-- Header & Form Filter Ketersediaan Realtime -->
    <header class="max-w-7xl mx-auto px-4 sm:px-6 pt-8 pb-4 w-full">
        <div class="bg-[#FAF6F0] rounded-3xl p-6 sm:p-8 border border-amber-900/10 shadow-sm relative overflow-hidden">
            <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-6">
                <div>
                    <h1 class="text-2xl sm:text-3xl font-extrabold text-gray-900 tracking-tight">
                        Katalog Sewa Alat Outdoor & Pendakian
                    </h1>
                    <p class="text-xs sm:text-sm text-gray-600 mt-2 max-w-2xl leading-relaxed">
                        Sewa perlengkapan ultralight, tenda badai, carrier ergonomis, dan peralatan masak gunung. Seluruh unit sudah dicuci dan lolos uji fungsi sebelum diserahkan.
                    </p>
                </div>
                
                <div class="flex sm:flex-col gap-2 shrink-0">
                    <div class="bg-white/80 backdrop-blur px-3.5 py-2 rounded-xl border border-gray-200 text-xs flex items-center gap-2">
                        <span class="text-brand">📅</span>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Status Operasional</p>
                            <p class="font-bold text-gray-800">Booking Buka 24/7</p>
                        </div>
                    </div>
                    <div class="bg-white/80 backdrop-blur px-3.5 py-2 rounded-xl border border-gray-200 text-xs flex items-center gap-2">
                        <span class="text-blue-500">🚚</span>
                        <div>
                            <p class="text-[10px] text-gray-400 font-bold uppercase">Opsi Kirim</p>
                            <p class="font-bold text-gray-800">Pengambilan langsung di basecamp</p>
                        </div>
                    </div>
                </div>
            </div>
    </header>

    <!-- Main Content Katalog -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 w-full flex-grow">
        
        <!-- Filter & Search Section -->
        <div class="space-y-4 mb-6">
            <!-- Secondary Search & Options -->
            <div class="flex flex-wrap items-center justify-between gap-3 bg-white p-3 rounded-2xl border border-gray-200 shadow-sm">
                <div class="flex-1 min-w-[240px] relative">
                    <input id="catalog-search" type="search" placeholder="Cari tenda ultralight, carrier 60L, kompor, sleeping bag..."
                           aria-label="Cari peralatan"
                           class="w-full text-xs bg-gray-50 border border-gray-200 rounded-xl px-3.5 py-2.5 focus:outline-none focus:ring-1 focus:ring-brand">
                </div>

                <select id="category-filter" aria-label="Filter kategori"
                        class="bg-gray-50 border border-gray-200 text-gray-700 px-3 py-2 rounded-xl text-xs font-medium focus:outline-none">
                    <option value="all">Semua Kategori</option>
                    <option value="tenda-shelter">Tenda & Shelter</option>
                    <option value="tas-carrier">Tas Carrier</option>
                    <option value="sleeping-gear">Sleeping Gear</option>
                    <option value="alat-masak">Alat Masak & Kompor</option>
                </select>

                <select id="price-sort" aria-label="Urutkan produk"
                        class="bg-gray-50 border border-gray-200 text-gray-700 px-3 py-2 rounded-xl text-xs font-medium focus:outline-none">
                    <option value="default">Pilihan Harga</option>
                    <option value="price-asc">Harga Terendah</option>
                    <option value="price-desc">Harga Tertinggi</option>
                    <option value="rating-desc">Rating Tertinggi</option>
                </select>
                </div>
            </div>

            <!-- Category Pills Bar -->
            <div class="flex items-center gap-2 overflow-x-auto pb-2 scrollbar-none text-xs font-semibold">
                <button class="px-4 py-2 rounded-full bg-dark text-white shadow-sm shrink-0 flex items-center gap-1.5">
                    <span>⛺</span> Semua Alat <span id="total-product-count" class="bg-white/20 text-white text-[10px] px-1.5 py-0.2 rounded-full">4</span>
                </button>
            </div>
        </div>

        <!-- Section Title & Info Status Real-time -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-sm font-bold text-gray-800 flex items-center gap-2">
                Daftar Peralatan <span class="text-xs font-normal text-gray-500 bg-gray-200 px-2 py-0.5 rounded-full"><span id="visible-product-count" aria-live="polite">4</span> Item Ditampilkan</span>
            </h2>
            <div class="flex items-center gap-1.5 text-[11px] text-emerald-600 font-medium bg-emerald-50 px-2.5 py-1 rounded-full border border-emerald-200">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span>Stok real-time tersinkronisasi</span>
            </div>
        </div>

        <!-- Product Cards Grid (4 Kolom Layout) -->
        <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            
            <!-- CARD 1: Naturehike Cloud Up 2 -->
            <div data-product-card data-category="tenda-shelter" data-price="45000" data-rating="4.9" class="bg-white rounded-2xl border border-gray-200 p-3 flex flex-col justify-between hover:shadow-lg transition group">
                <div>
                    <!-- Image Container -->
                    <div class="relative h-44 rounded-xl bg-gray-100 overflow-hidden mb-3">
                        <img src="https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=500&q=80" alt="Tenda" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <!-- Badges -->
                        <span class="absolute top-2.5 right-2.5 bg-emerald-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded-full backdrop-blur">
                            ● Tersedia (5 Unit)
                        </span>
                        <span class="absolute bottom-2.5 left-2.5 bg-black/60 text-white text-[9px] font-medium px-2 py-0.5 rounded backdrop-blur uppercase tracking-wider">
                            TENDA & SHELTER
                        </span>
                    </div>

                    <!-- Rating & Specs -->
                    <div class="flex justify-between items-center text-[11px] text-gray-500 mb-1">
                        <span class="flex items-center gap-1 font-semibold text-amber-500">⭐ 4.9 <span class="text-gray-400 font-normal">(120)</span></span>
                        <span>Berat: 1.8kg</span>
                    </div>

                    <h3 class="font-bold text-xs text-gray-900 group-hover:text-brand transition line-clamp-1 mb-1">
                        Naturehike Cloud Up 2 Pro...
                    </h3>
                    <p class="text-[10px] text-gray-500 line-clamp-2 mb-3">
                        Kapasitas 2P • Double Layer 20D Silnylon • Waterproof 4000mm • Free Footprint
                    </p>

                    <!-- Features Pills -->
                    <div class="grid grid-cols-2 gap-1.5 mb-3">
                        <div class="bg-gray-50 p-1.5 rounded-lg border border-gray-100 text-center">
                            <p class="text-[9px] text-gray-400">KAPASITAS</p>
                            <p class="text-[10px] font-bold text-gray-700">2 Orang</p>
                        </div>
                        <div class="bg-gray-50 p-1.5 rounded-lg border border-gray-100 text-center">
                            <p class="text-[9px] text-gray-400">KETAHANAN</p>
                            <p class="text-[10px] font-bold text-gray-700">PU 4000mm</p>
                        </div>
                    </div>
                </div>

                <!-- Price & Action -->
                <div class="pt-2 border-t border-gray-100 flex justify-between items-center">
                    <div>
                        <p class="text-[9px] text-gray-400 uppercase font-bold">TARIF SEWA</p>
                        <p class="text-xs font-extrabold text-brand">Rp45.000 <span class="text-[9px] font-normal text-gray-500">/hari</span></p>
                    </div>
                    <button class="bg-brand hover:bg-brand-hover text-white text-xs font-semibold px-3 py-1.5 rounded-xl flex items-center gap-1 shadow-sm transition">
                        <span>🛒</span> Sewa
                    </button>
                </div>
            </div>

            <!-- CARD 2: Osprey Atmos AG 65L -->
            <div data-product-card data-category="tas-carrier" data-price="55000" data-rating="4.95" class="bg-white rounded-2xl border border-gray-200 p-3 flex flex-col justify-between hover:shadow-lg transition group">
                <div>
                    <div class="relative h-44 rounded-xl bg-gray-100 overflow-hidden mb-3">
                        <img src="https://images.unsplash.com/photo-1553062407-98eeb64c6a62?auto=format&fit=crop&w=500&q=80" alt="Carrier" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-emerald-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded-full backdrop-blur">
                            ● Tersedia (4 Unit)
                        </span>
                        <span class="absolute bottom-2.5 left-2.5 bg-black/60 text-white text-[9px] font-medium px-2 py-0.5 rounded backdrop-blur uppercase tracking-wider">
                            TAS CARRIER
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-[11px] text-gray-500 mb-1">
                        <span class="flex items-center gap-1 font-semibold text-amber-500">⭐ 4.95 <span class="text-gray-400 font-normal">(94)</span></span>
                        <span>Beban Max: 22kg</span>
                    </div>

                    <h3 class="font-bold text-xs text-gray-900 group-hover:text-brand transition line-clamp-1 mb-1">
                        Osprey Atmos AG 65L Anti-...
                    </h3>
                    <p class="text-[10px] text-gray-500 line-clamp-2 mb-3">
                        Torso M/L Adjustable • Suspensi Anti-Gravity 3D Mesh • Include Raincover
                    </p>

                    <div class="grid grid-cols-2 gap-1.5 mb-3">
                        <div class="bg-gray-50 p-1.5 rounded-lg border border-gray-100 text-center">
                            <p class="text-[9px] text-gray-400">VOLUME</p>
                            <p class="text-[10px] font-bold text-gray-700">65 Liter</p>
                        </div>
                        <div class="bg-gray-50 p-1.5 rounded-lg border border-gray-100 text-center">
                            <p class="text-[9px] text-gray-400">BACKSYSTEM</p>
                            <p class="text-[10px] font-bold text-gray-700">AG Mesh 3D</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 flex justify-between items-center">
                    <div>
                        <p class="text-[9px] text-gray-400 uppercase font-bold">TARIF SEWA</p>
                        <p class="text-xs font-extrabold text-brand">Rp55.000 <span class="text-[9px] font-normal text-gray-500">/hari</span></p>
                    </div>
                    <button class="bg-brand hover:bg-brand-hover text-white text-xs font-semibold px-3 py-1.5 rounded-xl flex items-center gap-1 shadow-sm transition">
                        <span>🛒</span> Sewa
                    </button>
                </div>
            </div>

            <!-- CARD 3: Eiger Mammoth 4P -->
            <div data-product-card data-category="tenda-shelter" data-price="60000" data-rating="4.8" class="bg-white rounded-2xl border border-gray-200 p-3 flex flex-col justify-between hover:shadow-lg transition group">
                <div>
                    <div class="relative h-44 rounded-xl bg-gray-100 overflow-hidden mb-3">
                        <img src="https://images.unsplash.com/photo-1478828128388-69274296564e?auto=format&fit=crop&w=500&q=80" alt="Tenda Eiger" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-emerald-600/90 text-white text-[10px] font-bold px-2 py-0.5 rounded-full backdrop-blur">
                            ● Tersedia (3 Unit)
                        </span>
                        <span class="absolute bottom-2.5 left-2.5 bg-black/60 text-white text-[9px] font-medium px-2 py-0.5 rounded backdrop-blur uppercase tracking-wider">
                            TENDA & SHELTER
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-[11px] text-gray-500 mb-1">
                        <span class="flex items-center gap-1 font-semibold text-amber-500">⭐ 4.8 <span class="text-gray-400 font-normal">(85)</span></span>
                        <span>Berat: 3.4kg</span>
                    </div>

                    <h3 class="font-bold text-xs text-gray-900 group-hover:text-brand transition line-clamp-1 mb-1">
                        Eiger Mammoth 4P Dome...
                    </h3>
                    <p class="text-[10px] text-gray-500 line-clamp-2 mb-3">
                        Kapasitas 4 Orang Dewasa • Frame Alloy Duralumin 7001 • Teras Depan Luas
                    </p>

                    <div class="grid grid-cols-2 gap-1.5 mb-3">
                        <div class="bg-gray-50 p-1.5 rounded-lg border border-gray-100 text-center">
                            <p class="text-[9px] text-gray-400">KAPASITAS</p>
                            <p class="text-[10px] font-bold text-gray-700">4 Person</p>
                        </div>
                        <div class="bg-gray-50 p-1.5 rounded-lg border border-gray-100 text-center">
                            <p class="text-[9px] text-gray-400">FRAME MATERIAL</p>
                            <p class="text-[10px] font-bold text-gray-700">Alloy 8.5mm</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 flex justify-between items-center">
                    <div>
                        <p class="text-[9px] text-gray-400 uppercase font-bold">TARIF SEWA</p>
                        <p class="text-xs font-extrabold text-brand">Rp60.000 <span class="text-[9px] font-normal text-gray-500">/hari</span></p>
                    </div>
                    <button class="bg-brand hover:bg-brand-hover text-white text-xs font-semibold px-3 py-1.5 rounded-xl flex items-center gap-1 shadow-sm transition">
                        <span>🛒</span> Sewa
                    </button>
                </div>
            </div>

            <!-- CARD 4: Deuter Aircontact Pro (Habis) -->
            <div data-product-card data-category="tas-carrier" data-price="65000" data-rating="4.9" class="bg-white rounded-2xl border border-gray-200 p-3 flex flex-col justify-between opacity-80 hover:opacity-100 transition group">
                <div>
                    <div class="relative h-44 rounded-xl bg-gray-100 overflow-hidden mb-3">
                        <img src="https://images.unsplash.com/photo-1622260614153-03223fb72052?auto=format&fit=crop&w=500&q=80" alt="Deuter" class="w-full h-full object-cover grayscale group-hover:grayscale-0 transition duration-300">
                        <span class="absolute top-2.5 right-2.5 bg-gray-800/90 text-gray-300 text-[10px] font-bold px-2 py-0.5 rounded-full backdrop-blur">
                            ● Habis Tanggal Ini
                        </span>
                        <span class="absolute bottom-2.5 left-2.5 bg-black/60 text-white text-[9px] font-medium px-2 py-0.5 rounded backdrop-blur uppercase tracking-wider">
                            TAS CARRIER
                        </span>
                    </div>

                    <div class="flex justify-between items-center text-[11px] text-gray-500 mb-1">
                        <span class="flex items-center gap-1 font-semibold text-amber-500">⭐ 4.9 <span class="text-gray-400 font-normal">(67)</span></span>
                        <span class="text-gray-400">Expedition Tier</span>
                    </div>

                    <h3 class="font-bold text-xs text-gray-900 group-hover:text-brand transition line-clamp-1 mb-1">
                        Deuter Aircontact Pro 70+15L
                    </h3>
                    <p class="text-[10px] text-gray-500 line-clamp-2 mb-3">
                        Heavy Load Trekking • VariQuick System • Ergonomic Hip Belt ActiveFit
                    </p>

                    <div class="grid grid-cols-2 gap-1.5 mb-3">
                        <div class="bg-gray-50 p-1.5 rounded-lg border border-gray-100 text-center">
                            <p class="text-[9px] text-gray-400">VOLUME</p>
                            <p class="text-[10px] font-bold text-gray-700">70 + 15 L</p>
                        </div>
                        <div class="bg-gray-50 p-1.5 rounded-lg border border-gray-100 text-center">
                            <p class="text-[9px] text-gray-400">SISTEM PUNGGUNG</p>
                            <p class="text-[10px] font-bold text-gray-700">Aircontact Pro</p>
                        </div>
                    </div>
                </div>

                <div class="pt-2 border-t border-gray-100 flex justify-between items-center">
                    <div>
                        <p class="text-[9px] text-gray-400 uppercase font-bold">TARIF SEWA</p>
                        <p class="text-xs font-extrabold text-gray-700">Rp65.000 <span class="text-[9px] font-normal text-gray-500">/hari</span></p>
                    </div>
                    <button class="bg-gray-200 text-gray-600 text-xs font-semibold px-3 py-1.5 rounded-xl flex items-center gap-1 cursor-not-allowed">
                        <span>🔔</span> Antre Stok
                    </button>
                </div>
            </div>

        </div>

        <p id="empty-product-results" class="hidden rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500" role="status">
            Tidak ada peralatan yang cocok dengan pencarian atau filter ini.
        </p>

        <!-- Pagination Bar -->
        <div class="mt-8 flex flex-col sm:flex-row justify-between items-center gap-4 pt-4 border-t border-gray-200">
            <p class="text-xs text-gray-500">
                Menampilkan <span id="pagination-product-count" class="font-bold text-gray-800">4</span> dari <span class="font-bold text-gray-800">4</span> peralatan outdoor
            </p>

            <button class="bg-white hover:bg-gray-50 border border-gray-200 text-gray-800 text-xs font-bold px-4 py-2 rounded-full shadow-sm transition">
                + Muat Lebih Banyak Alat (+12)
            </button>

            <div class="flex items-center gap-1 text-xs font-semibold">
                <button class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-400 hover:bg-gray-100">‹</button>
                <button class="w-8 h-8 rounded-full bg-dark text-white flex items-center justify-center">1</button>
                <button class="w-8 h-8 rounded-full hover:bg-gray-200 flex items-center justify-center text-gray-700">2</button>
                <button class="w-8 h-8 rounded-full hover:bg-gray-200 flex items-center justify-center text-gray-700">3</button>
                <span class="px-1 text-gray-400">...</span>
                <button class="w-8 h-8 rounded-full hover:bg-gray-200 flex items-center justify-center text-gray-700">18</button>
                <button class="w-8 h-8 rounded-full border border-gray-200 flex items-center justify-center text-gray-700 hover:bg-gray-100">›</button>
            </div>
        </div>
    </main>

    <!-- Dark Footer -->
    <footer class="bg-dark text-gray-400 text-xs pt-10 pb-6 border-t border-gray-800">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 grid grid-cols-1 md:grid-cols-4 gap-8 mb-8">
            <!-- Brand Info -->
            <div>
                <div class="flex items-center gap-2 mb-3">
                    <div class="w-8 h-8 rounded-lg bg-brand flex items-center justify-center font-bold text-white text-xs">OS</div>
                    <span class="font-bold text-white text-sm">OutGear Station</span>
                </div>
                <p class="text-[11px] leading-relaxed mb-4 text-gray-400">
                    Penyedia persewaan perlengkapan outdoor, mountaineering & camping terpercaya sejak 2021.
                </p>
            </div>

            <!-- Quick Links -->
            <div>
                <h4 class="font-bold text-white text-xs mb-3 uppercase tracking-wider">Akses Cepat</h4>
                <ul class="space-y-2 text-[11px]">
                    <li><a href="#" class="hover:text-white transition">Katalog Alat</a></li>
                    <li><a href="#" class="hover:text-white transition">Prosedur Sewa & Jaminan</a></li>
                    <li><a href="#" class="hover:text-white transition">Paket Ekspedisi</a></li>
                    <li><a href="#" class="hover:text-white transition">Cek Denda & Standar QC</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div>
                <h4 class="font-bold text-white text-xs mb-3 uppercase tracking-wider">Kontak Basecamp</h4>
                <p class="text-[11px] mb-2 leading-relaxed">
                    Jalan Semeru No.12 Kota Malang
                </p>
                <p class="text-[11px] text-gray-300">🕒 Buka Setiap Hari: 09.00 - 22.00 WIB</p>
                <p class="text-[11px] text-emerald-400 font-medium mt-1">💬 WhatsApp 24/7 Aktif</p>
            </div>

            <!-- Payment Methods -->
            <div>
                <h4 class="font-bold text-white text-xs mb-3 uppercase tracking-wider">Pembayaran & Keamanan</h4>
                <p class="text-[11px] mb-3">Opsi Pembayaran:</p>
                <div class="flex flex-wrap gap-1.5 text-[9px] font-bold text-gray-300">
                    <span class="bg-[#242B30] px-2 py-1 rounded border border-gray-700">QRIS</span>
                    <span class="bg-[#242B30] px-2 py-1 rounded border border-gray-700">Tunai</span>
                </div>
            </div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 pt-4 border-t border-gray-800 flex flex-col sm:flex-row justify-between items-center text-[10px] text-gray-500">
            <p>© 2026 OutGear Station. All rights reserved.</p>
            <div class="flex gap-4 mt-2 sm:mt-0">
                <a href="#" class="hover:text-gray-400">Syarat & Ketentuan</a>
                <a href="#" class="hover:text-gray-400">Kebijakan Privasi</a>
            </div>
        </div>
    </footer>

    <script>
        const productGrid = document.getElementById('product-grid');
        const searchInput = document.getElementById('catalog-search');
        const categoryFilter = document.getElementById('category-filter');
        const sortSelector = document.getElementById('price-sort');
        const productCards = Array.from(productGrid.querySelectorAll('[data-product-card]'));
        const visibleCount = document.getElementById('visible-product-count');
        const paginationCount = document.getElementById('pagination-product-count');
        const totalCount = document.getElementById('total-product-count');
        const emptyResults = document.getElementById('empty-product-results');

        totalCount.textContent = productCards.length;

        function updateCatalog() {
            const searchTerm = searchInput.value.trim().toLocaleLowerCase('id-ID');
            const selectedCategory = categoryFilter.value;
            const sortBy = sortSelector.value;
            const matchingCards = [];

            productCards.forEach((card) => {
                const matchesSearch = card.textContent.toLocaleLowerCase('id-ID').includes(searchTerm);
                const matchesCategory = selectedCategory === 'all' || card.dataset.category === selectedCategory;
                const isVisible = matchesSearch && matchesCategory;

                card.hidden = !isVisible;
                card.classList.toggle('hidden', !isVisible);

                if (isVisible) {
                    matchingCards.push(card);
                }
            });

            if (sortBy === 'price-asc') {
                matchingCards.sort((first, second) => Number(first.dataset.price) - Number(second.dataset.price));
            } else if (sortBy === 'price-desc') {
                matchingCards.sort((first, second) => Number(second.dataset.price) - Number(first.dataset.price));
            } else if (sortBy === 'rating-desc') {
                matchingCards.sort((first, second) => Number(second.dataset.rating) - Number(first.dataset.rating));
            }

            matchingCards.forEach((card) => productGrid.append(card));
            visibleCount.textContent = matchingCards.length;
            paginationCount.textContent = matchingCards.length;
            emptyResults.classList.toggle('hidden', matchingCards.length > 0);
        }

        searchInput.addEventListener('input', updateCatalog);
        categoryFilter.addEventListener('change', updateCatalog);
        sortSelector.addEventListener('change', updateCatalog);
    </script>
</body>
</html>