@extends('layouts.owner')

@section('content')
<div class="space-y-6 pt-2 pb-10">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                <span>Master Data</span>
                <span>/</span>
                <span class="text-gray-700">Kelola Harga Sewa & Tarif</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 text-[10px] font-bold text-orange-600 bg-orange-50 rounded-md border border-orange-100">Owner Authority Only (Edit Tarif & Ganti Rugi)</span>
            </div>
        </div>
    </div>

    <!-- 4 Kartu Metrik Ringkasan -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-2xs relative overflow-hidden">
            <div class="absolute right-4 top-4 w-10 h-10 rounded-2xl bg-gray-50 text-gray-500 flex items-center justify-center font-bold text-base">⛺</div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Unit Keseluruhan</p>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-bold text-gray-800">{{ optional($alat)->sum('stok_total') ?? 0 }}</span>
                <span class="text-xs text-gray-400 font-medium">Unit Alat</span>
            </div>
            <span class="inline-block mt-3 px-2.5 py-1 bg-gray-50 text-gray-500 text-[10px] font-semibold rounded-lg border border-gray-100">Dari {{ optional($kategori_alat)->count() ?? 0 }} Kategori</span>
        </div>

        <div class="p-6 bg-white rounded-3xl border border-orange-100/60 shadow-2xs relative overflow-hidden bg-gradient-to-br from-white via-orange-50/10 to-orange-50/30">
            <div class="absolute right-4 top-4 w-10 h-10 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-xs">Rp</div>
            <p class="text-xs font-bold text-orange-700/80 uppercase tracking-wider mb-2">Rata-Rata Tarif Sewa</p>
            <div class="flex items-baseline gap-1">
                <span class="text-lg font-bold text-orange-600">Rp</span>
                <span class="text-2xl font-bold text-orange-600">{{ number_format(optional($alat)->avg('tarif_per_hari') ?? 0, 0, ',', '.') }}</span>
                <span class="text-xs text-gray-400 font-medium">/hari</span>
            </div>
        </div>

        <div class="p-6 bg-white rounded-3xl border border-emerald-100/60 shadow-2xs relative overflow-hidden bg-gradient-to-br from-white via-emerald-50/10 to-emerald-50/30">
            <div class="absolute right-4 top-4 w-10 h-10 rounded-2xl bg-emerald-50 text-emerald-600 flex items-center justify-center font-bold text-base">🛒</div>
            <p class="text-xs font-bold text-emerald-700/80 uppercase tracking-wider mb-2">Unit Sedang Disewa</p>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-bold text-emerald-600">{{ optional($alat)->sum('stok_disewa') ?? 0 }}</span>
                <span class="text-xs text-gray-400 font-medium">Unit</span>
            </div>
        </div>

        <div class="p-6 bg-white rounded-3xl border border-red-100/60 shadow-2xs relative overflow-hidden bg-gradient-to-br from-white via-red-50/10 to-red-50/30">
            <div class="absolute right-4 top-4 w-10 h-10 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-base">⚠️</div>
            <p class="text-xs font-bold text-red-700/80 uppercase tracking-wider mb-2">Perlu Maintenance</p>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-bold text-red-600">{{ optional($alat)->sum('stok_rusak') ?? 0 }}</span>
                <span class="text-xs text-gray-400 font-medium">Unit Rusak</span>
            </div>
            <span class="inline-block mt-3 px-2.5 py-1 bg-red-50 text-red-600 text-[10px] font-semibold rounded-lg border border-red-100">Tindakan diperlukan</span>
        </div>
    </div>

    <!-- Bagian Filter & Daftar Alat -->
    <div class="space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-2">
            <div>
                <h3 class="text-base font-bold text-gray-800">Daftar Tarif & Inventaris Alat</h3>
                <p class="text-xs text-gray-400">Kelola dan perbarui harga sewa serta nilai ganti rugi inventaris alat.</p>
            </div>

            <!-- Filter Kategori -->
            <form method="GET" action="{{ url('/owner/harga-sewa') }}" class="flex items-center gap-2.5 bg-white px-4 py-2 rounded-2xl border border-gray-200/80 shadow-2xs">
                <span class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider">FILTER KATEGORI:</span>
                <select name="kategori_id" onchange="this.form.submit()" class="bg-transparent text-gray-700 text-xs font-semibold focus:outline-none focus:ring-0 border-none cursor-pointer">
                    <option value="">Semua Kategori</option>
                    @foreach($kategori_alat as $kategori)
                        <option value="{{ $kategori->id }}" {{ request('kategori_id') == $kategori->id ? 'selected' : '' }}>
                            {{ $kategori->nama }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- List Baris Card Terpisah -->
        <div class="space-y-3">
            @forelse($alat as $item)
            @php
                // Logika warna badge kategori agar berbeda-beda
                $namaKat = strtolower($item->kategori->nama ?? '');
                if (str_contains($namaKat, 'tenda')) {
                    $badgeBg = 'bg-orange-50 text-orange-700 border-orange-200';
                    $dotBg = 'bg-orange-500';
                } elseif (str_contains($namaKat, 'carrier') || str_contains($namaKat, 'tas')) {
                    $badgeBg = 'bg-blue-50 text-blue-700 border-blue-200';
                    $dotBg = 'bg-blue-500';
                } elseif (str_contains($namaKat, 'sleeping')) {
                    $badgeBg = 'bg-purple-50 text-purple-700 border-purple-200';
                    $dotBg = 'bg-purple-500';
                } elseif (str_contains($namaKat, 'masak') || str_contains($namaKat, 'cooking')) {
                    $badgeBg = 'bg-emerald-50 text-emerald-700 border-emerald-200';
                    $dotBg = 'bg-emerald-500';
                } else {
                    $badgeBg = 'bg-gray-50 text-gray-700 border-gray-200';
                    $dotBg = 'bg-gray-500';
                }
            @endphp
            <div class="bg-white px-6 py-4 rounded-3xl border border-gray-100 shadow-2xs hover:shadow-sm hover:border-gray-200 transition-all grid grid-cols-1 lg:grid-cols-12 items-center gap-4 group">
                
                <!-- Kolom 1: Thumbnail & Nama Alat -->
                <div class="lg:col-span-3 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center text-sm font-bold shadow-2xs flex-shrink-0 group-hover:scale-105 transition-transform overflow-hidden">
                        @if($item->foto)
                            <img src="{{ asset('storage/' . $item->foto) }}" alt="{{ $item->nama }}" class="w-full h-full object-cover">
                        @else
                            ⛺
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-gray-800 text-sm leading-tight truncate">{{ $item->nama }}</h4>
                        <p class="text-[11px] text-gray-400 mt-0.5 font-normal truncate">
                            {{ $item->deskripsi ?? 'Kondisi alat terawat dan siap disewa.' }}
                        </p>
                    </div>
                </div>

                <!-- Kolom 2: Badge Kategori dengan Warna Dinamis -->
                <div class="lg:col-span-2">
                    <span class="px-3 py-1 {{ $badgeBg }} text-[11px] font-semibold rounded-xl border inline-flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full {{ $dotBg }}"></span> <span class="truncate max-w-[110px]">{{ $item->kategori->nama ?? 'Tanpa Kategori' }}</span>
                    </span>
                </div>

                <!-- Kolom 3: Tarif Sewa & Nilai Pengganti -->
                <div class="lg:col-span-3 flex items-center gap-6">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Tarif / Hari</p>
                        <p class="text-xs font-bold text-orange-600">Rp {{ number_format($item->tarif_per_hari, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Nilai Pengganti</p>
                        <p class="text-xs font-semibold text-gray-600">Rp {{ number_format($item->nilai_pengganti, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Kolom 4: Rincian Stok (Tanpa Ikon, Menggunakan Teks Keterangan Singkat) -->
                <div class="lg:col-span-2 flex items-center gap-2 text-[11px] font-medium text-gray-600 bg-[#FAF7F2]/60 px-3 py-1.5 rounded-xl border border-gray-200/40 w-fit">
                    <span class="text-emerald-700 font-semibold">{{ $item->stok_tersedia }} tersedia</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-blue-600 font-semibold">{{ $item->stok_disewa }} disewa</span>
                    <span class="text-gray-300">|</span>
                    <span class="text-red-500 font-semibold">{{ $item->stok_rusak }} rusak</span>
                </div>

                <!-- Kolom 5: Tombol Aksi Edit Tarif -->
                <div class="lg:col-span-2 flex items-center justify-end">
                    <div class="flex items-center gap-1 bg-[#FAF7F2]/60 p-1 rounded-xl border border-gray-200/40">
                        <button onclick="openModalEdit('{{ $item->id }}', '{{ $item->nama }}', '{{ $item->tarif_per_hari }}', '{{ $item->nilai_pengganti }}')" class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-brand hover:bg-white rounded-lg transition flex items-center gap-1.5 cursor-pointer" title="Edit Tarif & Harga">
                            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Tarif
                        </button>
                    </div>
                </div>

            </div>
            @empty
            <div class="bg-white p-12 rounded-3xl border border-gray-100 text-center text-gray-400 text-sm font-medium shadow-2xs">
                Belum ada data alat yang tersimpan di database.
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT TARIF & HARGA GANTI RUGI ================= -->
<div id="modalEditAlat" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-xl space-y-6 border border-gray-100 relative my-8">
        
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-800">Edit Harga & Tarif Sewa</h3>
                <p class="text-xs text-gray-400">Sesuaikan tarif sewa harian dan nilai ganti rugi alat.</p>
            </div>
            <button type="button" onclick="closeModalEdit()" class="w-7 h-7 rounded-full bg-gray-50 hover:bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-sm">&times;</button>
        </div>

        <form id="formEditAlat" action="" method="POST" class="space-y-4" autocomplete="off">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Nama Alat</label>
                <input type="text" id="inputNamaAlat" disabled class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-semibold text-gray-500 cursor-not-allowed">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Tarif per Hari (Rp)</label>
                    <input type="number" id="inputTarifHari" name="tarif_sewa" required class="w-full bg-[#FAF7F2] border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:outline-none focus:border-brand transition">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Nilai Pengganti (Rp)</label>
                    <input type="number" id="inputNilaiPengganti" name="nilai_pengganti" required class="w-full bg-[#FAF7F2] border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:outline-none focus:border-brand transition">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <button type="button" onclick="closeModalEdit()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-xs font-semibold transition">Batal</button>
                <button type="submit" class="px-5 py-2 bg-brand hover:bg-brand-hover text-white rounded-xl text-xs font-semibold shadow-xs transition">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL POP-UP NOTIFIKASI TENGAH (SUKSES/GAGAL) ================= -->
@if (session('success') || $errors->any())
<div id="modalNotif" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-xl space-y-4 text-center border border-gray-100 animate-in fade-in zoom-in duration-200">
        
        @if (session('success'))
            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-base font-bold">✓</div>
            <h3 class="font-bold text-gray-800 text-sm">Berhasil!</h3>
            <p class="text-xs text-gray-500 font-normal">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto text-base font-bold">✕</div>
            <h3 class="font-bold text-gray-800 text-sm">Terjadi Kesalahan</h3>
            <ul class="text-xs text-red-600 space-y-1 font-normal text-left bg-red-50/50 p-3 rounded-xl">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <div class="pt-2">
            <button type="button" onclick="document.getElementById('modalNotif').remove()" class="w-full py-2 bg-brand hover:bg-brand-hover text-white rounded-xl text-xs font-semibold shadow-xs transition cursor-pointer">
                OK, Mengerti
            </button>
        </div>
    </div>
</div>
@endif

<!-- JavaScript Modal Edit & Popup -->
<script>
    function openModalEdit(id, nama, tarif, pengganti) {
        document.getElementById('formEditAlat').action = "{{ url('/owner/harga-sewa') }}/" + id;
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