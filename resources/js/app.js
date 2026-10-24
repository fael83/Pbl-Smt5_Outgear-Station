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

        items: Array.isArray(config.items) ? config.items : [],
        ringkasanData: { ...(config.ringkasan ?? {}) },

        memuat: false,
        permintaanTerakhir: 0,

        // --- Modal form ---
        modalTerbuka: false,
        mode: 'create',
        formUrl: config.createUrl ?? '',
        form: lengkapiForm(config.nilaiAwal ?? {}),
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

                this.$refs.tabel.innerHTML = data.html;
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

        bukaTambah() {
            this.mode = 'create';
            this.formUrl = config.createUrl ?? '';
            this.form = lengkapiForm();
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

        items: Array.isArray(config.items) ? config.items : [],
        ringkasanData: { ...(config.ringkasan ?? {}) },

        memuat: false,
        permintaanTerakhir: 0,

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

                this.$refs.tabel.innerHTML = data.html;
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

        /**
         * Apakah item masih memenuhi filter status yang aktif?
         */
        cocokFilter(item) {
            if (!item) {
                return false;
            }

            return this.status === 'semua' || item.status_akun === this.status;
        },

        /**
         * Aktifkan / nonaktifkan akun lewat AJAX setelah konfirmasi.
         */
        async ubahStatus(form) {
            if (!window.confirm(form.dataset.pesan ?? 'Ubah status akun ini?')) {
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
                    this.notifikasi(data.message ?? 'Gagal mengubah status akun.', true);

                    return;
                }

                this.notifikasi(data.message ?? 'Status akun tersimpan.');
                this.ringkasanData = data.ringkasan ?? this.ringkasanData;

                const indeks = this.items.findIndex((item) => Number(item.id) === Number(data.id));

                if (indeks !== -1 && data.item) {
                    this.items[indeks] = data.item;
                }

                const baris = this.$refs.tabel.querySelector(`tr[data-pelanggan-id="${data.id}"]`);

                // Kalau akun tidak lagi cocok dengan tab aktif, tabel diambil
                // ulang supaya jumlah baris dan pagination tetap konsisten.
                if (baris && this.cocokFilter(data.item) && typeof data.baris === 'string') {
                    baris.outerHTML = data.baris;

                    return;
                }

                this.muatTabel(this.halaman);
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

Alpine.start();
