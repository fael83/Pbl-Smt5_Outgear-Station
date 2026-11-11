{{--
    Tombol "Muat ulang data" yang SAMA untuk semua halaman admin.

    Memanggil muatUlang() pada komponen Alpine halaman: data diambil ulang dari
    database (cache dibuang), ikonnya berputar, dan pop-up "Memuat data…"
    tampil selama menunggu server. Tidak ada reload halaman.

    Parameter opsional:
    - $fallback : URL untuk pengguna tanpa JavaScript (mis. ?segarkan=1).
                  Bila diisi tombol dirender sebagai tautan.
--}}
@php
    $kelasTombolMuatUlang = 'p-2 bg-white border border-zinc-200/80 rounded-full text-zinc-500 hover:text-zinc-800 hover:bg-zinc-50 shadow-sm transition flex items-center justify-center';
@endphp

@if (! empty($fallback))
    <a href="{{ $fallback }}"
       @click.prevent="muatUlang()"
       :class="memuat ? 'pointer-events-none opacity-60' : ''"
       class="{{ $kelasTombolMuatUlang }}"
       :title="memuat ? 'Memuat data…' : 'Muat ulang data'"
       aria-label="Muat ulang data">
        <svg class="w-4 h-4" :class="memuat ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
    </a>
@else
    <button type="button"
            @click="muatUlang()"
            :disabled="memuat"
            :class="memuat ? 'pointer-events-none opacity-60' : ''"
            class="{{ $kelasTombolMuatUlang }}"
            :title="memuat ? 'Memuat data…' : 'Muat ulang data'"
            aria-label="Muat ulang data">
        <svg class="w-4 h-4" :class="memuat ? 'animate-spin' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
        </svg>
    </button>
@endif
