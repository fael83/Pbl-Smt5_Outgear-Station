{{--
    Penanda proses dan notifikasi ringan yang SAMA untuk semua halaman admin
    (dashboard, pelanggan, peralatan, pemesanan, verifikasi, denda, laporan).

    Aturan tampilannya:
    - pencarian, filter, pindah halaman, dan muat ulang yang mengambil data dari
      database menampilkan pop-up "Memuat data…" di bagian atas layar;
    - hasil proses (berhasil/gagal) muncul sebagai notifikasi di pojok kanan bawah;
    - aksi yang tidak butuh database (membuka kotak cari atau dropdown filter)
      berjalan instan dan tidak menampilkan pop-up.

    Partial ini membutuhkan state Alpine pada elemen induknya:
    - memuat      (bool)   -> pop-up "Memuat data…"
    - pesan       (string) -> isi notifikasi
    - pesanGagal  (bool)   -> true = warna merah, false = hijau
--}}
<div x-cloak x-show="memuat" x-transition.opacity
     class="fixed inset-x-0 top-20 z-[70] flex justify-center pointer-events-none"
     role="status" aria-live="polite">
    <div class="flex items-center gap-2 bg-[#1A2024] text-white text-xs font-semibold px-4 py-2 rounded-full shadow-lg">
        <span class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
        Memuat data…
    </div>
</div>

<div x-cloak x-show="pesan" x-transition.opacity
     class="fixed bottom-6 right-6 z-[70] max-w-sm px-4 py-3 rounded-xl shadow-lg text-xs font-semibold border"
     :class="pesanGagal ? 'bg-red-50 text-red-700 border-red-100' : 'bg-emerald-50 text-emerald-700 border-emerald-100'"
     role="status">
    <span x-text="pesan"></span>
</div>
