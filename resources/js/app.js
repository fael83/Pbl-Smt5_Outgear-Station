import Alpine from 'alpinejs';

window.Alpine = Alpine;

/**
 * Ambil token CSRF dari layout panel.
 */
const tokenCsrf = () =>
    document.querySelector('meta[name="csrf-token"]')?.content ??
    document.querySelector('input[name="_token"]')?.value ??
    '';

/**
 * Kirim request dan baca balasan JSON.
 */
const kirim = async (url, { method = 'GET', body = null } = {}) => {
    const respons = await fetch(url, {
        method,
        credentials: 'same-origin',
        headers: {
            Accept: 'application/json',
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': tokenCsrf(),
        },
        body,
    });

    let data = {};

    try {
        data = await respons.json();
    } catch {
        data = { message: 'Balasan server tidak dapat dibaca.' };
    }

    return { ok: respons.ok, status: respons.status, data };
};

/**
 * Ubah `{ errors: { nama: ['pesan'] } }` menjadi `{ nama: 'pesan' }`.
 */
const ratakan = (errors = {}) =>
    Object.fromEntries(
        Object.entries(errors).map(([kunci, nilai]) => [
            kunci,
            Array.isArray(nilai) ? nilai[0] : String(nilai),
        ]),
    );

/**
 * Persentase satu angka desimal untuk badge kartu statistik.
 * Aman ketika total masih 0 (belum ada data).
 */
const persentase = (bagian, total) => {
    const pembagi = Number(total) || 0;

    if (pembagi <= 0) {
        return 0;
    }

    return Math.round(((Number(bagian) || 0) / pembagi) * 1000) / 10;
};

/**
 * Ganti isi wadah dengan HTML baru dari server.
 *
 * Kotak pencarian ikut dirender ulang karena berada di dalam HTML tabel, jadi
 * fokus dan posisi kursornya dipulihkan. Tanpa ini pengguna harus mengklik
 * kotak cari lagi setiap kali hasil pencarian selesai dimuat.
 * Kotak cari ditandai dengan atribut `data-cari`.
 */
const gantiIsi = (wadah, html) => {
    const aktif = document.activeElement;
    const sedangMengetik =
        aktif instanceof HTMLInputElement && aktif.hasAttribute('data-cari') && wadah.contains(aktif);
    const awal = sedangMengetik ? aktif.selectionStart : null;
    const akhir = sedangMengetik ? aktif.selectionEnd : null;

    wadah.innerHTML = html;

    if (!sedangMengetik) {
        return;
    }

    // Tunggu Alpine selesai menginisialisasi input baru (x-model mengisi nilainya).
    window.setTimeout(() => {
        const input = wadah.querySelector('input[data-cari]');

        if (!input) {
            return;
        }

        input.focus();

        try {
            input.setSelectionRange(awal, akhir);
        } catch {
            // Tipe input tertentu tidak mendukung seleksi; abaikan.
        }
    }, 0);
};

/**
 * Dasar bersama halaman admin yang datanya dicari/difilter di server
 * (pemesanan, denda, dan laporan). Cara kerjanya SAMA dengan halaman data
 * peralatan dan data pelanggan:
 *
 * - mencari, memfilter, pindah halaman, dan tombol muat ulang mengambil data
 *   dari database lewat satu request JSON ke `config.tabelUrl`, lalu hanya
 *   mengganti isi `x-ref="tabel"` sehingga halaman tidak dimuat ulang;
 * - selama menunggu server, pop-up "Memuat data…" tampil
 *   (resources/views/partials/status-muat.blade.php);
 * - angka ringkasan (kartu statistik) hanya diminta ulang saat muat ulang atau
 *   saat filter yang memengaruhinya berubah, karena menghitungnya butuh
 *   beberapa query tambahan;
 * - aksi yang tidak butuh database (membuka kotak cari, dropdown filter)
 *   berjalan instan di browser.
 *
 * Kontrak balasan JSON endpoint tabel: `{ html, url, ringkasan? }`.
 *
 * `tambahan` diterapkan lewat getOwnPropertyDescriptors supaya getter
 * (mis. `get adaFilter()`) tetap menjadi getter, bukan nilai sekali hitung.
 */
