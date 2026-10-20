
@extends('layouts.customer')

@section('title', 'Booking Saya - OutGear Station')

@section('content')
<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">

    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-7">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                Booking Saya
            </h1>
            <p class="text-sm text-gray-500 mt-1">
                Pantau jadwal dan status penyewaan alatmu di sini.
            </p>
        </div>

        <!-- Tombol Sewa Alat -->
        <a href="/katalog"
           class="inline-flex items-center justify-center gap-2 px-4 py-2.5
                  bg-brand hover:bg-brand-hover text-white rounded-lg
                  text-sm font-semibold transition self-start sm:self-auto">
            <svg class="w-4 h-4" fill="none" stroke="currentColor"
                 viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round"
                      stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Sewa Alat
        </a>
    </div>

    <!-- Filter Status -->
    <div class="flex gap-2 overflow-x-auto pb-2 mb-5">

        <button type="button"
                class="shrink-0 px-4 py-2 rounded-lg bg-brand
                       text-white text-sm font-semibold">
            Semua (2)
        </button>

        <button type="button"
                class="shrink-0 px-4 py-2 rounded-lg bg-white border
                       border-gray-200 text-gray-600 text-sm
                       hover:border-brand transition">
            Menunggu Pembayaran
        </button>

        <button type="button"
                class="shrink-0 px-4 py-2 rounded-lg bg-white border
                       border-gray-200 text-gray-600 text-sm
                       hover:border-brand transition">
            Sedang Disewa
        </button>

        <button type="button"
                class="shrink-0 px-4 py-2 rounded-lg bg-white border
                       border-gray-200 text-gray-600 text-sm
                       hover:border-brand transition">
            Selesai
        </button>

    </div>

    <!-- Daftar Booking -->
    <div class="space-y-4">

        <!-- Booking 1 -->
        <article class="bg-white border border-gray-200 rounded-xl overflow-hidden">

            <!-- Booking Header -->
            <div class="px-5 py-4 sm:px-6 flex flex-col sm:flex-row
                        sm:items-center sm:justify-between gap-3
                        border-b border-gray-100">

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-semibold text-gray-800">
                        #BK-20261013
                    </span>

                    <span class="inline-flex items-center gap-1.5
                                 px-2.5 py-1 rounded-md bg-blue-50
                                 text-blue-700 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span>
                        Sedang Disewa
                    </span>
                </div>

                <p class="text-xs text-gray-500">
                    Booking pada 10 Okt 2026
                </p>
            </div>

            <!-- Booking Details -->
            <div class="px-5 py-5 sm:px-6">

                <div class="flex flex-col md:flex-row
                            md:items-start md:justify-between gap-5">

                    <div class="flex-1">
                        <h2 class="font-bold text-gray-900">
                            Tenda Consina Magnum 4 + Carrier 60L
                        </h2>

                        <div class="flex flex-wrap items-center gap-x-5
                                    gap-y-2 mt-3 text-sm text-gray-500">

                            <span class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand"
                                     fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="16"
                                          rx="2" stroke-width="1.7"/>
                                    <path d="M16 3v4M8 3v4M3 10h18"
                                          stroke-width="1.7"
                                          stroke-linecap="round"/>
                                </svg>
                                14 Okt – 17 Okt 2026
                            </span>

                            <span>3 hari</span>
                        </div>
                    </div>

                    <div class="md:text-right md:min-w-36">
                        <p class="text-xs text-gray-500">Total biaya</p>
                        <p class="text-xl font-bold text-brand mt-1">
                            Rp185.000
                        </p>
                    </div>

                </div>

                <!-- Booking Actions -->
                <div class="mt-5 pt-4 border-t border-gray-100
                            flex flex-col sm:flex-row sm:items-center
                            sm:justify-between gap-3">

                    <p class="text-xs text-gray-500">
                        <span class="text-brand font-semibold">Perhatian:</span>
                        Bawa E-KTP saat pengambilan alat.
                    </p>

                    <div class="flex flex-wrap gap-2">

                        <button type="button"
                                onclick="alert('Fitur invoice belum dihubungkan.')"
                                class="px-3.5 py-2 rounded-lg border
                                       border-gray-200 text-gray-700
                                       text-xs font-semibold
                                       hover:bg-gray-50 transition">
                            Lihat Invoice
                        </button>

                        <button type="button"
                                onclick="alert('Silakan hubungi admin untuk bantuan.')"
                                class="px-3.5 py-2 rounded-lg bg-brand
                                       text-white text-xs font-semibold
                                       hover:bg-brand-hover transition">
                            Bantuan
                        </button>

                    </div>
                </div>

            </div>
        </article>

        <!-- Booking 2 -->
        <article class="bg-white border border-gray-200 rounded-xl overflow-hidden">

            <!-- Booking Header -->
            <div class="px-5 py-4 sm:px-6 flex flex-col sm:flex-row
                        sm:items-center sm:justify-between gap-3
                        border-b border-gray-100">

                <div class="flex flex-wrap items-center gap-2">
                    <span class="text-sm font-semibold text-gray-800">
                        #BK-20260901
                    </span>

                    <span class="inline-flex items-center gap-1.5
                                 px-2.5 py-1 rounded-md bg-green-50
                                 text-green-700 text-xs font-semibold">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-600"></span>
                        Selesai
                    </span>
                </div>

                <p class="text-xs text-gray-500">
                    Booking pada 1 Sep 2026
                </p>
            </div>

            <!-- Booking Details -->
            <div class="px-5 py-5 sm:px-6">

                <div class="flex flex-col md:flex-row
                            md:items-start md:justify-between gap-5">

                    <div class="flex-1">
                        <h2 class="font-bold text-gray-900">
                            Kompor Portable + Nesting Cooking Set
                        </h2>

                        <div class="flex flex-wrap items-center gap-x-5
                                    gap-y-2 mt-3 text-sm text-gray-500">

                            <span class="inline-flex items-center gap-2">
                                <svg class="w-4 h-4 text-brand"
                                     fill="none" stroke="currentColor"
                                     viewBox="0 0 24 24">
                                    <rect x="3" y="5" width="18" height="16"
                                          rx="2" stroke-width="1.7"/>
                                    <path d="M16 3v4M8 3v4M3 10h18"
                                          stroke-width="1.7"
                                          stroke-linecap="round"/>
                                </svg>
                                2 Sep – 4 Sep 2026
                            </span>

                            <span>2 hari</span>
                        </div>
                    </div>

                    <div class="md:text-right md:min-w-36">
                        <p class="text-xs text-gray-500">Total biaya</p>
                        <p class="text-xl font-bold text-brand mt-1">
                            Rp75.000
                        </p>
                    </div>

                </div>

                <!-- Booking Actions -->
                <div class="mt-5 pt-4 border-t border-gray-100
                            flex flex-col sm:flex-row sm:items-center
                            sm:justify-between gap-3">

                    <p class="text-xs text-gray-500">
                        Penyewaan selesai.
                    </p>

                    <a href="/katalog"
                       class="inline-flex items-center justify-center
                              px-3.5 py-2 rounded-lg border border-gray-200
                              text-gray-700 text-xs font-semibold
                              hover:bg-gray-50 transition self-start sm:self-auto">
                        Sewa Lagi
                    </a>

                </div>

            </div>
        </article>

    </div>
</div>
@endsection
