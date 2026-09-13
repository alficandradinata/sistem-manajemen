<x-app-layout title="Project">
    <div class="mb-5 flex items-start justify-between gap-3">
        <div class="min-w-0">
            <h1 class="page-title">Project</h1>
            <p class="muted mt-1">Semangat cintaku sayang manja comel lucu gemes </p>
        </div>
        <div class="flex shrink-0 items-center gap-2">
            <a href="{{ route('prices.index') }}" class="btn btn-secondary">
                <svg class="size-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 11V5a2 2 0 0 1 2-2h6l10 10-8 8L3 11Z"/><circle cx="7.5" cy="7.5" r="1.25" fill="currentColor" stroke="none"/>
                </svg>
                Harga
            </a>
            <a href="{{ route('projects.create') }}" class="btn btn-primary">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Baru
            </a>
        </div>
    </div>

    <form method="GET" class="mb-5">
        <div class="relative mb-3">
            <svg class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            </svg>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama project"
                   class="field-input pl-9">
        </div>

        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-0.5">
            <button name="status" value="" class="chip {{ request('status') ? '' : 'chip-active' }}">Semua</button>
            @foreach ($statuses as $value => $label)
                <button name="status" value="{{ $value }}"
                        class="chip {{ request('status') === $value ? 'chip-active' : '' }}">{{ $label }}</button>
            @endforeach
        </div>
    </form>

    @forelse ($projects as $project)
        <a href="{{ route('projects.show', $project) }}"
           class="card mb-2 block px-4 py-3.5 transition-colors hover:border-slate-300 hover:bg-slate-50/60">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="truncate font-medium text-slate-900">{{ $project->name }}</h2>
                    <p class="mt-1 flex items-center gap-1.5 text-sm text-slate-500">
                        <svg class="size-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                            <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/>
                        </svg>
                        {{ $project->deadline?->translatedFormat('d M Y') ?? 'Tanpa deadline' }}
                    </p>
                </div>
                <x-status-badge :status="$project->status" class="mt-0.5 shrink-0" />
            </div>
        </a>
    @empty
        <p class="empty-state">
            {{ request('q') || request('status')
                ? 'Tidak ada project yang cocok.'
                : 'Belum ada project. Tekan “Baru” untuk membuat.' }}
        </p>
    @endforelse
</x-app-layout>