const buatTabelServer = (config = {}, kunciFilter = [], tambahan = {}) => {
    const awal = config.filters ?? {};

    const dasar = {
        // --- Filter & data tabel ---
        cari: String(awal.search ?? ''),
        filter: Object.fromEntries(kunciFilter.map((kunci) => [kunci, String(awal[kunci] ?? '')])),
        perPage: Number(awal.per_page ?? 10),
        halaman: 1,
        showSearchInput: String(awal.search ?? '') !== '',
        showFilterDropdown: false,
        ringkasanData: { ...(config.ringkasan ?? {}) },

        // --- Proses & notifikasi (ditampilkan oleh partials.status-muat) ---
        memuat: false,
        permintaanTerakhir: 0,
        pesan: '',
        pesanGagal: false,
        timerPesan: null,

        get adaFilter() {
            return this.cari !== '' || Object.values(this.filter).some((nilai) => nilai !== '');
        },

        /**
         * URL endpoint tabel sesuai filter yang sedang aktif. Nilai kosong
         * tidak dikirim, jadi server memakai nilai bawaannya.
         */
        urlTabel(halaman = 1, { segarkan = false, ringkasan = false } = {}) {
            const parameter = new URLSearchParams();

            if (this.cari !== '') {
                parameter.set('search', this.cari);
            }

            for (const [kunci, nilai] of Object.entries(this.filter)) {
                if (nilai !== '') {
                    parameter.set(kunci, nilai);
                }
            }

            parameter.set('per_page', String(this.perPage));

            if (halaman > 1) {
                parameter.set('page', String(halaman));
            }

            if (segarkan) {
                parameter.set('segarkan', '1');
            }

            if (ringkasan) {
                parameter.set('ringkasan', '1');
            }

            return `${config.tabelUrl}?${parameter.toString()}`;
        },

        /**
         * Ambil ulang kartu tabel dari database tanpa reload halaman.
         *
         * - `segarkan`  : buang cache di server dan minta angka ringkasan terbaru;
         * - `ringkasan` : minta angka ringkasan saja (filter periode berubah).
         */
        async muatTabel(halaman = 1, { segarkan = false, ringkasan = false } = {}) {
            const urutan = ++this.permintaanTerakhir;

            this.memuat = true;

            try {
                const { ok, data } = await kirim(this.urlTabel(halaman, { segarkan, ringkasan }));

                // Abaikan balasan lama kalau pengguna sudah mengetik lagi.
                if (urutan !== this.permintaanTerakhir) {
                    return;
                }

                if (!ok || typeof data.html !== 'string') {
                    this.notifikasi(data.message ?? 'Gagal memuat data.', true);

                    return;
                }

                gantiIsi(this.$refs.tabel, data.html);
                this.ringkasanData = data.ringkasan ?? this.ringkasanData;
                this.halaman = halaman;

                if (data.url) {
                    window.history.replaceState({}, '', data.url);
                }

                if (segarkan) {
                    this.notifikasi('Data diperbarui.');
                }
            } catch {
                if (urutan === this.permintaanTerakhir) {
                    this.notifikasi('Tidak dapat terhubung ke server.', true);
                }
            } finally {
                if (urutan === this.permintaanTerakhir) {
                    this.memuat = false;
                }
            }
        },

        /**
         * Tombol "Muat ulang data": halaman yang sedang dibuka diambil ulang
         * dari database dengan cache dibuang.
         */
        muatUlang() {
            return this.muatTabel(this.halaman, { segarkan: true });
        },

        /**
         * Pagination dirender server sebagai <a href="...?page=2"> di dalam
         * elemen `data-paginasi`; kliknya dicegat agar tetap lewat AJAX.
         */
        tangkapKlik(event) {
            const tautan = event.target.closest('[data-paginasi] a[href]');

            if (!tautan) {
                return;
            }

            const url = new URL(tautan.href, window.location.origin);

            if (url.origin !== window.location.origin) {
                return;
            }

            event.preventDefault();
            this.muatTabel(Number(url.searchParams.get('page') ?? 1) || 1);
        },

        resetFilter() {
            this.cari = '';

            for (const kunci of Object.keys(this.filter)) {
                this.filter[kunci] = '';
            }

            this.muatTabel();
        },

        notifikasi(pesan, gagal = false) {
            this.pesan = pesan;
            this.pesanGagal = gagal;

            window.clearTimeout(this.timerPesan);
            this.timerPesan = window.setTimeout(() => {
                this.pesan = '';
            }, 4000);
        },
    };

    return Object.defineProperties(dasar, Object.getOwnPropertyDescriptors(tambahan));
};

/**
 * Komponen halaman admin "Data Peralatan" (/admin/peralatan).
 *
 * Halaman ini membaca data dari Supabase yang jaraknya jauh (~1,3 detik per
 * query), jadi semua interaksi diusahakan hanya memakai satu request:
 *
 * - pencarian / filter / pagination  -> GET /admin/peralatan/tabel (JSON berisi
 *   HTML tabel saja) lalu isi ulang wadah tabel tanpa reload halaman;
 * - simpan / edit / hapus            -> POST ke route yang sama seperti form
 *   biasa, tetapi dengan header JSON sehingga server membalas baris tabel yang
 *   sudah dirender, bukan redirect.
 *
 * Tanpa JavaScript, form tetap terkirim seperti biasa (redirect + flash message).
 */
