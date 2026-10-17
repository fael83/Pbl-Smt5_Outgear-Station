<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
        
        <!-- Logo Area -->
        <div class="mt-16 mb-6 flex flex-col items-center justify-center text-center">
            <a href="/">
                <div class="inline-flex items-center justify-center space-x-3 text-gray-900">
                    <svg class="w-10 h-10" fill="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 22h20L12 2zm0 4.5l6.5 13h-13L12 6.5z"/>
                        <path d="M12 10.5l-3 6h6l-3-6z"/>
                    </svg>
                    <h1 class="text-2xl font-bold tracking-wider">OUTGEAR STATION</h1>
                </div>
            </a>
            <p class="mt-2 text-sm text-gray-500">Otentikasi Pengguna</p>
        </div>

        <!-- Kartu Login -->
        <div class="w-full sm:max-w-md mt-4 px-8 py-10 bg-white shadow-xl overflow-hidden sm:rounded-2xl border border-gray-100">
            
            <!-- Session Status (Pesan sukses jika ada) -->
            <x-auth-session-status class="mb-4" :status="session('status')" />

            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Header Form -->
                <div class="mb-6 pb-4 border-b border-gray-100">
                    <h2 class="text-lg font-semibold text-gray-900">Masuk ke Akun</h2>
                    <p class="text-xs text-gray-500 mt-1">Masukkan kredensial Anda untuk mengakses sistem.</p>
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="block font-medium text-sm text-gray-700">Email</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M2.003 5.884L10 9.882l7.997-3.998A2 2 0 0016 4H4a2 2 0 00-1.997 1.884z" />
                                <path d="M18 8.118l-8 4-8-4V14a2 2 0 002 2h12a2 2 0 002-2V8.118z" />
                            </svg>
                        </div>
                        <input id="email" class="pl-10 block w-full @error('email') border-red-500 ring-red-500 @else border-gray-300 focus:border-orange-500 focus:ring-orange-500 @enderror rounded-md sm:text-sm bg-gray-50 py-2.5 transition-colors" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="Masukkan email Anda" />
                    </div>
                    <x-input-error :messages="$errors->get('email')" class="mt-2 text-red-600 text-xs" />
                </div>

                <!-- Password -->
                <div class="mt-5">
                    <div class="flex justify-between items-center">
                        <label for="password" class="block font-medium text-sm text-gray-700">Kata Sandi</label>
                        @if (Route::has('password.request'))
                            <a class="text-xs font-medium text-orange-600 hover:text-orange-800 transition-colors" href="{{ route('password.request') }}">
                                Lupa Kata Sandi?
                            </a>
                        @endif
                    </div>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-5 w-5 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <input id="password" class="pl-10 block w-full @error('password') border-red-500 ring-red-500 @else border-gray-300 focus:border-orange-500 focus:ring-orange-500 @enderror rounded-md sm:text-sm bg-gray-50 py-2.5 transition-colors" type="password" name="password" required autocomplete="current-password" placeholder="••••••••" />
                    </div>
                    <x-input-error :messages="$errors->get('password')" class="mt-2 text-red-600 text-xs" />
                </div>

                <!-- Remember Me -->
                <div class="mt-5 border border-gray-100 p-3 rounded-lg bg-gray-50/50">
                    <label for="remember_me" class="inline-flex items-center cursor-pointer">
                        <input id="remember_me" type="checkbox" class="rounded border-gray-300 text-orange-600 shadow-sm focus:ring-orange-500" name="remember">
                        <span class="ml-2 text-sm text-gray-600">Ingat sesi di perangkat ini</span>
                    </label>
                </div>

                <!-- Tombol Login -->
                <div class="mt-8">
                    <button type="submit" class="w-full flex justify-center items-center space-x-2 py-3 px-4 border border-transparent rounded-lg shadow-sm text-sm font-bold text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-900 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                        <span>Masuk ke Akun</span>
                    </button>
                </div>
            </form>
            
            <!-- Link ke Register -->
            <div class="mt-8 pt-6 border-t border-gray-100 text-center">
                <p class="text-sm text-gray-600">
                    Belum punya akun penyewa? 
                    <a href="{{ route('register') }}" class="font-bold text-orange-600 hover:text-orange-800 transition-colors">
                        Daftar sekarang
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