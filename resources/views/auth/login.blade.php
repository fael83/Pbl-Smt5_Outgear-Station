<x-guest-layout>
    <style>
        .login-page {
            --cream: #F6F1EB;
            --sand: #E5D5C3;
            --orange: #CB7950;
            --orange-dark: #AD603D;
            --ink: #293A38;
            --muted: #82776D;
        }

        .login-card {
            animation: pageEnter .45s ease-out both;
        }

        @keyframes pageEnter {
            from {
                opacity: 0;
                transform: translateY(8px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .login-field {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #E8D9CC;
            border-radius: 11px;
            background: #FAF8F5;
            padding: 12px 14px;
            font-size: 14px;
            color: var(--ink);
            transition: border-color .2s, box-shadow .2s;
        }

        .login-field:focus {
            outline: none;
            border-color: var(--orange);
            background: white;
            box-shadow: 0 0 0 3px rgb(203 121 80 / 13%);
        }

        .login-button {
            background: var(--orange);
            transition: background .2s, transform .2s;
        }

        .login-button:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        @media (prefers-reduced-motion: reduce) {
            .login-card {
                animation: none;
            }

            .login-field,
            .login-button {
                transition: none;
            }
        }
    </style>

    <div class="login-page min-h-screen flex items-center justify-center px-4 py-6 sm:px-6"
        style="background: var(--cream);">

        <main class="login-card w-full max-w-[1000px] overflow-hidden rounded-2xl border border-[#E9DCCF] bg-white shadow-[0_15px_45px_rgba(72,53,37,0.08)]">

            <div class="grid grid-cols-1 md:grid-cols-[1fr_0.9fr]">

                <!-- PANEL LOGIN -->
                <section class="min-w-0 bg-white px-6 py-8 sm:px-10 sm:py-10 lg:px-12">

                    <!-- Judul -->
                    <div class="mb-7">
                        <p class="mb-2 text-xs font-bold tracking-[2.5px] text-[#CB7950]">
                            WELCOME BACK
                        </p>

                        <h1 class="text-3xl font-bold tracking-tight text-[#293A38]">
                            Masuk ke Akun
                        </h1>

                        <p class="mt-2 text-sm leading-6 text-[#82776D]">
                            Masuk untuk melanjutkan petualanganmu bersama OutGear Station.
                        </p>
                    </div>

                    <!-- Status sesi -->
                    <x-auth-session-status
                        class="mb-4 text-sm text-green-700"
                        :status="session('status')" />

                    <!-- FORM LOGIN: logika backend dipertahankan -->
                    <form method="POST" action="{{ route('login') }}" class="space-y-5">
                        @csrf

                        <!-- Email -->
                        <div>
                            <label for="email"
                                class="mb-2 block text-sm font-semibold text-[#293A38]">
                                Email
                            </label>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="Masukkan email kamu"
                                class="login-field @error('email') !border-red-400 @enderror">

                            <x-input-error
                                :messages="$errors->get('email')"
                                class="mt-2 text-xs text-red-600" />
                        </div>

                        <!-- Password -->
                        <div>
                            <div class="mb-2 flex items-center justify-between gap-3">
                                <label for="password"
                                    class="text-sm font-semibold text-[#293A38]">
                                    Kata Sandi
                                </label>
                            </div>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="current-password"
                                placeholder="Masukkan kata sandi"
                                class="login-field @error('password') !border-red-400 @enderror">

                            <x-input-error
                                :messages="$errors->get('password')"
                                class="mt-2 text-xs text-red-600" />
                        </div>

                        <!-- Ingat sesi -->
                        <div class="flex items-center gap-2.5">
                            <input
                                id="remember_me"
                                type="checkbox"
                                name="remember"
                                value="1"
                                {{ old('remember') ? 'checked' : '' }}
                                class="h-4 w-4 rounded border-[#DCC9B8] text-[#CB7950] focus:ring-[#CB7950]">

                            <label for="remember_me"
                                class="cursor-pointer text-sm text-[#82776D]">
                                Ingat sesi di perangkat ini
                            </label>
                        </div>

                        <!-- Tombol masuk -->
                        <button
                            type="submit"
                            class="login-button flex w-full items-center justify-center gap-3 rounded-xl px-5 py-3.5 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-[#CB7950] focus:ring-offset-2">
                            <span>Masuk ke Akun</span>
                        </button>
                    </form>
                </section>

               
                <!-- PANEL PROMOSI: LOGO TETAP ADA -->
                <aside class="flex min-w-0 items-center justify-center border-t border-[#E9DCCF] px-6 py-10 sm:px-9 md:border-l md:border-t-0 lg:px-10"
                    style="background: var(--sand);">

                    <div class="mx-auto w-full max-w-[340px] text-center">

                        <!-- LOGO OUTGEAR STATION -->
                        <div class="flex justify-center -mb-10">
                            <img
                                src="{{ asset('images/logo.png') }}"
                                alt="Logo OutGear Station"
                                class="h-45 w-auto object-contain">
                        </div>

                        <p class="text-xs font-bold tracking-[2.5px] text-[#AD603D]">
                            YOUR OUTDOOR JOURNEY
                        </p>

                        <h2 class="mt-4 text-2xl font-bold leading-snug tracking-tight text-[#293A38] sm:text-[28px]">
                            Perlengkapan tepat untuk petualangan hebat.
                        </h2>

                        <p class="mx-auto mt-4 max-w-[300px] text-sm leading-6 text-[#786B60]">
                            Temukan dan sewa perlengkapan outdoor sesuai kebutuhan petualanganmu.
                        </p>

                        <a href="{{ route('register') }}"
                            class="mt-6 inline-flex items-center justify-center gap-3 rounded-full border border-[#CBA98D] bg-[#F8F2EB] px-6 py-3 text-sm font-bold text-[#8C5638] transition hover:-translate-y-0.5 hover:border-[#CB7950] hover:bg-white">
                            <span>Daftar Sekarang</span>
                        </a>
                    </div>
                </aside>
            </div>
            <!-- PENUTUP GRID PANEL -->

            <!-- FOOTER: DI BAWAH KEDUA PANEL -->
            <footer class="w-full border-t border-[#E9DCCF] bg-white px-4 py-4">
                <div class="flex w-full flex-col items-center justify-center text-center">
                    <p class="w-full text-center text-xs leading-5 text-[#82776D]">
                        Sistem Informasi Manajemen Penyewaan &amp; Inventaris Outdoor
                    </p>

                    <p class="mt-1 w-full text-center text-xs leading-5 text-[#A1958A]">
                        &copy; {{ date('Y') }} OutGear Station. All rights reserved.
                    </p>
                </div>
            </footer>
        </main>
    </div>
</x-guest-layout>