Alpine.data('halamanPeralatan', (config = {}) => {
    const formKosong = () => ({
        id: null,
        nama: '',
        kategori_id: '',
        deskripsi: '',
        tarif_per_hari: '',
        nilai_pengganti: '',
        stok_total: '',
        stok_rusak: 0,
        stok_disewa: 0,
        stok_tersedia: 0,
    });

    const lengkapiForm = (data = {}) => ({ ...formKosong(), ...data });

    return {
        // --- Filter & data tabel ---
        cari: String(config.filters?.search ?? ''),
        kategori: String(config.filters?.kategori ?? ''),
        kondisi: String(config.filters?.kondisi ?? 'semua'),
        perPage: Number(config.filters?.per_page ?? 10),
        halaman: 1,
        showSearchInput: String(config.filters?.search ?? '') !== '',
        showFilterDropdown: false,
        kategoriMap: { ...(config.kategoriMap ?? {}) },

        get namaKategoriAktif() {
            return this.kategoriMap[this.kategori] ?? '';
        },

        items: Array.isArray(config.items) ? config.items : [],
        ringkasanData: { ...(config.ringkasan ?? {}) },

        memuat: false,
        permintaanTerakhir: 0,

        // --- Modal form ---
        modalTerbuka: false,
        mode: 'create',
        formUrl: config.createUrl ?? '',
        form: lengkapiForm(config.nilaiAwal ?? {}),
        previewFoto: '',
        hapusFoto: false,
        errors: {},
        menyimpan: false,

        // --- Notifikasi ringan ---
        pesan: '',
        pesanGagal: false,
        timerPesan: null,

        /**
         * Buka kembali modal ketika validasi server gagal (jalur tanpa JS).
         */
        init() {
            if (!config.punyaError) {
                return;
            }

            this.mode = config.modeAwal === 'edit' ? 'edit' : 'create';

            const id = Number(config.alatIdAwal ?? 0) || 0;
            const alat = this.cariAlat(id);

            if (this.mode === 'edit' && id > 0) {
                this.formUrl = config.updateUrlAwal ?? alat?.update_url ?? config.createUrl;
                this.form = lengkapiForm({ ...(alat ?? {}), ...(config.nilaiAwal ?? {}), id });
            }

            this.modalTerbuka = true;
        },

        get adaFilter() {
            return this.cari !== '' || this.kategori !== '' || this.kondisi !== 'semua';
        },

        /**
         * Pindah tab status di atas tabel. `semua` berarti tanpa filter kondisi.
         */
        pilihKondisi(nilai) {
            if (this.kondisi === nilai) {
                return;
            }

            this.kondisi = nilai;
            this.muatTabel();
        },

        pilihKategori(nilai) {
            const strNilai = String(nilai ?? '');
            if (this.kategori === strNilai) {
                return;
            }

            this.kategori = strNilai;
            this.muatTabel();
        },

        /**
         * Persentase satu angka desimal untuk badge kartu statistik.
         * Aman ketika total masih 0 (belum ada alat).
         */
        rasio(bagian, total) {
            return persentase(bagian, total);
        },

        /**
         * URL endpoint tabel sesuai filter yang sedang aktif.
         */
        urlTabel(halaman = 1) {
            const parameter = new URLSearchParams();

            if (this.cari !== '') {
                parameter.set('search', this.cari);
            }

            if (this.kategori !== '') {
                parameter.set('kategori', this.kategori);
            }

            if (this.kondisi !== 'semua') {
                parameter.set('kondisi', this.kondisi);
            }

            parameter.set('per_page', String(this.perPage));

            if (halaman > 1) {
                parameter.set('page', String(halaman));
            }

            return `${config.tabelUrl}?${parameter.toString()}`;
        },

        /**
         * Ambil ulang kartu tabel (HTML + data modal + ringkasan) tanpa reload.
         */
        async muatTabel(halaman = 1) {
            const urutan = ++this.permintaanTerakhir;

            this.memuat = true;

            try {
                const { ok, data } = await kirim(this.urlTabel(halaman));

                // Abaikan balasan lama kalau pengguna sudah mengetik lagi.
                if (urutan !== this.permintaanTerakhir) {
                    return;
                }

                if (!ok || typeof data.html !== 'string') {
                    this.notifikasi(data.message ?? 'Gagal memuat data peralatan.', true);

                    return;
                }

                gantiIsi(this.$refs.tabel, data.html);
                this.items = Array.isArray(data.items) ? data.items : [];
                this.ringkasanData = data.ringkasan ?? this.ringkasanData;
                this.halaman = halaman;

                if (data.url) {
                    window.history.replaceState({}, '', data.url);
                }
            } catch {
                if (urutan === this.permintaanTerakhir) {
                    this.notifikasi('Tidak dapat terhubung ke server.', true);
                }
            } finally {
                if (urutan === this.permintaanTerakhir) {
                    this.memuat = false;
                }
            }
        },

        /**
         * Pagination dirender Laravel sebagai <a href="...?page=2">. Klik
         * ditangkap supaya halaman tidak dimuat ulang.
         */
        tangkapKlik(event) {
            const tautan = event.target.closest('a[href]');

            if (!tautan || tautan.target === '_blank') {
                return;
            }

            const url = new URL(tautan.href, window.location.origin);

            if (url.origin !== window.location.origin || !url.pathname.endsWith('/admin/peralatan')) {
                return;
            }

            event.preventDefault();
            this.muatTabel(Number(url.searchParams.get('page') ?? 1) || 1);
        },

        resetFilter() {
            this.cari = '';
            this.kategori = '';
            this.kondisi = 'semua';
            this.muatTabel();
        },

        cariAlat(id) {
            return this.items.find((alat) => Number(alat.id) === Number(id));
        },

        // --- Modal tambah / edit ---

        /** Kosongkan pilihan berkas dan tampilkan foto yang sudah tersimpan (bila ada). */
        aturFoto(url = '') {
            this.previewFoto = url;
            this.hapusFoto = false;

            const input = this.$refs.formAlat?.querySelector('input[type="file"]');

            if (input) {
                input.value = '';
            }
        },

        /** Pratinjau foto yang baru dipilih sebelum disimpan (tanpa ke server). */
        fotoDipilih(event) {
            const berkas = event.target.files?.[0];

            if (!berkas) {
                return;
            }

            if (this.previewFoto.startsWith('blob:')) {
                URL.revokeObjectURL(this.previewFoto);
            }

            this.previewFoto = URL.createObjectURL(berkas);
            this.hapusFoto = false;
        },

        bukaTambah() {
            this.mode = 'create';
            this.formUrl = config.createUrl ?? '';
            this.form = lengkapiForm();
            this.aturFoto();
            this.errors = {};
            this.modalTerbuka = true;
        },

        bukaEdit(id) {
            const alat = this.cariAlat(id);

            if (!alat) {
                this.notifikasi('Data alat tidak ditemukan, silakan muat ulang tabel.', true);

                return;
            }

            this.mode = 'edit';
            this.formUrl = alat.update_url;
            this.form = lengkapiForm({
                ...alat,
                kategori_id: String(alat.kategori_id ?? ''),
                stok_tersedia: Math.max(
                    0,
                    Number(alat.stok_total) - Number(alat.stok_disewa) - Number(alat.stok_rusak),
                ),
            });
            this.aturFoto(alat.foto_url ?? '');
            this.errors = {};
            this.modalTerbuka = true;
        },

        tutupModal() {
            this.modalTerbuka = false;
            this.menyimpan = false;
            this.errors = {};
        },

        /**
         * Kirim form modal sebagai AJAX. Server membalas JSON berisi HTML baris
         * tabel sehingga tidak perlu redirect + query ulang.
         */
        async simpan(form) {
            this.menyimpan = true;
            this.errors = {};

            try {
                const { ok, status, data } = await kirim(this.formUrl, {
                    method: 'POST',
                    body: new FormData(form),
                });

                if (!ok) {
                    if (status === 422) {
                        this.errors = ratakan(data.errors ?? {});
                    }

                    this.notifikasi(data.message ?? 'Gagal menyimpan data alat.', true);

                    return;
                }

                this.modalTerbuka = false;
                this.notifikasi(data.message ?? 'Data alat tersimpan.');

                if (this.mode === 'edit' && data.id) {
                    this.perbaruiBaris(data);
                } else {
                    // Alat baru bisa masuk ke halaman mana pun (tabel urut nama),
                    // jadi tabel diambil ulang dari halaman pertama.
                    this.muatTabel();
                }
            } catch {
                this.notifikasi('Tidak dapat terhubung ke server.', true);
            } finally {
                this.menyimpan = false;
            }
        },

        /**
         * Ganti satu baris tabel dengan HTML dari server. Kalau baris tersebut
         * tidak lagi cocok dengan filter aktif, tabel diambil ulang.
         */
        perbaruiBaris(data) {
            this.ringkasanData = data.ringkasan ?? this.ringkasanData;

            const indeks = this.items.findIndex((alat) => Number(alat.id) === Number(data.id));

            if (indeks !== -1 && data.item) {
                this.items[indeks] = data.item;
            }

            const baris = this.$refs.tabel.querySelector(`tr[data-alat-id="${data.id}"]`);
            const masihCocok = !this.adaFilter || this.cocokFilter(data.item);

            if (baris && masihCocok && typeof data.baris === 'string') {
                baris.outerHTML = data.baris;

                return;
            }

            this.muatTabel(this.halaman);
        },

        /**
         * Apakah item masih memenuhi filter kondisi yang aktif?
         */
        cocokFilter(item) {
            if (!item) {
                return false;
            }

            const tersedia = Math.max(
                0,
                Number(item.stok_total) - Number(item.stok_disewa) - Number(item.stok_rusak),
            );

            if (this.kondisi === 'tersedia') {
                return tersedia > 0;
            }

            if (this.kondisi === 'habis') {
                return tersedia <= 0;
            }

            if (this.kondisi === 'rusak') {
                return Number(item.stok_rusak) > 0;
            }

            return true;
        },

        /**
         * Hapus alat lewat AJAX setelah konfirmasi.
         */
        async hapus(form) {
            if (!window.confirm(form.dataset.pesan ?? 'Hapus alat ini?')) {
                return;
            }

            const tombol = form.querySelector('button[type="submit"]');

            if (tombol) {
                tombol.disabled = true;
            }

            try {
                const { ok, data } = await kirim(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                });

                if (!ok) {
                    this.notifikasi(data.message ?? 'Gagal menghapus alat.', true);

                    return;
                }

                this.notifikasi(data.message ?? 'Alat berhasil dihapus.');
                this.ringkasanData = data.ringkasan ?? this.ringkasanData;
                this.items = this.items.filter((alat) => Number(alat.id) !== Number(data.id));

                this.$refs.tabel.querySelector(`tr[data-alat-id="${data.id}"]`)?.remove();

                // Kalau halaman jadi kosong, ambil ulang supaya pesan "tidak ada
                // data" dan pagination ikut diperbarui.
                const sisaBaris = this.$refs.tabel.querySelectorAll('tbody tr[data-alat-id]').length;

                if (sisaBaris === 0) {
                    this.muatTabel(this.halaman);
                }
            } catch {
                this.notifikasi('Tidak dapat terhubung ke server.', true);
            } finally {
                if (tombol) {
                    tombol.disabled = false;
                }
            }
        },

        notifikasi(pesan, gagal = false) {
            this.pesan = pesan;
            this.pesanGagal = gagal;

            window.clearTimeout(this.timerPesan);
            this.timerPesan = window.setTimeout(() => {
                this.pesan = '';
            }, 4000);
        },
    };
});

