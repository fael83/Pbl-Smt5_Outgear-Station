@extends('layouts.owner')

@section('content')
<div class="space-y-6 pt-2 pb-10">

    <!-- Header -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-zinc-900 tracking-tight">
                Kelola Harga Sewa & Tarif
            </h2>
            <p class="text-xs text-zinc-500 mt-0.5">
                Kelola dan perbarui tarif sewa harian serta nilai ganti rugi peralatan outdoor
            </p>
        </div>
    </div>

    <!-- Kartu Metrik Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">

        <!-- Total Unit -->
        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <div class="text-3xl font-black text-slate-500">
                    {{ number_format(optional($alat)->sum('stok_total') ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">
                    Total Unit Keseluruhan
                </div>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-zinc-100 text-zinc-600 border border-zinc-200">
                {{ optional($kategori_alat)->count() ?? 0 }} Kategori
            </span>
        </div>

        <!-- Rata-rata Tarif -->
        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <div class="text-3xl font-black text-[#C17A45]">
                    Rp {{ number_format(optional($alat)->avg('tarif_per_hari') ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">
                    Rata-Rata Tarif / Hari
                </div>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-[#F8EEE5] text-[#A96835] border border-[#E8D1BE]">
                Tarif Sewa
            </span>
        </div>

        <!-- Unit Disewa -->
        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <div class="text-3xl font-black text-[#548579]">
                    {{ number_format(optional($alat)->sum('stok_disewa') ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">
                    Unit Sedang Disewa
                </div>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200 flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                Aktif
            </span>
        </div>

        <!-- Maintenance -->
        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <div class="text-3xl font-black text-[#C75B5B]">
                    {{ number_format(optional($alat)->sum('stok_rusak') ?? 0, 0, ',', '.') }}
                </div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">
                    Perlu Maintenance
                </div>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-red-50 text-red-700 border border-red-200">
                Perhatian
            </span>
        </div>

    </div>

    <!-- Filter dan Tabel -->
    <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">

        <!-- Header Tabel -->
        <form method="GET"
            action="{{ url('/owner/harga-sewa') }}"
            class="p-5 border-b border-zinc-100 flex flex-col md:flex-row md:items-center justify-between gap-4">

            <div class="flex items-center gap-3">
                <h3 class="font-bold text-zinc-900 text-base">
                    Daftar Tarif & Inventaris Alat
                </h3>
                <span class="text-xs font-medium text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">
                    {{ optional($alat)->count() ?? 0 }} data
                </span>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">
                    Filter Kategori:
                </span>

                <select name="kategori_id"
                    onchange="this.form.submit()"
                    class="bg-zinc-100 border border-zinc-300 text-xs font-medium text-zinc-700 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-zinc-400 cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori_alat as $kategori)
                    <option value="{{ $kategori->id }}"
                        {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                        {{ $kategori->nama }}
                    </option>
                    @endforeach
                </select>

                @if(request('kategori_id'))
                <a href="{{ url('/owner/harga-sewa') }}"
                    class="text-xs font-semibold text-zinc-700 hover:underline">
                    Reset
                </a>
                @endif
            </div>
        </form>

        <!-- Tabel Utama -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs align-middle">
                <thead>
                    <tr class="text-zinc-400 font-bold border-b border-zinc-100 bg-zinc-50/50">
                        <th class="p-4 uppercase">Alat & Deskripsi</th>
                        <th class="p-4 uppercase text-center">Kategori</th>
                        <th class="p-4 uppercase text-center">Tarif / Hari & Pengganti</th>
                        <th class="p-4 uppercase text-center">Stok (Tersedia / Disewa / Rusak)</th>
                        <th class="p-4 uppercase text-center">Aksi</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                    @forelse($alat as $item)
                    @php
                    $namaKat = strtolower($item->kategori->nama ?? '');

                    if (str_contains($namaKat, 'tenda')) {
                    $badgeBg = 'bg-orange-50 text-orange-700 border-orange-200';
                    $dotBg = 'bg-orange-500';
                    } elseif (str_contains($namaKat, 'carrier') || str_contains($namaKat, 'tas')) {
                    $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200';
                    $dotBg = 'bg-blue-500';
                    } elseif (str_contains($namaKat, 'masak') || str_contains($namaKat, 'cooking')) {
                    $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    $dotBg = 'bg-emerald-500';
                    } elseif (str_contains($namaKat, 'penerangan')) {
                    $badgeBg = 'bg-amber-50 text-amber-700 border-amber-200';
                    $dotBg = 'bg-amber-500';
                    } elseif (str_contains($namaKat, 'sleeping')) {
                    $badgeBg = 'bg-purple-50 text-purple-700 border-purple-200';
                    $dotBg = 'bg-purple-500';
                    } elseif (str_contains($namaKat, 'navigasi')) {
                    $badgeBg = 'bg-indigo-50 text-indigo-700 border-indigo-200';
                    $dotBg = 'bg-indigo-500';
                    } elseif (str_contains($namaKat, 'sepatu') || str_contains($namaKat, 'sandal')) {
                    $badgeBg = 'bg-teal-50 text-teal-700 border-teal-200';
                    $dotBg = 'bg-teal-500';
                    } elseif (str_contains($namaKat, 'matras') || str_contains($namaKat, 'alas')) {
                    $badgeBg = 'bg-cyan-50 text-cyan-700 border-cyan-200';
                    $dotBg = 'bg-cyan-500';
                    } else {
                    $badgeBg = 'bg-zinc-100 text-zinc-700 border-zinc-200';
                    $dotBg = 'bg-zinc-500';
                    }
                    @endphp

                    <tr class="hover:bg-zinc-50/80 transition">

                        <!-- Nama dan Deskripsi Alat -->
                        <td class="p-4 align-middle">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl bg-orange-50 text-orange-600 border border-zinc-200 flex items-center justify-center text-xs font-bold shadow-sm flex-shrink-0 overflow-hidden">
                                    @if($item->foto)
                                    <img src="{{ asset('storage/' . $item->foto) }}"
                                        alt="{{ $item->nama }}"
                                        class="w-full h-full object-cover">
                                    @else
                                    ⛺
                                    @endif
                                </div>

                                <div>
                                    <h4 class="font-bold text-zinc-900 text-sm leading-tight">
                                        {{ $item->nama }}
                                    </h4>
                                    <p class="text-[11px] text-zinc-400 mt-0.5">
                                        {{ $item->deskripsi ?? 'Kondisi alat terawat dan siap disewa.' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- Kategori -->
                        <td class="p-4 align-middle text-center">
                            <span class="px-3 py-1 {{ $badgeBg }} text-[11px] font-semibold rounded-full border inline-flex items-center gap-1.5">
                                <span class="w-1.5 h-1.5 rounded-full {{ $dotBg }}"></span>
                                <span>{{ $item->kategori->nama ?? 'Tanpa Kategori' }}</span>
                            </span>
                        </td>

                        <!-- Tarif dan Nilai Pengganti -->
                        <td class="p-4 align-middle text-center">
                            <div class="inline-flex items-center justify-center gap-6">
                                <div>
                                    <div class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">
                                        Tarif / Hari
                                    </div>
                                    <div class="text-xs font-bold text-zinc-900 mt-0.5">
                                        Rp {{ number_format($item->tarif_per_hari, 0, ',', '.') }}
                                    </div>
                                </div>

                                <div class="w-px h-6 bg-zinc-200"></div>

                                <div>
                                    <div class="text-[10px] font-semibold text-zinc-400 uppercase tracking-wider">
                                        Nilai Pengganti
                                    </div>
                                    <div class="text-xs font-semibold text-zinc-600 mt-0.5">
                                        Rp {{ number_format($item->nilai_pengganti, 0, ',', '.') }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- Stok -->
                        <td class="p-4 align-middle text-center">
                            <div class="inline-flex items-center justify-center gap-1.5 text-xs font-bold">
                                <span class="px-2.5 py-1 bg-emerald-50 text-emerald-700 border border-emerald-200 rounded-lg">
                                    {{ $item->stok_tersedia }}
                                </span>

                                <span class="text-zinc-300">/</span>

                                <span class="px-2.5 py-1 bg-zinc-100 text-zinc-600 border border-zinc-200 rounded-lg">
                                    {{ $item->stok_disewa }}
                                </span>

                                <span class="text-zinc-300">/</span>

                                @if($item->stok_rusak > 0)
                                <span class="px-2.5 py-1 bg-red-50 text-red-600 border border-red-200 rounded-lg">
                                    {{ $item->stok_rusak }}
                                </span>
                                @else
                                <span class="px-2.5 py-1 bg-zinc-100 text-zinc-600 border border-zinc-200 rounded-lg">
                                    0
                                </span>
                                @endif
                            </div>

                            <div class="text-[10px] text-zinc-400 mt-1 uppercase tracking-wider">
                                tersedia · disewa · rusak (dari {{ $item->stok_total }})
                            </div>
                        </td>

                        <!-- Aksi Edit -->
                        <td class="p-4 align-middle text-center">
                            <button type="button"
                                onclick="openModalEdit('{{ $item->id }}', '{{ $item->nama }}', '{{ $item->tarif_per_hari }}', '{{ $item->nilai_pengganti }}')"
                                class="px-3 py-1.5 text-xs font-semibold text-zinc-700 bg-zinc-100 hover:bg-zinc-900 hover:text-white rounded-xl transition inline-flex items-center gap-1.5 cursor-pointer shadow-sm">
                                <svg class="w-3.5 h-3.5"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round"
                                        stroke-linejoin="round"
                                        stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                                Edit Tarif
                            </button>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-12 text-center text-zinc-400 text-sm font-medium">
                            Belum ada data alat yang tersimpan di database.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal Edit Tarif -->
<div id="modalEditAlat"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 overflow-y-auto">

    <div class="bg-white rounded-2xl max-w-lg w-full p-8 shadow-xl space-y-6 border border-zinc-200 relative my-8">

        <div class="flex items-center justify-between pb-4 border-b border-zinc-100">
            <div>
                <h3 class="text-base font-bold text-zinc-900">
                    Edit Harga & Tarif Sewa
                </h3>
                <p class="text-xs text-zinc-500">
                    Sesuaikan tarif sewa harian dan nilai ganti rugi alat.
                </p>
            </div>

            <button type="button"
                onclick="closeModalEdit()"
                class="w-7 h-7 rounded-full bg-zinc-100 hover:bg-zinc-200 text-zinc-500 flex items-center justify-center font-bold text-sm cursor-pointer">
                &times;
            </button>
        </div>

        <form id="formEditAlat" action="" method="POST" class="space-y-4" autocomplete="off">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                    Nama Alat
                </label>
                <input type="text"
                    id="inputNamaAlat"
                    disabled
                    class="w-full bg-zinc-100 border border-zinc-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-zinc-500 cursor-not-allowed">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                        Tarif per Hari (Rp)
                    </label>
                    <input type="number"
                        id="inputTarifHari"
                        name="tarif_sewa"
                        required
                        class="w-full bg-white border border-zinc-300 rounded-xl px-4 py-2.5 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-zinc-400 transition">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 uppercase tracking-wider mb-1.5">
                        Nilai Pengganti (Rp)
                    </label>
                    <input type="number"
                        id="inputNilaiPengganti"
                        name="nilai_pengganti"
                        required
                        class="w-full bg-white border border-zinc-300 rounded-xl px-4 py-2.5 text-xs font-medium focus:outline-none focus:ring-1 focus:ring-zinc-400 transition">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-zinc-100">
                <button type="button"
                    onclick="closeModalEdit()"
                    class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 rounded-xl text-xs font-semibold transition cursor-pointer">
                    Batal
                </button>

                <button type="submit"
                    class="px-5 py-2 bg-zinc-900 hover:bg-zinc-800 text-white rounded-xl text-xs font-semibold shadow-sm transition cursor-pointer">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Modal Notifikasi -->
@if (session('success') || $errors->any())
<div id="modalNotif"
    class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">

    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl space-y-4 text-center border border-zinc-200 animate-in fade-in zoom-in duration-200">

        @if (session('success'))
        <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-base font-bold">
            ✓
        </div>
        <h3 class="font-bold text-zinc-900 text-sm">Berhasil!</h3>
        <p class="text-xs text-zinc-500 font-normal">
            {{ session('success') }}
        </p>
        @endif

        @if ($errors->any())
        <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto text-base font-bold">
            ✕
        </div>
        <h3 class="font-bold text-zinc-900 text-sm">Terjadi Kesalahan</h3>

        <ul class="text-xs text-red-600 space-y-1 font-normal text-left bg-red-50/50 p-3 rounded-xl">
            @foreach ($errors->all() as $error)
            <li>- {{ $error }}</li>
            @endforeach
        </ul>
        @endif

        <div class="pt-2">
            <button type="button"
                onclick="document.getElementById('modalNotif').remove()"
                class="w-full py-2 bg-zinc-900 hover:bg-zinc-800 text-white rounded-xl text-xs font-semibold shadow-sm transition cursor-pointer">
                OK, Mengerti
            </button>
        </div>
    </div>
</div>
@endif

<!-- JavaScript Modal Edit -->
<script>
    function openModalEdit(id, nama, tarif, pengganti) {
        document.getElementById('formEditAlat').action =
            "{{ url('/owner/harga-sewa') }}/" + id;

        document.getElementById('inputNamaAlat').value = nama;
        document.getElementById('inputTarifHari').value = Math.round(tarif);
        document.getElementById('inputNilaiPengganti').value = Math.round(pengganti);

        document.getElementById('modalEditAlat').classList.remove('hidden');
    }

    function closeModalEdit() {
        document.getElementById('modalEditAlat').classList.add('hidden');
    }
</script>
@endsection