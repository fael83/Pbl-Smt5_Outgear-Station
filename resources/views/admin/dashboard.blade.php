@extends('layouts.panel')

@section('content')
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold font-heading text-[#1A2024]">Dashboard Operasional</h1>
            <p class="text-xs text-gray-500 mt-1">Ringkasan aktivitas transaksi, booking hari ini, dan stok inventaris.</p>
        </div>
        <button class="px-4 py-2.5 bg-[#1A2024] text-white text-xs font-semibold rounded-xl hover:bg-gray-800 transition">
            + Tambah Booking Manual
        </button>
    </div>

    <!-- Stat Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-5">
        <x-ui.stat-card 
            title="Booking Hari Ini" 
            value="12 Transaksi" 
            subvalue="8 Siap Diambil Hari Ini" 
            icon="🗓️" 
            badgeColor="bg-[#1A2024]/10 text-[#1A2024]" />

        <x-ui.stat-card 
            title="Pembayaran Pending" 
            value="5 Transaksi" 
            subvalue="Total: Rp 1.450.000" 
            icon="⏳" 
            badgeColor="bg-[#15AEEA]/15 text-[#15AEEA]" />

        <x-ui.stat-card 
            title="Stok Critical" 
            value="3 Item" 
            subvalue="Perlu Restock Segera" 
            icon="⚠️" 
            badgeColor="bg-[#D86B43]/15 text-[#D86B43]" />

        <x-ui.stat-card 
            title="WA Bot Status" 
            value="● Connected" 
            subvalue="24 Pesan Terjawab Otomatis" 
            icon="💬" 
            badgeColor="bg-emerald-100 text-emerald-600" />
    </div>

    <!-- Section Tabel & Stok -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Tabel Pemesanan -->
        <div class="lg:col-span-2 bg-white rounded-2xl border border-gray-100 p-5 space-y-4 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="font-heading font-semibold text-base text-[#1A2024]">📋 Pemesanan Siap Diproses (Hari Ini)</h2>
                <a href="#" class="text-xs font-semibold text-[#15AEEA] hover:underline">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-gray-600">
                    <thead class="bg-[#F4EFEA]/60 text-[#1A2024] font-semibold rounded-lg">
                        <tr>
                            <th class="p-3 rounded-l-lg">ID Booking</th>
                            <th class="p-3">Customer</th>
                            <th class="p-3">Alat Utama</th>
                            <th class="p-3">Status</th>
                            <th class="p-3 rounded-r-lg text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        <tr>
                            <td class="p-3 font-semibold text-[#1A2024]">#BK-102</td>
                            <td class="p-3">Budi Santoso</td>
                            <td class="p-3">Tenda Eiger 4P</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-700 rounded-md">LUNAS</span>
                            </td>
                            <td class="p-3 text-right">
                                <button class="px-3 py-1.5 bg-[#D86B43] text-white rounded-lg font-semibold hover:bg-[#D86B43]/90">Serah Terima</button>
                            </td>
                        </tr>
                        <tr>
                            <td class="p-3 font-semibold text-[#1A2024]">#BK-103</td>
                            <td class="p-3">Siti Aminah</td>
                            <td class="p-3">Kompor Kovea Spider</td>
                            <td class="p-3">
                                <span class="px-2 py-1 text-[10px] font-bold bg-[#15AEEA]/15 text-[#15AEEA] rounded-md">PENDING</span>
                            </td>
                            <td class="p-3 text-right">
                                <button class="px-3 py-1.5 bg-gray-100 text-[#1A2024] rounded-lg font-semibold hover:bg-gray-200">Cek Bayar</button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Stok Menipis -->
        <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4 shadow-sm">
            <div class="flex items-center justify-between">
                <h2 class="font-heading font-semibold text-base text-[#1A2024]">⚠️ Stok Alat Menipis</h2>
                <span class="text-[10px] font-bold bg-[#D86B43]/15 text-[#D86B43] px-2 py-1 rounded-md">Restock</span>
            </div>

            <div class="space-y-3">
                <div class="flex items-center justify-between p-3 bg-[#F4EFEA]/50 rounded-xl">
                    <div>
                        <p class="font-semibold text-xs text-[#1A2024]">Carrier Deuter 60L</p>
                        <p class="text-[10px] text-gray-500">Sisa stok: 1 unit</p>
                    </div>
                    <button class="px-2.5 py-1 text-[11px] bg-[#1A2024] text-white rounded-lg font-medium">+ Stok</button>
                </div>
                <div class="flex items-center justify-between p-3 bg-[#F4EFEA]/50 rounded-xl">
                    <div>
                        <p class="font-semibold text-xs text-[#1A2024]">Tenda Dome Naturehike</p>
                        <p class="text-[10px] text-[#D86B43] font-bold">Stok Habis (0 unit)</p>
                    </div>
                    <button class="px-2.5 py-1 text-[11px] bg-[#1A2024] text-white rounded-lg font-medium">+ Stok</button>
                </div>
            </div>
        </div>
    </div>
@endsection