/**
 * Komponen halaman admin "Data Pelanggan" (/admin/pelanggan).
 *
 * Polanya sama seperti halamanPeralatan: pencarian, filter status, dan
 * pagination hanya mengambil ulang kartu tabel lewat
 * GET /admin/pelanggan/tabel, sedangkan perubahan status akun dikirim lewat
 * PATCH dan server membalas HTML baris yang sudah diperbarui.
 */
Alpine.data('halamanPelanggan', (config = {}) => {
    /**
     * Parameter query sesuai filter yang sedang aktif.
     */
    const parameterFilter = (state, halaman = 1) => {
        const parameter = new URLSearchParams();

        if (state.cari !== '') {
            parameter.set('search', state.cari);
        }

        if (state.status !== 'semua') {
            parameter.set('status', state.status);
        }

        parameter.set('per_page', String(state.perPage));

        if (halaman > 1) {
            parameter.set('page', String(halaman));
        }

        return parameter;
    };

    return {
        // --- Filter & data tabel ---
        cari: String(config.filters?.search ?? ''),
        status: String(config.filters?.status ?? 'semua'),
        perPage: Number(config.filters?.per_page ?? 10),
        halaman: 1,
        showSearchInput: String(config.filters?.search ?? '') !== '',
        showFilterDropdown: false,
        modalTerbuka: Boolean(config.modalTerbuka),
        formMode: String(config.formMode ?? 'create'),
        formAction: String(config.createUrl ?? ''),
        nilaiForm: { ...(config.oldValues ?? {}) },

        items: Array.isArray(config.items) ? config.items : [],
        ringkasanData: { ...(config.ringkasan ?? {}) },

        memuat: false,
        permintaanTerakhir: 0,

        init() {
            if (this.formMode === 'edit' && this.nilaiForm.id) {
                this.formAction = config.updateUrlTemplate.replace(
                    '__ID__',
                    encodeURIComponent(String(this.nilaiForm.id)),
                );
            }
        },

        bukaTambah() {
            this.formMode = 'create';
            this.formAction = config.createUrl;
            this.nilaiForm = { id: null, name: '', email: '', no_telepon: '' };
            this.modalTerbuka = true;
        },

        bukaEdit(tombol) {
            this.formMode = 'edit';
            this.formAction = tombol.dataset.updateUrl;
            this.nilaiForm = {
                id: tombol.dataset.pelangganId,
                name: tombol.dataset.nama,
                email: tombol.dataset.email,
                no_telepon: tombol.dataset.noTelepon ?? '',
            };
            this.modalTerbuka = true;
        },

        // --- Notifikasi ringan ---
        pesan: '',
        pesanGagal: false,
        timerPesan: null,

        get adaFilter() {
            return this.cari !== '' || this.status !== 'semua';
        },

        /**
         * URL unduhan CSV mengikuti filter yang sedang aktif.
         */
        get urlEkspor() {
            const query = parameterFilter(this).toString();

            return query === '' ? config.eksporUrl : `${config.eksporUrl}?${query}`;
        },

        /**
         * Pindah tab status di atas tabel. `semua` berarti tanpa filter.
         */
        pilihStatus(nilai) {
            if (this.status === nilai) {
                return;
            }

            this.status = nilai;
            this.muatTabel();
        },

        /**
         * Persentase untuk badge kartu statistik.
         */
        rasio(bagian, total) {
            return persentase(bagian, total);
        },

        /**
         * URL endpoint tabel sesuai filter yang sedang aktif.
         */
        urlTabel(halaman = 1) {
            return `${config.tabelUrl}?${parameterFilter(this, halaman).toString()}`;
        },

        /**
         * Ambil ulang kartu tabel (HTML + ringkasan) tanpa reload.
         */
        async muatTabel(halaman = 1) {
            const urutan = ++this.permintaanTerakhir;

            this.memuat = true;

            try {
                const { ok, data } = await kirim(this.urlTabel(halaman));

                // Abaikan balasan lama kalau pengguna sudah mengetik lagi.
                if (urutan !== this.permintaanTerakhir) {
                    return;
                }

                if (!ok || typeof data.html !== 'string') {
                    this.notifikasi(data.message ?? 'Gagal memuat data pelanggan.', true);

                    return;
                }

                gantiIsi(this.$refs.tabel, data.html);
                this.items = Array.isArray(data.items) ? data.items : [];
                this.ringkasanData = data.ringkasan ?? this.ringkasanData;
                this.halaman = halaman;

                if (data.url) {
                    window.history.replaceState({}, '', data.url);
                }
            } catch {
                this.notifikasi('Tidak dapat terhubung ke server.', true);
            } finally {
                this.memuat = false;
            }
        },

        /**
         * Pagination dirender server; kliknya dicegat agar tetap lewat AJAX.
         */
        tangkapKlik(event) {
            const tautan = event.target.closest('[data-paginasi] a');

            if (!tautan) {
                return;
            }

            const url = new URL(tautan.href, window.location.origin);

            if (url.origin !== window.location.origin || !url.pathname.endsWith('/admin/pelanggan')) {
                return;
            }

            event.preventDefault();
            this.muatTabel(Number(url.searchParams.get('page') ?? 1) || 1);
        },

        resetFilter() {
            this.cari = '';
            this.status = 'semua';
            this.muatTabel();
        },

        notifikasi(pesan, gagal = false) {
            this.pesan = pesan;
            this.pesanGagal = gagal;

            window.clearTimeout(this.timerPesan);
            this.timerPesan = window.setTimeout(() => {
                this.pesan = '';
            }, 4000);
        },
    };
});

