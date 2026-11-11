@extends('layouts.owner')

@section('content')
<div class="space-y-6 pt-2 pb-10">

    <!-- Header & Judul Utama -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-2xl font-black text-zinc-900 tracking-tight">Kelola Data Staff & Admin</h2>
            <p class="text-xs text-zinc-500 mt-0.5">Kelola akses login untuk seluruh petugas operasional gudang dan admin</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ url('/owner/kelola-staff/tambah') }}" class="px-5 py-2.5 text-xs font-bold text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl shadow-sm flex items-center gap-2 transition-all cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Tambah Akun Staff Baru
            </a>
        </div>
    </div>

    <!-- Ringkasan Kartu Statistik Staff -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <div class="text-3xl font-black text-zinc-800">{{ optional($stafList)->count() ?? 0 }}</div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">Total Seluruh Staff</div>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-zinc-100 text-zinc-600 border border-zinc-200">
                Orang
            </span>
        </div>

        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <div class="text-3xl font-black text-orange-600">{{ optional($stafList)->filter(fn($s) => (is_object($s->role) ? $s->role->value : $s->role) == 'admin')->count() }}</div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">Admin Operasional</div>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-orange-50 text-orange-700 border border-orange-200">
                Aktif
            </span>
        </div>

        <div class="p-6 bg-white rounded-2xl border border-zinc-200/80 shadow-sm relative overflow-hidden flex items-center justify-between">
            <div>
                <div class="text-3xl font-black text-emerald-600">{{ optional($stafList)->filter(fn($s) => (is_object($s->role) ? $s->role->value : $s->role) == 'petugas')->count() }}</div>
                <div class="text-xs font-semibold text-zinc-400 mt-1">Petugas Gudang</div>
            </div>
            <span class="text-[11px] font-bold px-3 py-1 rounded-full bg-emerald-50 text-emerald-700 border border-emerald-200">
                Aktif
            </span>
        </div>
    </div>

    <!-- TABEL UTAMA (Disatukan seperti format halaman pemesanan) -->
    <div class="bg-white rounded-2xl border border-zinc-200/80 shadow-sm overflow-hidden">

        <!-- Header Tabel & Filter Peran -->
        <form method="GET" action="{{ url('/owner/kelola-staff') }}" class="p-5 border-b border-zinc-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <h3 class="font-bold text-zinc-900 text-base">Daftar Akun Staff & Petugas</h3>
                <span class="text-xs font-medium text-zinc-500 bg-zinc-100 px-2.5 py-1 rounded-lg">
                    {{ optional($stafList)->count() ?? 0 }} data
                </span>
            </div>

            <div class="flex items-center gap-3">
                <span class="text-xs font-semibold text-zinc-400 uppercase tracking-wider">Filter Peran:</span>
                <select name="peran" onchange="this.form.submit()" class="bg-zinc-100 border border-zinc-300 text-xs font-medium text-zinc-700 rounded-lg px-3 py-2 focus:outline-none focus:ring-1 focus:ring-orange-500 cursor-pointer">
                    <option value="">Semua Peran</option>
                    <option value="admin" {{ request('peran') == 'admin' ? 'selected' : '' }}>Admin Operasional</option>
                    <option value="petugas" {{ request('peran') == 'petugas' ? 'selected' : '' }}>Petugas Gudang</option>
                </select>
                @if(request('peran'))
                    <a href="{{ url('/owner/kelola-staff') }}" class="text-xs font-semibold text-orange-600 hover:underline">Reset</a>
                @endif
            </div>
        </form>

        <!-- ISI TABEL -->
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs align-middle">
                <thead>
                    <tr class="text-zinc-400 font-bold border-b border-zinc-100 bg-zinc-50/50">
                        <th class="p-4 uppercase">STAFF & INFORMASI</th>
                        <th class="p-4 uppercase">KONTAK (EMAIL & TELEPON)</th>
                        <th class="p-4 uppercase text-center">PERAN / HAK AKSES</th>
                        <th class="p-4 uppercase text-center">AKSI</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 font-medium text-zinc-700">
                    @forelse($stafList as $staf)
                    @php 
                        $roleVal = is_object($staf->role) ? $staf->role->value : $staf->role; 
                    @endphp
                    <tr class="hover:bg-zinc-50/80 transition">
                        
                        <!-- Kolom 1: Avatar & Nama -->
                        <td class="p-4 align-middle">
                            <div class="flex items-center gap-3.5">
                                <div class="w-10 h-10 rounded-xl {{ $roleVal == 'admin' ? 'bg-orange-50 text-orange-600 border border-orange-200' : 'bg-emerald-50 text-emerald-600 border border-emerald-200' }} flex items-center justify-center text-xs font-bold shadow-sm flex-shrink-0">
                                    {{ strtoupper(substr($staf->name, 0, 1)) }}
                                </div>
                                <div>
                                    <h4 class="font-bold text-zinc-900 text-sm leading-tight">{{ $staf->name }}</h4>
                                    <p class="text-[11px] text-zinc-400 mt-0.5">
                                        Terdaftar sejak {{ $staf->created_at ? $staf->created_at->format('d M Y') : '-' }}
                                    </p>
                                </div>
                            </div>
                        </td>

                        <!-- Kolom 2: Kontak -->
                        <td class="p-4 align-middle">
                            <div class="space-y-1">
                                <div class="flex items-center gap-1.5 text-zinc-700">
                                    <span class="text-zinc-400">✉</span> {{ $staf->email }}
                                </div>
                                <div class="flex items-center gap-1.5 text-zinc-500 text-[11px]">
                                    <span class="text-zinc-400">📞</span> {{ $staf->no_telepon ?? '-' }}
                                </div>
                            </div>
                        </td>

                        <!-- Kolom 3: Peran -->
                        <td class="p-4 align-middle text-center">
                            @if($roleVal == 'admin')
                                <span class="px-3 py-1 bg-orange-50 text-orange-700 text-[11px] font-semibold rounded-full border border-orange-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-orange-500 animate-pulse"></span> Admin Operasional
                                </span>
                            @else
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 text-[11px] font-semibold rounded-full border border-emerald-200 inline-flex items-center gap-1.5">
                                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span> Petugas Gudang
                                </span>
                            @endif
                        </td>

                        <!-- Kolom 4: Aksi -->
                        <td class="p-4 align-middle text-center">
                            <div class="inline-flex items-center gap-1 bg-zinc-50 p-1 rounded-xl border border-zinc-200/60">
                                <a href="{{ url('/owner/kelola-staff/' . $staf->id . '/edit') }}" class="px-3 py-1.5 text-xs font-semibold text-zinc-600 hover:text-zinc-900 hover:bg-white rounded-lg transition flex items-center gap-1.5 shadow-2xs" title="Edit Akun">
                                    <svg class="w-3.5 h-3.5 text-zinc-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                    Edit
                                </a>
                                <button type="button" onclick="openModalHapus('{{ $staf->id }}', '{{ $staf->name }}')" class="p-1.5 text-zinc-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition cursor-pointer" title="Hapus Akun">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                        </td>

                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="p-12 text-center text-zinc-400 text-sm font-medium">
                            Belum ada data staff atau petugas gudang yang terdaftar di sistem.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</div>

