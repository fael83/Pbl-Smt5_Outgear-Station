<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Sewa Alat Outdoor & Pendakian - OutGear Station</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-[#F8F6F2] text-gray-800 font-sans min-h-screen flex flex-col justify-between"
      x-data="{ 
          cart: [], 
          isCartOpen: false,
          
          addToCart(item) {
              let existing = this.cart.find(i => i.id === item.id);
              if (existing) {
                  existing.qty += 1;
              } else {
                  this.cart.push({ ...item, qty: 1, selected: true });
              }
              this.isCartOpen = true; 
          },
          
          increaseQty(id) {
              let item = this.cart.find(i => i.id === id);
              if (item) item.qty += 1;
          },
          
          decreaseQty(id) {
              let item = this.cart.find(i => i.id === id);
              if (item && item.qty > 1) item.qty -= 1;
          },
          
          removeFromCart(id) {
              this.cart = this.cart.filter(i => i.id !== id);
          },
          
          get totalItems() {
              return this.cart.reduce((sum, item) => sum + item.qty, 0);
          },

          get totalPrice() {
              return this.cart.filter(i => i.selected).reduce((sum, item) => sum + (item.tarif * item.qty), 0);
          }
      }">

    <!-- Main Navigation Bar -->
    <nav class="bg-dark text-white px-8 py-6 flex items-center justify-between shadow-xl sticky top-0 z-50 border-b border-white/10 w-full">
        <!-- Logo Brand -->
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 rounded-2xl bg-brand flex items-center justify-center font-black text-white shadow-xl shadow-brand/30 text-base tracking-wider">
                OS
            </div>
            <div>
                <h1 class="font-black text-base tracking-wide text-white leading-tight">OutGear Station</h1>
                <p class="text-[11px] text-gray-400 tracking-widest uppercase font-semibold">Rental & Outdoor Equipment</p>
            </div>
        </div>

        <!-- Menu Navigasi Utama -->
        <div class="hidden md:flex items-center gap-10 text-sm font-bold tracking-wide">
            <a href="#" class="text-brand transition-colors duration-200">Katalog</a>
            <a href="{{ route('booking.saya') }}" class="text-gray-300 hover:text-brand transition-colors duration-200 flex items-center gap-2.5 group">
                <span>Booking Saya</span>
                <span class="bg-brand/20 text-brand group-hover:bg-brand group-hover:text-white text-xs px-2.5 py-0.5 rounded-full font-extrabold transition-colors duration-200">2</span>
            </a>
            <a href="{{ route('panduan') }}" class="text-gray-300 hover:text-brand transition-colors duration-200">Panduan Sewa</a>
        </div>

        <!-- Action Items -->
        <div class="flex items-center gap-4">
            <button @click="isCartOpen = true" class="bg-brand hover:bg-brand-hover text-white text-xs font-black px-5 py-3 rounded-2xl flex items-center gap-3 transition shadow-lg shadow-brand/25 active:scale-95">
                <span class="text-sm">🛒 Keranjang</span>
                <span class="bg-white/20 backdrop-blur text-white text-[11px] font-black px-2.5 py-0.5 rounded-full" x-text="totalItems + ' Item'">0 Item</span>
            </button>

            <!-- Profile Icon Dinamis -->
            @auth
                <a href="{{ route('profile.edit') }}" 
                   class="w-11 h-11 rounded-2xl bg-brand text-white flex items-center justify-center text-sm font-black border border-brand/50 shadow-md shadow-brand/20 hover:scale-105 transition"
                   title="Profil Saya ({{ auth()->user()->name }})">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </a>
            @else
                <a href="{{ route('login') }}" 
                   class="w-11 h-11 rounded-2xl bg-gray-800 text-gray-300 hover:text-white hover:bg-gray-700 flex items-center justify-center text-sm transition border border-gray-700/60 shadow-sm"
                   title="Masuk ke Akun">
                    👤
                </a>
            @endauth
        </div>
    </nav>

    <!-- Header & Hero Section (Sudah Dirapikan Tanpa Duplikasi) -->
    <header class="max-w-7xl mx-auto px-4 sm:px-6 pt-8 pb-4 w-full space-y-6">
        <!-- 1. FULL IMAGE HERO SLIDER (Alpine.js) -->
        <div x-data="{
            activeSlide: 1,
            slides: [
                { 
                    id: 1, 
                    image: 'https://images.unsplash.com/photo-1464822759023-fed622ff2c3b?w=1200&auto=format&fit=crop&q=80', 
                    title: 'RENTAL OUTGEAR STATION' 
                },
                { 
                    id: 2, 
                    image: 'https://images.unsplash.com/photo-1506744038136-46273834b3fb?w=1200&auto=format&fit=crop&q=80', 
                    title: 'DAPATKAN BARANG DENGAN KUALITAS TERBAIK' 
                },
                { 
                    id: 3, 
                    image: 'https://images.unsplash.com/photo-1510312305653-8ed496efae75?w=1200&auto=format&fit=crop&q=80', 
                    title: 'PENYEWAAN ALAT OUTDOOR' 
                }
            ],
            next() { this.activeSlide = this.activeSlide === this.slides.length ? 1 : this.activeSlide + 1; },
            prev() { this.activeSlide = this.activeSlide === 1 ? this.slides.length : this.activeSlide - 1; }
        }"
        x-init="setInterval(() => { next() }, 5000)"
        class="relative bg-gray-900 rounded-3xl overflow-hidden shadow-md border border-gray-200 h-[320px] sm:h-[400px] md:h-[450px]">

            <template x-for="slide in slides" :key="slide.id">
                <div x-show="activeSlide === slide.id" class="absolute inset-0 transition-opacity duration-700">
                    <img :src="slide.image" class="w-full h-full object-cover brightness-[0.75]">
                    <!-- Text Overlay -->
                    <div class="absolute inset-0 flex flex-col items-center justify-center text-center p-6 bg-gradient-to-t from-black/60 via-transparent to-transparent">
                        <h2 class="text-white text-xl sm:text-3xl md:text-4xl font-black tracking-wider uppercase drop-shadow-md" x-text="slide.title"></h2>
                        <p class="text-gray-200 text-xs sm:text-sm font-medium mt-2">Dapatkan perlengkapan terbaik dengan harga terjangkau di Basecamp Malang.</p>
                        <a href="#product-grid" class="mt-4 bg-brand hover:bg-brand-hover text-white text-xs font-bold px-6 py-2.5 rounded-xl transition shadow-lg">
                            Mulai Sewa Sekarang
                        </a>
                    </div>
                </div>
            </template>

            <!-- TOMBOL PANAH DI DALAM SLIDER -->
            <button @click="prev()" class="absolute left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/70 hover:bg-white shadow flex items-center justify-center text-gray-900 transition z-10 font-bold">❮</button>
            <button @click="next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-white/70 hover:bg-white shadow flex items-center justify-center text-gray-900 transition z-10 font-bold">❯</button>
        </div>

        <!-- 2. QUICK CATEGORIES -->
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 pt-2">
            <div class="bg-white rounded-2xl p-3 border border-gray-200 text-center shadow-sm hover:border-brand transition cursor-pointer group">
                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-xl flex items-center justify-center text-2xl mb-2 group-hover:scale-105 transition">⛺</div>
                <span class="text-[11px] font-extrabold uppercase tracking-wide text-gray-800">Tenda & Flysheet</span>
            </div>
            <div class="bg-white rounded-2xl p-3 border border-gray-200 text-center shadow-sm hover:border-brand transition cursor-pointer group">
                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-xl flex items-center justify-center text-2xl mb-2 group-hover:scale-105 transition">🎒</div>
                <span class="text-[11px] font-extrabold uppercase tracking-wide text-gray-800">Carrier / Tas</span>
            </div>
            <div class="bg-white rounded-2xl p-3 border border-gray-200 text-center shadow-sm hover:border-brand transition cursor-pointer group">
                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-xl flex items-center justify-center text-2xl mb-2 group-hover:scale-105 transition">🥾</div>
                <span class="text-[11px] font-extrabold uppercase tracking-wide text-gray-800">Sepatu Hiking</span>
            </div>
            <div class="bg-white rounded-2xl p-3 border border-gray-200 text-center shadow-sm hover:border-brand transition cursor-pointer group">
                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-xl flex items-center justify-center text-2xl mb-2 group-hover:scale-105 transition">🍳</div>
                <span class="text-[11px] font-extrabold uppercase tracking-wide text-gray-800">Cooking Set</span>
            </div>
            <div class="bg-white rounded-2xl p-3 border border-gray-200 text-center shadow-sm hover:border-brand transition cursor-pointer group">
                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-xl flex items-center justify-center text-2xl mb-2 group-hover:scale-105 transition">🔦</div>
                <span class="text-[11px] font-extrabold uppercase tracking-wide text-gray-800">Penerangan</span>
            </div>
            <div class="bg-white rounded-2xl p-3 border border-gray-200 text-center shadow-sm hover:border-brand transition cursor-pointer group">
                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-xl flex items-center justify-center text-2xl mb-2 group-hover:scale-105 transition">🧥</div>
                <span class="text-[11px] font-extrabold uppercase tracking-wide text-gray-800">Jaket & Apparel</span>
            </div>
            <div class="bg-white rounded-2xl p-3 border border-gray-200 text-center shadow-sm hover:border-brand transition cursor-pointer group">
                <div class="w-16 h-16 mx-auto bg-gray-50 rounded-xl flex items-center justify-center text-2xl mb-2 group-hover:scale-105 transition">🧭</div>
                <span class="text-[11px] font-extrabold uppercase tracking-wide text-gray-800">Aksesoris</span>
            </div>
        </div>
    </header>

    <!-- Main Content Katalog -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 w-full flex-grow">
        <div class="mb-5">
            <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight">Daftar Peralatan</h2>
        </div>

        <!-- Product Cards Grid -->
        <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse ($peralatan as $alat)
                <article class="bg-white rounded-2xl border border-gray-200 p-3 flex flex-col justify-between hover:shadow-lg transition group">
                    <div>
                        <div class="relative h-44 rounded-xl bg-gray-100 overflow-hidden mb-3 flex items-center justify-center">
                            @if (filled($alat->foto))
                                <img src="{{ filter_var($alat->foto, FILTER_VALIDATE_URL) ? $alat->foto : asset('storage/'.$alat->foto) }}"
                                     alt="{{ $alat->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                <span class="text-5xl">{{ \App\Support\IkonKategori::untuk($alat->kategori?->nama) }}</span>
                            @endif

                            <span class="absolute top-2.5 right-2.5 text-[10px] font-bold px-2 py-0.5 rounded-full border backdrop-blur {{ $alat->stok_tersedia > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-500 border-gray-200' }}">
                                {{ $alat->stok_tersedia > 0 ? 'Tersedia ('.$alat->stok_tersedia.' unit)' : 'Stok Habis' }}
                            </span>
                        </div>

                        <h3 class="font-bold text-xs text-gray-900 group-hover:text-brand transition line-clamp-1 mb-2">
                            {{ $alat->nama }}
                        </h3>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2">
                        <div>
                            <p class="text-[9px] text-gray-400 uppercase font-bold">TARIF / HARI</p>
                            <p class="text-xs font-extrabold text-brand">Rp {{ number_format((float) $alat->tarif_per_hari, 0, ',', '.') }}</p>
                        </div>

                        <!-- Tombol Sewa -->
                        <button @disabled($alat->stok_tersedia <= 0)
                                @click="addToCart({ id: {{ $alat->id }}, nama: '{{ addslashes($alat->nama) }}', tarif: {{ $alat->tarif_per_hari }}, foto: '{{ $alat->foto ? (filter_var($alat->foto, FILTER_VALIDATE_URL) ? $alat->foto : asset('storage/'.$alat->foto)) : '' }}' })"
                                class="{{ $alat->stok_tersedia > 0 ? 'bg-brand hover:bg-brand-hover text-white shadow-sm' : 'bg-gray-100 text-gray-400 cursor-not-allowed border border-gray-200' }} text-xs font-bold px-3.5 py-1.5 rounded-xl transition flex items-center gap-1">
                            <span>Sewa</span>
                        </button>
                    </div>
                </article>
            @empty
                <p class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
                    Belum ada peralatan yang terdaftar.
                </p>
            @endforelse
        </div>
    </main>

    <!-- SIDEBAR DRAWER KERANJANG BELANJA -->
    <div x-show="isCartOpen" style="display: none;" class="fixed inset-0 z-50 overflow-hidden">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm transition-opacity" @click="isCartOpen = false"></div>

        <div class="absolute inset-y-0 right-0 max-w-full flex pl-10">
            <div class="w-screen max-w-md bg-white shadow-2xl flex flex-col justify-between">
                <div>
                    <!-- Header Keranjang -->
                    <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-white">
                        <h3 class="font-bold text-base text-gray-900">Keranjang Belanja</h3>
                        <button @click="isCartOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
                    </div>

                    <!-- Opsi Pilih Semua -->
                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between text-xs bg-gray-50/50">
                        <label class="flex items-center gap-2 cursor-pointer font-medium text-gray-700">
                            <input type="checkbox" @click="cart.forEach(i => i.selected = !i.selected)" :checked="cart.every(i => i.selected) && cart.length > 0" class="rounded text-brand focus:ring-brand">
                            <span>Pilih Semua</span>
                        </label>
                        <button @click="cart = []" class="text-brand font-semibold hover:underline">Hapus</button>
                    </div>

                    <!-- List Item dalam Keranjang -->
                    <div class="max-h-[calc(100vh-250px)] overflow-y-auto p-4 space-y-4">
                        <template x-for="item in cart" :key="item.id">
                            <div class="flex gap-3 pb-4 border-b border-gray-100 items-start">
                                <input type="checkbox" x-model="item.selected" class="mt-8 rounded text-brand focus:ring-brand">

                                <div class="w-16 h-16 rounded-xl bg-gray-100 overflow-hidden shrink-0 flex items-center justify-center border border-gray-200">
                                    <template x-if="item.foto">
                                        <img :src="item.foto" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!item.foto">
                                        <span class="text-xs text-gray-400">⛺</span>
                                    </template>
                                </div>

                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <h4 class="font-bold text-xs text-gray-900 line-clamp-1" x-text="item.nama"></h4>
                                        <span class="font-extrabold text-xs text-gray-900" x-text="'Rp ' + Number(item.tarif * item.qty).toLocaleString('id-ID')"></span>
                                    </div>
                                    <p class="text-[10px] text-emerald-600 font-medium mt-0.5">✔ Stock Tersedia</p>

                                    <div class="flex justify-between items-center mt-3">
                                        <button @click="removeFromCart(item.id)" class="text-gray-400 hover:text-red-500 border border-gray-200 px-2 py-1 rounded-lg text-[10px] flex items-center gap-1 transition">
                                            🗑️ Hapus
                                        </button>

                                        <div class="flex items-center border border-gray-200 rounded-lg overflow-hidden bg-gray-50">
                                            <button @click="decreaseQty(item.id)" class="px-2 py-1 text-gray-600 hover:bg-gray-200 text-xs font-bold transition">-</button>
                                            <span class="px-3 py-1 text-xs font-bold text-gray-800" x-text="item.qty"></span>
                                            <button @click="increaseQty(item.id)" class="px-2 py-1 text-gray-600 hover:bg-gray-200 text-xs font-bold transition">+</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </template>

                        <template x-if="cart.length === 0">
                            <div class="text-center py-16 text-gray-400 text-xs space-y-2">
                                <p class="text-3xl">🛒</p>
                                <p>Keranjang belanja masih kosong.</p>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Footer Checkout Keranjang -->
                <div class="p-4 border-t border-gray-200 bg-white space-y-3">
                    <div class="flex justify-between items-center text-xs">
                        <span class="text-gray-500 font-medium">Total Harga</span>
                        <span class="font-black text-base text-gray-900" x-text="'Rp ' + Number(totalPrice).toLocaleString('id-ID')"></span>
                    </div>
                    <button class="w-full bg-gray-900 hover:bg-brand text-white text-xs font-bold py-3 rounded-2xl transition shadow-lg tracking-wide uppercase">
                        Checkout (<span x-text="totalItems"></span>)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Dark Footer Full Width (Menempel Penuh ke Bawah & Full Selebar Layar) -->
    <footer class="bg-dark text-gray-400 text-xs pt-12 pb-6 border-t border-gray-800 w-full mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <!-- Kolom 1 -->
                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-brand flex items-center justify-center font-bold text-white text-xs">OS</div>
                        <span class="font-bold text-white text-sm">OutGear Station</span>
                    </div>
                    <p class="text-[11px] leading-relaxed text-gray-400">
                        Penyedia persewaan perlengkapan outdoor, mountaineering & camping terpercaya sejak 2021 di Malang.
                    </p>
                    <div class="pt-2 text-[11px] text-gray-300">
                        <p class="font-bold text-white mb-1">📍 Basecamp Utama:</p>
                        <p>Jl. Soekarno Hatta No. 9, Malang, Jawa Timur</p>
                    </div>
                </div>

                <!-- Kolom 2 -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-sm tracking-wider uppercase">Jam Operasional</h4>
                    <div class="space-y-2 text-[11px]">
                        <p><strong class="text-white">Senin — Jumat:</strong> 08:00 - 21:00 WIB</p>
                        <p><strong class="text-white">Sabtu — Minggu:</strong> 07:00 - 22:00 WIB</p>
                        <p class="text-emerald-400 font-semibold pt-1">✔ Layanan Ambil/Kembali Buka Setiap Hari</p>
                    </div>
                </div>

                <!-- Kolom 3 -->
                <div class="space-y-3">
                    <h4 class="font-bold text-white text-sm tracking-wider uppercase">Metode Pembayaran</h4>
                    <p class="text-[11px] text-gray-400">Kami mendukung pembayaran online praktis via Midtrans Gateway serta pembayaran tunai langsung di tempat.</p>
                    <div class="flex flex-wrap gap-2 pt-2">
                        <span class="bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-xl text-[11px] font-bold text-white flex items-center gap-1.5">
                            💳 Midtrans Payment
                        </span>
                        <span class="bg-gray-800 border border-gray-700 px-3 py-1.5 rounded-xl text-[11px] font-bold text-white flex items-center gap-1.5">
                            💵 Tunai / Cash
                        </span>
                    </div>
                </div>
            </div>

            <!-- Copyright -->
            <div class="pt-6 border-t border-gray-800 text-center text-[10px] text-gray-500">
                <p>© 2026 OutGear Station. All rights reserved.</p>
            </div>
        </div>
    </footer>

</body>

</html>