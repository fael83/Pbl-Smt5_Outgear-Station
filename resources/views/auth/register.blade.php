<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        
      <!-- Logo Area -->
        <div class="mt-16 mb-6 flex flex-col items-center justify-center text-center">
            <!-- (Kode logo dan judul tetap sama di dalam sini) -->
            <a href="/">
                <div class="inline-flex items-center justify-center space-x-3 text-gray-900">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13h-13L12 6.5z"/>
                        <path d="M12 10.5l-3 6h6l-3-6z"/>
                    </svg>
                    <h1 class="text-2xl font-bold tracking-wider">OUTGEAR STATION</h1>
                </div>
            </a>
            <p class="mt-2 text-sm text-gray-500">Pendaftaran Akun Penyewa Baru</p>
        </div>

        <!-- Kartu Register -->
        <div class="w-full sm:max-w-md mt-4 px-8 py-10 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
            
            <form method="POST" action="{{ route('register') }}">
                @csrf

                <!-- Header Form -->
                <div class="mb-6 pb-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Buat Akun Anda</h2>
                    <p class="text-xs text-gray-500 mt-1">Mohon isi data dengan valid untuk keperluan jaminan sewa.</p>
                </div>

                <!-- Nama Lengkap -->
                <div>
                    <label for="name" class="block font-medium text-sm text-gray-700">Nama Lengkap</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="name" class="pl-10 block w-full @error('name') border-red-500 ring-red-500 @else border-gray-300 focus:border-orange-500 focus:ring-orange-500 @enderror rounded-md sm:text-sm bg-gray-50 py-2.5 transition-colors" type="text" name="name" value="{{ old('name') }}" required autofocus autocomplete="name" placeholder="Masukkan nama asli (Sesuai KTP)" />
                    </div>
                    <x-input-error :messages="$errors->get('name')" class="mt-2 text-red-600 text-xs" />
                </div>

                <!-- Alamat Email -->
                <div class="mt-5">
                    <label for="email" class="block font-medium text-sm text-gray-700">Alamat Email</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <input id="email" class="pl-10 block w-full @error('email') border-red-500 ring-red-500 @else border-gray-300 focus:border-orange-500 focus:ring-orange-500 @enderror rounded-md sm:text-sm bg-gray-50 py-2.5 transition-colors" type="email" name="email" value="{{ old('email') }}" required autocomplete="username" placeholder="Gunakan email yang masih aktif" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-xs" />
                </div>

                <!-- Nomor Telepon -->
                <div class="mt-5">
                    <label for="no_telepon" class="block font-medium text-sm text-gray-700">Nomor Telepon / WhatsApp</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                            </svg>
                        </div>
                        <input id="no_telepon" class="pl-10 block w-full @error('no_telepon') border-red-500 ring-red-500 @else border-gray-300 focus:border-orange-500 focus:ring-orange-500 @enderror rounded-md sm:text-sm bg-gray-50 py-2.5 transition-colors" type="text" name="no_telepon" value="{{ old('no_telepon') }}" required placeholder="Wajib nomor yang terhubung WhatsApp" />
                    </div>
                    <p class="text-[11px] text-gray-400 mt-1">*Nomor akan digunakan untuk konfirmasi pengambilan alat.</p>
                    <x-input-error :messages="$errors->get('no_telepon')" class="mt-1 text-red-600 text-xs" />
                </div>

                <!-- Password -->
                <div class="mt-5">
                    <label for="password" class="block font-medium text-sm text-gray-700">Kata Sandi</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password" class="pl-10 block w-full @error('password') border-red-500 ring-red-500 @else border-gray-300 focus:border-orange-500 focus:ring-orange-500 @enderror rounded-md sm:text-sm bg-gray-50 py-2.5 transition-colors" type="password" name="password" required autocomplete="new-password" placeholder="Wajib lebih dari 8 karakter" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-xs" />
                </div>

                <!-- Confirm Password -->
                <div class="mt-5">
                    <label for="password_confirmation" class="block font-medium text-sm text-gray-700">Konfirmasi Kata Sandi</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M2 5a2 2 0 012-2h12a2 2 0 012 2v10a2 2 0 01-2 2H4a2 2 0 01-2-2V5zm3.293 1.293a1 1 0 011.414 0l3 3a1 1 0 010 1.414l-3 3a1 1 0 01-1.414-1.414L7.586 10 5.293 7.707a1 1 0 010-1.414zM11 12a1 1 0 100 2h3a1 1 0 100-2h-3z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password_confirmation" class="pl-10 block w-full @error('password_confirmation') border-red-500 ring-red-500 @else border-gray-300 focus:border-orange-500 focus:ring-orange-500 @enderror rounded-md sm:text-sm bg-gray-50 py-2.5 transition-colors" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Ketik ulang kata sandi dengan benar" />
                    </div>
                    <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2 text-red-600 text-xs" />
                </div>

                <!-- Tombol Register -->
                <div class="mt-8">
                    <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors">
                        Daftar Akun Sekarang
                    </button>
                </div>
            </form>
            
            <!-- Link ke Login -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-600">
                    Sudah punya akun? 
                    <a href="{{ route('login') }}" class="font-bold text-orange-600 hover:text-orange-800 transition-colors">
                        Masuk di sini
                    </a>
                </p>
            </div>
            
        </div>
        
        <!-- Footer Info -->
        <div class="mt-8 mb-8 text-center text-xs text-gray-400">
            <p>Sistem Informasi Manajemen Penyewaan & Inventaris Outdoor</p>
            <p class="mt-1">&copy; 2026 Outgear Station. All rights reserved.</p>
        </div>
    </div>
</x-guest-layout>