{{--
    Satu baris tabel data peralatan.

    Dipakai dua tempat:
    1. partial tabel (_tabel.blade.php) saat halaman dirender penuh / lewat AJAX;
    2. respons JSON simpan/edit, supaya browser cukup mengganti satu <tr>
       tanpa memuat ulang halaman.
--}}
<tr data-alat-id="{{ $alat->id }}" class="hover:bg-gray-50/50 transition">
    <td class="p-3.5 font-semibold text-[#1A2024] whitespace-nowrap">
        #AL-{{ str_pad((string) $alat->id, 3, '0', STR_PAD_LEFT) }}
    </td>
    <td class="p-3.5">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-lg border border-gray-200">
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
    <td class="p-3.5 text-gray-500 whitespace-nowrap">{{ $alat->kategori?->nama ?? '-' }}</td>
    <td class="p-3.5 font-medium text-[#1A2024] whitespace-nowrap">
        Rp {{ number_format((float) $alat->tarif_per_hari, 0, ',', '.') }}
    </td>
    <td class="p-3.5 text-gray-500 whitespace-nowrap">
        Rp {{ number_format((float) $alat->nilai_pengganti, 0, ',', '.') }}
    </td>
    <td class="p-3.5 text-center font-bold text-[#1A2024]">{{ $alat->stok_total }}</td>
    <td class="p-3.5 text-center">
        <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg {{ $alat->stok_tersedia > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-gray-100 text-gray-500' }}">
            {{ $alat->stok_tersedia }} Unit
        </span>
    </td>
    <td class="p-3.5 text-center">
        <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg {{ $alat->stok_disewa > 0 ? 'bg-[#15AEEA]/15 text-[#15AEEA]' : 'bg-gray-100 text-gray-500' }}">
            {{ $alat->stok_disewa }} Unit
        </span>
    </td>
    <td class="p-3.5 text-center">
        <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg {{ $alat->stok_rusak > 0 ? 'bg-[#D86B43]/15 text-[#D86B43]' : 'bg-gray-100 text-gray-500' }}">
            {{ $alat->stok_rusak }} Unit
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