/**
 * Komponen halaman admin "Dashboard Operasional" (/admin).
 *
 * Tombol "Muat ulang data" mengambil ulang isi dashboard lewat GET /admin/data
 * (JSON berisi HTML yang sudah dirender server) lalu mengganti isi wadah
 * `x-ref="isi"` tanpa memuat ulang halaman — sama seperti pencarian dan filter
 * pada halaman data peralatan.
 *
 * Tanpa JavaScript, tombolnya tetap berupa tautan `?segarkan=1` sehingga cache
 * dibuang dan halaman dimuat ulang seperti biasa.
 */
Alpine.data('halamanDashboard', (config = {}) => ({
    memuat: false,
    jumlahVerifikasi: Number(config.jumlahVerifikasi ?? 0),
    pesan: '',
    pesanGagal: false,
    timerPesan: null,

    /**
     * Keterangan tombol lonceng verifikasi mengikuti jumlah terbaru.
     */
    get judulVerifikasi() {
        return this.jumlahVerifikasi > 0
            ? `${this.jumlahVerifikasi} pembayaran menunggu verifikasi`
            : 'Tidak ada pembayaran yang menunggu';
    },

    /**
     * Ambil ulang isi dashboard (kartu statistik, booking, dan inventaris).
     */
    async muatUlang() {
        if (this.memuat) {
            return;
        }

        this.memuat = true;

        try {
            const { ok, data } = await kirim(config.dataUrl);

            if (!ok || typeof data.html !== 'string') {
                this.notifikasi(data.message ?? 'Gagal memuat ulang data dashboard.', true);

                return;
            }

            this.$refs.isi.innerHTML = data.html;
            this.jumlahVerifikasi = Number(data.perlu_verifikasi ?? 0);
            window.dispatchEvent(new CustomEvent('badge-verifikasi', { detail: this.jumlahVerifikasi }));
            this.notifikasi('Data diperbarui.');
        } catch {
            this.notifikasi('Tidak dapat terhubung ke server.', true);
        } finally {
            this.memuat = false;
        }
    },

    notifikasi(pesan, gagal = false) {
        this.pesan = pesan;
        this.pesanGagal = gagal;

        window.clearTimeout(this.timerPesan);
        this.timerPesan = window.setTimeout(() => {
            this.pesan = '';
        }, 4000);
    },
}));

