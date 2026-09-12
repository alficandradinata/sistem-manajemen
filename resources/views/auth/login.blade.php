<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Sistem Datalaila</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex min-h-full items-center justify-center px-4 py-12">
        <div class="w-full max-w-sm">
            <div class="mb-9 text-center">
                <svg class="mx-auto mb-5 h-10 w-10 text-slate-900" viewBox="0 0 40 40" fill="none"
                     stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 19 20 6l16 13"/>
                    <path d="M9 23v11h22V23"/>
                </svg>
                <h1 class="text-sm font-medium tracking-[0.25em] text-slate-900 uppercase">Datalaila</h1>
                <div class="mx-auto my-3.5 h-px w-8 bg-slate-200"></div>
                <p class="text-xs tracking-wide text-slate-400">Manajemen file &amp; harga project</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="card-pad">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-error">{{ $errors->first() }}</div>
                @endif

                <label for="email" class="field-label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       autocomplete="username" inputmode="email" class="field-input mb-4">

                <label for="password" class="field-label">Kata sandi</label>
                <input id="password" name="password" type="password" required
                       autocomplete="current-password" class="field-input mb-4">

                <label class="mb-5 flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1"
                           class="size-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                    Ingat saya
                </label>

                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>
