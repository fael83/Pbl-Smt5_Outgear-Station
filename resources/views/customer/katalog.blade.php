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
          selectedCategory: 'semua',
          tanggalMulai: new Date().toISOString().split('T')[0],
          tanggalSelesai: new Date(Date.now() + 2 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
          namaPenyewa: '{{ auth()->user()?->name ?? 'Budi Pelanggan' }}',
          noTelepon: '{{ auth()->user()?->no_telepon ?? '081100000004' }}',
          isCheckingOut: false,
          showSuccessModal: false,
          successOrder: null,

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

          async submitCheckout() {
              let selected = this.selectedItems;
              if (selected.length === 0) {
                  alert('Pilih minimal 1 peralatan yang dicentang di keranjang untuk checkout.');
                  return;
              }

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
                          tanggal_selesai: this.tanggalSelesai,
                          nama_penyewa: this.namaPenyewa,
                          no_telepon: this.noTelepon
                      })
                  });

                  if (res.status === 401) {
                      alert('Silakan login terlebih dahulu untuk melakukan booking persewaan.');
                      window.location.href = '{{ route('login') }}';
                      return;
                  }

                  let data = await res.json();
                  if (res.ok && data.success) {
                      let checkedIds = selected.map(i => i.id);
                      this.cart = this.cart.filter(i => !checkedIds.includes(i.id));
                      this.isCartOpen = false;
                      this.successOrder = data;
                      this.showSuccessModal = true;
                  } else {
                      alert(data.message || 'Gagal memproses checkout. Silakan periksa kembali data Anda.');
                  }
              } catch (err) {
                  console.error(err);
                  alert('Terjadi kesalahan saat memproses booking ke database.');
              } finally {
                  this.isCheckingOut = false;
              }
          }
      }">

    <!-- Main Navigation Bar -->
    <nav class="bg-[#121619] text-white px-4 sm:px-8 py-4 flex items-center justify-between shadow-xl sticky top-0 z-50 border-b border-white/10 w-full">
        <!-- Logo Brand -->
        <a href="{{ route('katalog') }}" class="flex items-center gap-3.5">
            <div class="w-11 h-11 rounded-2xl bg-brand flex items-center justify-center font-black text-white shadow-lg shadow-brand/30 text-sm tracking-wider">
                OS
            </div>
            <div>
                <h1 class="font-black text-sm sm:text-base tracking-wide text-white leading-tight">OutGear Station</h1>
                <p class="text-[10px] text-gray-400 tracking-widest uppercase font-semibold">Rental & Outdoor Equipment</p>
            </div>
        </a>

        <!-- Menu Navigasi Utama -->
        <div class="hidden md:flex items-center gap-8 text-xs sm:text-sm font-bold tracking-wide">
            <a href="{{ route('katalog') }}" class="text-brand transition-colors duration-200">Katalog</a>
            <a href="{{ route('booking.saya') }}" class="text-gray-300 hover:text-brand transition-colors duration-200 flex items-center gap-2 group">
                <span>Booking Saya</span>
                @auth
                    @php
                        $jumlahBookingAktif = \App\Models\Pemesanan::where('user_id', Auth::id())
                            ->whereIn('status', ['menunggu_pembayaran', 'dikonfirmasi', 'berlangsung'])
                            ->count();
                    @endphp
                    @if($jumlahBookingAktif > 0)
                        <span class="bg-brand text-white text-[10px] px-2 py-0.5 rounded-full font-extrabold">{{ $jumlahBookingAktif }}</span>
                    @endif
                @else
                    <span class="bg-brand text-white text-[10px] px-2 py-0.5 rounded-full font-extrabold">2</span>
                @endauth
            </a>
            <a href="{{ route('panduan') }}" class="text-gray-300 hover:text-brand transition-colors duration-200">Panduan Sewa</a>
        </div>

        <!-- Action Items -->
        <div class="flex items-center gap-3">
            <button @click="isCartOpen = true" class="bg-brand hover:bg-brand-hover text-white text-xs font-bold px-4 py-2.5 rounded-full flex items-center gap-2 transition shadow-md shadow-brand/25 active:scale-95">
                <span>🛒 Keranjang</span>
                <span class="bg-black/20 text-white text-[11px] font-extrabold px-2 py-0.5 rounded-full" x-text="totalItems + ' Item'">0 Item</span>
            </button>

            @auth
                <a href="{{ route('profile.edit') }}"
                   class="w-10 h-10 rounded-full bg-gray-800 hover:bg-gray-700 text-white flex items-center justify-center text-xs font-black border border-white/10 transition"
                   title="Profil Saya ({{ auth()->user()->name }})">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="w-10 h-10 rounded-full bg-gray-800 text-gray-300 hover:text-white hover:bg-gray-700 flex items-center justify-center text-sm transition border border-white/10"
                   title="Masuk ke Akun">
                    👤
                </a>
            @endauth
        </div>
    </nav>

    <!-- Header & Hero Banner Full-Bleed -->
    <header class="max-w-7xl mx-auto px-4 sm:px-6 pt-6 pb-2 w-full"
            x-data="{
                activeSlide: 0,
                autoplayTimer: null,
                slides: [
                    {
                        id: 1,
                        title: 'OUTGEAR STATION',
                        subtitle: 'OutGear Station siap menemani setiap langkahmu booking mudah, alat terawat, dan layanan ramah langsung dari Basecamp Malang.',
                        image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1600&q=80',
                        cta: 'Mulai Sewa Sekarang',
                        category: 'tenda'
                    },
                    {
                        id: 2,
                        title: 'OUTGEAR STATION',
                        subtitle: 'Percayakan perjalanan alammu bersama OutGear Station pengalaman sewa praktis dengan standar kebersihan dan kesiapan alat yang terjaga.',
                        image: 'https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=1600&q=80',
                        cta: 'Mulai Sewa Sekarang',
                        category: 'carrier'
                    },
                    {
                        id: 3,
                        title: 'OUTGEAR STATION',
                        subtitle: 'Wujudkan trip outdoor yang lebih nyaman dan terencana bareng OutGear Station mulai atur jadwal petualanganmu hari ini!',
                        image: 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=1600&q=80',
                        cta: 'Mulai Sewa Sekarang',
                        category: 'masak'
                    }
                ],
                next() {
                    this.activeSlide = (this.activeSlide + 1) % this.slides.length;
                },
                prev() {
                    this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
                },
                startAutoplay() {
                    this.autoplayTimer = setInterval(() => { this.next(); }, 5500);
                }
            }"
            x-init="startAutoplay()"
            @mouseenter="clearInterval(autoplayTimer)"
            @mouseleave="startAutoplay()">

        <div class="relative rounded-3xl overflow-hidden shadow-lg bg-[#121619] min-h-[300px] sm:min-h-[370px] flex items-center justify-center">
            <template x-for="(slide, index) in slides" :key="slide.id">
                <div x-show="activeSlide === index"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     class="absolute inset-0 w-full h-full">
                    <img :src="slide.image" :alt="slide.title" class="w-full h-full object-cover object-center">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/45 to-black/30"></div>
                </div>
            </template>

            <!-- Teks Tengah & Tombol CTA -->
            <div class="relative z-10 max-w-2xl mx-auto px-12 text-center text-white space-y-2.5">
                <h2 class="text-2xl sm:text-4xl font-black tracking-wide uppercase drop-shadow"
                    x-text="slides[activeSlide].title"></h2>
                <p class="text-xs sm:text-sm text-gray-200 font-medium drop-shadow"
                   x-text="slides[activeSlide].subtitle"></p>
                <div class="pt-2.5">
                    <a href="#daftar-peralatan"
                       @click="selectedCategory = slides[activeSlide].category"
                       class="inline-block bg-brand hover:bg-brand-hover text-white text-xs font-bold px-5 py-2.5 rounded-lg shadow-md transition"
                       x-text="slides[activeSlide].cta"></a>
                </div>
            </div>

            <!-- Panah Kiri / Kanan -->
            <button @click="prev()"
                    aria-label="Slide Sebelumnya"
                    class="absolute left-3.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-gray-800 flex items-center justify-center shadow text-sm font-bold transition">
                ‹
            </button>
            <button @click="next()"
                    aria-label="Slide Berikutnya"
                    class="absolute right-3.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-gray-800 flex items-center justify-center shadow text-sm font-bold transition">
                ›
            </button>
        </div>
    </header>

    <!-- Main Content Katalog -->
    <main id="daftar-peralatan" class="max-w-7xl mx-auto px-4 sm:px-6 py-6 w-full flex-grow space-y-6">
        <!-- Strip Kategori Ikon -->
        <section class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3">
            <template x-for="cat in [
                { key: 'tenda', label: 'TENDA & FLYSHEET', icon: '⛺' },
                { key: 'carrier', label: 'CARRIER / TAS', icon: '🎒' },
                { key: 'sepatu', label: 'SEPATU HIKING', icon: '🥾' },
                { key: 'masak', label: 'COOKING SET', icon: '🍳' },
                { key: 'penerangan', label: 'PENERANGAN', icon: '🔦' },
                { key: 'jaket', label: 'JAKET & APPAREL', icon: '🧥' },
                { key: 'aksesoris', label: 'AKSESORIS', icon: '🧭' }
            ]" :key="cat.key">
                <button type="button"
                        @click="selectedCategory = (selectedCategory === cat.key ? 'semua' : cat.key)"
                        :class="selectedCategory === cat.key ? 'border-brand ring-2 ring-brand/20 bg-white' : 'border-gray-200/90 bg-white hover:border-gray-300'"
                        class="rounded-2xl border px-3 py-4 flex flex-col items-center justify-center gap-2 shadow-2xs transition cursor-pointer">
                    <span class="text-xl leading-none" x-text="cat.icon"></span>
                    <span class="text-[10px] font-extrabold tracking-wider text-gray-700 uppercase text-center leading-tight"
                          x-text="cat.label"></span>
                </button>
            </template>
        </section>

        <div class="flex items-center justify-between">
            <h2 class="text-lg sm:text-xl font-extrabold text-gray-900 tracking-tight">Daftar Peralatan</h2>
            <button x-show="selectedCategory !== 'semua'"
                    @click="selectedCategory = 'semua'"
                    class="text-xs font-bold text-brand hover:underline">
                Tampilkan Semua
            </button>
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
                    $fotoUrl = null;
                    if (filled($alat->foto)) {
                        if (filter_var($alat->foto, FILTER_VALIDATE_URL)) {
                            $fotoUrl = $alat->foto;
                        } elseif (file_exists(public_path('images/alat/' . $alat->foto))) {
                            $fotoUrl = asset('images/alat/' . $alat->foto);
                        } else {
                            $fotoUrl = asset('storage/' . $alat->foto);
                        }
                    }
                    $namaKategori = strtolower($alat->kategori?->nama ?? '');
                @endphp
                <article x-show="selectedCategory === 'semua' || '{{ $namaKategori }}'.includes(selectedCategory)"
                         class="bg-white rounded-2xl border border-gray-200 p-3 flex flex-col justify-between hover:shadow-lg transition group">
                    <div>
                        <!-- Foto & Status Stok -->
                        <div @click="bukaDetail({{ $alat->id }})" class="relative h-48 sm:h-52 rounded-2xl bg-gradient-to-b from-gray-50/90 to-gray-100/80 overflow-hidden mb-3.5 flex items-center justify-center cursor-pointer group/img p-3.5 border border-gray-100/90">
                            @if ($fotoUrl)
                                <img src="{{ $fotoUrl }}"
                                     alt="{{ $alat->nama }}"
                                     loading="lazy"
                                     class="w-full h-full object-contain object-center group-hover/img:scale-108 transition-transform duration-300 drop-shadow-xs">
                            @else
                                @include('partials.foto-kosong', ['kelas' => 'w-14 h-14'])
                            @endif

                            <div class="absolute inset-0 bg-black/0 group-hover/img:bg-black/5 transition-colors duration-300 pointer-events-none"></div>

                            <span class="absolute top-2.5 right-2.5 z-10 text-[10px] font-bold px-2.5 py-0.5 rounded-full border backdrop-blur shadow-xs {{ $alat->stok_tersedia > 0 ? 'bg-emerald-500/90 text-white border-emerald-400' : 'bg-gray-800/80 text-gray-200 border-gray-700' }}">
                                {{ $alat->stok_tersedia > 0 ? 'Tersedia: '.$alat->stok_tersedia : 'Stok Habis' }}
                            </span>

                            @if ($alat->kategori)
                                <span class="absolute bottom-2.5 left-2.5 z-10 text-[10px] font-black uppercase px-2.5 py-0.5 rounded-lg bg-black/60 text-white backdrop-blur border border-white/20">
                                    {{ $alat->kategori->nama }}
                                </span>
                            @endif
                        </div>

                        <h3 @click="bukaDetail({{ $alat->id }})" class="font-extrabold text-sm text-gray-900 group-hover:text-brand transition-colors duration-200 line-clamp-1 mb-1.5 cursor-pointer" title="{{ $alat->nama }}">
                            {{ $alat->nama }}
                        </h3>

                        @if ($alat->deskripsi)
                            <p class="text-[11px] text-gray-500 line-clamp-2 leading-relaxed mb-3">
                                {{ $alat->deskripsi }}
                            </p>
                        @endif
                    </div>

                    <div class="pt-3 border-t border-gray-100 flex items-center justify-between gap-2 mt-2">
                        <div>
                            <p class="text-[9px] text-gray-400 uppercase font-black tracking-wider">TARIF / HARI</p>
                            <p class="text-sm font-black text-brand">Rp {{ number_format((float) $alat->tarif_per_hari, 0, ',', '.') }}</p>
                        </div>

                        <button @disabled($alat->stok_tersedia <= 0)
                                @click="addToCart({ id: {{ $alat->id }}, nama: '{{ addslashes($alat->nama) }}', tarif: {{ $alat->tarif_per_hari }}, foto: '{{ $fotoUrl ?? '' }}' })"
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
                    <div class="p-4 border-b border-gray-200 flex items-center justify-between bg-white">
                        <h3 class="font-bold text-base text-gray-900">Keranjang Belanja</h3>
                        <button @click="isCartOpen = false" class="text-gray-400 hover:text-gray-600 font-bold text-lg">✕</button>
                    </div>

                    <div class="px-4 py-3 border-b border-gray-100 flex items-center justify-between text-xs bg-gray-50/50">
                        <label class="flex items-center gap-2 cursor-pointer font-medium text-gray-700">
                            <input type="checkbox" @click="cart.forEach(i => i.selected = !i.selected)" :checked="cart.every(i => i.selected) && cart.length > 0" class="rounded text-brand focus:ring-brand">
                            <span>Pilih Semua</span>
                        </label>
                        <button @click="cart = []" class="text-brand font-semibold hover:underline">Hapus</button>
                    </div>

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

                <div class="p-4 border-t border-gray-200 bg-white space-y-3">
                    <div class="bg-gray-50 rounded-2xl p-3 border border-gray-200 space-y-2">
                        <div class="flex items-center justify-between text-[11px] font-bold text-gray-700">
                            <span>📅 Jadwal Sewa</span>
                            <span class="text-brand bg-brand/10 px-2 py-0.5 rounded-full text-[10px]" x-text="'Durasi: ' + lamaSewa + ' Hari'"></span>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <label class="block text-[10px] text-gray-500 font-semibold mb-1">Mulai</label>
                                <input type="date" x-model="tanggalMulai" class="w-full text-xs rounded-xl border-gray-300 bg-white py-1.5 px-2.5 focus:ring-brand focus:border-brand">
                            </div>
                            <div>
                                <label class="block text-[10px] text-gray-500 font-semibold mb-1">Selesai</label>
                                <input type="date" x-model="tanggalSelesai" :min="tanggalMulai" class="w-full text-xs rounded-xl border-gray-300 bg-white py-1.5 px-2.5 focus:ring-brand focus:border-brand">
                            </div>
                        </div>
                    </div>

                    @guest
                    <div class="bg-gray-50 rounded-2xl p-3 border border-gray-200 space-y-2">
                        <span class="text-[11px] font-bold text-gray-700 block">👤 Data Penyewa</span>
                        <div class="grid grid-cols-2 gap-2 text-xs">
                            <div>
                                <input type="text" x-model="namaPenyewa" placeholder="Nama Lengkap" class="w-full text-xs rounded-xl border-gray-300 bg-white py-1.5 px-2.5 focus:ring-brand focus:border-brand">
                            </div>
                            <div>
                                <input type="text" x-model="noTelepon" placeholder="No. WhatsApp" class="w-full text-xs rounded-xl border-gray-300 bg-white py-1.5 px-2.5 focus:ring-brand focus:border-brand">
                            </div>
                        </div>
                    </div>
                    @endguest

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
                            <span class="text-gray-900 font-black">Total Pembayaran</span>
                            <span class="font-black text-base text-brand" x-text="'Rp ' + Number(totalPrice).toLocaleString('id-ID')"></span>
                        </div>
                    </div>

                    <button @click="submitCheckout()"
                            :disabled="isCheckingOut || selectedItems.length === 0"
                            class="w-full bg-gray-900 hover:bg-brand disabled:opacity-50 disabled:cursor-not-allowed text-white text-xs font-bold py-3.5 rounded-2xl transition shadow-lg tracking-wide uppercase flex items-center justify-center gap-2">
                        <span x-show="!isCheckingOut">Checkout (<span x-text="selectedItems.length"></span> Item)</span>
                        <span x-show="isCheckingOut" class="flex items-center gap-2">
                            <svg class="animate-spin w-4 h-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                            <span>Memproses Booking...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Popup Konfirmasi Sukses Booking -->
    <div x-show="showSuccessModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
        <div class="min-h-screen px-4 text-center flex items-center justify-center">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showSuccessModal = false"></div>

            <div class="inline-block w-full max-w-md p-6 my-8 text-left align-middle transition-all transform bg-white shadow-2xl rounded-3xl relative z-10 border border-gray-100">
                <button @click="showSuccessModal = false" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 font-bold text-lg p-1 transition">
                    ✕
                </button>

                <div class="text-center space-y-3 pt-2">
                    <div class="w-16 h-16 bg-emerald-100 text-emerald-600 rounded-full flex items-center justify-center mx-auto text-3xl font-black shadow-inner">
                        ✓
                    </div>
                    <h3 class="text-xl font-black text-gray-900">Booking Berhasil Dibuat!</h3>
                    <p class="text-xs text-gray-500">
                        Pesanan sewa Anda telah berhasil disimpan dan langsung masuk ke daftar pemesanan di panel admin.
                    </p>
                </div>

                <div class="my-5 p-4 rounded-2xl bg-gray-50 border border-gray-100 space-y-2.5 text-xs">
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Kode Booking</span>
                        <span class="font-extrabold text-brand text-sm" x-text="'#' + (successOrder?.kode_pemesanan ?? '')"></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Status</span>
                        <span class="px-2.5 py-0.5 rounded-full bg-amber-50 text-amber-700 font-bold border border-amber-200 text-[10px]">Menunggu Pembayaran</span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-500">Durasi Sewa</span>
                        <span class="font-bold text-gray-800" x-text="(successOrder?.lama_sewa ?? 1) + ' Hari'"></span>
                    </div>
                    <div class="flex justify-between items-center border-t border-gray-200 pt-2">
                        <span class="text-gray-900 font-bold">Total Biaya</span>
                        <span class="font-black text-base text-gray-900" x-text="'Rp ' + Number(successOrder?.total_biaya ?? 0).toLocaleString('id-ID')"></span>
                    </div>
                </div>

                <button @click="showSuccessModal = false"
                        class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold py-3 rounded-2xl transition">
                    Tutup
                </button>
            </div>
        </div>
    </div>

    <!-- Dark Footer Full Width -->
    <footer class="bg-dark text-gray-400 text-xs pt-12 pb-6 border-t border-gray-800 w-full mt-auto">
        <div class="max-w-7xl mx-auto px-4 sm:px-6">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 mb-10">
                <div class="space-y-3">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-8 h-8 rounded-lg bg-brand flex items-center justify-center font-bold text-white text-xs">OS</div>
                        <span class="font-bold text-white text-sm">OutGear Station</span>
                    </div>
                    <div class="rounded-2xl bg-gray-50 border border-gray-100 p-3">
                        <p class="text-[10px] text-gray-400 uppercase font-bold">Stok tersedia</p>
                        <p class="font-black text-sm" :class="detail?.stok > 0 ? 'text-emerald-600' : 'text-red-500'" x-text="detail?.stok > 0 ? detail.stok + ' unit' : 'Habis'"></p>
                    </div>
                </div>

                <div class="space-y-3">
                    <h4 class="font-bold text-white text-sm tracking-wider uppercase">Jam Operasional</h4>
                    <div class="space-y-2 text-[11px]">
                        <p><strong class="text-white">Senin — Jumat:</strong> 08:00 - 21:00 WIB</p>
                        <p><strong class="text-white">Sabtu — Minggu:</strong> 07:00 - 22:00 WIB</p>
                        <p class="text-emerald-400 font-semibold pt-1">✔ Layanan Ambil/Kembali Buka Setiap Hari</p>
                    </div>
                </div>

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
                    <p x-show="qtyDiKeranjang > 0" class="text-[11px] text-gray-500" x-text="qtyDiKeranjang + ' unit alat ini sudah ada di keranjang'"></p>
                    <button type="button" @click="masukkanDariDetail()" :disabled="sisaBisaDitambah < 1"
                            class="w-full bg-brand hover:bg-brand-hover hover:scale-[1.01] active:scale-95 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed disabled:transform-none text-white text-xs sm:text-sm font-black py-4 rounded-2xl transition-all duration-200 shadow-xl shadow-brand/25 uppercase tracking-wide flex items-center justify-center gap-2 cursor-pointer"
                            x-text="detail?.stok < 1 ? 'Stok Habis' : (sisaBisaDitambah < 1 ? 'Semua Stok Sudah di Keranjang' : '🛒 Sewa & Masukkan ke Keranjang')"></button>
                </div>
            </div>

            <div class="pt-6 border-t border-gray-800 text-center text-[10px] text-gray-500">
                <p>© 2026 OutGear Station. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>

@endsection
