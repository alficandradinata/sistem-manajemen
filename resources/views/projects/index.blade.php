<x-app-layout title="Project">
    <div class="mb-6 flex items-end justify-between gap-4">
        <div class="min-w-0">
            <h1 class="page-title">Project</h1>
            <p class="page-subtitle">Semangat cintaku sayang manja comel lucu gemes</p>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <a href="{{ route('prices.index') }}" class="btn btn-secondary">Daftar Harga</a>
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Project
            </a>
        </div>
    </div>

    <form method="GET" class="mb-5 flex flex-wrap items-center gap-2">
        <div class="relative min-w-0 flex-1">
            <svg class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-zinc-400 dark:text-zinc-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            </svg>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari project atau nama berkas"
                   class="field-input pl-8">
        </div>

        <div class="flex gap-1.5 overflow-x-auto">
            <button name="status" value="" class="chip {{ request('status') ? '' : 'chip-active' }}">Semua</button>
            @foreach ($statuses as $value => $label)
                <button name="status" value="{{ $value }}"
                        class="chip {{ request('status') === $value ? 'chip-active' : '' }}">{{ $label }}</button>
            @endforeach
        </div>
    </form>

    <div class="mb-1.5 flex items-baseline justify-between">
        <h2 class="section-title mb-0">Daftar</h2>
        <span class="figure">{{ $projects->count() }}</span>
    </div>

    <div class="panel overflow-hidden">
        @forelse ($projects as $project)
            <a href="{{ route('projects.show', $project) }}" class="row-link group">
                <div class="flex items-center justify-between gap-4">
                    <div class="min-w-0">
                        <h3 class="truncate text-[15px] font-medium text-zinc-900 dark:text-zinc-100">{{ $project->name }}</h3>
                        <p class="figure mt-0.5 flex items-center gap-2">
                            <span>{{ $project->deadline?->translatedFormat('d M Y') ?? '—' }}</span>
                            @if ($project->deadline_label)
                                <span @class([
                                    'font-medium',
                                    'text-red-600 dark:text-red-400' => $project->deadline_tone === 'overdue',
                                    'text-amber-600 dark:text-amber-400' => $project->deadline_tone === 'soon',
                                ])>{{ $project->deadline_label }}</span>
                            @endif
                        </p>
                    </div>
                    <div class="flex shrink-0 items-center gap-3">
                        <x-status-badge :status="$project->status" />
                        <svg class="size-4 text-zinc-300 transition-colors group-hover:text-zinc-500 dark:text-zinc-700 dark:group-hover:text-zinc-500" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m9 18 6-6-6-6"/>
                        </svg>
                    </div>
                </div>

                @if ($search && $project->files->isNotEmpty())
                    <ul class="mt-2 space-y-0.5 border-l-2 border-zinc-200 pl-3 dark:border-zinc-700">
                        @foreach ($project->files as $file)
                            <li class="figure truncate text-[12px] text-zinc-500 dark:text-zinc-400">
                                {{ $file->original_name }}
                            </li>
                        @endforeach
                    </ul>
                @endif
            </a>
        @empty
            <p class="empty-state">
                {{ request('q') || request('status')
                    ? 'Tidak ada project yang cocok.'
                    : 'Belum ada project.' }}
            </p>
        @endforelse
    </div>
</x-app-layout>