<!-- ================= MODAL KONFIRMASI HAPUS ================= -->
<div id="modalHapus" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl space-y-4 text-center border border-zinc-200">
        <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto text-base font-bold">⚠️</div>
        <h3 class="font-bold text-zinc-900 text-sm">Hapus Akun Ini?</h3>
        <p class="text-xs text-zinc-500">Akun <span id="namaStaffHapus" class="font-bold text-zinc-800"></span> akan dihapus permanen dari sistem database.</p>
        
        <form id="formHapus" method="POST" action="" class="flex justify-end gap-2 pt-2 border-t border-zinc-100">
            @csrf
            @method('DELETE')
            <button type="button" onclick="closeModalHapus()" class="px-4 py-2 bg-zinc-100 hover:bg-zinc-200 text-zinc-700 rounded-xl text-xs font-semibold transition cursor-pointer">Batal</button>
            <button type="submit" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white rounded-xl text-xs font-semibold shadow-sm transition cursor-pointer">Ya, Hapus</button>
        </form>
    </div>
</div>

<!-- ================= MODAL POP-UP TENGAH UNTUK NOTIFIKASI (SUKSES / GAGAL) ================= -->
@if (session('success') || $errors->any())
<div id="modalNotif" class="fixed inset-0 bg-black/40 backdrop-blur-xs z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-sm w-full p-6 shadow-xl space-y-4 text-center border border-zinc-200 animate-in fade-in zoom-in duration-200">
        
        @if (session('success'))
            <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center mx-auto text-base font-bold">✓</div>
            <h3 class="font-bold text-zinc-900 text-sm">Berhasil!</h3>
            <p class="text-xs text-zinc-500 font-normal">{{ session('success') }}</p>
        @endif

        @if ($errors->any())
            <div class="w-10 h-10 rounded-full bg-red-50 text-red-600 flex items-center justify-center mx-auto text-base font-bold">✕</div>
            <h3 class="font-bold text-zinc-900 text-sm">Terjadi Kesalahan</h3>
            <ul class="text-xs text-red-600 space-y-1 font-normal text-left bg-red-50/50 p-3 rounded-xl">
                @foreach ($errors->all() as $error)
                    <li>- {{ $error }}</li>
                @endforeach
            </ul>
        @endif

        <div class="pt-2">
            <button type="button" onclick="document.getElementById('modalNotif').remove()" class="w-full py-2 bg-zinc-900 hover:bg-zinc-800 text-white rounded-xl text-xs font-semibold shadow-sm transition cursor-pointer">
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
        document.getElementById('formHapus').action = "{{ url('/owner/kelola-staff') }}/" + id;
        document.getElementById('modalHapus').classList.remove('hidden');
    }

    function closeModalHapus() {
        document.getElementById('modalHapus').classList.add('hidden');
    }
</script>
@endsection