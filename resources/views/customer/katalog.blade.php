<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Katalog Sewa Alat Outdoor & Pendakian - OutGear Station</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @if (filled($clientKey ?? null))
        {{-- Pop-up pembayaran Midtrans Snap (Sandbox/Production mengikuti .env) --}}
        <script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
    @endif
</head>
<body class="bg-[#F8F6F2] text-gray-800 font-sans min-h-screen flex flex-col justify-between"
      x-data="{
          cart: [],
          isCartOpen: false,
          hariIni: '{{ now()->toDateString() }}',
          tanggalMulai: '{{ now()->toDateString() }}',
          tanggalSelesai: '{{ now()->addDays(2)->toDateString() }}',
          sudahLogin: {{ auth()->check() ? 'true' : 'false' }},
          isCustomer: {{ auth()->user()?->isCustomer() ? 'true' : 'false' }},
          isCheckingOut: false,
          showHasilModal: false,
          hasil: null,
          pesanError: '',
          showDetail: false,
          detail: null,
          detailQty: 1,
          toast: '',
          toastTimer: null,

          init() {
              // Dibuka dari ikon keranjang di halaman lain (Booking Saya / Panduan Sewa).
              if (new URLSearchParams(window.location.search).get('keranjang') === '1') this.isCartOpen = true;

              // Keranjang & jadwal disimpan di browser supaya tidak hilang saat pindah halaman / login.
              try {
                  const simpan = JSON.parse(localStorage.getItem('outgear.keranjang') || 'null');
                  if (simpan && Array.isArray(simpan.cart)) {
                      this.cart = simpan.cart;
                      if (simpan.tanggalMulai && simpan.tanggalMulai >= this.hariIni) this.tanggalMulai = simpan.tanggalMulai;
                      if (simpan.tanggalSelesai && simpan.tanggalSelesai >= this.tanggalMulai) this.tanggalSelesai = simpan.tanggalSelesai;
                  }
              } catch (e) {}
              if (this.tanggalSelesai < this.tanggalMulai) this.tanggalSelesai = this.tanggalMulai;
              this.$watch('cart', () => this.simpanKeranjang());
              this.$watch('tanggalMulai', (v) => { if (this.tanggalSelesai < v) this.tanggalSelesai = v; this.simpanKeranjang(); });
              this.$watch('tanggalSelesai', () => this.simpanKeranjang());
          },

          simpanKeranjang() {
              try {
                  localStorage.setItem('outgear.keranjang', JSON.stringify({
                      cart: this.cart, tanggalMulai: this.tanggalMulai, tanggalSelesai: this.tanggalSelesai
                  }));
              } catch (e) {}
          },

          rp(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },

          bukaDetail(id) {
              this.detail = (window.DATA_ALAT || {})[id] || null;
              if (!this.detail) return;
              this.detailQty = 1;
              this.showDetail = true;
          },

          get qtyDiKeranjang() {
              if (!this.detail) return 0;
              const ada = this.cart.find(i => i.id === this.detail.id);
              return ada ? ada.qty : 0;
          },

          get sisaBisaDitambah() {
              return this.detail ? Math.max(0, this.detail.stok - this.qtyDiKeranjang) : 0;
          },

          masukkanDariDetail() {
              if (!this.detail || this.sisaBisaDitambah < 1) return;
              const jumlah = Math.min(Math.max(1, this.detailQty), this.sisaBisaDitambah);
              this.addToCart({ id: this.detail.id, nama: this.detail.nama, tarif: this.detail.tarif, foto: this.detail.foto, stok: this.detail.stok }, jumlah);
              this.showDetail = false;
              this.tampilToast(jumlah + ' × ' + this.detail.nama + ' masuk keranjang');
          },

          tampilToast(teks) {
              this.toast = teks;
              clearTimeout(this.toastTimer);
              this.toastTimer = setTimeout(() => this.toast = '', 3000);
          },

          addToCart(item, jumlah = 1) {
              let existing = this.cart.find(i => i.id === item.id);
              if (existing) {
                  existing.qty += jumlah;
                  existing.stok = item.stok;
              } else {
                  this.cart.push({ ...item, qty: jumlah, selected: true });
              }
          },

          increaseQty(id) {
              let item = this.cart.find(i => i.id === id);
              if (item && (item.stok === undefined || item.qty < item.stok)) item.qty += 1;
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

          get selectedItems() {
              return this.cart.filter(i => i.selected);
          },

          get lamaSewa() {
              if (!this.tanggalMulai || !this.tanggalSelesai) return 1;
              let d1 = new Date(this.tanggalMulai);
              let d2 = new Date(this.tanggalSelesai);
              let diff = Math.round((d2 - d1) / (1000 * 60 * 60 * 24));
              return Math.max(1, diff + 1);
          },

          get tarifPerHari() {
              return this.cart.filter(i => i.selected).reduce((sum, item) => sum + (item.tarif * item.qty), 0);
          },

          get totalPrice() {
              return this.tarifPerHari * this.lamaSewa;
          },

          get jumlahDp() {
              return Math.round(this.totalPrice * 0.5);
          },

          async submitCheckout() {
              let selected = this.selectedItems;
              if (selected.length === 0) {
                  this.pesanError = 'Pilih minimal 1 peralatan yang dicentang di keranjang.';
                  return;
              }
              if (!this.tanggalMulai || !this.tanggalSelesai || this.tanggalMulai < this.hariIni || this.tanggalSelesai < this.tanggalMulai) {
                  this.pesanError = 'Periksa kembali jadwal sewa: tanggal mulai tidak boleh sebelum hari ini dan tanggal selesai tidak boleh sebelum tanggal mulai.';
                  return;
              }
              if (!this.sudahLogin) {
                  this.simpanKeranjang();
                  window.location.href = '{{ route('login') }}';
                  return;
              }
              if (!this.isCustomer) {
                  this.pesanError = 'Hanya akun pelanggan yang dapat menyewa alat.';
                  return;
              }

              this.pesanError = '';
              this.isCheckingOut = true;
              try {
                  let res = await fetch('{{ route('checkout.store') }}', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': '{{ csrf_token() }}',
                          'Accept': 'application/json'
                      },
                      body: JSON.stringify({
                          items: selected.map(i => ({ id: i.id, qty: i.qty })),
                          tanggal_mulai: this.tanggalMulai,
                          tanggal_selesai: this.tanggalSelesai
                      })
                  });

                  if (res.status === 401 || res.status === 419) {
                      this.simpanKeranjang();
                      window.location.href = '{{ route('login') }}';
                      return;
                  }

                  let data = await res.json().catch(() => ({}));
                  if (!res.ok || !data.success) {
                      this.pesanError = data.message || 'Gagal memproses checkout. Silakan coba lagi.';
                      return;
                  }

                  // Booking sudah dibuat: item yang dibeli keluar dari keranjang.
                  let ids = selected.map(i => i.id);
                  this.cart = this.cart.filter(i => !ids.includes(i.id));
                  this.isCartOpen = false;
                  this.hasil = { ...data, status: 'menunggu' };

                  if (typeof window.snap === 'undefined') {
                      this.showHasilModal = true;
                      return;
                  }

                  const selesai = () => this.sinkronkanHasil();
                  window.snap.pay(data.snap_token, {
                      onSuccess: selesai,
                      onPending: selesai,
                      onError: () => { this.hasil.status = 'error'; this.showHasilModal = true; },
                      onClose: selesai
                  });
              } catch (err) {
                  console.error(err);
                  this.pesanError = 'Terjadi kesalahan saat menghubungi server.';
              } finally {
                  this.isCheckingOut = false;
              }
          },

          async sinkronkanHasil() {
              this.hasil.status = 'menunggu';
              this.showHasilModal = true;
              try {
                  let res = await fetch(this.hasil.url_sinkron, {
                      method: 'POST',
                      headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' }
                  });
                  let d = await res.json();
                  if (d.status_transaksi === 'berhasil') this.hasil.status = 'lunas';
                  else if (d.status_transaksi === 'gagal') this.hasil.status = 'gagal';
              } catch (e) {}
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
                @if (($badgeBooking ?? 0) > 0)
                    <span class="bg-brand/20 text-brand group-hover:bg-brand group-hover:text-white text-xs px-2.5 py-0.5 rounded-full font-extrabold transition-colors duration-200">{{ $badgeBooking }}</span>
                @endif
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

    <!-- Header & Hero Section -->
    <header class="max-w-7xl mx-auto px-4 sm:px-6 pt-6 pb-2 w-full">
        <!-- 1. HERO SLIDER PROMO (Alpine.js) -->
        <div x-data="{
            activeSlide: 0,
            autoplayTimer: null,
            slides: [
                {
                    id: 1,
                    badge: '🔥 PROMO SPESIAL AKHIR PEKAN',
                    title: 'PAKET CAMPING HEMAT 3H 2M',
                    subtitle: 'Sewa Tenda Dome + Sleeping Bag + Matras diskon hingga 25%! Dijamin bersih, wangi, & siap muncak tanpa ribet.',
                    highlight: 'Hemat s/d Rp 45.000',
                    features: ['Tenda Double Layer Waterproof', 'Garansi Bersih & Wangi', 'Bebas Antre Booking'],
                    image: '{{ asset('images/alat/tenda-camp-6.jpg') }}',
                    cta: 'Sewa Paket Tenda',
                    accentColor: 'from-amber-500/20 to-orange-500/10'
                },
                {
                    id: 2,
                    badge: '🎒 PILIHAN FAVORIT PENDAKI',
                    title: 'CARRIER 60L ERGONOMIC SERIES',
                    subtitle: 'Backsystem empuk dengan sirkulasi udara optimal. Nyaman, kuat, dan bebas pegal saat summit ke puncak gunung impian.',
                    highlight: 'Free Rain Cover',
                    features: ['Kapasitas Luas 60 Liter', 'Bahan Cordura Tahan Sobek', 'Include Waterproof Raincover'],
                    image: '{{ asset('images/alat/carrier-60l.jpeg') }}',
                    cta: 'Lihat Koleksi Carrier',
                    accentColor: 'from-orange-500/20 to-red-500/10'
                },
                {
                    id: 3,
                    badge: '🍳 PAKET MASAK PUNCAK',
                    title: 'SEWA 3 HARI, GRATIS 1 HARI!',
                    subtitle: 'Masak hangat di puncak makin asyik dengan Kompor Windproof & Nesting Cook Set anti-lengket. Booking sekarang!',
                    highlight: 'Sewa 3 Hari = Bayar 2 Hari',
                    features: ['Kompor Anti-Angin Suhu Dingin', 'Panci & Wajan Komplit', 'Ringan & Praktis'],
                    image: '{{ asset('images/alat/nesting-cook-set.jpeg') }}',
                    cta: 'Klaim Promo Masak',
                    accentColor: 'from-emerald-500/20 to-teal-500/10'
                },
                {
                    id: 4,
                    badge: '🥾 KEAMANAN & KENYAMANAN',
                    title: 'SEPATU HIKING ALL-TERRAIN',
                    subtitle: 'Grip karet anti-selip di medan becek dan bebatuan tajam. Melangkah mantap, aman, dan percaya diri ke puncak.',
                    highlight: 'Sol Anti-Slip Ekstra Mantap',
                    features: ['Grip Sol Kuat & Fleksibel', 'Water-Resistant Upper', 'Higienis & Siap Pakai'],
                    image: '{{ asset('images/alat/sepatu-hiking-42.avif') }}',
                    cta: 'Pilih Ukuran Sepatu',
                    accentColor: 'from-sky-500/20 to-indigo-500/10'
                }
            ],
            next() {
                this.activeSlide = (this.activeSlide + 1) % this.slides.length;
            },
            prev() {
                this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
            },
            goTo(index) {
                this.activeSlide = index;
                this.resetAutoplay();
            },
            startAutoplay() {
                this.autoplayTimer = setInterval(() => { this.next(); }, 5000);
            },
            resetAutoplay() {
                clearInterval(this.autoplayTimer);
                this.startAutoplay();
            }
        }"
        x-init="startAutoplay()"
        @mouseenter="clearInterval(autoplayTimer)"
        @mouseleave="startAutoplay()"
        class="relative bg-gradient-to-br from-[#1C2227] via-[#171B1F] to-[#0F1316] rounded-3xl overflow-hidden shadow-2xl border border-white/10 text-white p-6 sm:p-8 md:p-10 select-none">

            <!-- Ambient Glow Effect -->
            <div class="absolute -top-24 -right-24 w-96 h-96 bg-brand/20 rounded-full blur-3xl pointer-events-none"></div>
            <div class="absolute -bottom-24 -left-24 w-80 h-80 bg-emerald-500/15 rounded-full blur-3xl pointer-events-none"></div>

            <!-- Slide Content Container -->
            <div class="relative min-h-[360px] md:min-h-[320px] flex items-center">
                <template x-for="(slide, index) in slides" :key="slide.id">
                    <div x-show="activeSlide === index"
                         x-transition:enter="transition-all ease-out duration-300"
                         x-transition:enter-start="opacity-0 scale-[0.98]"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition-all ease-in duration-300 absolute inset-0 pointer-events-none"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-[0.98]"
                         class="w-full flex flex-col md:flex-row items-center justify-between gap-6 md:gap-10">

                        <!-- Gambar Promo (Kiri) -->
                        <div class="w-full md:w-5/12 h-60 sm:h-72 md:h-80 rounded-2xl overflow-hidden relative shadow-2xl bg-black/40 border border-white/10 group">
                            <img :src="slide.image" :alt="slide.title"
                                 class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">

                            <!-- Tag Badge di atas gambar -->
                            <div class="absolute top-3 left-3 bg-black/60 backdrop-blur-md text-white text-[10px] sm:text-xs font-bold px-3 py-1 rounded-full border border-white/20 flex items-center gap-1.5 shadow-md">
                                <span class="w-2 h-2 rounded-full bg-emerald-400 animate-pulse"></span>
                                <span>Unit Siap Sewa</span>
                            </div>

                            <!-- Tag Highlight di bawah gambar -->
                            <div class="absolute bottom-3 right-3 bg-brand/90 backdrop-blur-md text-white text-[11px] font-black px-3 py-1 rounded-xl shadow-lg border border-white/20">
                                <span x-text="slide.highlight"></span>
                            </div>
                        </div>

                        <!-- Teks & Penawaran Promosi (Kanan) -->
                        <div class="w-full md:w-7/12 text-center md:text-left space-y-4">
                            <!-- Badge Penawaran -->
                            <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full text-[11px] font-black tracking-wide uppercase bg-brand/20 text-brand border border-brand/30 shadow-sm"
                                 x-text="slide.badge">
                            </div>

                            <!-- Judul Besar Promo -->
                            <h2 class="text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-black font-heading text-white tracking-tight uppercase leading-tight"
                                x-text="slide.title"></h2>

                            <!-- Deskripsi & Benefit -->
                            <p class="text-xs sm:text-sm font-medium text-gray-300 leading-relaxed max-w-xl"
                               x-text="slide.subtitle"></p>

                            <!-- Keunggulan / Bullet Points -->
                            <div class="flex flex-wrap items-center justify-center md:justify-start gap-2 pt-1">
                                <template x-for="(feature, idx) in slide.features" :key="idx">
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-xl bg-white/5 border border-white/10 text-[11px] font-semibold text-gray-200">
                                        <span class="text-emerald-400 font-bold">✔</span>
                                        <span x-text="feature"></span>
                                    </span>
                                </template>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            <!-- Tombol Navigasi Panah Kiri / Kanan -->
            <button @click="prev(); resetAutoplay();"
                    aria-label="Previous Slide"
                    class="absolute left-3 sm:left-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 hover:bg-brand text-white border border-white/15 backdrop-blur-md shadow-lg flex items-center justify-center transition hover:scale-105 active:scale-95 z-20 text-sm">
                ❮
            </button>
            <button @click="next(); resetAutoplay();"
                    aria-label="Next Slide"
                    class="absolute right-3 sm:right-4 top-1/2 -translate-y-1/2 w-10 h-10 rounded-full bg-black/50 hover:bg-brand text-white border border-white/15 backdrop-blur-md shadow-lg flex items-center justify-center transition hover:scale-105 active:scale-95 z-20 text-sm">
                ❯
            </button>

            <!-- Indikator Slide Bulat / Batang di Bawah -->
            <div class="flex items-center justify-center gap-2 mt-6 pt-2">
                <template x-for="(s, i) in slides" :key="s.id">
                    <button @click="goTo(i)"
                            class="h-2 rounded-full transition-all duration-300 cursor-pointer"
                            :class="activeSlide === i ? 'w-8 bg-brand shadow-sm shadow-brand/50' : 'w-2 bg-white/30 hover:bg-white/60'"
                            :aria-label="'Slide ' + (i + 1)">
                    </button>
                </template>
            </div>
        </div>
    </header>

    <!-- Main Content Katalog -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 w-full flex-grow">
        <div class="mb-5">
            <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight">Daftar Peralatan</h2>
        </div>

        @php
            $dataAlat = [];
            foreach ($peralatan as $item) {
                $dataAlat[$item->id] = [
                    'id' => $item->id,
                    'nama' => $item->nama,
                    'deskripsi' => $item->deskripsi,
                    'kategori' => $item->kategori?->nama,
                    'foto' => $item->foto_url,
                    'tarif' => (float) $item->tarif_per_hari,
                    'stok' => (int) $item->stok_tersedia,
                ];
            }
        @endphp
        <script>
            window.DATA_ALAT = {!! json_encode($dataAlat, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) !!};
        </script>

        <!-- Product Cards Grid -->
        <div id="product-grid" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
            @forelse ($peralatan as $alat)
                @php
                    $fotoUrl = $alat->foto_url;
                @endphp
                <article class="bg-white rounded-2xl border border-gray-200 p-3 flex flex-col justify-between hover:shadow-lg transition group">
                    <div>
                        <div @click="bukaDetail({{ $alat->id }})" class="relative h-44 rounded-xl bg-gray-100 overflow-hidden mb-3 flex items-center justify-center cursor-pointer">
                            @if ($fotoUrl)
                                <img src="{{ $fotoUrl }}"
                                     alt="{{ $alat->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-300">
                            @else
                                @include('partials.foto-kosong', ['kelas' => 'w-14 h-14'])
                            @endif

                            <span class="absolute top-2.5 right-2.5 text-[10px] font-bold px-2 py-0.5 rounded-full border backdrop-blur {{ $alat->stok_tersedia > 0 ? 'bg-emerald-50 text-emerald-700 border-emerald-200' : 'bg-gray-100 text-gray-500 border-gray-200' }}">
                                {{ $alat->stok_tersedia > 0 ? 'Tersedia ('.$alat->stok_tersedia.' unit)' : 'Stok Habis' }}
                            </span>
                        </div>

                        <h3 @click="bukaDetail({{ $alat->id }})" class="font-bold text-xs text-gray-900 group-hover:text-brand transition line-clamp-1 mb-2 cursor-pointer">
                            {{ $alat->nama }}
                        </h3>
                    </div>

                    <div class="pt-2 border-t border-gray-100 flex items-center justify-between gap-2">
                        <div>
                            <p class="text-[9px] text-gray-400 uppercase font-bold">TARIF / HARI</p>
                            <p class="text-xs font-extrabold text-brand">Rp {{ number_format((float) $alat->tarif_per_hari, 0, ',', '.') }}</p>
                        </div>

                        <!-- Tombol Detail: lihat dulu, baru masukkan ke keranjang -->
                        <button type="button" @click="bukaDetail({{ $alat->id }})"
                                class="bg-brand hover:bg-brand-hover text-white shadow-sm text-xs font-bold px-3.5 py-1.5 rounded-xl transition flex items-center gap-1">
                            <span>Lihat Detail</span>
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
                                        @include('partials.foto-kosong', ['kelas' => 'w-6 h-6'])
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
                    <!-- Input Jadwal Sewa -->
                    <div class="bg-gray-50 rounded-2xl p-3 border border-gray-200 space-y-2">
                        <div class="flex items-center justify-between text-[11px] font-bold text-gray-700">
                            <span>📅 Jadwal Sewa</span>
                            <span class="text-brand bg-brand/10 px-2 py-0.5 rounded-full text-[10px]" x-text="'Durasi: ' + lamaSewa + ' Hari'"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <label class="block text-[10px] text-gray-500 font-semibold mb-1">Mulai</label>
                                <input type="date" x-model="tanggalMulai" :min="hariIni" class="w-full text-xs rounded-xl border-gray-300 bg-white py-1.5 px-2.5 focus:ring-brand focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 font-semibold mb-1">Selesai</label>
                                <input type="date" x-model="tanggalSelesai" :min="tanggalMulai" class="w-full text-xs rounded-xl border-gray-300 bg-white py-1.5 px-2.5 focus:ring-brand focus:border-brand">
                            </div>
                        </div>
                    </div>

                    @guest
                    <p class="text-[11px] text-amber-700 bg-amber-50 border border-amber-200 rounded-xl px-3 py-2">
                        Keranjang boleh diisi tanpa login. Untuk checkout kamu akan diminta <a href="{{ route('login') }}" class="font-bold underline">masuk</a> atau <a href="{{ route('register') }}" class="font-bold underline">daftar</a> dulu; isi keranjang tetap tersimpan.
                    </p>
                    @endguest

                    <!-- Rincian Total Harga -->
                    <div class="space-y-1 pt-1 text-xs">
                        <div class="flex justify-between items-center text-gray-500 text-[11px]">
                            <span>Tarif / Hari (<span x-text="selectedItems.length"></span> item)</span>
                            <span x-text="'Rp ' + Number(tarifPerHari).toLocaleString('id-ID')"></span>
                        </div>
                        <div class="flex justify-between items-center text-gray-500 text-[11px]">
                            <span>Durasi Sewa</span>
                            <span x-text="lamaSewa + ' Hari'"></span>
                        </div>
                        <div class="flex justify-between items-center font-bold pt-1 border-t border-gray-100">
                            <span class="text-gray-900 font-black">Total Sewa</span>
                            <span class="font-black text-base text-gray-900" x-text="rp(totalPrice)"></span>
                        </div>
                        <div class="flex justify-between items-center bg-brand/10 rounded-xl px-3 py-2 mt-1">
                            <span class="text-brand font-black text-[11px]">Bayar sekarang (DP 50%)</span>
                            <span class="font-black text-base text-brand" x-text="rp(jumlahDp)"></span>
                        </div>
                        <p class="text-[10px] text-gray-400 leading-snug">Sisa pembayaran dilunasi saat pengambilan alat di basecamp. Metode bayar dipilih di langkah berikutnya (Midtrans).</p>
                        <p x-show="pesanError" x-text="pesanError" class="text-[11px] text-red-600 bg-red-50 border border-red-200 rounded-xl px-3 py-2" x-cloak></p>
                    </div>

                    <!-- Tombol Checkout -->
                    <button @click="submitCheckout()"
                            :disabled="isCheckingOut || selectedItems.length === 0"
                            class="w-full bg-gray-900 hover:bg-brand disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold py-3.5 rounded-2xl transition shadow-lg tracking-wide uppercase flex items-center justify-center gap-2">
                        <span x-show="!isCheckingOut">Checkout &amp; Bayar DP (<span x-text="selectedItems.length"></span> Item)</span>
                        <span x-show="isCheckingOut" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span>Memproses Booking...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
