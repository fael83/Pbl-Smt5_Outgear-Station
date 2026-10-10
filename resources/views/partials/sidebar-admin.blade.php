<aside class="w-64 bg-[#1A2024] text-white flex flex-col shrink-0">
    <!-- Brand Header -->
    <div class="p-6 border-b border-gray-800 flex items-center gap-3">
        <div class="w-9 h-9 bg-[#D86B43] rounded-xl flex items-center justify-center text-white font-bold font-heading">
            OS
        </div>
        <div>
            <h1 class="font-heading font-bold text-base tracking-wide text-white">OUTGEAR</h1>
            <p class="text-[10px] text-gray-400 tracking-wider uppercase">Management System</p>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 p-4 space-y-6 text-sm overflow-y-auto">
        <div>
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Operasional & POS</p>
            <ul class="space-y-1">
                <li>
                    <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg bg-[#D86B43] text-white font-medium">
                        <span>📊</span> Dashboard
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition">
                        <span>📦</span> Data Alat & Stok
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition">
                        <span>🏷️</span> Kategori Barang
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Transaksi & Penyewaan</p>
            <ul class="space-y-1">
                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition">
                        <span>👥</span> Data Customer
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition">
                        <span>📋</span> Kelola Booking
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition">
                        <span>💳</span> Verifikasi Pembayaran
                    </a>
                </li>
                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition">
                        <span>⚠️</span> Kelola Denda
                    </a>
                </li>
            </ul>
        </div>

        <div>
            <p class="text-[11px] font-semibold text-gray-400 uppercase tracking-wider mb-2 px-3">Laporan</p>
            <ul class="space-y-1">
                <li>
                    <a href="#" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-gray-300 hover:bg-gray-800 hover:text-white transition">
                        <span>📈</span> Laporan Transaksi
                    </a>
                </li>
            </ul>
        </div>
    </nav>
</aside>