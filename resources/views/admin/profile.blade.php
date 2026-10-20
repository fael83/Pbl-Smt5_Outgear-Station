@extends('layouts.panel')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    <!-- 1. Header Profil (Warna Putih Bersih & Terang) -->
    <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col sm:flex-row items-center gap-6">
        <!-- Avatar -->
        <div class="w-24 h-24 bg-orange-50 border border-orange-100 rounded-full flex items-center justify-center text-orange-600 text-4xl font-bold shadow-sm flex-shrink-0">
            AD
        </div>
        
        <!-- Info Singkat -->
        <div class="text-center sm:text-left flex-1">
            <h2 class="text-2xl font-bold text-gray-900 font-heading">Admin Toko</h2>
            <p class="text-gray-500 mt-1 font-medium">admin@outgear.com</p>
            
            <div class="mt-4 inline-flex items-center gap-2 px-3 py-1.5 bg-green-50 text-green-700 rounded-full text-xs font-bold border border-green-200 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                Staff Aktif
            </div>
        </div>

        <!-- Tombol -->
        <div>
            <button onclick="alert('Hubungi Owner untuk ganti sandi')" class="px-4 py-2 bg-white border border-gray-200 hover:bg-gray-50 text-gray-700 rounded-xl text-sm font-bold shadow-sm transition-all flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                Ubah Sandi
            </button>
        </div>
    </div>

    <!-- 2. Alert Read-Only -->
    <div class="bg-orange-50 border-l-4 border-orange-400 rounded-r-2xl p-4 flex items-start gap-3 shadow-sm">
        <svg class="w-5 h-5 text-orange-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
        <div>
            <h4 class="font-bold text-orange-900 text-sm">Informasi Akun (Read-Only)</h4>
            <p class="text-sm text-orange-800 mt-1">Sesuai kebijakan keamanan sistem, data profil dan hak akses Staff hanya dapat dikelola oleh <strong>Owner</strong>. Silakan ajukan perubahan jika terdapat ketidaksesuaian data.</p>
        </div>
    </div>

    <!-- 3. Layout Grid 2 Kolom -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Kolom Kiri: Detail Identitas (Besar) -->
        <div class="md:col-span-2 bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
            <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                <h3 class="font-bold text-gray-900 font-heading text-lg">Detail Identitas</h3>
            </div>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nama Lengkap</label>
                    <div class="bg-gray-50/50 px-4 py-3 rounded-xl border border-gray-200 text-gray-900 font-medium cursor-not-allowed">
                        Admin Toko
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Nomor Telepon / WA</label>
                    <div class="bg-gray-50/50 px-4 py-3 rounded-xl border border-gray-200 text-gray-900 font-medium cursor-not-allowed">
                        +62 813-7281-9922
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Alamat Email</label>
                    <div class="bg-gray-50/50 px-4 py-3 rounded-xl border border-gray-200 text-gray-900 font-medium cursor-not-allowed">
                        admin@outgear.com
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Tanggal Bergabung</label>
                    <div class="bg-gray-50/50 px-4 py-3 rounded-xl border border-gray-200 text-gray-900 font-medium cursor-not-allowed">
                        12 Maret 2024
                    </div>
                </div>
            </div>
        </div>

        <!-- Kolom Kanan: Otoritas Sistem (Kecil/Sidebar style) -->
        <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex flex-col">
            <div class="flex items-center gap-2 mb-6 border-b border-gray-100 pb-4">
                <svg class="w-5 h-5 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                <h3 class="font-bold text-gray-900 font-heading text-lg">Otoritas Anda</h3>
            </div>
            
            <div class="flex-1 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 bg-green-100 text-green-600 p-1 rounded-full"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Kelola Transaksi</p>
                        <p class="text-xs text-gray-500">Akses modul Kasir & POS</p>
                    </div>
                </div>
                <div class="flex items-start gap-3">
                    <div class="mt-0.5 bg-green-100 text-green-600 p-1 rounded-full"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg></div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">Verifikasi Booking</p>
                        <p class="text-xs text-gray-500">Cek & proses pesanan</p>
                    </div>
                </div>
                <div class="flex items-start gap-3 opacity-50">
                    <div class="mt-0.5 bg-red-100 text-red-500 p-1 rounded-full"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg></div>
                    <div>
                        <p class="text-sm font-bold text-gray-900 line-through">Ubah Master Data</p>
                        <p class="text-xs text-gray-500">Hanya hak Owner</p>
                    </div>
                </div>
            </div>

            <div class="mt-6 pt-4 border-t border-gray-100 text-center">
                <span class="text-xs text-gray-500">Level Akses:</span>
                <span class="text-xs font-bold text-orange-600 bg-orange-50 px-2 py-1 rounded-md ml-1 border border-orange-100">Staff Operasional</span>
            </div>
        </div>
        
    </div>
</div>
@endsection