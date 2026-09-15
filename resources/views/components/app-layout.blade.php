@props(['title' => null])

<!DOCTYPE html>
<html lang="id" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ? $title . ' — ' : '' }}Datalaila</title>
    <script>
        if (localStorage.theme === 'dark'
            || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full">
    <div class="flex min-h-full flex-col">
        <header class="sticky top-0 z-20 border-b border-zinc-200 bg-zinc-50/85 backdrop-blur-sm dark:border-zinc-800 dark:bg-zinc-950/85">
            <div class="mx-auto flex h-13 max-w-3xl items-center px-4">
                <a href="{{ route('projects.index') }}"
                   class="flex items-baseline gap-2 text-zinc-900 dark:text-zinc-100">
                    <span class="text-[15px] font-semibold tracking-tight">Datalaila</span>
                    <span class="hidden font-mono text-[11px] text-zinc-400 sm:inline dark:text-zinc-600">estimator</span>
                </a>

                <nav class="ml-auto flex items-center gap-0.5">
                    <button type="button" id="theme-toggle" class="theme-toggle" aria-label="Ganti mode terang/gelap">
                        <svg class="size-[18px] dark:hidden" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="12" r="4"/>
                            <path d="M12 2v2M12 20v2M4.93 4.93l1.41 1.41M17.66 17.66l1.41 1.41M2 12h2M20 12h2M6.34 17.66l-1.41 1.41M19.07 4.93l-1.41 1.41"/>
                        </svg>
                        <svg class="hidden size-[18px] dark:block" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79Z"/>
                        </svg>
                    </button>
                    <a href="{{ route('account.edit') }}" class="icon-btn" aria-label="Akun">
                        <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="12" cy="8" r="4"/><path d="M4 21a8 8 0 0 1 16 0"/>
                        </svg>
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="icon-btn" aria-label="Keluar">
                            <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/>
                            </svg>
                        </button>
                    </form>
                </nav>
            </div>
        </header>

        <main class="mx-auto w-full max-w-3xl flex-1 px-4 py-7">
            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            {{ $slot }}
        </main>
    </div>

    <script>
        document.getElementById('theme-toggle').addEventListener('click', function () {
            const isDark = document.documentElement.classList.toggle('dark');
            localStorage.theme = isDark ? 'dark' : 'light';
        });
    </script>
</body>
</html>
