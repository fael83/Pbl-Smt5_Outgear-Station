<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'OutGear Station')</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @php
        // Satu sumber data untuk SEMUA halaman pelanggan (navbar, badge, keranjang, Midtrans).
        $midtransLayout = app(\App\Services\MidtransService::class);
        $badgeBooking = auth()->check() ? \App\Support\BadgeBooking::untuk(auth()->id()) : 0;
    @endphp
    @if (filled($midtransLayout->clientKey()))
        {{-- Pop-up pembayaran Midtrans Snap (Sandbox/Production mengikuti .env) --}}
        <script src="{{ $midtransLayout->urlSnapJs() }}" data-client-key="{{ $midtransLayout->clientKey() }}"></script>
    @endif

    <script>
        // State keranjang & checkout dipakai SEMUA halaman pelanggan (satu sumber), jadi keranjang
        // selalu terbuka di halaman yang sedang dilihat tanpa berpindah ke katalog.
        function keranjangOutgear() {
            const hariIni = {!! json_encode(now()->toDateString()) !!};
            const urlLogin = {!! json_encode(route('login')) !!};
            const urlCheckout = {!! json_encode(route('checkout.store')) !!};
            const csrf = {!! json_encode(csrf_token()) !!};
            return {
                cart: [],
                isCartOpen: false,
                hariIni: hariIni,
                tanggalMulai: hariIni,
                tanggalSelesai: {!! json_encode(now()->addDays(2)->toDateString()) !!},
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
                    this.muatKeranjang();
                    if (this.tanggalSelesai < this.tanggalMulai) this.tanggalSelesai = this.tanggalMulai;
                    this.$watch('cart', () => this.simpanKeranjang());
                    this.$watch('tanggalMulai', (v) => { if (this.tanggalSelesai < v) this.tanggalSelesai = v; this.simpanKeranjang(); });
                    this.$watch('tanggalSelesai', () => this.simpanKeranjang());
                    // Sinkron bila keranjang diubah dari tab lain.
                    window.addEventListener('storage', (e) => { if (e.key === 'outgear.keranjang') this.muatKeranjang(); });
                },

                muatKeranjang() {
                    try {
                        const simpan = JSON.parse(localStorage.getItem('outgear.keranjang') || 'null');
                        if (simpan && Array.isArray(simpan.cart)) {
                            this.cart = simpan.cart;
                            if (simpan.tanggalMulai && simpan.tanggalMulai >= this.hariIni) this.tanggalMulai = simpan.tanggalMulai;
                            if (simpan.tanggalSelesai && simpan.tanggalSelesai >= this.tanggalMulai) this.tanggalSelesai = simpan.tanggalSelesai;
                        }
                    } catch (e) {}
                },

                simpanKeranjang() {
                    try {
                        localStorage.setItem('outgear.keranjang', JSON.stringify({
                            cart: this.cart, tanggalMulai: this.tanggalMulai, tanggalSelesai: this.tanggalSelesai
                        }));
                    } catch (e) {}
                },

                rp(n) { return 'Rp ' + Number(n || 0).toLocaleString('id-ID'); },

                // --- Detail alat (hanya dipakai di katalog) ---
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

                // --- Keranjang ---
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
                    let diff = Math.round((new Date(this.tanggalSelesai) - new Date(this.tanggalMulai)) / 86400000);
                    return Math.max(1, diff + 1);
                },
                get tarifPerHari() {
                    return this.cart.filter(i => i.selected).reduce((sum, item) => sum + (item.tarif * item.qty), 0);
                },
                get totalPrice() { return this.tarifPerHari * this.lamaSewa; },
                get jumlahDp() { return Math.round(this.totalPrice * 0.5); },

                // --- Checkout + Midtrans Snap ---
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
                        window.location.href = urlLogin;
                        return;
                    }
                    if (!this.isCustomer) {
                        this.pesanError = 'Hanya akun pelanggan yang dapat menyewa alat.';
                        return;
                    }

                    this.pesanError = '';
                    this.isCheckingOut = true;
                    try {
                        let res = await fetch(urlCheckout, {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' },
                            body: JSON.stringify({
                                items: selected.map(i => ({ id: i.id, qty: i.qty })),
                                tanggal_mulai: this.tanggalMulai,
                                tanggal_selesai: this.tanggalSelesai
                            })
                        });

                        if (res.status === 401 || res.status === 419) {
                            this.simpanKeranjang();
                            window.location.href = urlLogin;
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
                            headers: { 'X-CSRF-TOKEN': csrf, 'Accept': 'application/json' }
                        });
                        let d = await res.json();
                        if (d.status_transaksi === 'berhasil') this.hasil.status = 'lunas';
                        else if (d.status_transaksi === 'gagal') this.hasil.status = 'gagal';
                    } catch (e) {}
                }
            };
        }
    </script>
    @stack('head')
</head>

