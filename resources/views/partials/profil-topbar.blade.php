{{--
    Profil pengguna di pojok kanan atas panel (admin & petugas).

    Dipakai halaman data peralatan (lewat layouts.panel), data booking, dan
    verifikasi pembayaran supaya tampilannya seragam: foto profil (avatar
    inisial nama), nama, dan email. Semua nilai dibaca langsung dari pengguna
    yang sedang login (tabel `users`), bukan data statis.
--}}
@php
    $penggunaProfil = auth()->user();
    $peranProfil = $penggunaProfil?->role?->value ?? $penggunaProfil?->role;
    $urlProfilTopbar = $peranProfil === 'petugas' ? route('petugas.profile') : route('admin.profile');
    $namaProfilTopbar = $penggunaProfil?->name ?? 'Petugas Gudang';
    $emailProfilTopbar = $penggunaProfil?->email ?? '-';
    $inisialProfilTopbar = $penggunaProfil?->inisial() ?? strtoupper(substr($namaProfilTopbar, 0, 2));
@endphp

<a href="{{ $urlProfilTopbar }}" title="Buka profil pengguna"
   class="flex items-center gap-3 pl-4 border-l border-zinc-200 hover:opacity-80 transition cursor-pointer group">
    <!-- Foto profil: avatar inisial nama pengguna -->
    <div class="w-9 h-9 rounded-full bg-[#1A2024] text-white flex items-center justify-center font-bold text-xs group-hover:bg-[#D86B43] transition shrink-0">
        {{ $inisialProfilTopbar }}
    </div>
    <div class="text-xs text-left">
        <p class="font-semibold text-[#1A2024] group-hover:text-[#D86B43] transition leading-tight">{{ $namaProfilTopbar }}</p>
        <p class="text-zinc-400 text-[11px] leading-tight mt-0.5">{{ $emailProfilTopbar }}</p>
    </div>
</a>
