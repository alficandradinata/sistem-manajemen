<x-app-layout title="Akun">
    <a href="{{ route('projects.index') }}"
       class="mb-4 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Kembali
    </a>

    <h1 class="page-title mb-5">Akun</h1>

    <h2 class="section-title mb-2.5">Profil</h2>

    <form method="POST" action="{{ route('account.profile') }}" class="card-pad mb-6">
        @csrf
        @method('PUT')

        @if ($errors->profile->any())
            <div class="alert alert-error">{{ $errors->profile->first() }}</div>
        @endif

        <label for="name" class="field-label">Nama</label>
        <input id="name" name="name" type="text" required maxlength="255"
               value="{{ old('name', auth()->user()->name) }}" class="field-input mb-4">

        <label for="email" class="field-label">Email</label>
        <p class="field-hint">Dipakai untuk masuk. Tidak ada email yang dikirim ke alamat ini.</p>
        <input id="email" name="email" type="email" required maxlength="255" inputmode="email"
               value="{{ old('email', auth()->user()->email) }}" class="field-input mb-5">

        <button type="submit" class="btn btn-primary btn-block">Simpan profil</button>
    </form>

    <h2 class="section-title mb-2.5">Kata sandi</h2>

    <form method="POST" action="{{ route('account.password') }}" class="card-pad">
        @csrf
        @method('PUT')

        @if ($errors->password->any())
            <div class="alert alert-error">{{ $errors->password->first() }}</div>
        @endif

        <label for="current_password" class="field-label">Kata sandi saat ini</label>
        <input id="current_password" name="current_password" type="password" required
               autocomplete="current-password" class="field-input mb-4">

        <label for="password" class="field-label">Kata sandi baru</label>
        <p class="field-hint">Minimal 8 karakter.</p>
        <input id="password" name="password" type="password" required minlength="8"
               autocomplete="new-password" class="field-input mb-4">

        <label for="password_confirmation" class="field-label">Ulangi kata sandi baru</label>
        <input id="password_confirmation" name="password_confirmation" type="password" required minlength="8"
               autocomplete="new-password" class="field-input mb-5">

        <button type="submit" class="btn btn-primary btn-block">Ubah kata sandi</button>
    </form>
</x-app-layout>