<!-- Modal detail alat -->
<div x-show="showDetail" x-cloak style="display: none;" class="fixed inset-0 z-[55] overflow-y-auto" @keydown.escape.window="showDetail = false">
    <div class="min-h-screen px-4 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showDetail = false"></div>

        <div class="relative z-10 w-full max-w-3xl my-8 bg-white rounded-3xl shadow-2xl overflow-hidden grid md:grid-cols-2">
            <button type="button" @click="showDetail = false" class="absolute top-3 right-3 z-10 w-8 h-8 rounded-full bg-white/90 text-gray-500 hover:text-gray-800 font-bold shadow">✕</button>

            <div class="bg-gray-100 min-h-[260px] md:min-h-[420px] flex items-center justify-center">
                <template x-if="detail && detail.foto">
                    <img :src="detail.foto" :alt="detail.nama" class="w-full h-full object-cover">
                </template>
                <template x-if="detail && !detail.foto">
                    @include('partials.foto-kosong', ['kelas' => 'w-20 h-20'])
                </template>
            </div>

            <div class="p-6 flex flex-col gap-4" x-show="detail">
                <div class="space-y-2">
                    <span x-show="detail?.kategori" x-text="detail?.kategori" class="inline-block text-[10px] font-bold uppercase tracking-wide text-brand bg-brand/10 px-2.5 py-1 rounded-full"></span>
                    <h3 class="text-xl font-black text-gray-900 leading-tight" x-text="detail?.nama"></h3>
                    <p class="text-xs text-gray-500 leading-relaxed" x-text="detail?.deskripsi || 'Belum ada deskripsi untuk alat ini.'"></p>
                </div>

                <div class="grid grid-cols-2 gap-3 text-xs">
                    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-3">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Tarif / hari</p>
                        <p class="font-black text-brand text-sm" x-text="rp(detail?.tarif)"></p>
                    </div>
                    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-3">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Stok tersedia</p>
                        <p class="font-black text-sm" :class="detail?.stok > 0 ? 'text-emerald-600' : 'text-red-500'" x-text="detail?.stok > 0 ? detail.stok + ' unit' : 'Habis'"></p>
                    </div>
                </div>

                <div class="mt-auto space-y-3">
                    <div class="flex items-center justify-between">
                        <span class="text-xs font-bold text-gray-700">Jumlah</span>
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-gray-50">
                            <button type="button" @click="detailQty = Math.max(1, detailQty - 1)" class="px-3 py-1.5 text-gray-600 hover:bg-gray-200 font-bold">-</button>
                            <span class="px-4 text-sm font-bold text-gray-800" x-text="detailQty"></span>
                            <button type="button" @click="detailQty = Math.min(Math.max(1, sisaBisaDitambah), detailQty + 1)" class="px-3 py-1.5 text-gray-600 hover:bg-gray-200 font-bold">+</button>
                        </div>
                    </div>
                    <p x-show="qtyDiKeranjang > 0" class="text-[11px] text-gray-500" x-text="qtyDiKeranjang + ' unit alat ini sudah ada di keranjang'"></p>
                    <button type="button" @click="masukkanDariDetail()" :disabled="sisaBisaDitambah < 1"
                            class="w-full bg-brand hover:bg-brand-hover disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white text-xs font-bold py-3.5 rounded-2xl transition shadow-lg shadow-brand/20 uppercase tracking-wide"
                            x-text="detail?.stok < 1 ? 'Stok Habis' : (sisaBisaDitambah < 1 ? 'Semua stok sudah di keranjang' : '🛒 Masukkan ke Keranjang')"></button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Toast singkat -->
