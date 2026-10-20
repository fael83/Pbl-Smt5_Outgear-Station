{{--
    Satu baris tabel data pelanggan.

    Dipakai dua tempat:
    1. partial tabel (_tabel.blade.php) saat halaman dirender penuh / lewat AJAX;
    2. respons JSON ubah status, supaya browser cukup mengganti satu <tr>
       tanpa memuat ulang halaman.
--}}
@php
    $aktif = $pelanggan->status_akun === 'aktif';

    // Tombol aksi mengirim status tujuan, bukan status saat ini, sehingga form
    // tetap idempoten walau halaman sudah lama dibuka.
    $statusTujuan = $aktif ? 'nonaktif' : 'aktif';
    $jumlahPemesanan = (int) ($pelanggan->pemesanan_count ?? 0);

    $status = $aktif
        ? [
            'label' => 'Aktif',
            'kelas' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'titik' => 'bg-emerald-500',
        ]
        : [
            'label' => 'Nonaktif',
            'kelas' => 'bg-red-50 text-red-600 border-red-200',
            'titik' => 'bg-red-500',
        ];
@endphp
<tr data-pelanggan-id="{{ $pelanggan->id }}" class="hover:bg-[#F4EFEA]/40 transition">
    <td class="p-3.5">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 rounded-full bg-[#1A2024] text-white flex items-center justify-center font-bold text-[10px] shrink-0">
                {{ $pelanggan->inisial() }}
            </div>
            <div class="min-w-0">
                <p class="font-semibold text-[#1A2024] truncate max-w-[200px]">{{ $pelanggan->name }}</p>
                <p class="text-[10px] text-gray-400 truncate max-w-[200px]">{{ $pelanggan->email }}</p>
            </div>
        </div>
    </td>
    <td class="p-3.5 font-semibold text-[#1A2024] whitespace-nowrap">
        #PL-{{ str_pad((string) $pelanggan->id, 3, '0', STR_PAD_LEFT) }}
    </td>
    <td class="p-3.5 text-gray-600 whitespace-nowrap">
        {{ filled($pelanggan->no_telepon) ? $pelanggan->no_telepon : '-' }}
    </td>
    <td class="p-3.5 whitespace-nowrap">
        <span class="px-2.5 py-1 text-[10px] font-bold rounded-lg border {{ $jumlahPemesanan > 0 ? 'bg-[#15AEEA]/15 text-[#15AEEA] border-[#15AEEA]/30' : 'bg-gray-100 text-gray-500 border-gray-200' }}">
            {{ $jumlahPemesanan }} booking
        </span>
    </td>
    <td class="p-3.5 text-gray-500 whitespace-nowrap">
        {{ $pelanggan->created_at?->translatedFormat('d M Y') ?? '-' }}
    </td>
    <td class="p-3.5 text-center whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 text-[10px] font-bold rounded-full border {{ $status['kelas'] }}">
            <span class="w-1.5 h-1.5 rounded-full {{ $status['titik'] }}"></span>
            {{ $status['label'] }}
        </span>
    </td>
    <td class="p-3.5 text-right whitespace-nowrap">
        {{-- Tanpa JavaScript form ini tetap terkirim seperti biasa (POST + _method PATCH). --}}
        <form method="POST" action="{{ route('admin.pelanggan.status', $pelanggan) }}" class="inline"
              @submit.prevent="ubahStatus($el)"
              data-pesan="{{ $aktif
                  ? 'Nonaktifkan akun "'.$pelanggan->name.'"? Pelanggan tidak bisa masuk sampai akun diaktifkan kembali.'
                  : 'Aktifkan kembali akun "'.$pelanggan->name.'"?' }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status_akun" value="{{ $statusTujuan }}">
            <input type="hidden" name="search" value="{{ $filters['search'] }}">
            <input type="hidden" name="status" value="{{ $filters['status'] }}">
            <input type="hidden" name="per_page" value="{{ $filters['per_page'] }}">
            <button type="submit"
                    class="px-3 py-1.5 rounded-lg font-semibold transition {{ $aktif
                        ? 'bg-red-50 text-red-600 hover:bg-red-100'
                        : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                {{ $aktif ? 'Nonaktifkan' : 'Aktifkan' }}
            </button>
        </form>
    </td>
</tr>
