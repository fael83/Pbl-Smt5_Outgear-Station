@extends('layouts.customer')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
    
    <!-- Header Halaman -->
    <div class="text-center max-w-2xl mx-auto mb-16">
        <h1 class="text-3xl font-bold text-gray-900 mb-4">Panduan Penyewaan Alat</h1>
        <p class="text-gray-600">Ikuti langkah-langkah mudah di bawah ini untuk menyewa perlengkapan outdoor di Outgear Station. Pastikan Anda membaca syarat dan ketentuan sebelum melakukan pemesanan.</p>
    </div>

    <!-- Langkah-langkah (Step-by-step Grid) -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-16">
        <!-- Langkah 1 -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm relative overflow-hidden transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center font-bold text-xl mb-4">1</div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Pilih Tanggal & Alat</h3>
            <p class="text-sm text-gray-500">Cari alat pendakian yang Anda butuhkan di halaman katalog dan tentukan tanggal pengambilan serta pengembalian.</p>
        </div>

        <!-- Langkah 2 -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center font-bold text-xl mb-4">2</div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Booking & Bayar DP</h3>
            <p class="text-sm text-gray-500">Masukkan ke keranjang, isi data diri, dan lakukan pembayaran Uang Muka (DP) untuk mengamankan stok alat.</p>
        </div>

        <!-- Langkah 3 -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center font-bold text-xl mb-4">3</div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Ambil di Basecamp</h3>
            <p class="text-sm text-gray-500">Datang ke basecamp kami pada tanggal yang dipilih. Bawa KTP asli sebagai jaminan dan lunasi sisa pembayaran.</p>
        </div>

        <!-- Langkah 4 -->
        <div class="bg-white p-6 rounded-2xl border border-gray-100 shadow-sm transition-transform hover:-translate-y-1 hover:shadow-md duration-300">
            <div class="w-12 h-12 bg-orange-100 text-orange-600 rounded-xl flex items-center justify-center font-bold text-xl mb-4">4</div>
            <h3 class="text-lg font-bold text-gray-900 mb-2">Kembalikan Alat</h3>
            <p class="text-sm text-gray-500">Kembalikan alat sesuai tanggal yang disepakati. Denda akan berlaku jika terjadi keterlambatan atau kerusakan.</p>
        </div>
    </div>

    <!-- Syarat dan Ketentuan -->
    <div class="bg-white p-8 rounded-2xl border border-gray-100 shadow-sm max-w-4xl mx-auto">
        <h2 class="text-2xl font-bold text-gray-900 mb-6 flex items-center">
            <svg class="w-6 h-6 text-orange-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
            Syarat & Ketentuan Umum
        </h2>
        
        <div class="space-y-4 text-gray-600 text-sm md:text-base">
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-orange-50 text-orange-600 font-bold text-xs mr-3 mt-0.5">1</div>
                <p>Penyewa wajib meninggalkan <strong>Identitas Asli (E-KTP)</strong> yang masih berlaku sebagai jaminan selama masa sewa.</p>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-orange-50 text-orange-600 font-bold text-xs mr-3 mt-0.5">2</div>
                <p>Alat yang disewa menjadi tanggung jawab penuh penyewa. Kerusakan (sobek, patah, terbakar) atau kehilangan alat akan dikenakan denda sesuai dengan harga perbaikan atau harga beli alat baru.</p>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-orange-50 text-orange-600 font-bold text-xs mr-3 mt-0.5">3</div>
                <p>Hitungan sewa adalah per hari (24 Jam). Keterlambatan pengembalian akan dikenakan denda sebesar harga sewa per hari untuk setiap alat yang terlambat.</p>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-orange-50 text-orange-600 font-bold text-xs mr-3 mt-0.5">4</div>
                <p>Uang muka (DP) yang sudah dibayarkan tidak dapat dikembalikan (hangus) apabila penyewa melakukan pembatalan sepihak pada hari-H pengambilan.</p>
            </div>
            <div class="flex items-start">
                <div class="flex-shrink-0 w-6 h-6 flex items-center justify-center rounded-full bg-orange-50 text-orange-600 font-bold text-xs mr-3 mt-0.5">5</div>
                <p>Tidak perlu mencuci tenda atau alat masak setelah digunakan, cukup pastikan alat dalam keadaan kering dan bebas dari sampah.</p>
            </div>
        </div>
    </div>
</div>
@endsection