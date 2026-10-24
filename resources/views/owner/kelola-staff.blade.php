@extends('layouts.owner')

@section('content')
<div class="space-y-6 pt-2 pb-10">

    <!-- Header & Breadcrumb -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">
                <span>SDM & Analitik</span>
                <span>/</span>
                <span class="text-gray-900">Kelola Data Staff & Admin</span>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-1 text-[10px] font-extrabold text-orange-700 bg-orange-100 rounded-md">Owner Authority Only</span>
            </div>
        </div>
        <div class="flex gap-3">
            <a href="{{ url('/owner/kelola-staff/tambah') }}" class="px-5 py-2.5 text-sm font-bold text-white bg-brand hover:bg-brand-hover rounded-2xl shadow-sm flex items-center gap-2 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Akun Staff Baru
            </a>
        </div>
    </div>

    <!-- Ringkasan Kartu Statistik Staff -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="p-6 bg-white rounded-3xl border border-gray-100 shadow-sm relative overflow-hidden">
            <div class="absolute right-4 top-4 w-10 h-10 rounded-2xl bg-gray-100 text-gray-600 flex items-center justify-center font-black text-lg">👥</div>
            <p class="text-xs font-extrabold text-gray-400 uppercase tracking-wider mb-2">Total Seluruh Staff</p>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-black text-gray-900">{{ optional($stafList)->count() ?? 0 }}</span>
                <span class="text-xs text-gray-500 font-bold">Orang</span>
            </div>
        </div>
        <div class="p-6 bg-white rounded-3xl border border-orange-100 shadow-sm relative overflow-hidden bg-gradient-to-br from-white via-orange-50/20 to-orange-50/40">
            <div class="absolute right-4 top-4 w-10 h-10 rounded-2xl bg-orange-100 text-orange-700 flex items-center justify-center font-black text-lg">🛡️</div>
            <p class="text-xs font-extrabold text-orange-700 uppercase tracking-wider mb-2">Admin Operasional</p>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-black text-orange-600">{{ optional($stafList)->filter(fn($s) => (is_object($s->role) ? $s->role->value : $s->role) == 'admin')->count() }}</span>
                <span class="text-xs text-gray-500 font-bold">Akun Aktif</span>
            </div>
        </div>
        <div class="p-6 bg-white rounded-3xl border border-emerald-100 shadow-sm relative overflow-hidden bg-gradient-to-br from-white via-emerald-50/20 to-emerald-50/40">
            <div class="absolute right-4 top-4 w-10 h-10 rounded-2xl bg-emerald-100 text-emerald-700 flex items-center justify-center font-black text-lg">📦</div>
            <p class="text-xs font-extrabold text-emerald-700 uppercase tracking-wider mb-2">Petugas Gudang</p>
            <div class="flex items-baseline gap-1">
                <span class="text-3xl font-black text-emerald-600">{{ optional($stafList)->filter(fn($s) => (is_object($s->role) ? $s->role->value : $s->role) == 'petugas')->count() }}</span>
                <span class="text-xs text-gray-500 font-bold">Akun Aktif</span>
            </div>
        </div>
    </div>

    <!-- Bagian Filter & Judul Daftar Akun -->
    <div class="space-y-4">
        
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 px-2">
            <div>
                <h3 class="text-lg font-black text-gray-900">Daftar Akun Staff & Petugas</h3>
                <p class="text-xs text-gray-500">Kelola akses login untuk seluruh petugas operasional gudang dan admin.</p>
            </div>

            <form method="GET" action="{{ url('/owner/kelola-staff') }}" class="flex items-center gap-2.5 bg-white px-4 py-2.5 rounded-2xl border border-gray-200 shadow-2xs">
                <span class="text-[11px] font-black text-gray-400 uppercase tracking-wider">FILTER:</span>
                <select name="peran" onchange="this.form.submit()" class="bg-transparent text-gray-900 text-xs font-black focus:outline-none focus:ring-0 border-none cursor-pointer">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ request('peran') == 'admin' ? 'selected' : '' }}>Admin Operasional</option>
                    <option value="petugas" {{ request('peran') == 'petugas' ? 'selected' : '' }}>Petugas Gudang</option>
                </select>
            </form>
        </div>

        <!-- List Baris Card Terpisah -->
        <div class="space-y-3">
            @forelse($stafList as $staf)
            @php 
                $roleVal = is_object($staf->role) ? $staf->role->value : $staf->role; 
            @endphp
            <div class="bg-white p-5 rounded-3xl border border-gray-100 shadow-sm hover:shadow-md hover:border-gray-200 transition-all flex flex-col md:flex-row items-start md:items-center justify-between gap-4 group">
                
                <div class="flex items-center gap-4 min-w-[260px]">
                    <div class="w-12 h-12 rounded-2xl {{ $roleVal == 'admin' ? 'bg-orange-100 text-orange-700 border border-orange-200' : 'bg-emerald-100 text-emerald-700 border border-emerald-200' }} flex items-center justify-center text-base font-black shadow-xs flex-shrink-0 group-hover:scale-105 transition-transform">
                        {{ substr($staf->name, 0, 1) }}
                    </div>
                    <div>
                        <h4 class="font-black text-gray-900 text-base leading-tight">{{ $staf->name }}</h4>
                        <p class="text-[11px] text-gray-400 flex items-center gap-1.5 mt-1 font-semibold">
                            <svg class="w-3.5 h-3.5 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            Terdaftar sejak {{ $staf->created_at ? $staf->created_at->format('d M Y') : '-' }}
                        </p>
                    </div>
                </div>

                <div class="flex flex-col gap-1.5 text-xs font-semibold text-gray-600">
                    <div class="flex items-center gap-2 bg-[#FAF7F2] px-3.5 py-1.5 rounded-xl border border-gray-200/60 w-fit shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        <span class="text-gray-700">{{ $staf->email }}</span>
                    </div>
                    <div class="flex items-center gap-2 bg-[#FAF7F2] px-3.5 py-1.5 rounded-xl border border-gray-200/60 w-fit shadow-2xs">
                        <svg class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        <span class="text-gray-700">{{ $staf->no_telepon ?? '-' }}</span>
                    </div>
                </div>

                <div>
                    @if($roleVal == 'admin')
                        <span class="px-4 py-2 bg-orange-50 text-orange-700 text-xs font-black rounded-2xl border border-orange-200 inline-flex items-center gap-2 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-orange-500 animate-pulse"></span> Admin Operasional
                        </span>
                    @else
                        <span class="px-4 py-2 bg-emerald-50 text-emerald-700 text-xs font-black rounded-2xl border border-emerald-200 inline-flex items-center gap-2 shadow-2xs">
                            <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span> Petugas Gudang
                        </span>
                    @endif
                </div>

                <div class="flex items-center gap-1.5 bg-[#FAF7F2] p-1.5 rounded-2xl border border-gray-200/60 shadow-xs">
                    <a href="{{ url('/owner/kelola-staff/' . $staf->id . '/edit') }}" class="p-2.5 text-gray-500 hover:text-brand hover:bg-white rounded-xl transition font-bold shadow-2xs" title="Edit Akun">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </a>
                    <button onclick="openModalHapus('{{ $staf->id }}', '{{ $staf->name }}')" class="p-2.5 text-gray-500 hover:text-red-600 hover:bg-white rounded-xl transition font-bold shadow-2xs" title="Hapus Akun">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </div>

            </div>
            @empty
            <div class="bg-white p-12 rounded-3xl border border-gray-100 text-center text-gray-400 text-sm font-medium shadow-sm">
                Belum ada data staff atau petugas gudang yang terdaftar di sistem.
            </div>
            @endforelse
        </div>
    </div>
