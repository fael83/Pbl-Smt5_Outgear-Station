{{--
    Satu baris tabel data peralatan.

    Dipakai dua tempat:
    1. partial tabel (_tabel.blade.php) saat halaman dirender penuh / lewat AJAX;
    2. respons JSON simpan/edit, supaya browser cukup mengganti satu <tr>
       tanpa memuat ulang halaman.
--}}
@php
    // Status diturunkan dari stok yang tersimpan di database, bukan data statis.
    $status = match (true) {
        $alat->stok_tersedia > 0 && $alat->stok_rusak > 0 => [
            'label' => 'Sebagian Rusak',
            'kelas' => 'bg-amber-50 text-amber-700 border-amber-200',
            'titik' => 'bg-amber-500',
        ],
        $alat->stok_tersedia > 0 => [
            'label' => 'Tersedia',
            'kelas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'titik' => 'bg-emerald-500',
        ],
        $alat->stok_rusak > 0 => [
            'label' => 'Perlu Perbaikan',
            'kelas' => 'bg-red-50 text-red-600 border-red-200',
            'titik' => 'bg-red-500',
        ],
        default => [
            'label' => 'Stok Habis',
            'kelas' => 'bg-gray-100 text-gray-500 border-gray-200',
            'titik' => 'bg-gray-400',
        ],
    };
@endphp
<tr data-alat-id="{{ $alat->id }}" class="hover:bg-[#F4EFEA]/40 transition">
    <td class="p-3.5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-[#F4EFEA] flex items-center justify-center text-lg border border-gray-200 shrink-0">
                {{ \App\Support\IkonKategori::untuk($alat->kategori?->nama) }}
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-[#1A2024] truncate max-w-[220px]">{{ $alat->nama }}</p>
                @if (filled($alat->deskripsi))
                    <p class="text-[10px] text-gray-400 truncate max-w-[220px]">{{ $alat->deskripsi }}</p>
                @endif
            </div>
        </div>
    </td>
    <td class="p-3.5 font-semibold text-[#1A2024] whitespace-nowrap">
        #AL-{{ str_pad((string) $alat->id, 3, '0', STR_PAD_LEFT) }}
    </td>
    <td class="p-3.5 whitespace-nowrap">
        <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg bg-[#F4EFEA] text-[#1A2024] border border-gray-200">
            {{ $alat->kategori?->nama ?? '-' }}
        </span>
    </td>
    <td class="p-3.5 font-semibold text-[#1A2024] whitespace-nowrap">
        Rp {{ number_format((float) $alat->tarif_per_hari, 0, ',', '.') }}
    </td>
    <td class="p-3.5 text-gray-500 whitespace-nowrap">
        Rp {{ number_format((float) $alat->nilai_pengganti, 0, ',', '.') }}
    </td>
    <td class="p-3.5 whitespace-nowrap">
        <div class="flex items-center gap-1.5">
            <span class="px-2 py-1 text-[10px] font-bold rounded-lg {{ $alat->stok_tersedia > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
                {{ $alat->stok_tersedia }}
            </span>
            <span class="text-[10px] text-gray-300">/</span>
            <span class="px-2 py-1 text-[10px] font-bold rounded-lg {{ $alat->stok_disewa > 0 ? 'bg-[#15AEEA]/15 text-[#15AEEA]' : 'bg-gray-100 text-gray-500' }}">
                {{ $alat->stok_disewa }}
            </span>
            <span class="text-[10px] text-gray-300">/</span>
            <span class="px-2 py-1 text-[10px] font-bold rounded-lg {{ $alat->stok_rusak > 0 ? 'bg-[#D86B43]/15 text-[#D86B43]' : 'bg-gray-100 text-gray-500' }}">
                {{ $alat->stok_rusak }}
            </span>
            <span class="text-[10px] text-gray-400 ml-0.5">dari {{ $alat->stok_total }}</span>
        </div>
        <p class="text-[10px] text-gray-400 mt-1">tersedia · disewa · rusak</p>
    </td>
    <td class="p-3.5 text-center whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $status['kelas'] }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $status['titik'] }}"></span>
            {{ $status['label'] }}
        </span>
    </td>
    <td class="p-3.5 text-right whitespace-nowrap">
        <button type="button" @click="bukaEdit({{ $alat->id }})"
                class="px-3 py-1.5 bg-[#15AEEA]/15 text-[#15AEEA] rounded-lg font-semibold hover:bg-[#15AEEA]/25 transition">
            Edit
        </button>
        {{-- Tanpa JavaScript form ini tetap terkirim seperti biasa (POST + _method DELETE). --}}
        <form method="POST" action="{{ route('admin.peralatan.destroy', $alat) }}" class="inline"
              @submit.prevent="hapus($el)"
              data-pesan="Hapus alat &quot;{{ $alat->nama }}&quot;? Tindakan ini tidak bisa dibatalkan.">
            @csrf
            @method('DELETE')
            <input type="hidden" name="search" value="{{ $filters['search'] }}">
            <input type="hidden" name="kategori" value="{{ $filters['kategori'] ?: '' }}">
            <input type="hidden" name="kondisi" value="{{ $filters['kondisi'] }}">
            <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
            <button type="submit"
                    class="px-3 py-1.5 bg-red-50 text-red-600 rounded-lg font-semibold hover:bg-red-100 transition">
                Hapus
            </button>
        </form>
    </td>
</tr>
