@extends('layouts.panel', ['title' => 'Pengembalian Barang'])

@section('content')
    <div class="space-y-6"
         x-data="halamanPengembalian({
            createUrl: {{ Js::from(route('petugas.pengembalian.store')) }},
            items: {{ Js::from($daftarPengembalian) }}
         })">

        <!-- Judul & Aksi -->
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-black text-[#1A2024] tracking-tight">Pengembalian Barang</h1>
                <p class="text-xs text-gray-500 mt-1 max-w-2xl">
                    Manajemen pencatatan pengembalian alat outdoor, pemeriksaan keterlambatan, dan checklist kondisi akhir barang sewa.
                </p>
            </div>
            <div>
            </div>
        </div>

        <!-- Kartu Statistik -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Total Pengembalian</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-zinc-900">{{ $ringkasan['total_pengembalian'] }}</p>
                </div>
                <div class="text-[11px] text-zinc-500 font-medium pt-2 border-t border-zinc-100">
                    Seluruh riwayat tercatat
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Tepat Waktu</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-emerald-600">{{ $ringkasan['tepat_waktu'] }}</p>
                </div>
                <div class="text-[11px] text-emerald-700 font-bold pt-2 border-t border-zinc-100">
                    Pengembalian sesuai jadwal
                </div>
            </div>

            <div class="bg-white p-5 rounded-2xl border border-zinc-200/70 shadow-sm flex flex-col justify-between">
                <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400">Terlambat</span>
                <div class="my-2">
                    <p class="text-3xl font-black font-heading text-rose-600">{{ $ringkasan['terlambat'] }}</p>
                </div>
                <div class="text-[11px] text-rose-700 font-bold pt-2 border-t border-zinc-100">
                    Melebihi batas sewa
                </div>
            </div>
        </div>

        <!-- Tabel Data Pengembalian -->
        <div class="bg-white rounded-2xl border border-zinc-200/70 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-zinc-50 text-zinc-400 text-[10px] uppercase tracking-wider font-bold border-b border-zinc-200/60">
                            <th class="py-3.5 px-6">ID</th>
                            <th class="py-3.5 px-6">Kode Booking</th>
                            <th class="py-3.5 px-6">Pelanggan</th>
                            <th class="py-3.5 px-6">Waktu Pengambilan</th>
                            <th class="py-3.5 px-6 text-center">Keterlambatan</th>
                            <th class="py-3.5 px-6 text-center">Aksi & Checklist</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 text-xs text-zinc-700 font-medium">
                        @forelse($daftarPengembalian as $item)
                            <tr class="hover:bg-zinc-50/50 transition">
                                <td class="py-4 px-6 font-mono text-zinc-400 font-bold">#{{ $item->id }}</td>
                                <td class="py-4 px-6">
                                    <div class="font-extrabold text-[#D86B43] font-mono">
                                        {{ $item->pemesanan->kode_pemesanan ?? '#'.$item->pemesanan_id }}
                                    </div>
                                </td>
                                <td class="py-4 px-6 font-semibold text-zinc-800">
                                    {{ $item->pemesanan->pelanggan->name ?? 'Pelanggan' }}
                                </td>
                                <td class="py-4 px-6 text-zinc-600 font-mono text-[11px]">{{ $item->waktu_pengambilan }}</td>
                                <td class="py-4 px-6 text-center">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-bold {{ $item->hari_terlambat > 0 ? 'bg-rose-50 text-rose-700 border border-rose-200' : 'bg-emerald-50 text-emerald-700 border border-emerald-200' }}">
                                        {{ $item->hari_terlambat > 0 ? $item->hari_terlambat . ' Hari Terlambat' : 'Tepat Waktu' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-center flex items-center justify-center gap-2">
                                    <button type="button" @click="bukaDetail({{ Js::from($item) }})"
                                            class="px-3 py-1.5 rounded-xl bg-[#15AEEA]/10 text-[#15AEEA] hover:bg-[#15AEEA]/20 transition text-[11px] font-bold inline-flex items-center gap-1">
                                        🔍 Checklist Akhir
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-12 text-center text-zinc-400 italic">Belum ada data pengembalian alat.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($daftarPengembalian->hasPages())
                <div class="p-4 border-t border-zinc-100">
                    {{ $daftarPengembalian->links() }}
                </div>
            @endif
        </div>

        <!-- Modal Catat Pengembalian Baru -->
        <div x-cloak x-show="modalTerbuka" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-[#1A2024]/60 backdrop-blur-xs p-4 md:p-8"
             @keydown.escape.window="tutupModal()">
            <div class="w-full max-w-xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-zinc-100" @click.outside="tutupModal()">
                <div class="flex items-start justify-between gap-4 p-6 border-b border-zinc-100 bg-gradient-to-r from-[#F4EFEA]/80 via-white to-white relative">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-[#D86B43]"></div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-[#D86B43] bg-[#D86B43]/10 px-2.5 py-1 rounded-full">
                            Gudang Logistik
                        </span>
                        <h2 class="text-xl font-black text-[#1A2024] tracking-tight mt-2">Catat Pengembalian Baru</h2>
                        <p class="text-xs text-zinc-500 mt-0.5">Masukkan ID Pemesanan dari data booking admin dan tentukan jadwal pengembalian.</p>
                    </div>
                    <button type="button" @click="tutupModal()" class="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-500 hover:bg-zinc-200 transition flex items-center justify-center font-bold">✕</button>
                </div>

                <form method="POST" :action="createUrl" @submit.prevent="simpan($el)" class="p-6 space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-[#1A2024] mb-1">ID Pemesanan / Booking</label>
                        <input type="number" name="pemesanan_id" placeholder="Contoh: 1 atau 2" required
                               class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-[#1A2024] mb-1">Waktu Pengambilan</label>
                        <input type="datetime-local" name="waktu_pengambilan" required
                               class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    </div>

                    <!-- Input waktu_submit disembunyikan/diisi otomatis via backend atau diset default tersembunyi jika diperlukan, namun tetap aman diinput -->
                    <input type="hidden" name="waktu_submit" :value="new Date().toISOString().slice(0, 19).replace('T', ' ')">

                    <div>
                        <label class="block text-xs font-semibold text-[#1A2024] mb-1">Hari Terlambat</label>
                        <input type="number" name="hari_terlambat" value="0" min="0" required
                               class="w-full bg-[#F4EFEA]/60 border border-transparent rounded-xl px-4 py-2.5 text-xs text-[#1A2024] focus:ring-2 focus:ring-[#15AEEA]">
                    </div>

                    <div class="flex items-center justify-end gap-2 pt-4 border-t border-zinc-100">
                        <button type="button" @click="tutupModal()"
                                class="px-4 py-2.5 bg-zinc-100 text-zinc-700 text-xs font-semibold rounded-xl hover:bg-zinc-200 transition">
                            Batal
                        </button>
                        <button type="submit" :disabled="menyimpan"
                                class="px-5 py-2.5 bg-[#D86B43] text-white text-xs font-bold rounded-xl hover:bg-[#D86B43]/90 transition shadow-sm disabled:opacity-60">
                            <span x-text="menyimpan ? 'Menyimpan…' : 'Simpan Data'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- MODAL POP-UP CHECKLIST KONDISI AKHIR -->
        <div x-cloak x-show="modalDetailTerbuka" x-transition.opacity
             class="fixed inset-0 z-50 flex items-center justify-center overflow-y-auto bg-[#1A2024]/60 backdrop-blur-xs p-4 md:p-8"
             @keydown.escape.window="tutupModalDetail()">
            <div class="w-full max-w-4xl bg-white rounded-3xl shadow-2xl overflow-hidden border border-zinc-100 flex flex-col max-h-[90vh]" @click.outside="tutupModalDetail()">
                
                <div class="flex items-start justify-between gap-4 p-6 border-b border-zinc-100 bg-gradient-to-r from-[#F4EFEA]/80 via-white to-white relative shrink-0">
                    <div class="absolute top-0 left-0 w-1.5 h-full bg-[#15AEEA]"></div>
                    <div>
                        <span class="text-[10px] font-black uppercase tracking-widest text-[#15AEEA] bg-[#15AEEA]/10 px-2.5 py-1 rounded-full">
                            Pemeriksaan Fisik Logistik
                        </span>
                        <h2 class="text-xl font-black text-[#1A2024] tracking-tight mt-2 flex items-center gap-2">
                            Checklist Kondisi Akhir Barang <span class="text-[#15AEEA]" x-text="itemAktif ? '#' + itemAktif.id : ''"></span>
                        </h2>
                        <p class="text-xs text-zinc-500 mt-0.5">
                            Periksa jumlah kondisi alat (baik, rusak, atau hilang). Jika ada alat hilang, wajib menyertakan alasan. Alat rusak & hilang otomatis diteruskan ke laporan Kerusakan & Kehilangan.
                        </p>
                    </div>
                    <button type="button" @click="tutupModalDetail()" class="w-9 h-9 rounded-xl bg-zinc-100 text-zinc-500 hover:bg-zinc-200 hover:text-zinc-900 transition flex items-center justify-center font-bold">✕</button>
                </div>

                <div class="p-6 space-y-6 overflow-y-auto bg-zinc-50/40">
                    
                    <!-- Info Booking & Pelanggan -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 bg-gradient-to-br from-[#F4EFEA]/70 to-[#F4EFEA]/30 p-5 rounded-2xl border border-zinc-200/80 shadow-xs">
                        <div class="bg-white/80 p-3.5 rounded-xl border border-zinc-200/60 shadow-2xs">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#D86B43] block mb-1">Kode Booking Pemesanan</span>
                            <p class="font-bold text-zinc-900 text-sm font-mono" x-text="itemAktif?.pemesanan?.kode_pemesanan || ('#' + itemAktif?.pemesanan_id)"></p>
                        </div>
                        <div class="bg-white/80 p-3.5 rounded-xl border border-zinc-200/60 shadow-2xs">
                            <span class="text-[10px] font-bold uppercase tracking-wider text-[#15AEEA] block mb-1">Nama Pelanggan</span>
                            <p class="font-bold text-zinc-900 text-sm" x-text="itemAktif?.pemesanan?.pelanggan?.name || 'Pelanggan'"></p>
                        </div>
                    </div>

                    <!-- Daftar Alat Sewa untuk Checklist -->
                    <div class="bg-white border border-zinc-200/70 rounded-2xl shadow-xs overflow-hidden">
                        <div class="px-5 py-3.5 bg-zinc-50/80 border-b border-zinc-100 flex items-center justify-between">
                            <h3 class="text-xs font-bold text-zinc-800 uppercase tracking-wider">Daftar Peralatan yang Harus Dikembalikan</h3>
                            <span class="text-[11px] text-zinc-400 font-medium">Harap pastikan jumlah total fisik sesuai dengan jumlah sewa</span>
                        </div>

                        <div class="p-4 space-y-4">
                            <template x-for="(it, index) in checklistItems" :key="it.detail_pemesanan_id">
                                <div class="p-4 rounded-2xl border border-zinc-200/80 bg-zinc-50/30 space-y-3 transition hover:border-zinc-300">
                                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-6 h-6 rounded-lg bg-zinc-200/80 text-zinc-700 font-mono font-bold text-xs flex items-center justify-center" x-text="index + 1"></span>
                                            <div>
                                                <h4 class="font-bold text-sm text-[#1A2024]" x-text="it.nama_alat"></h4>
                                                <span class="text-[11px] text-zinc-400" x-text="it.kategori ? 'Kategori: ' + it.kategori : ''"></span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-2">
                                            <span class="px-3 py-1 rounded-full text-xs font-bold bg-zinc-100 text-zinc-700 border border-zinc-200 font-mono">
                                                Total Sewa: <span x-text="it.total_sewa"></span> Unit
                                            </span>
                                            <template x-if="(Number(it.jumlah_baik) + Number(it.jumlah_rusak) + Number(it.jumlah_hilang)) !== it.total_sewa">
                                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-700 border border-amber-200">
                                                    ⚠️ Belum Pas (<span x-text="Number(it.jumlah_baik) + Number(it.jumlah_rusak) + Number(it.jumlah_hilang)"></span>/<span x-text="it.total_sewa"></span>)
                                                </span>
                                            </template>
                                        </div>
                                    </div>

                                    <!-- Input Kondisi: Baik, Rusak, Hilang -->
                                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 pt-2">
                                        <!-- Baik -->
                                        <div class="bg-emerald-50/40 p-3 rounded-xl border border-emerald-200/60">
                                            <label class="block text-[11px] font-bold text-emerald-800 uppercase tracking-wider mb-1">
                                                ✓ Jumlah Baik
                                            </label>
                                            <input type="number" min="0" :max="it.total_sewa" x-model.number="it.jumlah_baik"
                                                   class="w-full bg-white border border-emerald-200 rounded-lg px-3 py-1.5 text-xs font-mono font-bold text-emerald-900 focus:ring-2 focus:ring-emerald-400">
                                        </div>

                                        <!-- Rusak -->
                                        <div class="bg-amber-50/40 p-3 rounded-xl border border-amber-200/60">
                                            <label class="block text-[11px] font-bold text-amber-800 uppercase tracking-wider mb-1">
                                                ⚠ Jumlah Rusak
                                            </label>
                                            <input type="number" min="0" :max="it.total_sewa" x-model.number="it.jumlah_rusak"
                                                   class="w-full bg-white border border-amber-200 rounded-lg px-3 py-1.5 text-xs font-mono font-bold text-amber-900 focus:ring-2 focus:ring-amber-400">
                                        </div>

                                        <!-- Hilang -->
                                        <div class="bg-rose-50/40 p-3 rounded-xl border border-rose-200/60">
                                            <label class="block text-[11px] font-bold text-rose-800 uppercase tracking-wider mb-1">
                                                ✕ Jumlah Hilang
                                            </label>
                                            <input type="number" min="0" :max="it.total_sewa" x-model.number="it.jumlah_hilang"
                                                   class="w-full bg-white border border-rose-200 rounded-lg px-3 py-1.5 text-xs font-mono font-bold text-rose-900 focus:ring-2 focus:ring-rose-400">
                                        </div>
                                    </div>

                                    <!-- Input Alasan Hilang (Muncul hanya jika jumlah_hilang > 0) -->
                                    <template x-if="it.jumlah_hilang > 0">
                                        <div class="p-3.5 rounded-xl bg-rose-50 border border-rose-200 space-y-1 transition">
                                            <div class="flex items-center justify-between">
                                                <label class="text-xs font-bold text-rose-800 flex items-center gap-1.5">
                                                    <span>❗</span>
                                                    <span>Alasan Kehilangan Alat (Wajib Diisi):</span>
                                                </label>
                                                <span class="text-[10px] text-rose-600 font-semibold">*Wajib diisi</span>
                                            </div>
                                            <input type="text" x-model="it.alasan_hilang"
                                                   placeholder="Contoh: Jatuh ke jurang saat pendakian, hanyut di sungai, atau tertinggal di shelter camp"
                                                   class="w-full bg-white border border-rose-300 rounded-xl px-3.5 py-2 text-xs text-rose-950 placeholder-rose-300 focus:ring-2 focus:ring-rose-500 font-medium">
                                        </div>
                                    </template>
                                </div>
                            </template>

                            <template x-if="checklistItems.length === 0">
                                <div class="py-12 text-center text-zinc-400 italic">
                                    Tidak ada data detail alat untuk pemesanan ini.
                                </div>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-6 py-4 bg-white border-t border-zinc-100 shrink-0">
                    <p class="text-[11px] text-zinc-400 font-medium">
                        💡 Data alat rusak dan hilang otomatis tersinkronisasi ke laporan <strong class="text-zinc-600">Kerusakan & Kehilangan</strong>.
                    </p>
                    <div class="flex items-center gap-2 w-full sm:w-auto justify-end">
                        <button type="button" @click="tutupModalDetail()"
                                class="px-4 py-2.5 bg-zinc-100 text-zinc-700 text-xs font-semibold rounded-xl hover:bg-zinc-200 transition">
                            Tutup
                        </button>
                        <button type="button" @click="simpanChecklist()" :disabled="menyimpanChecklist"
                                class="px-5 py-2.5 bg-[#15AEEA] text-white text-xs font-bold rounded-xl hover:bg-[#15AEEA]/90 transition shadow-sm disabled:opacity-60 flex items-center gap-2">
                            <span x-text="menyimpanChecklist ? 'Menyimpan Checklist…' : 'Simpan Checklist Kondisi'"></span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('halamanPengembalian', (config) => ({
                createUrl: config.createUrl,
                modalTerbuka: false,
                modalDetailTerbuka: false,
                itemAktif: null,
                menyimpan: false,
                menyimpanChecklist: false,
                checklistItems: [],

                bukaTambah() {
                    this.modalTerbuka = true;
                },

                tutupModal() {
                    this.modalTerbuka = false;
                },

                bukaDetail(item) {
                    this.itemAktif = item;
                    const detailPemesanan = item.pemesanan?.detail_pemesanan || [];
                    const detailPengembalian = item.detail_pengembalian || [];

                    // Inisialisasi daftar item pemeriksaan
                    this.checklistItems = detailPemesanan.map(dp => {
                        const pg = detailPengembalian.find(p => p.detail_pemesanan_id === dp.id);
                        const totalSewa = Number(dp.jumlah) || 1;

                        return {
                            detail_pemesanan_id: dp.id,
                            nama_alat: dp.alat?.nama_alat || ('Alat #' + dp.alat_id),
                            kategori: dp.alat?.kategori?.nama || '',
                            total_sewa: totalSewa,
                            jumlah_baik: pg ? Number(pg.jumlah_baik) : totalSewa,
                            jumlah_rusak: pg ? Number(pg.jumlah_rusak) : 0,
                            jumlah_hilang: pg ? Number(pg.jumlah_hilang) : 0,
                            alasan_hilang: pg ? (pg.alasan_hilang || '') : ''
                        };
                    });

                    this.modalDetailTerbuka = true;
                },

                tutupModalDetail() {
                    this.modalDetailTerbuka = false;
                    this.itemAktif = null;
                    this.checklistItems = [];
                },

                simpanChecklist() {
                    if (!this.itemAktif) return;

                    // Validasi alasan hilang
                    for (const it of this.checklistItems) {
                        if (it.jumlah_hilang > 0 && (!it.alasan_hilang || !it.alasan_hilang.trim())) {
                            alert('Harap isi alasan kehilangan untuk alat: ' + it.nama_alat);
                            return;
                        }
                    }

                    // Peringatan jika total unit belum cocok dengan total sewa
                    const tidakCocok = this.checklistItems.some(it => {
                        const totalUnit = Number(it.jumlah_baik) + Number(it.jumlah_rusak) + Number(it.jumlah_hilang);
                        return totalUnit !== it.total_sewa;
                    });

                    if (tidakCocok) {
                        if (!confirm('Total jumlah fisik beberapa alat belum sesuai dengan jumlah yang disewa. Yakin ingin tetap menyimpan?')) {
                            return;
                        }
                    }

                    this.menyimpanChecklist = true;

                    fetch(`/petugas/pengembalian/${this.itemAktif.id}/checklist`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({
                            items: this.checklistItems
                        })
                    })
                    .then(async res => {
                        const data = await res.json();
                        if (!res.ok) throw data;
                        return data;
                    })
                    .then(data => {
                        this.menyimpanChecklist = false;
                        alert(data.pesan || 'Checklist kondisi fisik barang berhasil disimpan!');
                        this.tutupModalDetail();
                        location.reload();
                    })
                    .catch(err => {
                        this.menyimpanChecklist = false;
                        alert(err.message || 'Gagal menyimpan checklist.');
                    });
                },

                simpan(elForm) {
                    this.menyimpan = true;
                    const formData = new FormData(elForm);

                    fetch(this.createUrl, {
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
                        location.reload();
                    })
                    .catch(err => {
                        this.menyimpan = false;
                        alert(err.message || 'Terjadi kesalahan.');
                    });
                },

                hapus(id) {
                    if (!confirm('Yakin ingin menghapus data pengembalian ini?')) return;

                    fetch(`/petugas/pengembalian/${id}`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        body: JSON.stringify({ _method: 'DELETE' })
                    })
                    .then(async res => res.json())
                    .then(data => {
                        location.reload();
                    })
                    .catch(() => {
                        alert('Gagal menghapus data.');
                    });
                }
            }));
        });
    </script>
@endsection