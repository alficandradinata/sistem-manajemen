<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Datalaila</title>
    <script>
        if (localStorage.theme === 'dark'
            || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex min-h-full items-center justify-center px-4 py-12">
        <div class="w-full max-w-[340px]">
            <div class="mb-8">
                <h1 class="text-[19px] font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">Datalaila</h1>
                <p class="mt-1 font-mono text-[12px] text-zinc-400 dark:text-zinc-600">
                    manajemen berkas &amp; harga project
                </p>
            </div>

            <form method="POST" action="{{ route('login') }}">
                @csrf

                @if ($errors->any())
                    <div class="alert alert-error">{{ $errors->first() }}</div>
                @endif

                <label for="email" class="field-label">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       autocomplete="username" inputmode="email" class="field-input mb-3.5">

                <label for="password" class="field-label">Kata sandi</label>
                <input id="password" name="password" type="password" required
                       autocomplete="current-password" class="field-input mb-4">

                <label class="mb-5 flex cursor-pointer items-center gap-2 text-[13px] text-zinc-600 dark:text-zinc-400">
                    <input type="checkbox" name="remember" value="1"
                           class="size-3.5 rounded-sm border-zinc-300 text-zinc-900 focus:ring-1 focus:ring-blue-600/30
                           dark:border-zinc-700 dark:bg-zinc-900">
                    Ingat saya
                </label>

                <button type="submit" class="btn btn-primary btn-block">Masuk</button>
            </form>
        </div>
    </div>
</body>
</html>
