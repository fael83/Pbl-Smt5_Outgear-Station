<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4">

    {{-- Total Pemesanan --}}
    <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold text-zinc-500">
                Total Pemesanan
            </p>

            <div class="w-10 h-10 rounded-xl bg-orange-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-orange-500" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 8h6m-6 4h6" />
                </svg>
            </div>
        </div>

        <h3 class="text-2xl font-black text-zinc-900 mt-4">
            {{ number_format($ringkasan['total_pemesanan']) }}
        </h3>

        <p class="text-xs text-zinc-400 mt-1">
            Seluruh data pemesanan
        </p>
    </div>


    {{-- Pemesanan Selesai --}}
    <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold text-zinc-500">
                Pemesanan Selesai
            </p>

            <div class="w-10 h-10 rounded-xl bg-green-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-green-600" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M5 13l4 4L19 7" />
                </svg>
            </div>
        </div>

        <h3 class="text-2xl font-black text-green-600 mt-4">
            {{ number_format($ringkasan['pemesanan_selesai']) }}
        </h3>

        <p class="text-xs text-zinc-400 mt-1">
            Pemesanan berstatus selesai
        </p>
    </div>


    {{-- Menunggu Pembayaran --}}
    <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold text-zinc-500">
                Menunggu Pembayaran
            </p>

            <div class="w-10 h-10 rounded-xl bg-amber-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-amber-500" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
        </div>

        <h3 class="text-2xl font-black text-amber-500 mt-4">
            {{ number_format($ringkasan['menunggu_pembayaran']) }}
        </h3>

        <p class="text-xs text-zinc-400 mt-1">
            Menunggu proses pembayaran
        </p>
    </div>


    {{-- Pembayaran Berhasil --}}
    <div class="bg-white rounded-2xl border border-zinc-200/80 p-5 shadow-sm">
        <div class="flex items-center justify-between">
            <p class="text-xs font-semibold text-zinc-500">
                Pembayaran Berhasil
            </p>

            <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center">
                <svg class="w-5 h-5 text-blue-500" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round"
                        stroke-width="1.8"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8V6m0 2v8m0 0v2m0-2c-1.657 0-3-.895-3-2m6 0c0 1.105-1.343 2-3 2" />
                </svg>
            </div>
        </div>

        <h3 class="text-xl font-black text-zinc-900 mt-4">
            Rp {{ number_format($ringkasan['total_pendapatan'], 0, ',', '.') }}
        </h3>

        <p class="text-xs text-zinc-400 mt-1">
            Akumulasi pembayaran berhasil
        </p>
    </div>

</div>