<x-guest-layout>
    <style>
        .register-page {
            --cream: #F6F1EB;
            --sand: #E5D5C3;
            --orange: #CB7950;
            --orange-dark: #AD603D;
            --ink: #293A38;
            --muted: #82776D;
        }

        .register-card {
            animation: pageEnter .35s ease-out both;
        }

        @keyframes pageEnter {
            from {
                opacity: 0;
                transform: translateX(12px);
            }

            to {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .register-field {
            width: 100%;
            box-sizing: border-box;
            border: 1px solid #E8D9CC;
            border-radius: 11px;
            background: #E8F0FE;
            padding: 8px 12px;
            font-size: 14px;
            color: var(--ink);
            transition: border-color .2s, box-shadow .2s;
        }

        .register-field:focus {
            outline: none;
            border-color: var(--orange);
            background: #E8F0FE;
            box-shadow: 0 0 0 3px rgb(203 121 80 / 13%);
        }

        /* Pertahankan warna biru saat autofill Chrome */
        .register-field:-webkit-autofill,
        .register-field:-webkit-autofill:hover,
        .register-field:-webkit-autofill:focus {
            -webkit-text-fill-color: var(--ink);
            -webkit-box-shadow: 0 0 0 1000px #E8F0FE inset;
            box-shadow: 0 0 0 1000px #E8F0FE inset;
        }

        .register-button {
            background: var(--orange);
            transition: background .2s, transform .2s;
        }

        .register-button:hover {
            background: var(--orange-dark);
            transform: translateY(-1px);
        }

        @media (prefers-reduced-motion: reduce) {
            .register-card {
                animation: none;
            }

            .register-field,
            .register-button {
                transition: none;
            }
        }
    </style>

    <div class="register-page min-h-screen flex items-center justify-center px-4 py-6 sm:px-6"
        style="background: var(--cream);">

        <!-- max-w diubah menjadi 800px agar card keseluruhan lebih ramping -->
        <main class="register-card w-full max-w-[800px] overflow-hidden rounded-2xl border border-[#E9DCCF] bg-white shadow-[0_15px_45px_rgba(72,53,37,0.08)]">

            <div class="grid grid-cols-1 md:grid-cols-[1fr_0.9fr]">

                <!-- PANEL REGISTER -->
                <section class="min-w-0 bg-white px-6 py-5 sm:px-8 sm:py-6 lg:px-9">

                    <div class="mb-4">
                        <p class="mb-1 text-xs font-bold tracking-[2.5px] text-[#CB7950]">
                            JOIN OUTGEAR STATION
                        </p>

                        <h1 class="text-3xl font-bold tracking-tight text-[#293A38]">
                            Buat Akun
                        </h1>

                        <p class="mt-1 text-sm leading-5 text-[#82776D]">
                            Daftar untuk mulai menyewa perlengkapan petualanganmu.
                        </p>
                    </div>

                    <!-- FORM REGISTER (space-y-4 diubah jadi space-y-3 agar rapat) -->
                    <form method="POST" action="{{ route('register') }}" class="space-y-3">
                        @csrf

                        <!-- NAMA LENGKAP -->
                        <div>
                            <label for="name" class="mb-1 block text-sm font-semibold text-[#293A38]">
                                Nama Lengkap
                            </label>

                            <input
                                id="name"
                                name="name"
                                type="text"
                                value="{{ old('name') }}"
                                required
                                autofocus
                                autocomplete="name"
                                placeholder="Masukkan nama lengkap"
                                class="register-field @error('name') !border-red-400 @enderror">

                            <x-input-error
                                :messages="$errors->get('name')"
                                class="mt-1 text-xs text-red-600" />
                        </div>

                        <!-- EMAIL -->
                        <div>
                            <label for="email" class="mb-1 block text-sm font-semibold text-[#293A38]">
                                Alamat Email
                            </label>

                            <input
                                id="email"
                                name="email"
                                type="email"
                                value="{{ old('email') }}"
                                required
                                autocomplete="username"
                                placeholder="Masukkan email aktif"
                                class="register-field @error('email') !border-red-400 @enderror">

                            <x-input-error
                                :messages="$errors->get('email')"
                                class="mt-1 text-xs text-red-600" />
                        </div>

                        <!-- NOMOR TELEPON -->
                        <div>
                            <label for="no_telepon" class="mb-1 block text-sm font-semibold text-[#293A38]">
                                Nomor Telepon / WhatsApp
                            </label>

                            <input
                                id="no_telepon"
                                name="no_telepon"
                                type="text"
                                value="{{ old('no_telepon') }}"
                                required
                                placeholder="Nomor WhatsApp aktif"
                                class="register-field @error('no_telepon') !border-red-400 @enderror">

                            <p class="mt-1 text-[11px] leading-4 text-[#82776D]">
                                Digunakan untuk konfirmasi pengambilan alat.
                            </p>

                            <x-input-error
                                :messages="$errors->get('no_telepon')"
                                class="mt-1 text-xs text-red-600" />
                        </div>

                        <!-- PASSWORD -->
                        <div>
                            <label for="password" class="mb-1 block text-sm font-semibold text-[#293A38]">
                                Kata Sandi
                            </label>

                            <input
                                id="password"
                                name="password"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Buat kata sandi"
                                class="register-field @error('password') !border-red-400 @enderror">

                            <x-input-error
                                :messages="$errors->get('password')"
                                class="mt-1 text-xs text-red-600" />
                        </div>

                        <!-- KONFIRMASI PASSWORD -->
                        <div>
                            <label for="password_confirmation" class="mb-1 block text-sm font-semibold text-[#293A38]">
                                Konfirmasi Kata Sandi
                            </label>

                            <input
                                id="password_confirmation"
                                name="password_confirmation"
                                type="password"
                                required
                                autocomplete="new-password"
                                placeholder="Ulangi kata sandi"
                                class="register-field @error('password_confirmation') !border-red-400 @enderror">

                            <x-input-error
                                :messages="$errors->get('password_confirmation')"
                                class="mt-1 text-xs text-red-600" />
                        </div>

                        <!-- TOMBOL DAFTAR -->
                        <button
                            type="submit"
                            class="register-button flex w-full items-center justify-center gap-3 rounded-xl px-5 py-2.5 mt-2 text-sm font-bold text-white shadow-sm focus:outline-none focus:ring-2 focus:ring-[#CB7950] focus:ring-offset-2">
                            <span>Daftar Sekarang</span>
                        </button>
                    </form>

                    <!-- LINK LOGIN -->
                    <p class="mt-4 text-center text-sm text-[#82776D]">
                        Sudah punya akun?
                        <a href="{{ route('login') }}"
                            data-auth-transition
                            class="ml-1 font-bold text-[#CB7950] hover:text-[#AD603D]">
                            Masuk di sini
                        </a>
                    </p>
                </section>

                <!-- PANEL PROMOSI -->
                <aside
                    class="flex min-w-0 items-center justify-center border-t border-[#E9DCCF] px-6 py-6 sm:px-8 md:border-l md:border-t-0 lg:px-8"
                    style="background: var(--sand);">

                    <div class="mx-auto w-full max-w-[300px] text-center">

                        <!-- LOGO -->
                        <div class="flex justify-center -mb-10">
                            <img
                                src="{{ asset('images/logo.png') }}"
                                alt="Logo OutGear Station"
                                class="h-45 w-auto object-contain relative z-10">
                        </div>

                        <p class="text-[11px] font-bold tracking-[2px] text-[#AD603D] relative z-20">
                            YOUR OUTDOOR JOURNEY
                        </p>

                        <h2 class="mt-3 text-xl font-bold leading-snug tracking-tight text-[#293A38] sm:text-[22px]">
                            Siap untuk petualangan berikutnya?
                        </h2>

                        <p class="mx-auto mt-2 max-w-[260px] text-xs leading-5 text-[#786B60]">
                            Temukan dan sewa perlengkapan outdoor sesuai kebutuhan petualanganmu.
                        </p>
                    </div>
                </aside>
            </div>

            <!-- FOOTER -->
            <footer class="w-full border-t border-[#E9DCCF] bg-white px-4 py-3">
                <div class="flex w-full flex-col items-center justify-center text-center">
                    <p class="w-full text-center text-[11px] leading-4 text-[#82776D]">
                        Sistem Informasi Manajemen Penyewaan &amp; Inventaris Outdoor
                    </p>

                    <p class="mt-1 w-full text-center text-[11px] leading-4 text-[#A1958A]">
                        &copy; {{ date('Y') }} OutGear Station. All rights reserved.
                    </p>
                </div>
            </footer>
        </main>
    </div>

    <script src="{{ asset('js/auth-transition.js') }}"></script>
    <script src="{{ asset('js/register-validation.js') }}"></script>
</x-guest-layout>