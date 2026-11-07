@extends('layouts.customer')

@section('title', 'Booking Saya - OutGear Station')

@push('head')
    @if (filled($clientKey))
        <script src="{{ $snapJsUrl }}" data-client-key="{{ $clientKey }}"></script>
    @endif
@endpush

@section('content')
@php
    // Pengelompokan tab dan tampilan tiap status pemesanan.
    $tampilan = [
        'menunggu_pembayaran' => ['label' => 'Menunggu Pembayaran DP', 'kelas' => 'bg-amber-50 text-amber-700', 'tab' => 'menunggu'],
        'dikonfirmasi' => ['label' => 'Dikonfirmasi', 'kelas' => 'bg-emerald-50 text-emerald-700', 'tab' => 'aktif'],
        'berlangsung' => ['label' => 'Sedang Disewa', 'kelas' => 'bg-blue-50 text-blue-700', 'tab' => 'aktif'],
        'selesai' => ['label' => '✓ Selesai / Dikembalikan', 'kelas' => 'bg-emerald-50 text-emerald-700', 'tab' => 'selesai'],
        'dibatalkan' => ['label' => 'Dibatalkan', 'kelas' => 'bg-red-50 text-red-700', 'tab' => 'batal'],
    ];
    $hitung = fn (string $tab) => $daftarBooking->filter(fn ($b) => ($tampilan[$b->status]['tab'] ?? '') === $tab)->count();
    $tabs = [
        'semua' => ['Semua', $daftarBooking->count()],
        'menunggu' => ['Menunggu Bayar', $hitung('menunggu')],
        'aktif' => ['Aktif', $hitung('aktif')],
        'selesai' => ['Selesai', $hitung('selesai')],
        'batal' => ['Dibatalkan', $hitung('batal')],
    ];