/**
 * Komponen halaman admin "Data Pemesanan" (/admin/pemesanan).
 *
 * Pencarian, filter status, dan pagination mengambil ulang kartu tabel lewat
 * GET /admin/pemesanan/tabel (lihat buatTabelServer untuk aturan bersamanya).
 * Nilai `filter.status` kosong berarti semua status.
 */
Alpine.data('halamanPemesanan', (config = {}) =>
    buatTabelServer(config, ['status'], {
        labelStatus: { ...(config.labelStatus ?? {}) },

        /** Teks pada chip "Filter: …". */
        get labelFilter() {
            return this.labelStatus[this.filter.status] ?? 'Semua Status';
        },

        pilihStatus(nilai) {
            const baru = String(nilai ?? '');

            if (this.filter.status === baru) {
                return;
            }

            this.filter.status = baru;
            this.muatTabel();
        },
    }),
);

/**
 * Komponen halaman admin "Kelola Denda" (/admin/denda).
 *
 * Tabel diambil lewat GET /admin/denda/tabel. Tombol muat ulang juga meminta
 * ulang angka ringkasan (kartu statistik dan lencana tagihan belum lunas).
 */
Alpine.data('halamanDenda', (config = {}) =>
    buatTabelServer(config, ['status'], {
        /** Teks pada chip "Filter: …". */
        get labelFilter() {
            return { belum_dibayar: 'Belum Lunas', lunas: 'Lunas' }[this.filter.status] ?? 'Semua Status';
        },

        pilihStatus(nilai) {
            const baru = String(nilai ?? '');

            if (this.filter.status === baru) {
                return;
            }

            this.filter.status = baru;
            this.muatTabel();
        },
    }),
);

