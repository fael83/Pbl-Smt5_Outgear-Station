@extends('layouts.customer')

@section('title', 'Katalog Sewa Alat Outdoor & Pendakian - OutGear Station')

@section('content')
    <div x-data="{
        kategoriAktif: 'semua',
        barangDiperluas: false,
        pencarianTerbuka: false,
        kataKunci: '',
        cocokDenganPencarian(id) {
            const alat = (window.DATA_ALAT || {})[id];
            const kataKunci = this.kataKunci.trim().toLowerCase();
            if (!alat) return false;
            if (!kataKunci) return true;
            return [alat.nama, alat.deskripsi, alat.kategori]
                .filter(Boolean)
                .join(' ')
                .toLowerCase()
                .includes(kataKunci);
        },
        jumlahHasilPencarian() {
            return Object.values(window.DATA_ALAT || {}).filter((alat) => (
                (this.kategoriAktif === 'semua' || Number(alat.kategori_id) === Number(this.kategoriAktif)) &&
                this.cocokDenganPencarian(alat.id)
            )).length;
        }
    }" class="w-full">
    <!-- Header & Hero Section -->
    <header class="max-w-7xl mx-auto px-4 sm:px-6 pt-6 pb-2 w-full">
        <!-- Hero Slider -->
        <div x-data="{
            activeSlide: 0,
            autoplayTimer: null,
            slides: [
                {
                    id: 1,
                    title: 'OUTGEAR STATION',
                    subtitle: 'OutGear Station siap menemani setiap langkahmu booking mudah, alat terawat, dan layanan ramah langsung dari Basecamp Malang.',
                    image: 'https://images.unsplash.com/photo-1504280390367-361c6d9f38f4?auto=format&fit=crop&w=1600&q=80',
                    cta: 'Mulai Sewa Sekarang'
                },
                {
                    id: 2,
                    title: 'OUTGEAR STATION',
                    subtitle: 'Percayakan perjalanan alammu bersama OutGear Station pengalaman sewa praktis dengan standar kebersihan dan kesiapan alat yang terjaga.',
                    image: 'https://images.unsplash.com/photo-1551632811-561732d1e306?auto=format&fit=crop&w=1600&q=80',
                    cta: 'Mulai Sewa Sekarang'
                },
                {
                    id: 3,
                    title: 'OUTGEAR STATION',
                    subtitle: 'Wujudkan trip outdoor yang lebih nyaman dan terencana bareng OutGear Station mulai atur jadwal petualanganmu hari ini!',
                    image: 'https://images.unsplash.com/photo-1478131143081-80f7f84ca84d?auto=format&fit=crop&w=1600&q=80',
                    cta: 'Mulai Sewa Sekarang'
                }
            ],
            next() {
                this.activeSlide = (this.activeSlide + 1) % this.slides.length;
            },
            prev() {
                this.activeSlide = (this.activeSlide - 1 + this.slides.length) % this.slides.length;
            },
            startAutoplay() {
                clearInterval(this.autoplayTimer);
                this.autoplayTimer = setInterval(() => { this.next(); }, 5500);
            }
        }"
        x-init="startAutoplay()"
        @mouseenter="clearInterval(autoplayTimer)"
        @mouseleave="startAutoplay()"
        class="relative mx-auto w-full max-w-6xl">
            <div class="relative min-h-[250px] sm:min-h-[300px] rounded-2xl overflow-hidden shadow-lg bg-[#121619] flex items-center justify-center">
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

                <div class="relative z-10 max-w-2xl mx-auto px-12 text-center text-white space-y-2.5">
                    <h2 class="text-2xl sm:text-3xl font-black tracking-wide uppercase drop-shadow"
                        x-text="slides[activeSlide].title"></h2>
                    <p class="text-xs sm:text-sm text-gray-200 font-medium drop-shadow"
                       x-text="slides[activeSlide].subtitle"></p>
                    <div class="pt-1">
                        <a href="#kategori-katalog"
                           class="inline-block bg-brand hover:bg-brand-hover text-white text-[11px] font-bold px-4 py-2 rounded-lg shadow-md transition"
                           x-text="slides[activeSlide].cta"></a>
                    </div>
                </div>

                <button type="button" @click="prev()" aria-label="Slide Sebelumnya"
                        class="absolute left-3.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-gray-800 flex items-center justify-center shadow text-sm font-bold transition">
                    ‹
                </button>
                <button type="button" @click="next()" aria-label="Slide Berikutnya"
                        class="absolute right-3.5 top-1/2 -translate-y-1/2 z-20 w-8 h-8 rounded-full bg-white/90 hover:bg-white text-gray-800 flex items-center justify-center shadow text-sm font-bold transition">
                    ›
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content Katalog -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 w-full flex-grow">
        <section id="kategori-katalog" aria-label="Filter kategori peralatan" class="mb-7">
        <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-9 gap-2">
            <button type="button" @click="kategoriAktif = 'semua'; barangDiperluas = false"
                    aria-label="Tampilkan semua peralatan"
                    :aria-pressed="kategoriAktif === 'semua'"
                    :class="kategoriAktif === 'semua' ? 'border-brand bg-brand/10 text-brand shadow-sm' : 'border-gray-100 bg-white text-gray-700 hover:border-brand/40 hover:shadow-md'"
                    class="flex min-h-[72px] flex-col items-center justify-center gap-1 rounded-xl border px-2 py-2 text-center transition-all">
                <span class="text-lg leading-none" aria-hidden="true">🏕️</span>
                <span class="text-[8px] font-bold uppercase tracking-wide leading-tight">Semua</span>
            </button>
            @foreach ($peralatan->pluck('kategori')->filter()->unique('id')->values() as $kategori)
                    @php
                        $namaKategori = strtolower($kategori->nama);
                        $ikonKategori = match (true) {
                            str_contains($namaKategori, 'tenda') => '⛺',
                            str_contains($namaKategori, 'carrier'), str_contains($namaKategori, 'tas') => '🎒',
                            str_contains($namaKategori, 'sepatu') => '🥾',
                            str_contains($namaKategori, 'masak') => '🍳',
                            str_contains($namaKategori, 'penerangan') => '🔦',
                            str_contains($namaKategori, 'sleeping') => '🛌',
                            str_contains($namaKategori, 'navigasi') => '🧭',
                            str_contains($namaKategori, 'matras') => '🛏️',
                            default => '🏕️',
                        };
                    @endphp
                    <button type="button" data-kategori="{{ $kategori->id }}"
                            @click="kategoriAktif = kategoriAktif === {{ $kategori->id }} ? 'semua' : {{ $kategori->id }}; barangDiperluas = false"
                            :aria-pressed="kategoriAktif === {{ $kategori->id }}"
                            :class="kategoriAktif === {{ $kategori->id }} ? 'border-brand bg-brand/10 text-brand shadow-sm' : 'border-gray-100 bg-white text-gray-700 hover:border-brand/40 hover:shadow-md'"
                            class="flex min-h-[72px] flex-col items-center justify-center gap-1 rounded-xl border px-2 py-2 text-center transition-all">
                        <span class="text-lg leading-none" aria-hidden="true">{{ $ikonKategori }}</span>
                        <span class="text-[8px] font-bold uppercase tracking-wide leading-tight">{{ $kategori->nama }}</span>
                    </button>
                @endforeach
            </div>
        </section>

        <div class="mb-6 flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-gray-200">
            <div>
                <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight flex items-center gap-2">
                    <span>Daftar Peralatan Outdoor</span>
                </h2>
                <p class="text-xs text-gray-500 mt-1">Pilih perlengkapan petualanganmu dan klik tombol <strong class="text-brand">Sewa Barang</strong> untuk langsung memesan.</p>
            </div>
            <div class="flex flex-col items-stretch gap-2 self-start sm:flex-row sm:items-center sm:self-auto">
                <div class="flex items-center gap-2">
                    <div class="text-xs text-gray-500 font-semibold flex items-center gap-2 bg-white px-3.5 py-2 rounded-2xl border border-gray-200 shadow-xs">
                        <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                        <span>{{ $peralatan->count() }} Pilihan Alat Siap Sewa</span>
                    </div>
                    <button type="button" x-show="!pencarianTerbuka" x-cloak
                            @click="pencarianTerbuka = true; $nextTick(() => $refs.inputPencarian.focus())"
                            :aria-expanded="pencarianTerbuka"
                            aria-label="Cari peralatan"
                            class="rounded-2xl border border-gray-200 bg-white p-2.5 text-gray-500 shadow-xs transition hover:border-brand/40 hover:text-brand">
                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <circle cx="11" cy="11" r="7" stroke-width="2"/>
                            <path stroke-linecap="round" stroke-width="2" d="m20 20-4-4"/>
                        </svg>
                    </button>
                </div>
                <div x-show="pencarianTerbuka" x-transition x-cloak class="relative">
                    <svg class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <circle cx="11" cy="11" r="7" stroke-width="2"/>
                        <path stroke-linecap="round" stroke-width="2" d="m20 20-4-4"/>
                    </svg>
                    <input x-ref="inputPencarian" x-model.trim="kataKunci" type="search"
                           aria-label="Cari barang di katalog" placeholder="Cari nama barang..."
                           class="w-full rounded-2xl border border-gray-200 bg-white py-2 pl-9 pr-9 text-xs text-gray-700 shadow-xs outline-none transition focus:border-brand focus:ring-2 focus:ring-brand/20 sm:w-56">
                    <button type="button" @click="pencarianTerbuka = false; kataKunci = ''"
                            aria-label="Tutup pencarian"
                            class="absolute right-2 top-1/2 -translate-y-1/2 rounded-full px-1.5 py-0.5 text-gray-400 transition hover:bg-gray-100 hover:text-gray-700">
                        ✕
                    </button>
                </div>
            </div>
        </div>

        @php
            $dataAlat = [];
            foreach ($peralatan as $item) {
                $dataAlat[$item->id] = [
                    'id' => $item->id,
                    'kategori_id' => $item->kategori_id,
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
            @forelse ($peralatan as $index => $alat)
                @php
                    $fotoUrl = $alat->foto_url;
                @endphp
                <article x-show="cocokDenganPencarian({{ $alat->id }}) && (kategoriAktif === 'semua' ? (barangDiperluas || kataKunci.trim() !== '' || {{ $index }} < 12) : kategoriAktif === {{ $alat->kategori_id ?? 'null' }})"
                         @if ($index >= 12) x-cloak @endif
                         class="bg-white rounded-3xl border border-gray-200/90 p-3.5 sm:p-4 flex flex-col justify-between hover:shadow-xl hover:border-brand/40 hover:-translate-y-1.5 transition-all duration-300 group">
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

                        <!-- Tombol Sewa Barang dengan Respon Interaktif Kursor -->
                        @if ($alat->stok_tersedia > 0)
                            <button type="button" @click="bukaDetail({{ $alat->id }})"
                                    class="bg-brand hover:bg-brand-hover hover:scale-105 active:scale-95 text-white shadow-md shadow-brand/20 hover:shadow-lg hover:shadow-brand/35 text-xs font-bold px-3.5 sm:px-4 py-2 rounded-xl transition-all duration-200 flex items-center gap-1.5 group/btn cursor-pointer">
                                <span>Sewa Barang</span>
                                <svg class="w-3.5 h-3.5 transition-transform duration-200 group-hover/btn:translate-x-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 5l7 7-7 7"/>
                                </svg>
                            </button>
                        @else
                            <button type="button" disabled
                                    class="bg-gray-100 text-gray-400 text-xs font-bold px-3.5 py-2 rounded-xl cursor-not-allowed border border-gray-200">
                                Stok Habis
                            </button>
                        @endif
                    </div>
                </article>
            @empty
                <p class="col-span-full rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
                    Belum ada peralatan yang terdaftar.
                </p>
            @endforelse
        </div>
        <p x-show="jumlahHasilPencarian() === 0 && (kataKunci.trim() !== '' || kategoriAktif !== 'semua')" x-cloak
           class="mt-4 rounded-2xl border border-dashed border-gray-300 bg-white p-8 text-center text-sm text-gray-500">
            Tidak ada barang yang cocok dengan pencarian atau kategori ini.
        </p>
        @if ($peralatan->count() > 12)
            <div x-show="kategoriAktif === 'semua' && kataKunci.trim() === ''" x-cloak class="mt-6 flex justify-center">
                <button type="button" @click="barangDiperluas = !barangDiperluas"
                        :aria-expanded="barangDiperluas"
                        class="inline-flex items-center gap-1 rounded-full px-4 py-2 text-sm font-bold text-brand transition hover:bg-brand/10">
                    <span x-text="barangDiperluas ? 'Tampilkan lebih sedikit' : 'Tampilkan lebih banyak'">Tampilkan lebih banyak</span>
                    <svg class="h-4 w-4 transition-transform" :class="barangDiperluas ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
            </div>
        @endif
    </main>
    </div>

<!-- Modal detail alat -->
<div x-show="showDetail" x-cloak style="display: none;" class="fixed inset-0 z-[55] overflow-y-auto" @keydown.escape.window="showDetail = false">
    <div class="min-h-screen px-4 flex items-center justify-center">
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm transition-opacity" @click="showDetail = false"></div>

        <div class="relative z-10 w-full max-w-3xl my-8 bg-white rounded-3xl shadow-2xl overflow-hidden grid md:grid-cols-2 border border-gray-100">
            <button type="button" @click="showDetail = false"
                    class="absolute top-3 right-3 z-10 w-9 h-9 rounded-full bg-white/90 text-gray-500 hover:text-gray-900 hover:bg-white hover:scale-105 active:scale-95 font-bold shadow-md flex items-center justify-center transition-all duration-200 cursor-pointer">✕</button>

            <div class="bg-gradient-to-br from-gray-50 via-gray-100/70 to-gray-100 min-h-[280px] md:min-h-[440px] flex items-center justify-center p-6 relative overflow-hidden group">
                <!-- Ambient blur backdrop -->
                <template x-if="detail && detail.foto">
                    <img :src="detail.foto" class="absolute inset-0 w-full h-full object-cover blur-2xl opacity-20 scale-125 pointer-events-none">
                </template>

                <template x-if="detail && detail.foto">
                    <img :src="detail.foto" :alt="detail.nama"
                         class="relative z-10 w-full h-full max-h-[300px] md:max-h-[390px] object-contain object-center drop-shadow-lg transition-transform duration-300 group-hover:scale-105">
                </template>
                <template x-if="detail && !detail.foto">
                    <div class="relative z-10 flex items-center justify-center">
                        @include('partials.foto-kosong', ['kelas' => 'w-20 h-20'])
                    </div>
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

                <div class="mt-auto space-y-3 pt-2">
                    <div class="flex items-center justify-between bg-gray-50 p-3 rounded-2xl border border-gray-100">
                        <span class="text-xs font-bold text-gray-700">Jumlah Unit Sewa</span>
                        <div class="flex items-center border border-gray-200 rounded-xl overflow-hidden bg-white shadow-xs">
                            <button type="button" @click="detailQty = Math.max(1, detailQty - 1)"
                                    class="px-3.5 py-1.5 text-gray-600 hover:bg-gray-100 active:scale-90 font-bold transition cursor-pointer">-</button>
                            <span class="px-4 text-sm font-extrabold text-gray-900" x-text="detailQty"></span>
                            <button type="button" @click="detailQty = Math.min(Math.max(1, sisaBisaDitambah), detailQty + 1)"
                                    class="px-3.5 py-1.5 text-gray-600 hover:bg-gray-100 active:scale-90 font-bold transition cursor-pointer">+</button>
                        </div>
                    </div>
                    <p x-show="qtyDiKeranjang > 0" class="text-[11px] text-gray-500" x-text="qtyDiKeranjang + ' unit alat ini sudah ada di keranjang'"></p>
                    <button type="button" @click="masukkanDariDetail()" :disabled="sisaBisaDitambah < 1"
                            class="w-full bg-brand hover:bg-brand-hover hover:scale-[1.01] active:scale-95 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed disabled:transform-none text-white text-xs sm:text-sm font-black py-4 rounded-2xl transition-all duration-200 shadow-xl shadow-brand/25 uppercase tracking-wide flex items-center justify-center gap-2 cursor-pointer"
                            x-text="detail?.stok < 1 ? 'Stok Habis' : (sisaBisaDitambah < 1 ? 'Semua Stok Sudah di Keranjang' : '🛒 Sewa & Masukkan ke Keranjang')"></button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
