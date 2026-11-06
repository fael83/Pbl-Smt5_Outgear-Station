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

    // Tab status di atas tabel. `url` menjadi fallback saat JavaScript mati,
    // sedangkan klik normal ditangani Alpine lewat pilihKondisi().
    $tabStatus = collect([
        ['kunci' => 'semua', 'label' => 'Semua', 'kunci_ringkasan' => 'total_alat'],
        ['kunci' => 'tersedia', 'label' => 'Siap Sewa', 'kunci_ringkasan' => 'alat_tersedia'],
        ['kunci' => 'habis', 'label' => 'Stok Habis', 'kunci_ringkasan' => 'alat_habis'],
        ['kunci' => 'rusak', 'label' => 'Perlu Perbaikan', 'kunci_ringkasan' => 'alat_rusak'],
    ])->map(function (array $tab) use ($filters): array {
        $tab['url'] = route('admin.peralatan.index', array_filter([
            'search' => $filters['search'],
            'kategori' => $filters['kategori'] ?: null,
            'kondisi' => $tab['kunci'] === 'semua' ? null : $tab['kunci'],
            'per_page' => $filters['per_page'],
        ], static fn ($nilai): bool => $nilai !== null && $nilai !== ''));

        return $tab;
    })->all();
@endphp

@section('content')
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
            kategoriMap: {{ Js::from(collect($daftarKategori ?? [])->pluck('nama', 'id')->mapWithKeys(fn ($nama, $id) => [(string) $id => $nama])->all()) }},
            punyaError: {{ $errors->any() ? 'true' : 'false' }}
         })">

        <!-- Judul & Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-[#1A2024] tracking-tight">Data Peralatan</h1>
                <p class="text-xs text-gray-500 mt-1 max-w-2xl">
                    Manajemen katalog master alat outdoor, tarif sewa harian, nilai pengganti, dan status
                    ketersediaan unit. Semua angka di halaman ini dibaca langsung dari database.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="bukaTambah()"
                        class="px-4 py-2.5 bg-[#D86B43] text-white text-xs font-bold rounded-xl hover:bg-[#D86B43]/90 transition shadow-md">
                    + Tambah Peralatan
                </button>
            </div>
        </div>

        <!-- Kartu Statistik (dihitung dari tabel alat) -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Unit Tersedia</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-emerald-600"
                       x-text="ringkasanData.unit_tersedia">{{ $ringkasan['unit_tersedia'] }}</p>
                </div>
                <div class="text-[11px] text-emerald-700 font-bold pt-2 border-t border-zinc-100"
                     x-text="rasio(ringkasanData.unit_tersedia, ringkasanData.total_unit) + '% siap'">
                    {{ $ringkasan['total_unit'] > 0 ? round($ringkasan['unit_tersedia'] / $ringkasan['total_unit'] * 100, 1) : 0 }}% siap
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Sedang Disewa</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-blue-700"
                       x-text="ringkasanData.unit_disewa">{{ $ringkasan['unit_disewa'] }}</p>
                </div>
                <div class="text-[11px] text-sky-700 font-bold pt-2 border-t border-zinc-100"
                     x-text="rasio(ringkasanData.unit_disewa, ringkasanData.total_unit) + '% aktif'">
                    {{ $ringkasan['total_unit'] > 0 ? round($ringkasan['unit_disewa'] / $ringkasan['total_unit'] * 100, 1) : 0 }}% aktif
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Unit Rusak</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-rose-600"
                       x-text="ringkasanData.unit_rusak">{{ $ringkasan['unit_rusak'] }}</p>
                </div>
                <div class="text-[11px] text-zinc-400 font-medium pt-2 border-t border-zinc-100">
                    <span x-text="ringkasanData.alat_rusak">{{ $ringkasan['alat_rusak'] }}</span> jenis alat
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Unit Terdaftar</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-blue-700"
                       x-text="ringkasanData.total_unit">{{ $ringkasan['total_unit'] }}</p>
                </div>
                <div class="text-[11px] text-zinc-400 font-medium pt-2 border-t border-zinc-100">
                    <span x-text="ringkasanData.total_alat">{{ $ringkasan['total_alat'] }}</span> jenis alat
                </div>
            </div>
        </div>

        <!-- Tabel Data Peralatan (diperbarui lewat AJAX, tanpa reload halaman) -->
        <div class="relative">
            <div x-ref="tabel" @click="tangkapKlik($event)" :class="memuat ? 'opacity-60' : ''" class="transition-opacity">
                @include('admin.peralatan._tabel')
            </div>
        </div>

        <!-- Modal Tambah / Edit Alat -->
        <div x-cloak x-show="modalTerbuka" x-transition.opacity
             class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-[#1A2024]/50 p-4 md:p-8"
             @keydown.escape.window="tutupModal()">
            <div class="w-full max-w-2xl bg-white rounded-2xl shadow-xl" @click.outside="tutupModal()">
                <div class="flex items-start justify-between gap-4 p-5 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-bold text-[#1A2024]"
                            x-text="mode === 'edit' ? 'Edit Data Alat' : 'Tambah Alat Baru'">Tambah Alat Baru</h2>
                        <p class="text-xs text-gray-500 mt-0.5">
                            Stok tersedia dihitung otomatis: total − disewa − rusak.
                        </p>
                    </div>
                    <button type="button" @click="tutupModal()"
                            class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition">✕</button>
                </div>

                <form x-ref="formAlat" method="POST" enctype="multipart/form-data" :action="formUrl" @submit.prevent="simpan($el)" class="p-5 space-y-4">
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
                            <label for="foto" class="block text-xs font-semibold text-[#1A2024] mb-1">Foto Alat <span class="text-gray-400 font-normal">(JPG/PNG/WEBP, maks. 2 MB)</span></label>
                            <div class="flex items-center gap-4">
                                <div class="w-24 h-24 rounded-xl bg-[#F4EFEA] border border-gray-200 overflow-hidden flex items-center justify-center shrink-0">
                                    <template x-if="previewFoto && !hapusFoto">
                                        <img :src="previewFoto" alt="Pratinjau foto alat" class="w-full h-full object-cover">
                                    </template>
                                    <template x-if="!previewFoto || hapusFoto">
                                        <svg class="w-8 h-8 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </template>
                                </div>
                                <div class="flex-1 space-y-2">
                                    <input type="file" id="foto" name="foto" accept="image/jpeg,image/png,image/webp"
                                           @change="fotoDipilih($event)"
                                           class="block w-full text-xs text-gray-500 file:mr-3 file:px-3 file:py-2 file:rounded-lg file:border-0 file:bg-[#F4EFEA] file:text-[#1A2024] file:font-semibold hover:file:bg-gray-200">
                                    <label class="inline-flex items-center gap-2 text-[11px] text-gray-500" x-show="mode === 'edit' && previewFoto && !previewFoto.startsWith('blob:')">
                                        <input type="checkbox" name="hapus_foto" value="1" x-model="hapusFoto" class="rounded border-gray-300">
                                        Hapus foto saat ini
                                    </label>
                                </div>
                            </div>
                            <p class="mt-1 text-[10px] text-red-600" x-show="errors.foto" x-text="errors.foto"></p>
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


        @include('partials.status-muat')
    </div>
@endsection
