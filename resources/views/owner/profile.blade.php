
@extends('layouts.owner')

@section('content')
<div class="max-w-5xl mx-auto space-y-6">

    {{-- Header --}}
    <div>
        <h1 class="text-2xl font-bold text-gray-900">Profil Owner</h1>
        <p class="text-sm text-gray-500 mt-1">
            Lihat dan kelola informasi akun Owner OutGear Station.
        </p>
    </div>

    {{-- Notifikasi sukses --}}
    @if (session('success'))
        <div class="p-4 rounded-xl bg-green-50 border border-green-200 text-green-700 text-sm">
            {{ session('success') }}
        </div>
    @endif

    {{-- Informasi Profil --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-bold text-gray-900">Informasi Pribadi</h2>
            <p class="text-sm text-gray-500 mt-1">
                Informasi identitas akun Owner.
            </p>
        </div>

        <div class="p-6">

            {{-- Foto / Inisial Profil --}}
            <div class="flex items-center gap-4 mb-8">
                <div class="w-16 h-16 rounded-full bg-[#293A38] text-white
                    flex items-center justify-center text-2xl font-bold">
                    {{ strtoupper(substr($user->name, 0, 1)) }}
                </div>

                <div>
                    <h3 class="font-bold text-gray-900 text-lg">
                        {{ $user->name }}
                    </h3>
                    <p class="text-sm text-gray-500">Owner OutGear Station</p>
                    <span class="inline-flex mt-2 px-2.5 py-1 rounded-full
                        bg-green-50 text-green-700 text-xs font-semibold">
                        {{ $user->status_akun === 'aktif' ? 'Akun Aktif' : 'Akun Nonaktif' }}
                    </span>
                </div>
            </div>

            {{-- Form Informasi Profil --}}
            <form action="{{ route('owner.profil.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                    {{-- Nama --}}
                    <div>
                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nama Lengkap
                        </label>
                        <input
                            type="text"
                            id="name"
                            name="name"
                            value="{{ old('name', $user->name) }}"
                            required
                            class="w-full rounded-xl border border-gray-200 px-4 py-3
                            text-sm focus:ring-2 focus:ring-[#CB7950] focus:border-[#CB7950]
                            outline-none"
                        >
                        @error('name')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Email --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Email
                        </label>
                        <input
                            type="email"
                            value="{{ $user->email }}"
                            readonly
                            class="w-full rounded-xl border border-gray-200 px-4 py-3
                            text-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                        >
                        <p class="text-xs text-gray-400 mt-1">
                            Email tidak dapat diubah melalui halaman ini.
                        </p>
                    </div>

                    {{-- Nomor Telepon --}}
                    <div>
                        <label for="no_telepon" class="block text-sm font-semibold text-gray-700 mb-2">
                            Nomor Telepon
                        </label>
                        <input
                            type="text"
                            id="no_telepon"
                            name="no_telepon"
                            value="{{ old('no_telepon', $user->no_telepon) }}"
                            required
                            class="w-full rounded-xl border border-gray-200 px-4 py-3
                            text-sm focus:ring-2 focus:ring-[#CB7950] focus:border-[#CB7950]
                            outline-none"
                        >
                        @error('no_telepon')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Role --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">
                            Role
                        </label>
                        <input
                            type="text"
                            value="Owner"
                            readonly
                            class="w-full rounded-xl border border-gray-200 px-4 py-3
                            text-sm bg-gray-50 text-gray-500 cursor-not-allowed"
                        >
                    </div>

                </div>

                <div class="flex justify-end mt-7 pt-5 border-t border-gray-100">
                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#293A38] hover:bg-[#1F2D2B]
                        text-white text-sm font-semibold transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Keamanan Akun --}}
    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">

        <div class="px-6 py-5 border-b border-gray-100">
            <h2 class="font-bold text-gray-900">Keamanan Akun</h2>
            <p class="text-sm text-gray-500 mt-1">
                Perbarui kata sandi untuk menjaga keamanan akun Owner.
            </p>
        </div>

        <div class="p-6">
            <form action="{{ route('owner.profil.password') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="space-y-5 max-w-2xl">

                    {{-- Password Lama --}}
                    <div>
                        <label for="current_password"
                            class="block text-sm font-semibold text-gray-700 mb-2">
                            Kata Sandi Lama
                        </label>
                        <input
                            type="password"
                            id="current_password"
                            name="current_password"
                            required
                            autocomplete="current-password"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3
                            text-sm focus:ring-2 focus:ring-[#CB7950] focus:border-[#CB7950]
                            outline-none"
                        >
                        @error('current_password')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div>
                        <label for="password"
                            class="block text-sm font-semibold text-gray-700 mb-2">
                            Kata Sandi Baru
                        </label>
                        <input
                            type="password"
                            id="password"
                            name="password"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3
                            text-sm focus:ring-2 focus:ring-[#CB7950] focus:border-[#CB7950]
                            outline-none"
                        >
                        @error('password')
                            <p class="text-red-600 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password --}}
                    <div>
                        <label for="password_confirmation"
                            class="block text-sm font-semibold text-gray-700 mb-2">
                            Konfirmasi Kata Sandi Baru
                        </label>
                        <input
                            type="password"
                            id="password_confirmation"
                            name="password_confirmation"
                            required
                            autocomplete="new-password"
                            class="w-full rounded-xl border border-gray-200 px-4 py-3
                            text-sm focus:ring-2 focus:ring-[#CB7950] focus:border-[#CB7950]
                            outline-none"
                        >
                    </div>

                </div>

                <div class="flex justify-end mt-7 pt-5 border-t border-gray-100">
                    <button
                        type="submit"
                        class="px-5 py-2.5 rounded-xl bg-[#293A38] hover:bg-[#1F2D2B]
                        text-white text-sm font-semibold transition-colors">
                        Ubah Kata Sandi
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