<div x-show="toast" x-transition x-cloak style="display: none;" class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[70] bg-gray-900 text-white text-xs font-bold px-5 py-3 rounded-2xl shadow-xl flex items-center gap-3">
    <span x-text="toast"></span>
    <button type="button" @click="isCartOpen = true; toast = ''" class="text-brand underline">Lihat keranjang</button>
</div>

<!-- Modal hasil checkout: menunggu / lunas / gagal -->
<div x-show="showHasilModal" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak>
    <div class="min-h-screen px-4 text-center flex items-center justify-center">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showHasilModal = false"></div>

        <div class="inline-block w-full max-w-md p-6 my-8 text-left align-middle bg-white shadow-2xl rounded-3xl relative z-10 border border-gray-100">
            <button @click="showHasilModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 font-bold text-lg p-1 transition">✕</button>

            <div class="text-center space-y-3 pt-2">
                <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto text-3xl font-black shadow-inner"
                     :class="{
                         'bg-emerald-100 text-emerald-600': hasil?.status === 'lunas',
                         'bg-amber-100 text-amber-600': hasil?.status === 'menunggu',
                         'bg-red-100 text-red-600': hasil?.status === 'gagal' || hasil?.status === 'error'
                     }"
                     x-text="hasil?.status === 'lunas' ? '✓' : (hasil?.status === 'menunggu' ? '⏳' : '!')"></div>
                <h3 class="text-xl font-black text-gray-900"
                    x-text="hasil?.status === 'lunas' ? 'DP Berhasil Dibayar!' : (hasil?.status === 'menunggu' ? 'Menunggu Pembayaran DP' : 'Pembayaran Tidak Berhasil')"></h3>
                <p class="text-xs text-gray-500"
                   x-text="hasil?.status === 'lunas'
                        ? 'Terima kasih! Booking kamu sudah masuk ke admin dan berstatus Dikonfirmasi. Sisa pembayaran dilunasi saat pengambilan alat.'
                        : (hasil?.status === 'menunggu'
                            ? 'Booking tersimpan. Selesaikan pembayaran DP dari halaman Booking Saya sebelum batas waktu, kalau tidak booking dibatalkan otomatis dan stok dikembalikan.'
                            : 'Pembayaran gagal atau dibatalkan, booking dibatalkan dan stok dikembalikan. Silakan checkout ulang.')"></p>
            </div>

            <div class="my-5 p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2.5 text-xs">
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Kode Booking</span>
                    <span class="font-extrabold text-brand text-sm" x-text="'#' + (hasil?.kode_pemesanan ?? '')"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Durasi Sewa</span>
                    <span class="font-bold text-gray-800" x-text="(hasil?.lama_sewa ?? 1) + ' Hari'"></span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-gray-500">Total Sewa</span>
                    <span class="font-bold text-gray-800" x-text="rp(hasil?.total_biaya)"></span>
                </div>
                <div class="flex justify-between items-center border-t border-gray-200 pt-2">
                    <span class="text-gray-900 font-bold">DP 50%</span>
                    <span class="font-black text-base text-gray-900" x-text="rp(hasil?.jumlah_dp)"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-2">
                <button @click="showHasilModal = false" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-3 rounded-2xl transition">Tutup</button>
                <a href="{{ route('booking.saya') }}" class="bg-gray-900 hover:bg-brand text-white text-xs font-bold py-3 rounded-2xl transition text-center">Lihat Booking Saya</a>
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