/**
 * Komponen halaman admin "Laporan Operasional" (/admin/laporan).
 *
 * Satu tab laporan (pendapatan, penyewaan, atau denda) dimuat dari database
 * sesuai rentang tanggal dan kata kunci lewat GET /admin/laporan/tabel.
 * Mengganti tab atau mengetik pencarian hanya memuat tabelnya; mengubah
 * rentang tanggal atau menekan muat ulang juga menghitung ulang kartu
 * ringkasan karena angkanya bergantung pada periode.
 */
Alpine.data('halamanLaporan', (config = {}) => {
    const periodeBawaan = {
        dari: String(config.bawaan?.dari ?? ''),
        sampai: String(config.bawaan?.sampai ?? ''),
    };

    return buatTabelServer(config, ['tipe', 'dari', 'sampai'], {
        /** Tab bukan "filter" yang perlu di-reset; hanya kata kunci dan periode. */
        get adaFilter() {
            return (
                this.cari !== '' ||
                this.filter.dari !== periodeBawaan.dari ||
                this.filter.sampai !== periodeBawaan.sampai
            );
        },

        /** URL ekspor (Excel/PDF) mengikuti tab, periode, dan kata kunci yang aktif. */
        urlEkspor(dasar) {
            const parameter = new URLSearchParams({
                tipe: this.filter.tipe,
                dari: this.filter.dari,
                sampai: this.filter.sampai,
            });

            if (this.cari !== '') {
                parameter.set('search', this.cari);
            }

            return `${dasar}?${parameter.toString()}`;
        },

        /** Teks pada chip jenis laporan yang aktif. */
        get labelTipe() {
            return (
                {
                    pendapatan: 'Ringkasan Pendapatan',
                    penyewaan: 'Histori Penyewaan',
                    denda: 'Laporan Denda',
                }[this.filter.tipe] ?? 'Ringkasan Pendapatan'
            );
        },

        pilihTipe(nilai) {
            const baru = String(nilai ?? '');

            if (this.filter.tipe === baru) {
                return;
            }

            this.filter.tipe = baru;
            this.muatTabel();
        },

        /** Rentang tanggal berubah: tabel dan kartu ringkasan dihitung ulang. */
        ubahPeriode() {
            if (this.filter.dari === '' || this.filter.sampai === '') {
                return;
            }

            this.muatTabel(1, { ringkasan: true });
        },

        resetFilter() {
            this.cari = '';
            this.filter.dari = periodeBawaan.dari;
            this.filter.sampai = periodeBawaan.sampai;
            this.muatTabel(1, { ringkasan: true });
        },
    });
});

/**
 * Komponen halaman admin "Pembayaran" (/admin/pembayaran).
 *
 * Dua tab: DP dan Pelunasan (sisa sewa + denda). Pencarian dan filter
 * berjalan di browser; aksi (terima tunai, bayar Midtrans, tolak) memanggil
 * server lalu mengganti seluruh data dengan balasan terbaru.
 */
