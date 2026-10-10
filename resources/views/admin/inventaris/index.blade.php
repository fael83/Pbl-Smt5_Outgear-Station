@extends('layouts.panel')

@section('content')
    <!-- Header Page -->
    <div class="flex items-center justify-between">
        <div>
            <h1 class="text-2xl font-bold font-heading text-[#1A2024]">Data Alat & Stok</h1>
            <p class="text-xs text-gray-500 mt-1">Kelola semua inventaris, kategori, tarif sewa, dan kondisi barang.</p>
        </div>
        <button class="px-4 py-2.5 bg-[#D86B43] text-white text-xs font-semibold rounded-xl hover:bg-[#D86B43]/90 transition shadow-sm">
            + Tambah Alat Baru
        </button>
    </div>

    <!-- Filter & Search Bar Section -->
    <div class="bg-white rounded-2xl border border-gray-100 p-4 shadow-sm flex flex-col md:flex-row items-center justify-between gap-4">
        <!-- Filter Kategori -->
        <div class="w-full md:w-1/4">
            <select class="w-full bg-[#F4EFEA]/60 border-none rounded-xl px-4 py-2.5 text-xs text-[#1A2024] font-medium focus:ring-2 focus:ring-[#15AEEA]">
                <option value="">Filter Kategori (Semua)</option>
                <option value="tenda">Tenda & Camping</option>
                <option value="carrier">Carrier & Tas</option>
                <option value="sepatu">Sepatu Hiking</option>
                <option value="masak">Peralatan Masak</option>
            </select>
        </div>

        <!-- Search Bar -->
        <div class="w-full md:w-1/3 relative">
            <input type="text" placeholder="Cari nama alat atau ID..." 
                   class="w-full bg-[#F4EFEA]/60 border-none rounded-xl pl-10 pr-4 py-2.5 text-xs focus:ring-2 focus:ring-[#15AEEA] text-[#1A2024]">
            <span class="absolute left-3.5 top-3 text-gray-400">🔍</span>
        </div>
    </div>

    <!-- Tabel Data Alat & Stok -->
    <div class="bg-white rounded-2xl border border-gray-100 p-5 space-y-4 shadow-sm">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-gray-600">
                <thead class="bg-[#F4EFEA]/60 text-[#1A2024] font-semibold">
                    <tr>
                        <th class="p-3.5 rounded-l-xl w-10 text-center">
                            <input type="checkbox" class="rounded border-gray-300 text-[#1A2024] focus:ring-[#15AEEA]">
                        </th>
                        <th class="p-3.5">ID Alat</th>
                        <th class="p-3.5">Nama Alat</th>
                        <th class="p-3.5">Kategori</th>
                        <th class="p-3.5">Harga Sewa / Hari</th>
                        <th class="p-3.5 text-center">Stok Total</th>
                        <th class="p-3.5 text-center">Kondisi Baik</th>
                        <th class="p-3.5 text-center">Kondisi Rusak/Denda</th>
                        <th class="p-3.5 rounded-r-xl text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    <!-- Baris 1 -->
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="p-3.5 text-center">
                            <input type="checkbox" class="rounded border-gray-300 text-[#1A2024] focus:ring-[#15AEEA]">
                        </td>
                        <td class="p-3.5 font-semibold text-[#1A2024]">#AL-101</td>
                        <td class="p-3.5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-lg overflow-hidden border border-gray-200">
                                ⛺
                            </div>
                            <span class="font-semibold text-[#1A2024]">Tenda Eiger 4P</span>
                        </td>
                        <td class="p-3.5 text-gray-500">Tenda & Camping</td>
                        <td class="p-3.5 font-medium text-[#1A2024]">Rp 150.000</td>
                        <td class="p-3.5 text-center font-bold text-[#1A2024]">30</td>
                        <td class="p-3.5 text-center">
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-700 rounded-lg">28 Unit</span>
                        </td>
                        <td class="p-3.5 text-center">
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-[#D86B43]/15 text-[#D86B43] rounded-lg">2 Unit</span>
                        </td>
                        <td class="p-3.5 text-right space-x-1">
                            <button class="px-3 py-1.5 bg-[#15AEEA]/15 text-[#15AEEA] rounded-lg font-semibold hover:bg-[#15AEEA]/25 transition">Edit</button>
                            <button class="px-3 py-1.5 bg-gray-100 text-[#1A2024] rounded-lg font-semibold hover:bg-gray-200 transition">Detail</button>
                        </td>
                    </tr>

                    <!-- Baris 2 -->
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="p-3.5 text-center">
                            <input type="checkbox" class="rounded border-gray-300 text-[#1A2024] focus:ring-[#15AEEA]">
                        </td>
                        <td class="p-3.5 font-semibold text-[#1A2024]">#AL-102</td>
                        <td class="p-3.5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-lg overflow-hidden border border-gray-200">
                                🎒
                            </div>
                            <span class="font-semibold text-[#1A2024]">Carrier Deuter 60L</span>
                        </td>
                        <td class="p-3.5 text-gray-500">Carrier & Tas</td>
                        <td class="p-3.5 font-medium text-[#1A2024]">Rp 200.000</td>
                        <td class="p-3.5 text-center font-bold text-[#1A2024]">11</td>
                        <td class="p-3.5 text-center">
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-700 rounded-lg">10 Unit</span>
                        </td>
                        <td class="p-3.5 text-center">
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-gray-100 text-gray-500 rounded-lg">0 Unit</span>
                        </td>
                        <td class="p-3.5 text-right space-x-1">
                            <button class="px-3 py-1.5 bg-[#15AEEA]/15 text-[#15AEEA] rounded-lg font-semibold hover:bg-[#15AEEA]/25 transition">Edit</button>
                            <button class="px-3 py-1.5 bg-gray-100 text-[#1A2024] rounded-lg font-semibold hover:bg-gray-200 transition">Detail</button>
                        </td>
                    </tr>

                    <!-- Baris 3 -->
                    <tr class="hover:bg-gray-50/50 transition">
                        <td class="p-3.5 text-center">
                            <input type="checkbox" class="rounded border-gray-300 text-[#1A2024] focus:ring-[#15AEEA]">
                        </td>
                        <td class="p-3.5 font-semibold text-[#1A2024]">#AL-103</td>
                        <td class="p-3.5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-lg overflow-hidden border border-gray-200">
                                🥾
                            </div>
                            <span class="font-semibold text-[#1A2024]">Sepatu Hiking</span>
                        </td>
                        <td class="p-3.5 text-gray-500">Sepatu Hiking</td>
                        <td class="p-3.5 font-medium text-[#1A2024]">Rp 150.000</td>
                        <td class="p-3.5 text-center font-bold text-[#1A2024]">10</td>
                        <td class="p-3.5 text-center">
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-emerald-100 text-emerald-700 rounded-lg">8 Unit</span>
                        </td>
                        <td class="p-3.5 text-center">
                            <span class="px-2.5 py-1 text-[10px] font-bold bg-[#D86B43]/15 text-[#D86B43] rounded-lg">2 Unit</span>
                        </td>
                        <td class="p-3.5 text-right space-x-1">
                            <button class="px-3 py-1.5 bg-[#15AEEA]/15 text-[#15AEEA] rounded-lg font-semibold hover:bg-[#15AEEA]/25 transition">Edit</button>
                            <button class="px-3 py-1.5 bg-gray-100 text-[#1A2024] rounded-lg font-semibold hover:bg-gray-200 transition">Detail</button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination & Rows per page -->
        <div class="flex items-center justify-between pt-4 border-t border-gray-100">
            <p class="text-xs text-gray-500">Menampilkan 1-3 dari total 12 alat</p>
            <div class="flex items-center gap-2">
                <span class="text-xs text-gray-500">Baris per halaman:</span>
                <select class="bg-[#F4EFEA]/60 border-none rounded-lg px-2 py-1 text-xs text-[#1A2024] font-semibold">
                    <option value="10">10</option>
                    <option value="25">25</option>
                    <option value="50">50</option>
                </select>
            </div>
        </div>
    </div>
@endsection