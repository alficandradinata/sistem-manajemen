<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Masuk — Sistem Datalaila</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-slate-100 text-slate-900 antialiased">
    <div class="flex min-h-full items-center justify-center px-4 py-10">
        <div class="w-full max-w-sm">
            <div class="mb-6 text-center">
                <div class="mx-auto mb-3 flex size-14 items-center justify-center rounded-2xl bg-slate-900 text-white">
                    <svg class="size-7" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M3 21h18M5 21V7l7-4 7 4v14M9 21v-6h6v6"/>
                    </svg>
                </div>
                <h1 class="text-xl font-semibold">Sistem Datalaila</h1>
                <p class="mt-1 text-sm text-slate-500">Manajemen file &amp; harga project</p>
            </div>

            <form method="POST" action="{{ route('login') }}" class="rounded-2xl bg-white p-5 shadow-sm">
                @csrf

                @if ($errors->any())
                    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                        {{ $errors->first() }}
                    </div>
                @endif

                <label for="email" class="block text-sm font-medium text-slate-700">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus
                       autocomplete="username" inputmode="email"
                       class="mt-1 mb-4 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base shadow-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none">

                <label for="password" class="block text-sm font-medium text-slate-700">Kata sandi</label>
                <input id="password" name="password" type="password" required autocomplete="current-password"
                       class="mt-1 mb-4 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base shadow-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none">

                <label class="mb-5 flex items-center gap-2 text-sm text-slate-600">
                    <input type="checkbox" name="remember" value="1"
                           class="size-4 rounded border-slate-300 text-slate-900 focus:ring-slate-900">
                    Ingat saya
                </label>

                <button type="submit"
                        class="w-full rounded-lg bg-slate-900 px-4 py-3 text-base font-medium text-white hover:bg-slate-800 focus:ring-2 focus:ring-slate-900 focus:ring-offset-2 focus:outline-none">
                    Masuk
                </button>
            </form>
        </div>
    </div>
</body>
</html>
