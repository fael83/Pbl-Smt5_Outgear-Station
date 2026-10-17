@extends('layouts.panel', ['title' => 'Data Peralatan'])

@php
    // Nilai awal form modal. Dipakai juga untuk membuka kembali modal ketika
    // validasi server gagal (jalur tanpa JavaScript).
    $nilaiAwal = [
        'nama' => old('nama', ''),
        'kategori_id' => (string) old('kategori_id', ''),
        'deskripsi' => old('deskripsi', ''),
        'tarif_per_hari' => old('tarif_per_hari', ''),
        'nilai_pengganti' => old('nilai_pengganti', ''),
        'stok_total' => old('stok_total', ''),
        'stok_rusak' => old('stok_rusak', ''),
    ];

    $modeAwal = (string) old('form_mode', 'create');
    $alatIdAwal = (int) old('alat_id', 0);
    $updateUrlAwal = $alatIdAwal > 0 ? route('admin.peralatan.update', $alatIdAwal) : null;

    $filterAwal = [
        'search' => $filters['search'],
        'kategori' => (string) ($filters['kategori'] ?: ''),
        'kondisi' => $filters['kondisi'],
        'per_page' => (string) $filters['per_page'],
    ];
@endphp

@section('content')
    <style>
        [x-cloak] { display: none !important; }
    </style>

    <div class="space-y-6"
         x-data="halamanPeralatan({
            createUrl: {{ Js::from(route('admin.peralatan.store')) }},
            tabelUrl: {{ Js::from(route('admin.peralatan.tabel')) }},
            items: {{ Js::from($dataAlat) }},
            filters: {{ Js::from($filterAwal) }},
            ringkasan: {{ Js::from($ringkasan) }},
            nilaiAwal: {{ Js::from($nilaiAwal) }},
            modeAwal: {{ Js::from($modeAwal) }},
            alatIdAwal: {{ $alatIdAwal }},
            updateUrlAwal: {{ Js::from($updateUrlAwal) }},
            punyaError: {{ $errors->any() ? 'true' : 'false' }}
         })">

        <!-- Judul & Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold font-heading text-[#1A2024]">Data Peralatan</h1>
                <p class="text-xs text-gray-500 mt-1">
                    Data alat, tarif sewa, dan stok dibaca langsung dari database Supabase.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.peralatan.index') }}" @click.prevent="muatTabel()"
                   class="px-4 py-2.5 bg-white border border-gray-200 text-[#1A2024] text-xs font-semibold rounded-xl hover:bg-gray-50 transition shadow-sm">
                    Muat Ulang
                </a>
                <button type="button" @click="bukaTambah()"
                        class="px-4 py-2.5 bg-[#D86B43] text-white text-xs font-semibold rounded-xl hover:bg-[#D86B43]/90 transition shadow-sm">
                    + Tambah Alat Baru
                </button>
            </div>
        </div>

        <!-- Ringkasan Stok -->
        <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Jenis Alat</p>
                <p class="text-2xl font-bold font-heading text-[#1A2024] mt-1"
                   x-text="ringkasanData.total_alat">{{ $ringkasan['total_alat'] }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">alat terdaftar</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Total Unit</p>
                <p class="text-2xl font-bold font-heading text-[#1A2024] mt-1"
                   x-text="ringkasanData.total_unit">{{ $ringkasan['total_unit'] }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">unit di seluruh stok</p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Unit Tersedia</p>
                <p class="text-2xl font-bold font-heading text-emerald-600 mt-1"
                   x-text="ringkasanData.unit_tersedia">{{ $ringkasan['unit_tersedia'] }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">
                    <span x-text="ringkasanData.unit_disewa">{{ $ringkasan['unit_disewa'] }}</span> unit sedang disewa
                </p>
            </div>
            <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm">
                <p class="text-[10px] font-bold uppercase tracking-wider text-gray-400">Unit Rusak</p>
                <p class="text-2xl font-bold font-heading text-[#D86B43] mt-1"
                   x-text="ringkasanData.unit_rusak">{{ $ringkasan['unit_rusak'] }}</p>
                <p class="text-[10px] text-gray-400 mt-0.5">perlu perbaikan / QC</p>
            </div>
        </div>

        <!-- Filter & Pencarian (server-side, langsung ke database) -->
        <form method="GET" action="{{ route('admin.peralatan.index') }}" @submit.prevent="muatTabel()"
              class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm flex flex-col lg:flex-row lg:items-center gap-3">
            <div class="relative flex-1 min-w-0">
                <span class="absolute left-3.5 top-2.5 text-gray-400">🔍</span>
                <input type="search" name="search" x-model="cari" @input.debounce.400ms="muatTabel()"
                       value="{{ $filters['search'] }}"
                       placeholder="Cari nama alat, kategori, atau ID (#AL-001)..."
                       class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl pl-10 pr-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
            </div>

            <select name="kategori" x-model="kategori" @change="muatTabel()"
                    class="w-full lg:w-56 bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] font-medium focus:ring-2 focus:ring-[#15AEEA]">
                <option value="">Semua Kategori</option>
                @foreach ($daftarKategori as $kategoriAlat)
                    <option value="{{ $kategoriAlat->id }}" @selected($filters['kategori'] === (int) $kategoriAlat->id)>
                        {{ $kategoriAlat->nama }} ({{ $kategoriAlat->alat_count }})
                    </option>
                @endforeach
            </select>

            <select name="kondisi" x-model="kondisi" @change="muatTabel()"
                    class="w-full lg:w-48 bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] font-medium focus:ring-2 focus:ring-[#15AEEA]">
                <option value="semua" @selected($filters['kondisi'] === 'semua')>Semua Kondisi</option>
                <option value="tersedia" @selected($filters['kondisi'] === 'tersedia')>Stok Tersedia</option>
                <option value="habis" @selected($filters['kondisi'] === 'habis')>Stok Habis</option>
                <option value="rusak" @selected($filters['kondisi'] === 'rusak')>Ada Unit Rusak</option>
            </select>

            <input type="hidden" name="per_page" x-model="perPage" value="{{ $filters['per_page'] }}">

            <div class="flex items-center gap-2">
                <button type="submit"
                        class="px-4 py-2.5 bg-[#1A2024] text-white text-xs font-semibold rounded-xl hover:bg-[#1A2024]/90 transition shadow-sm">
                    Terapkan
                </button>
                <a href="{{ route('admin.peralatan.index') }}" x-show="adaFilter" x-cloak @click.prevent="resetFilter()"
                   class="px-4 py-2.5 bg-gray-100 text-[#1A2024] text-xs font-semibold rounded-xl hover:bg-gray-200 transition">
                    Reset
                </a>
            </div>
        </form>

        <!-- Tabel Data Peralatan (diperbarui lewat AJAX, tanpa reload halaman) -->
        <div class="relative">
            <div x-ref="tabel" @click="tangkapKlik($event)" :class="memuat ? 'opacity-60' : ''" class="transition-opacity">
                @include('admin.inventaris._tabel')
            </div>

            <div x-cloak x-show="memuat" class="absolute inset-x-0 top-0 flex justify-center pt-16 pointer-events-none">
                <div class="flex items-center gap-2 bg-[#1A2024] text-white text-xs font-semibold px-4 py-2 rounded-full shadow-lg">
                    <span class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    Memuat data…
                </div>
            </div>
        </div>
        <!-- Notifikasi ringan setelah simpan/hapus -->
        <div x-cloak x-show="pesan" x-transition.opacity
             class="fixed bottom-6 right-6 z-[60] max-w-sm px-4 py-3 rounded-xl shadow-lg text-xs font-semibold"
             :class="pesanGagal ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100'">
            <span x-text="pesan"></span>
        </div>

        <!-- Modal Tambah / Edit Alat -->
        <div x-cloak x-show="modalTerbuka" x-transition.opacity
             class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-[#1A2024]/50 p-4 md:p-8"
             @keydown.escape.window="tutupModal()">
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl" @click.outside="tutupModal()">
                <div class="flex items-start justify-between gap-4 p-5 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-bold font-heading text-[#1A2024]"
                            x-text="mode === 'edit' ? 'Edit Data Alat' : 'Tambah Alat Baru'">Tambah Alat Baru</h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Stok tersedia dihitung otomatis: total − disewa − rusak.
                        </p>
                    </div>
                    <button type="button" @click="tutupModal()"
                            class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition">✕</button>
                </div>

                <form x-ref="formAlat" method="POST" :action="formUrl" @submit.prevent="simpan($el)" class="p-5 space-y-4">
                    @csrf
                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <input type="hidden" name="form_mode" :value="mode">
                    <input type="hidden" name="alat_id" :value="form.id ?? ''">
                    <input type="hidden" name="search" :value="cari">
                    <input type="hidden" name="kategori" :value="kategori">
                    <input type="hidden" name="kondisi" :value="kondisi">
                    <input type="hidden" name="per_page" :value="perPage">

                    @if ($errors->any())
                        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-xs text-red-700 space-y-1">
                            <p class="font-semibold">Periksa kembali data berikut:</p>
                            @foreach ($errors->all() as $pesanError)
                                <p>{{ $pesanError }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="md:col-span-2">
                            <label for="nama" class="block text-xs font-semibold text-[#1A2024] mb-1">Nama Alat</label>
                            <input type="text" id="nama" name="nama" x-model="form.nama" required
                                   class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            <p class="mt-1 text-[10px] text-red-600" x-show="errors.nama" x-text="errors.nama"></p>
                        </div>

                        <div>
                            <label for="kategori_id" class="block text-xs font-semibold text-[#1A2024] mb-1">Kategori</label>
                            <select id="kategori_id" name="kategori_id" x-model="form.kategori_id" required
                                    class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                                <option value="">Pilih kategori</option>
                                @foreach ($daftarKategori as $kategoriAlat)
                                    <option value="{{ $kategoriAlat->id }}">{{ $kategoriAlat->nama }}</option>
                                @endforeach
                            </select>
                            <p class="mt-1 text-[10px] text-red-600" x-show="errors.kategori_id" x-text="errors.kategori_id"></p>
                        </div>

                        <div>
                            <label for="tarif_per_hari" class="block text-xs font-semibold text-[#1A2024] mb-1">Tarif / Hari (Rp)</label>
                            <input type="number" id="tarif_per_hari" name="tarif_per_hari" x-model="form.tarif_per_hari"
                                   min="0" step="1000" required
                                   class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            <p class="mt-1 text-[10px] text-red-600" x-show="errors.tarif_per_hari" x-text="errors.tarif_per_hari"></p>
                        </div>

                        <div>
                            <label for="nilai_pengganti" class="block text-xs font-semibold text-[#1A2024] mb-1">Nilai Pengganti (Rp)</label>
                            <input type="number" id="nilai_pengganti" name="nilai_pengganti" x-model="form.nilai_pengganti"
                                   min="0" step="1000" required
                                   class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            <p class="mt-1 text-[10px] text-red-600" x-show="errors.nilai_pengganti" x-text="errors.nilai_pengganti"></p>
                        </div>

                        <div>
                            <label for="stok_total" class="block text-xs font-semibold text-[#1A2024] mb-1">Stok Total (unit)</label>
                            <input type="number" id="stok_total" name="stok_total" x-model="form.stok_total"
                                   min="0" step="1" required
                                   class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            <p class="mt-1 text-[10px] text-red-600" x-show="errors.stok_total" x-text="errors.stok_total"></p>
                        </div>

                        <div>
                            <label for="stok_rusak" class="block text-xs font-semibold text-[#1A2024] mb-1">Stok Rusak (unit)</label>
                            <input type="number" id="stok_rusak" name="stok_rusak" x-model="form.stok_rusak"
                                   min="0" step="1"
                                   class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                            <p class="mt-1 text-[10px] text-red-600" x-show="errors.stok_rusak" x-text="errors.stok_rusak"></p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="deskripsi" class="block text-xs font-semibold text-[#1A2024] mb-1">Deskripsi <span class="text-gray-400 font-normal">(opsional)</span></label>
                            <textarea id="deskripsi" name="deskripsi" x-model="form.deskripsi" rows="3"
                                      class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]"></textarea>
                            <p class="mt-1 text-[10px] text-red-600" x-show="errors.deskripsi" x-text="errors.deskripsi"></p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between gap-3 pt-4 border-t border-gray-100">
                        <p class="text-[10px] text-gray-400" x-show="mode === 'edit'">
                            Stok tersedia saat ini:
                            <span class="font-semibold text-[#1A2024]" x-text="(form.stok_tersedia ?? 0) + ' unit'"></span>
                        </p>
                        <div class="flex items-center gap-2 ml-auto">
                            <button type="button" @click="tutupModal()"
                                    class="px-4 py-2.5 bg-gray-100 text-[#1A2024] text-xs font-semibold rounded-xl hover:bg-gray-200 transition">
                                Batal
                            </button>
                            <button type="submit" :disabled="menyimpan"
                                    class="px-4 py-2.5 bg-[#D86B43] text-white text-xs font-semibold rounded-xl hover:bg-[#D86B43]/90 transition shadow-sm disabled:opacity-60">
                                <span x-text="menyimpan ? 'Menyimpan…' : (mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Alat')"></span>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection
