@extends('layouts.owner')

@section('content')
<div class="space-y-6 pt-2 pb-10">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-semibold text-gray-400 uppercase tracking-wider mb-1">
                <span>Master Data</span>
                <span>/</span>
                <span class="text-gray-700">Kelola Aturan Denda</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 text-[10px] font-bold text-orange-600 bg-orange-50 rounded-md border border-orange-100">Owner Authority Only (Kelola Tarif Denda)</span>
            </div>
        </div>
    </div>

    <!-- Ringkasan Info Denda -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
        <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-2xs relative overflow-hidden">
            <div class="absolute right-4 top-4 w-10 h-10 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-base">⚠️</div>
            <p class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Total Jenis Aturan Denda</p>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-bold text-gray-800">{{ optional($dendaList)->count() ?? 0 }}</span>
                <span class="text-xs text-gray-400 font-medium">Kategori</span>
            </div>
        </div>
        <div class="p-6 bg-white rounded-3xl border border-orange-100/60 shadow-2xs relative overflow-hidden bg-gradient-to-br from-white via-orange-50/10 to-orange-50/30">
            <div class="absolute right-4 top-4 w-10 h-10 rounded-2xl bg-orange-50 text-orange-600 flex items-center justify-center font-bold text-xs">⏱️</div>
            <p class="text-xs font-bold text-orange-700/80 uppercase tracking-wider mb-2">Sistem Hitung Denda</p>
            <div class="flex items-baseline gap-1">
                <span class="text-sm font-bold text-orange-600">Per Hari & Persentase</span>
            </div>
        </div>
        <div class="p-6 bg-white rounded-3xl border border-red-100/60 shadow-2xs relative overflow-hidden bg-gradient-to-br from-white via-red-50/10 to-red-50/30">
            <div class="absolute right-4 top-4 w-10 h-10 rounded-2xl bg-red-50 text-red-600 flex items-center justify-center font-bold text-base">🔧</div>
            <p class="text-xs font-bold text-red-700/80 uppercase tracking-wider mb-2">Kerusakan / Kehilangan</p>
            <div class="flex items-baseline gap-1">
                <span class="text-xs text-gray-500 font-bold">Dihitung Berdasarkan Persentase / Nilai Alat</span>
            </div>
        </div>
    </div>

    <!-- Daftar Aturan Denda -->
    <div class="space-y-4">
        <div>
            <h3 class="text-base font-bold text-gray-800">Daftar Kebijakan & Tarif Denda</h3>
            <p class="text-xs text-gray-400">Atur besaran denda keterlambatan pengembalian dan kerusakan unit alat.</p>
        </div>

        <div class="space-y-3">
            @forelse($dendaList ?? [] as $denda)
            <div class="bg-white px-6 py-4 rounded-3xl border border-gray-100 shadow-2xs hover:shadow-sm hover:border-gray-200 transition-all grid grid-cols-1 lg:grid-cols-12 items-center gap-4 group">
                
                <!-- Kolom 1: Jenis Denda (Lebar 4 / 12) -->
                <div class="lg:col-span-4 flex items-center gap-3.5">
                    <div class="w-11 h-11 rounded-2xl bg-orange-50 text-orange-600 border border-orange-100 flex items-center justify-center text-sm font-bold shadow-2xs flex-shrink-0 group-hover:scale-105 transition-transform">
                        📌
                    </div>
                    <div class="min-w-0">
                        <h4 class="font-bold text-gray-800 text-sm leading-tight truncate capitalize">{{ str_replace('_', ' ', $denda->jenis_denda) }}</h4>
                        <p class="text-[11px] text-gray-400 mt-0.5 font-normal truncate">
                            Metode perhitungan: {{ $denda->perhitungan_denda }}
                        </p>
                    </div>
                </div>

                <!-- Kolom 2: Perhitungan Denda Badge (Lebar 3 / 12) -->
                <div class="lg:col-span-3">
                    <span class="px-3 py-1 bg-orange-50 text-orange-700 text-[11px] font-semibold rounded-xl border border-orange-100 inline-flex items-center gap-1.5">
                        <span class="w-1.5 h-1.5 rounded-full bg-orange-500"></span> 
                        <span class="truncate max-w-[150px] uppercase font-bold">{{ $denda->perhitungan_denda }}</span>
                    </span>
                </div>

                <!-- Kolom 3: Nilai Denda (Lebar 3 / 12) -->
                <div class="lg:col-span-3 flex items-center gap-6">
                    <div>
                        <p class="text-[10px] font-semibold text-gray-400 uppercase tracking-wider">Nilai / Tarif</p>
                        <p class="text-xs font-bold text-orange-600">
                            @if($denda->perhitungan_denda == 'per_hari')
                                Rp {{ number_format($denda->nilai_denda, 0, ',', '.') }}
                            @else
                                {{ $denda->nilai_denda }}% dari Nilai Alat
                            @endif
                        </p>
                    </div>
                </div>

                <!-- Kolom 4: Aksi Edit (Lebar 2 / 12) -->
                <div class="lg:col-span-2 flex items-center justify-end">
                    <div class="flex items-center gap-1 bg-[#FAF7F2]/60 p-1 rounded-xl border border-gray-200/40 shadow-xs">
                        <button onclick="openModalEdit('{{ $denda->id }}', '{{ $denda->jenis_denda }}', '{{ $denda->perhitungan_denda }}', '{{ $denda->nilai_denda }}')" class="px-3 py-1.5 text-xs font-semibold text-gray-600 hover:text-brand hover:bg-white rounded-lg transition flex items-center gap-1.5 cursor-pointer" title="Edit Aturan Denda">
                            <svg class="w-3.5 h-3.5 text-brand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            Edit Aturan
                        </button>
                    </div>
                </div>

            </div>
            @empty
            <div class="bg-white p-12 rounded-3xl border border-gray-100 text-center text-gray-400 text-sm font-medium shadow-2xs">
                Belum ada aturan denda yang terdaftar di database.
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ================= MODAL EDIT ATURAN DENDA ================= -->
<div id="modalEditDenda" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-white rounded-3xl max-w-lg w-full p-8 shadow-xl space-y-6 border border-gray-100 relative my-8">
        
        <div class="flex items-center justify-between pb-4 border-b border-gray-100">
            <div>
                <h3 class="text-base font-bold text-gray-800">Edit Aturan Denda</h3>
                <p class="text-xs text-gray-400">Sesuaikan metode perhitungan dan nominal denda.</p>
            </div>
            <button type="button" onclick="closeModalEdit()" class="w-7 h-7 rounded-full bg-gray-50 hover:bg-gray-100 text-gray-400 flex items-center justify-center font-bold text-sm">&times;</button>
        </div>

        <form id="formEditDenda" action="" method="POST" class="space-y-4" autocomplete="off">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Jenis Denda</label>
                <input type="text" id="inputJenisDenda" name="jenis_denda" required class="w-full bg-[#FAF7F2] border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:outline-none focus:border-brand transition">
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Perhitungan Denda</label>
                    <select id="inputPerhitungan" name="perhitungan_denda" class="w-full bg-[#FAF7F2] border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:outline-none focus:border-brand transition cursor-pointer">
                        <option value="per_hari">Per Hari</option>
                        <option value="persentase">Persentase (%)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-gray-600 uppercase tracking-wider mb-1.5">Nilai Denda</label>
                    <input type="number" step="any" id="inputNilaiDenda" name="nilai_denda" required class="w-full bg-[#FAF7F2] border border-gray-200 rounded-xl px-4 py-2.5 text-xs font-medium focus:outline-none focus:border-brand transition">
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

<!-- JavaScript Modal Edit -->
<script>
    function openModalEdit(id, jenis, perhitungan, nilai) {
        document.getElementById('formEditDenda').action = "{{ url('/owner/aturan-denda') }}/" + id;
        document.getElementById('inputJenisDenda').value = jenis;
        document.getElementById('inputPerhitungan').value = perhitungan;
        document.getElementById('inputNilaiDenda').value = nilai;
        document.getElementById('modalEditDenda').classList.remove('hidden');
    }

    function closeModalEdit() {
        document.getElementById('modalEditDenda').classList.add('hidden');
    }
</script>
@endsection