Alpine.data('halamanPembayaran', (config = {}) => ({
    tab: new URLSearchParams(window.location.search).get('tab') || config.tab || 'dp',
    search: '',
    filterStatus: 'Semua',
    dpList: Array.isArray(config.dp) ? config.dp : [],
    pelunasanList: Array.isArray(config.pelunasan) ? config.pelunasan : [],

    modalBayar: false,
    itemBayar: null,
    cara: 'tunai',

    memuat: false,
    pesan: '',
    pesanGagal: false,
    timerPesan: null,

    formatRupiah(val) {
        return 'Rp ' + Number(val ?? 0).toLocaleString('id-ID');
    },

    terapkan(data) {
        if (Array.isArray(data.dp)) {
            this.dpList = data.dp;
        }

        if (Array.isArray(data.pelunasan)) {
            this.pelunasanList = data.pelunasan;
        }

        window.dispatchEvent(new CustomEvent('badge-verifikasi', { detail: this.dpMenunggu }));
    },

    async muatUlang() {
        if (this.memuat) {
            return;
        }

        this.memuat = true;

        try {
            const { ok, data } = await kirim(config.dataUrl);

            if (!ok) {
                this.notifikasi(data.message ?? 'Gagal memuat ulang data pembayaran.', true);

                return;
            }

            this.terapkan(data);
            this.notifikasi('Data diperbarui.');
        } catch {
            this.notifikasi('Tidak dapat terhubung ke server.', true);
        } finally {
            this.memuat = false;
        }
    },

    /** Aksi pada pembayaran DP: verifikasi, terima tunai, atau tolak. */
    async aksiDp(item, kunciUrl, cara = null) {
        if (this.memuat) {
            return;
        }

        this.memuat = true;

        try {
            const body = new FormData();

            if (cara) {
                body.append('cara', cara);
            }

            const { ok, data } = await kirim(item[kunciUrl], { method: 'PATCH', body });

            if (!ok) {
                this.notifikasi(data.message ?? 'Aksi gagal diproses.', true);

                return;
            }

            this.terapkan(data);
            this.notifikasi(data.message ?? 'Pembayaran diperbarui.');
        } catch {
            this.notifikasi('Tidak dapat terhubung ke server.', true);
        } finally {
            this.memuat = false;
        }
    },

    bukaBayar(item) {
        this.itemBayar = item;
        this.cara = 'tunai';
        this.modalBayar = true;
    },

    /** Proses pelunasan: tunai langsung lunas, Midtrans membuka pop-up Snap. */
    async prosesBayar() {
        const item = this.itemBayar;

        if (!item || this.memuat) {
            return;
        }

        this.memuat = true;

        try {
            const body = new FormData();
            body.append('cara', this.cara);

            const { ok, data } = await kirim(item.url_bayar, { method: 'POST', body });

            if (!ok) {
                this.notifikasi(data.message ?? 'Pelunasan gagal diproses.', true);

                return;
            }

            this.terapkan(data);
            this.modalBayar = false;
            this.notifikasi(data.message ?? 'Pelunasan diproses.');

            if (data.snap_token) {
                this.bukaSnap(data.snap_token, data.pembayaran_id);
            }
        } catch {
            this.notifikasi('Tidak dapat terhubung ke server.', true);
        } finally {
            this.memuat = false;
        }
    },

    /** Buka lagi pop-up Midtrans untuk tagihan yang masih menunggu. */
    lanjutkanMidtrans(item) {
        if (item.snap_token) {
            this.bukaSnap(item.snap_token, item.pembayaran_menunggu_id);
        }
    },

    bukaSnap(token, pembayaranId) {
        if (typeof window.snap === 'undefined') {
            this.notifikasi('Pop-up Midtrans gagal dimuat. Muat ulang halaman.', true);

            return;
        }

        const selesai = () => this.cekStatus(pembayaranId);

        window.snap.pay(token, {
            onSuccess: selesai,
            onPending: selesai,
            onError: selesai,
            onClose: selesai,
        });
    },

    async cekStatus(pembayaranId) {
        if (!pembayaranId) {
            return this.muatUlang();
        }

        try {
            const url = config.sinkronUrl.replace('__ID__', pembayaranId);
            const { ok, data } = await kirim(url, { method: 'POST' });

            if (ok) {
                this.terapkan(data);
            }

            this.notifikasi(data.message ?? 'Status diperbarui.', !ok);
        } catch {
            this.notifikasi('Tidak dapat terhubung ke server.', true);
        }
    },

    resetFilter() {
        this.search = '';
        this.filterStatus = 'Semua';
    },

    get adaFilter() {
        return this.search !== '' || this.filterStatus !== 'Semua';
    },

    get dpTersaring() {
        const q = this.search.toLowerCase().trim();

        return this.dpList.filter((i) => {
            const cocokStatus = this.filterStatus === 'Semua' || i.status_transaksi === this.filterStatus;
            const cocokCari =
                q === '' ||
                i.kode_pemesanan.toLowerCase().includes(q) ||
                i.pelanggan_nama.toLowerCase().includes(q) ||
                i.transaction_id.toLowerCase().includes(q);

            return cocokStatus && cocokCari;
        });
    },

    get pelunasanTersaring() {
        const q = this.search.toLowerCase().trim();

        return this.pelunasanList.filter((i) => {
            const cocokStatus = this.filterStatus === 'Semua' || i.status === this.filterStatus;
            const cocokCari =
                q === '' ||
                i.kode_pemesanan.toLowerCase().includes(q) ||
                i.pelanggan_nama.toLowerCase().includes(q);

            return cocokStatus && cocokCari;
        });
    },

    get dpMenunggu() {
        return this.dpList.filter((p) => p.status_transaksi === 'pending').length;
    },

    get dpBerhasil() {
        return this.dpList.filter((p) => p.status_transaksi === 'berhasil').length;
    },

    get pelunasanPerluBayar() {
        return this.pelunasanList.filter((p) => p.status !== 'lunas' && p.dikembalikan).length;
    },

    get totalTagihan() {
        return this.pelunasanList
            .filter((p) => p.status !== 'lunas' && p.dikembalikan)
            .reduce((jumlah, p) => jumlah + p.total_tagihan, 0);
    },

    pindahTab(nama) {
        this.tab = nama;
        this.filterStatus = 'Semua';
        try {
            const url = new URL(window.location.href);
            url.searchParams.set('tab', nama);
            window.history.replaceState({}, '', url);
        } catch {}
    },

    notifikasi(pesan, gagal = false) {
        this.pesan = pesan;
        this.pesanGagal = gagal;

        window.clearTimeout(this.timerPesan);
        this.timerPesan = window.setTimeout(() => {
            this.pesan = '';
        }, 4000);
    },
}));

Alpine.start();
