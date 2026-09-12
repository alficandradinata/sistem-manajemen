@php
    $inputClass = 'mt-1 mb-4 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base shadow-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none';
@endphp

<x-app-layout title="Akun">
    <a href="{{ route('projects.index') }}" class="mb-3 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Kembali
    </a>

    <h1 class="mb-4 text-xl font-semibold">Akun</h1>

    <form method="POST" action="{{ route('account.profile') }}" class="mb-4 rounded-2xl bg-white p-5 shadow-sm">
        @csrf
        @method('PUT')

        <h2 class="mb-3 text-sm font-semibold text-slate-900">Profil</h2>

        @if ($errors->profile->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ $errors->profile->first() }}
            </div>
        @endif

        <label for="name" class="block text-sm font-medium text-slate-700">Nama</label>
        <input id="name" name="name" type="text" required maxlength="255"
               value="{{ old('name', auth()->user()->name) }}" class="{{ $inputClass }}">

        <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
        <p class="mt-0.5 mb-1 text-xs text-slate-500">Dipakai untuk masuk. Tidak ada email yang dikirim ke alamat ini.</p>
        <input id="email" name="email" type="email" required maxlength="255" inputmode="email"
               value="{{ old('email', auth()->user()->email) }}" class="{{ $inputClass }}">

        <button type="submit"
                class="mt-1 w-full rounded-lg bg-slate-900 px-4 py-3 text-base font-medium text-white hover:bg-slate-800">
            Simpan profil
        </button>
    </form>

    <form method="POST" action="{{ route('account.password') }}" class="rounded-2xl bg-white p-5 shadow-sm">
        @csrf
        @method('PUT')

        <h2 class="mb-3 text-sm font-semibold text-slate-900">Kata Sandi</h2>

        @if ($errors->password->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ $errors->password->first() }}
            </div>
        @endif

        <label for="current_password" class="block text-sm font-medium text-slate-700">Kata sandi saat ini</label>
        <input id="current_password" name="current_password" type="password" required
               autocomplete="current-password" class="{{ $inputClass }}">

        <label for="password" class="block text-sm font-medium text-slate-700">Kata sandi baru</label>
        <input id="password" name="password" type="password" required minlength="8"
               autocomplete="new-password" class="{{ $inputClass }}">

        <label for="password_confirmation" class="block text-sm font-medium text-slate-700">Ulangi kata sandi baru</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
               autocomplete="new-password" class="{{ $inputClass }}">

        <button type="submit"
                class="mt-1 w-full rounded-lg bg-slate-900 px-4 py-3 text-base font-medium text-white hover:bg-slate-800">
            Ubah kata sandi
        </button>
    </form>
</x-app-layout>