<body class="bg-[#F8F6F2] text-gray-800 font-sans antialiased min-h-screen flex flex-col" x-data="keranjangOutgear()">

    <!-- Navbar pelanggan (sama di semua halaman) -->
    <nav class="bg-dark text-white px-6 sm:px-8 py-5 sm:py-6 flex items-center justify-between shadow-xl sticky top-0 z-50 border-b border-white/10 w-full backdrop-blur-md bg-dark/95">
        <a href="{{ route('katalog') }}" class="flex items-center gap-4 group">
            <div class="w-12 h-12 rounded-2xl bg-brand flex items-center justify-center font-black text-white shadow-xl shadow-brand/30 text-base tracking-wider group-hover:scale-105 group-hover:shadow-brand/50 group-hover:rotate-1 transition-all duration-300">
                OS
            </div>
            <div>
                <h1 class="font-black text-base tracking-wide text-white leading-tight group-hover:text-brand transition-colors duration-200">OutGear Station</h1>
                <p class="text-[11px] text-gray-400 tracking-widest uppercase font-semibold">Rental & Outdoor Equipment</p>
            </div>
        </a>

        <div class="hidden md:flex items-center gap-2 lg:gap-3 text-sm font-bold tracking-wide">
            <a href="{{ route('katalog') }}"
               class="relative px-4 py-2 rounded-xl transition-all duration-200 flex items-center gap-2 group {{ request()->routeIs('katalog') ? 'bg-brand/20 text-brand border border-brand/40 shadow-sm shadow-brand/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                <span class="group-hover:scale-105 transition-transform duration-200">Katalog</span>
                @if(request()->routeIs('katalog'))
                    <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
                @endif
            </a>

            <a href="{{ route('booking.saya') }}"
               class="relative px-4 py-2 rounded-xl transition-all duration-200 flex items-center gap-2.5 group {{ request()->routeIs('booking.saya') ? 'bg-brand/20 text-brand border border-brand/40 shadow-sm shadow-brand/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                <span class="group-hover:translate-x-0.5 group-hover:text-white transition-all duration-200">Booking Saya</span>
                @if ($badgeBooking > 0)
                    <span class="bg-brand text-white text-xs px-2.5 py-0.5 rounded-full font-black shadow-sm group-hover:scale-110 group-hover:shadow-brand/50 transition-all duration-200">{{ $badgeBooking }}</span>
                @endif
                @if(request()->routeIs('booking.saya'))
                    <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
                @endif
            </a>

            <a href="{{ route('panduan') }}"
               class="relative px-4 py-2 rounded-xl transition-all duration-200 flex items-center gap-2 group {{ request()->routeIs('panduan') ? 'bg-brand/20 text-brand border border-brand/40 shadow-sm shadow-brand/10' : 'text-gray-300 hover:text-white hover:bg-white/10' }}">
                <span class="group-hover:scale-105 transition-transform duration-200">Panduan Sewa</span>
                @if(request()->routeIs('panduan'))
                    <span class="w-1.5 h-1.5 rounded-full bg-brand animate-pulse"></span>
                @endif
            </a>
        </div>

        <div class="flex items-center gap-3 sm:gap-4">
            <button type="button" @click="isCartOpen = true"
                    class="bg-brand hover:bg-brand-hover hover:scale-105 active:scale-95 text-white text-xs font-black px-4 sm:px-5 py-3 rounded-2xl flex items-center gap-2.5 sm:gap-3 transition-all duration-200 shadow-lg shadow-brand/25 hover:shadow-brand/40 cursor-pointer">
                <span class="text-sm">🛒 Keranjang</span>
                <span class="bg-white/20 backdrop-blur text-white text-[11px] font-black px-2.5 py-0.5 rounded-full" x-text="totalItems + ' Item'">0 Item</span>
            </button>

            @auth
                <a href="{{ route('profile.edit') }}"
                   class="w-11 h-11 rounded-2xl bg-brand text-white flex items-center justify-center text-sm font-black border {{ request()->routeIs('profile.*') ? 'border-white' : 'border-brand/50' }} shadow-md shadow-brand/20 hover:scale-105 hover:shadow-brand/40 transition-all duration-200"
                   title="Profil Saya ({{ auth()->user()->name }})">
                    {{ strtoupper(substr(auth()->user()->name ?? 'U', 0, 2)) }}
                </a>
            @else
                <a href="{{ route('login') }}"
                   class="w-11 h-11 rounded-2xl bg-gray-800 text-gray-300 hover:text-white hover:bg-gray-700 hover:scale-105 flex items-center justify-center text-sm transition-all duration-200 border border-gray-700/60 shadow-sm"
                   title="Masuk ke Akun">👤</a>
            @endauth
        </div>
    </nav>

    <!-- Konten halaman -->
    <div class="flex-grow w-full">
        @yield('content')
    </div>

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

                                <div class="w-16 h-16 rounded-xl bg-gray-50 overflow-hidden shrink-0 flex items-center justify-center border border-gray-200 p-1">
                                    <template x-if="item.foto">
                                        <img :src="item.foto" class="w-full h-full object-contain object-center">
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
