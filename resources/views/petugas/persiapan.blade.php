@extends('layouts.panel', ['title' => 'Persiapan & Serah Terima'])

@php
    $nilaiAwal = [
        'pemesanan_id' => old('pemesanan_id', ''),
        'nama_ktp' => old('nama_ktp', ''),
        'nomor_ktp' => old('nomor_ktp', ''),
        'waktu_serah_terima' => old('waktu_serah_terima', now()->format('Y-m-d\TH:i')),
        'status_serah_terima' => old('status_serah_terima', 'siap'),
    ];

    $modeAwal = (string) old('form_mode', 'create');
    $serahTerimaIdAwal = (int) old('serah_terima_id', 0);
    $updateUrlAwal = $serahTerimaIdAwal > 0 ? route('petugas.persiapan.update', $serahTerimaIdAwal) : null;

    $filterAwal = [
        'search' => $filters['search'] ?? '',
        'status' => $filters['status'] ?? '',
        'per_page' => $filters['per_page'] ?? 10,
    ];
@endphp

@section('content')
    <div class="space-y-6"
         x-data="halamanSerahTerima({
            createUrl: {{ Js::from(route('petugas.persiapan.store')) }},
            tabelUrl: {{ Js::from(route('petugas.persiapan.tabel')) }},
            items: {{ Js::from($daftarPersiapan) }},
            filters: {{ Js::from($filterAwal) }},
            ringkasan: {{ Js::from($ringkasan) }},
            nilaiAwal: {{ Js::from($nilaiAwal) }},
            modeAwal: {{ Js::from($modeAwal) }},
            serahTerimaIdAwal: {{ $serahTerimaIdAwal }},
            updateUrlAwal: {{ Js::from($updateUrlAwal) }},
            punyaError: {{ $errors->any() ? 'true' : 'false' }}
         })">

        <!-- Judul & Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-[#1A2024] tracking-tight">Persiapan & Serah Terima</h1>
                <p class="text-xs text-gray-500 mt-1 max-w-2xl">
                    Manajemen verifikasi KTP, jadwal serah terima peralatan outdoor, dan pemantauan status peminjaman harian petugas gudang.
                </p>
            </div>
            <div class="flex items-center gap-2">
                <button type="button" @click="bukaTambah()"
                        class="px-4 py-2.5 bg-[#D86B43] text-white text-xs font-bold rounded-xl hover:bg-[#D86B43]/90 transition shadow-md">
                    + Catat Serah Terima
                </button>
            </div>
        </div>

        <!-- Kartu Statistik (Ringkasan Data Serah Terima) -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Agenda</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-zinc-900" x-text="ringkasanData.total_agenda">{{ $ringkasan['total_agenda'] }}</p>
                </div>
                <div class="text-[11px] text-zinc-500 font-medium pt-2 border-t border-zinc-100">
                    Semua jadwal tercatat
                </div>
            </div>

           <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Diambil / Proses</span>
                <div class="my-2">
                    <!-- Perhatikan bagian x-text di bawah ini -->
                    <p class="text-3xl font-black font-heading text-amber-600" x-text="ringkasanData.siap_diserahkan">0</p>
                </div>
                <div class="text-[11px] text-amber-700 font-bold pt-2 border-t border-zinc-100">
                    Barang sedang/siap diambil
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Selesai / Dikembalikan</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-emerald-600" x-text="ringkasanData.sudah_dikembalikan">{{ $ringkasan['sudah_dikembalikan'] }}</p>
                </div>
                <div class="text-[11px] text-emerald-700 font-bold pt-2 border-t border-zinc-100">
                    Barang sudah kembali ke gudang
                </div>
            </div>
        </div>

        <!-- Tabel Data Serah Terima (Dihitung/Diperbarui lewat AJAX) -->
        <div class="relative">
            <div x-ref="tabel" @click="tangkapKlik($event)" :class="memuat ? 'opacity-60' : ''" class="transition-opacity">
                @include('petugas._tabel-persiapan')
            </div>

            <div x-cloak x-show="memuat" class="absolute inset-x-0 top-0 flex justify-center pt-16 pointer-events-none">
                <div class="flex items-center gap-2 bg-[#1A2024] text-white text-xs font-semibold px-4 py-2 rounded-full shadow-lg">
                    <span class="w-3 h-3 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                    Memuat data…
                </div>
            </div>
        </div>

        <!-- Notifikasi Ringan -->
        <div x-cloak x-show="pesan" x-transition.opacity
             class="fixed bottom-6 right-6 z-[60] max-w-sm px-4 py-3 rounded-xl shadow-lg text-xs font-semibold"
             :class="pesanGagal ? 'bg-red-50 text-red-700 border border-red-100' : 'bg-emerald-50 text-emerald-700 border border-emerald-100'">
            <span x-text="pesan"></span>
        </div>

        <!-- Modal Tambah / Edit Serah Terima -->
        <div x-cloak x-show="modalTerbuka" x-transition.opacity
             class="fixed inset-0 z-50 flex items-start justify-center overflow-y-auto bg-[#1A2024]/50 p-4 md:p-8"
             @keydown.escape.window="tutupModal()">
            <div class="w-full max-w-xl bg-white rounded-2xl shadow-xl" @click.outside="tutupModal()">
                <div class="flex items-start justify-between gap-4 p-5 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-bold text-[#1A2024]" x-text="mode === 'edit' ? 'Edit Serah Terima' : 'Catat Serah Terima Baru'"></h2>
                        <p class="text-xs text-gray-500 mt-0.5">Pastikan data KTP dan ID Pemesanan sesuai dengan fisik penyewa.</p>
                    </div>
                    <button type="button" @click="tutupModal()" class="w-8 h-8 rounded-lg bg-gray-100 text-gray-500 hover:bg-gray-200 transition">✕</button>
                </div>

                <form x-ref="formSerahTerima" method="POST" :action="formUrl" @submit.prevent="simpan($el)" class="p-5 space-y-4">
                    @csrf
                    <template x-if="mode === 'edit'">
                        <input type="hidden" name="_method" value="PUT">
                    </template>
                    <input type="hidden" name="form_mode" :value="mode">
                    <input type="hidden" name="serah_terima_id" :value="form.id ?? ''">
                    <input type="hidden" name="search" :value="cari">
                    <input type="hidden" name="status" :value="statusFilter">
                    <input type="hidden" name="per_page" :value="perPage">

                    @if ($errors->any())
                        <div class="px-4 py-3 rounded-xl bg-red-50 border border-red-100 text-xs text-red-700 space-y-1">
                            <p class="font-semibold">Periksa kembali data berikut:</p>
                            @foreach ($errors->all() as $pesanError)
                                <p>{{ $pesanError }}</p>
                            @endforeach
                        </div>
                    @endif

                    <div class="space-y-4">
                        <div>
                            <label for="pemesanan_id" class="block text-xs font-semibold text-[#1A2024] mb-1">ID Pemesanan</label>
                            <input type="number" id="pemesanan_id" name="pemesanan_id" x-model="form.pemesanan_id" required
                                   class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                        </div>

                        <div>
                            <label for="nama_ktp" class="block text-xs font-semibold text-[#1A2024] mb-1">Nama Sesuai KTP</label>
                            <input type="text" id="nama_ktp" name="nama_ktp" x-model="form.nama_ktp" required
                                   class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                        </div>

                        <div>
                            <label for="nomor_ktp" class="block text-xs font-semibold text-[#1A2024] mb-1">Nomor KTP (NIK)</label>
                            <input type="text" id="nomor_ktp" name="nomor_ktp" x-model="form.nomor_ktp" required
                                   class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                        </div>

                        <div>
                            <label for="waktu_serah_terima" class="block text-xs font-semibold text-[#1A2024] mb-1">Waktu Serah Terima</label>
                            <input type="datetime-local" id="waktu_serah_terima" name="waktu_serah_terima" x-model="form.waktu_serah_terima" required
                                   class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                        </div>

                        <div>
                            <label for="status_serah_terima" class="block text-xs font-semibold text-[#1A2024] mb-1">Status Serah Terima</label>
                            <select id="status_serah_terima" name="status_serah_terima" x-model="form.status_serah_terima" required
                                    class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                                <option value="diambil">Diambil</option>
                                <option value="dikembalikan">Dikembalikan</option>
                            </select>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-gray-100">
                        <button type="button" @click="tutupModal()"
                                class="px-4 py-2.5 bg-gray-100 text-[#1A2024] text-xs font-semibold rounded-xl hover:bg-gray-200 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="menyimpan"
                                class="px-4 py-2.5 bg-[#D86B43] text-white text-xs font-semibold rounded-xl hover:bg-[#D86B43]/90 transition shadow-sm disabled:opacity-60">
                            <span x-text="menyimpan ? 'Menyimpan…' : (mode === 'edit' ? 'Simpan Perubahan' : 'Simpan Data')"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Skrip Alpine.js untuk Halaman Persiapan / Serah Terima -->
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('halamanSerahTerima', (config) => ({
                createUrl: config.createUrl,
                tabelUrl: config.tabelUrl,
                items: config.items,
                ringkasanData: config.ringkasan,
                cari: config.filters.search,
                statusFilter: config.filters.status,
                perPage: config.filters.per_page,
                
                modalTerbuka: config.punyaError,
                mode: config.modeAwal,
                formUrl: config.modeAwal === 'edit' ? config.updateUrlAwal : config.createUrl,
                form: config.nilaiAwal,
                
                memuat: false,
                menyimpan: false,
                pesan: '',
                pesanGagal: false,
                timerPesan: null,

                init() {
                    if (config.punyaError && config.serahTerimaIdAwal > 0) {
                        this.form.id = config.serahTerimaIdAwal;
                    }
                },

                bukaTambah() {
                    this.mode = 'create';
                    this.formUrl = this.createUrl;
                    this.form = {
                        pemesanan_id: '',
                        nama_ktp: '',
                        nomor_ktp: '',
                        waktu_serah_terima: new Date().toISOString().slice(0, 16),
                        status_serah_terima: 'siap'
                    };
                    this.modalTerbuka = true;
                },

                bukaEdit(item) {
                    this.mode = 'edit';
                    this.formUrl = `/petugas/persiapan/${item.id}`;
                    this.form = {
                        id: item.id,
                        pemesanan_id: item.pemesanan_id,
                        nama_ktp: item.nama_ktp,
                        nomor_ktp: item.nomor_ktp,
                        waktu_serah_terima: item.waktu_serah_terima ? item.waktu_serah_terima.replace(' ', 'T').slice(0, 16) : '',
                        status_serah_terima: item.status_serah_terima
                    };
                    this.modalTerbuka = true;
                },

                tutupModal() {
                    this.modalTerbuka = false;
                },

                tampilkanPesan(teks, gagal = false) {
                    this.pesan = teks;
                    this.pesanGagal = gagal;
                    clearTimeout(this.timerPesan);
                    this.timerPesan = setTimeout(() => {
                        this.pesan = '';
                    }, 4000);
                },

                muatTabel() {
                    this.memuat = true;
                    const params = new URLSearchParams({
                        search: this.cari,
                        status: this.statusFilter,
                        per_page: this.perPage
                    });

                    fetch(`${this.tabelUrl}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.text())
                    .then(html => {
                        this.$refs.tabel.innerHTML = html;
                        this.memuat = false;
                    })
                    .catch(() => {
                        this.memuat = false;
                        this.tampilkanPesan('Gagal memuat data tabel.', true);
                    });
                },

                simpan(elForm) {
                    this.menyimpan = true;
                    const formData = new FormData(elForm);

                    fetch(this.formUrl, {
                        method: 'POST',
                        body: formData,
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        this.menyimpan = false;
                        this.tutupModal();
                        this.muatTabel();
                        if (data.ringkasan) this.ringkasanData = data.ringkasan;
                        this.tampilkanPesan(data.pesan || 'Data berhasil disimpan.');
                    })
                    .catch(err => {
                        this.menyimpan = false;
                        if (err.errors) {
                            let pesanPertama = Object.values(err.errors)[0][0];
                            this.tampilkanPesan(pesanPertama, true);
                        } else {
                            this.tampilkanPesan(err.message || 'Terjadi kesalahan.', true);
                        }
                    });
                },

                hapus(id) {
                    if (!confirm('Yakin ingin menghapus data serah terima ini?')) return;

                    fetch(`/petugas/persiapan/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        this.muatTabel();
                        if (data.ringkasan) this.ringkasanData = data.ringkasan;
                        this.tampilkanPesan(data.pesan || 'Data berhasil dihapus.');
                    })
                    .catch(err => {
                        this.tampilkanPesan(err.message || 'Gagal menghapus data.', true);
                    });
                },

                pilihStatus(status) {
                    this.statusFilter = status;
                    this.muatTabel();
                },

                tangkapKlik(e) {
                    const tombolEdit = e.target.closest('[data-aksi="edit"]');
                    if (tombolEdit) {
                        e.preventDefault();
                        const rawItem = tombolEdit.getAttribute('data-item');
                        try {
                            const item = JSON.parse(rawItem);
                            this.bukaEdit(item);
                        } catch(err) { console.error(err); }
                        return;
                    }

                    const tombolHapus = e.target.closest('[data-aksi="hapus"]');
                    if (tombolHapus) {
                        e.preventDefault();
                        const id = tombolHapus.getAttribute('data-id');
                        this.hapus(id);
                        return;
                    }

                    const paginasiLink = e.target.closest('.pagination a, nav[role="navigation"] a');
                    if (paginasiLink) {
                        e.preventDefault();
                        const url = new URL(paginasiLink.href);
                        const page = url.searchParams.get('page');
                        
                        const params = new URLSearchParams({
                            search: this.cari,
                            status: this.statusFilter,
                            per_page: this.perPage,
                            page: page
                        });

                        this.memuat = true;
                        fetch(`${this.tabelUrl}?${params.toString()}`, {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        })
                        .then(res => res.text())
                        .then(html => {
                            this.$refs.tabel.innerHTML = html;
                            this.memuat = false;
                        });
                    }
                }
            }));
        });
    </script>
@endsection