@props(['title' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' — ' : '' }}Sistem Datalaila</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex min-h-full flex-col">
        <header class="sticky top-0 z-20 border-b border-slate-200 bg-white/90 backdrop-blur">
            <div class="mx-auto flex h-14 max-w-3xl items-center px-4">
                <a href="{{ route('projects.index') }}"
                   class="flex items-center gap-2.5 text-slate-900">
                    <svg class="size-5" viewBox="0 0 40 40" fill="none" stroke="currentColor" stroke-width="1.75"
                         stroke-linecap="round" stroke-linejoin="round">
                        <path d="M4 19 20 6l16 13"/>
                        <path d="M9 23v11h22V23"/>
                    </svg>
                    <span class="text-[13px] font-medium tracking-[0.18em] uppercase">Datalaila</span>
                </a>

                <nav class="ml-auto flex items-center gap-0.5">
                    <a href="{{ route('account.edit') }}" class="icon-btn" aria-label="Akun">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="icon-btn" aria-label="Keluar">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                            </svg>
                        </button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-3xl flex-1 px-4 py-6">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            {{ $slot }}
        </main>
    </div>
</body>
</html>
