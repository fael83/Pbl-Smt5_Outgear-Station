@extends('layouts.customer')

@section('title', 'Katalog Sewa Alat Outdoor & Pendakian - OutGear Station')

@section('content')
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

@endsection