@endphp

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-10"
     x-data="{
        activeTab: 'semua',
        sedangBayar: null,
        konfirmasiBatal: null,
        pesan: '',
        async bayar(urlBayar, id, ganti = false) {
            if (this.sedangBayar) return;
            this.sedangBayar = id; this.pesan = '';
            try {
                const res = await fetch(urlBayar, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                    body: JSON.stringify({ ganti: ganti })
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.success) {
                    this.pesan = data.message || 'Tagihan tidak dapat dibayar.';
                    if (res.status === 409) setTimeout(() => location.reload(), 1500);
                    this.sedangBayar = null;
                    return;
                }
                if (typeof window.snap === 'undefined') { this.pesan = 'Pop-up pembayaran Midtrans gagal dimuat. Muat ulang halaman.'; this.sedangBayar = null; return; }
                const selesai = async () => {
                    try { await fetch(data.url_sinkron, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } }); } catch (e) {}
                    location.reload();
                };
                window.snap.pay(data.snap_token, { onSuccess: selesai, onPending: selesai, onError: selesai, onClose: selesai });
            } catch (e) {
                this.pesan = 'Tidak dapat terhubung ke server.';
                this.sedangBayar = null;
            }
        },
        async batalkan() {
            if (this.sedangBayar || !this.konfirmasiBatal) return;
            const { url, kode } = this.konfirmasiBatal;
            this.sedangBayar = 'batal-' + kode; this.pesan = '';
            try {
                const res = await fetch(url, { method: 'POST', headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' } });
                const data = await res.json().catch(() => ({}));
                if (!res.ok || !data.success) {
                    this.pesan = data.message || 'Booking tidak dapat dibatalkan.';
                    this.konfirmasiBatal = null;
                    this.sedangBayar = null;
                    return;
                }
                location.reload();
            } catch (e) {
                this.pesan = 'Tidak dapat terhubung ke server.';
                this.sedangBayar = null;
            }
        }
     }">


    <!-- Konfirmasi pembatalan (pengganti pop-up bawaan browser) -->
    <div x-show="konfirmasiBatal" x-cloak style="display: none;" class="fixed inset-0 z-50 flex items-center justify-center px-4" @keydown.escape.window="if (!sedangBayar) konfirmasiBatal = null">
        <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" @click="if (!sedangBayar) konfirmasiBatal = null"></div>
        <div class="relative w-full max-w-sm bg-white rounded-3xl shadow-2xl p-6 space-y-4 text-center">
            <div class="w-14 h-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center mx-auto text-2xl font-black">!</div>
            <h3 class="text-lg font-black text-gray-900">Batalkan pesanan?</h3>
            <p class="text-xs text-gray-500 leading-relaxed">
                Booking <strong class="text-gray-800" x-text="'#' + (konfirmasiBatal?.kode ?? '')"></strong> akan dibatalkan dan alat yang dipesan dikembalikan ke stok. Tindakan ini tidak bisa diurungkan.
            </p>
            <div class="grid grid-cols-2 gap-2">
                <button type="button" @click="konfirmasiBatal = null" :disabled="sedangBayar !== null"
                        class="bg-gray-100 hover:bg-gray-200 disabled:opacity-60 text-gray-700 text-xs font-bold py-3 rounded-2xl transition">Kembali</button>
                <button type="button" @click="batalkan()" :disabled="sedangBayar !== null"
                        class="bg-red-600 hover:bg-red-700 disabled:opacity-60 text-white text-xs font-bold py-3 rounded-2xl transition">
                    <span x-show="sedangBayar === null">Ya, Batalkan</span>
                    <span x-show="sedangBayar !== null" x-cloak>Memproses…</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Header Halaman -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <h1 class="text-2xl sm:text-3xl font-black text-gray-900 tracking-tight">Booking & Status Penyewaan</h1>
            <p class="text-xs sm:text-sm text-gray-500 mt-1">Pantau pembayaran, jadwal pengambilan, dan status perlengkapan outdoor kamu.</p>
        </div>

        <div class="shrink-0">
            <a href="{{ route('katalog') }}" class="bg-brand hover:bg-brand-hover text-white text-xs font-bold px-5 py-3 rounded-2xl transition shadow-lg shadow-brand/20 inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                <span>Sewa Alat Baru</span>
            </a>
        </div>
    </div>

    <p x-show="pesan" x-text="pesan" x-cloak class="mb-4 text-xs text-red-600 bg-red-50 border border-red-200 rounded-xl px-4 py-3"></p>

    <!-- Filter Tab Status -->
    <div class="flex flex-wrap items-center gap-2 border-b border-gray-200 pb-4 mb-8 text-xs font-bold">
        @foreach ($tabs as $kunci => [$nama, $jumlah])
            <button @click="activeTab = '{{ $kunci }}'"
                    :class="activeTab === '{{ $kunci }}' ? 'bg-gray-900 text-white shadow-sm' : 'bg-white text-gray-600 border border-gray-200 hover:border-gray-300'"
                    class="px-4 py-2 rounded-xl transition">
                {{ $nama }} ({{ $jumlah }})
            </button>
        @endforeach
    </div>

    <div class="space-y-4">
        @forelse ($daftarBooking as $booking)
            @php
                $tpl = $tampilan[$booking->status] ?? ['label' => $booking->status, 'kelas' => 'bg-gray-100 text-gray-700', 'tab' => 'semua'];
                $dp = $booking->pembayaran->first();
                $daftarAlat = $booking->detailPemesanan->map(fn ($d) => ($d->alat?->nama ?? 'Alat') . ' (' . $d->jumlah . ')')->implode(', ');
                $lama = $booking->lamaSewa();
                $batas = $booking->created_at->copy()->addMinutes($batasBayarMenit);
            @endphp

            <div x-show="activeTab === 'semua' || activeTab === '{{ $tpl['tab'] }}'"
                 class="bg-white rounded-3xl p-6 sm:p-7 border border-gray-200 shadow-sm hover:shadow-md transition duration-300 {{ $booking->status === 'dibatalkan' ? 'opacity-80' : '' }}">

                <div class="flex flex-col sm:flex-row sm:items-center justify-between pb-4 border-b border-gray-100 gap-2">
                    <div class="flex flex-wrap items-center gap-3">
                        <span class="font-mono text-xs font-bold text-gray-500 bg-gray-50 px-3 py-1 rounded-lg border border-gray-200/60">#{{ $booking->kode_pemesanan }}</span>
                        <span class="px-3 py-1 rounded-full text-[11px] font-bold {{ $tpl['kelas'] }}">{{ $tpl['label'] }}</span>
                    </div>
                    <div class="text-xs text-gray-400 font-medium">Dipesan pada {{ $booking->created_at->translatedFormat('d M Y, H:i') }}</div>
                </div>

                <div class="py-5 flex flex-col md:flex-row md:items-center justify-between gap-6">
                    <div class="space-y-2 flex-1">
                        <h3 class="font-extrabold text-gray-900 text-sm sm:text-base leading-snug">{{ $daftarAlat ?: 'Peralatan' }}</h3>
                        <div class="inline-flex items-center gap-2 text-xs text-gray-600 bg-gray-50 px-3 py-1.5 rounded-xl border border-gray-100">
                            <span>📅</span>
                            <span>Ambil: <strong class="text-gray-900">{{ $booking->tanggal_mulai->translatedFormat('d M Y') }}</strong> &nbsp;—&nbsp; Kembali: <strong class="text-gray-900">{{ $booking->tanggal_selesai->translatedFormat('d M Y') }}</strong> <span class="text-brand font-bold">({{ $lama }} Hari)</span></span>
                        </div>
                    </div>

                    <div class="text-left md:text-right shrink-0 space-y-0.5">
                        <span class="text-[10px] text-gray-400 uppercase font-bold tracking-wider block">Total Biaya</span>
                        <span class="text-lg sm:text-xl font-black text-brand">Rp {{ number_format((float) $booking->total_biaya, 0, ',', '.') }}</span>
                        <span class="block text-[11px] text-gray-500">
                            DP 50%: Rp {{ number_format((float) $booking->jumlah_dp, 0, ',', '.') }}
                            @if ($dp?->status_transaksi === 'berhasil') <span class="text-emerald-600 font-bold">(lunas)</span> @endif
                        </span>
                    </div>
                </div>

                <div class="pt-4 border-t border-gray-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                    @if ($booking->status === 'menunggu_pembayaran')
                        <p class="text-xs text-amber-700 bg-amber-50/80 px-3.5 py-2 rounded-xl border border-amber-200/60">
                            ⏳ Bayar DP sebelum <strong>{{ $batas->translatedFormat('d M, H:i') }}</strong>, kalau lewat booking dibatalkan otomatis.
                        </p>
                        <div class="flex flex-wrap items-center gap-2 shrink-0">
                            <button @click="konfirmasiBatal = { url: '{{ route('pemesanan.batalkan', $booking) }}', kode: '{{ $booking->kode_pemesanan }}' }"
                                    :disabled="sedangBayar !== null"
                                    class="px-4 py-2.5 border border-red-200 text-red-600 hover:bg-red-50 disabled:opacity-60 rounded-xl text-xs font-bold transition">
                                Batalkan Pesanan
                            </button>
                            @if ($dp)
                                @if (filled($dp->metode_pembayaran))
                                    <button @click="bayar('{{ route('pembayaran.bayar', $dp) }}', {{ $dp->id }}, true)"
                                            :disabled="sedangBayar !== null"
                                            class="px-4 py-2.5 border border-gray-200 hover:bg-gray-50 disabled:opacity-60 text-gray-700 rounded-xl text-xs font-bold transition">
                                        Ganti Metode Pembayaran
                                    </button>
                                @endif
                                <button @click="bayar('{{ route('pembayaran.bayar', $dp) }}', {{ $dp->id }})"
                                        :disabled="sedangBayar !== null"
                                        class="px-5 py-2.5 bg-brand hover:bg-brand-hover disabled:opacity-60 text-white rounded-xl text-xs font-bold transition shadow-sm">
                                    <span x-show="sedangBayar !== {{ $dp->id }}">{{ filled($dp->metode_pembayaran) ? 'Lanjutkan Pembayaran' : 'Bayar DP Sekarang' }}</span>
                                    <span x-show="sedangBayar === {{ $dp->id }}" x-cloak>Memuat…</span>
                                </button>
                            @endif
                        </div>
                    @elseif (in_array($booking->status, ['dikonfirmasi', 'berlangsung'], true))
                        <p class="text-xs text-amber-700 bg-amber-50/80 px-3.5 py-2 rounded-xl border border-amber-200/60 flex items-center gap-2">
                            <span>⚠️</span>
                            <span>Bawa <strong>E-KTP asli</strong> saat mengambil alat. Sisa pelunasan <strong>Rp {{ number_format(max(0, (float) $booking->total_biaya - (float) $booking->jumlah_dp), 0, ',', '.') }}</strong> dibayar saat pengambilan.</span>
                        </p>
                    @elseif ($booking->status === 'dibatalkan')
                        <span class="text-xs text-gray-400">Booking dibatalkan{{ $booking->waktu_pembatalan ? ' pada '.$booking->waktu_pembatalan->translatedFormat('d M Y, H:i') : '' }}. Stok sudah dikembalikan.</span>
                        <a href="{{ route('katalog') }}" class="px-4 py-2 border border-gray-200 hover:bg-brand hover:text-white hover:border-brand text-gray-700 rounded-xl text-xs font-bold transition">Sewa Lagi</a>
                    @else
                        <span class="text-xs text-gray-400">Seluruh unit telah dikembalikan.</span>
                        <a href="{{ route('katalog') }}" class="px-4 py-2 border border-gray-200 hover:bg-brand hover:text-white hover:border-brand text-gray-700 rounded-xl text-xs font-bold transition">Sewa Lagi</a>
                    @endif
                </div>
            </div>
        @empty
            <div class="bg-white rounded-3xl p-10 border border-gray-200 text-center space-y-3">
                <p class="text-4xl">🎒</p>
                <p class="text-sm text-gray-500">Kamu belum punya booking. Pilih alat di katalog lalu checkout.</p>
                <a href="{{ route('katalog') }}" class="inline-block bg-brand hover:bg-brand-hover text-white text-xs font-bold px-5 py-3 rounded-2xl transition">Lihat Katalog</a>
            </div>
        @endforelse
    </div>
</div>
@endsection