</div>

<!-- ================= MODAL KONFIRMASI HAPUS ================= -->
<div id="modalHapus" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-center border border-gray-100">
        <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto text-xl font-bold">⚠️</div>
        <h3 class="font-extrabold text-gray-900 text-base">Hapus Akun Ini?</h3>
        <p class="text-xs text-gray-500">Akun <span id="namaStaffHapus" class="font-bold text-gray-800"></span> akan dihapus permanen dari sistem database.</p>
        
        <!-- Form untuk Eksekusi Hapus yang Sebenarnya -->
        <form id="formHapus" method="POST" action="" class="flex justify-center gap-2 pt-2">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeModalHapus()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-600 rounded-xl text-sm font-bold transition">Batal</button>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-sm font-bold shadow-sm transition">Ya, Hapus</button>
        </form>
    </div>
</div>

<!-- ================= MODAL POP-UP TENGAH UNTUK NOTIFIKASI (SUKSES / GAGAL) ================= -->
@if (session('success') || $errors->any())
<div id="modalNotif" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-3xl max-w-sm w-full p-6 shadow-2xl space-y-4 text-center border border-gray-100 animate-in fade-in zoom-in duration-200">
        
        @if (session('success'))
            <div class="w-12 h-12 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center mx-auto text-xl font-bold">✓</div>
            <h3 class="font-extrabold text-gray-900 text-base">Berhasil!</h3>
            <p class="text-xs text-gray-600 font-medium">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div class="w-12 h-12 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto text-xl font-bold">✕</div>
            <h3 class="font-extrabold text-gray-900 text-base">Terjadi Kesalahan</h3>
            <ul class="text-xs text-red-600 space-y-1 font-medium text-left bg-red-50 p-3 rounded-xl">
                @foreach ($errors->all() as $error)
                    <li>• {{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <div class="pt-2">
            <button type="button" onclick="document.getElementById('modalNotif').remove()" class="w-full py-2.5 bg-brand hover:bg-brand-hover text-white rounded-xl text-sm font-bold shadow-sm transition cursor-pointer">
                OK, Mengerti
            </button>
        </div>
    </div>
</div>
@endif

<!-- Skrip JavaScript Modal Hapus & Dinamis -->
<script>
    function openModalHapus(id, nama) {
        document.getElementById('namaStaffHapus').innerText = nama;
        // Mengarahkan action form ke rute delete Laravel yang sesuai
        document.getElementById('formHapus').action = "{{ url('/owner/kelola-staff') }}/" + id;
        document.getElementById('modalHapus').classList.remove('hidden');
    }

    function closeModalHapus() {
        document.getElementById('modalHapus').classList.add('hidden');
    }
</script>
@endsection