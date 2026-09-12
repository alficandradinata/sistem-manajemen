<x-app-layout title="Project">
    <div class="mb-4 flex items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">Project</h1>
        <div class="flex items-center gap-2">
            <a href="{{ route('prices.index') }}"
               class="inline-flex items-center gap-1.5 rounded-lg border border-slate-300 bg-white px-3.5 py-2.5 text-sm font-medium text-slate-700 hover:bg-slate-50">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M3 11V5a2 2 0 0 1 2-2h6l10 10-8 8L3 11Z"/><circle cx="7.5" cy="7.5" r="1.5" fill="currentColor" stroke="none"/>
                </svg>
                Harga
            </a>
            <a href="{{ route('projects.create') }}"
               class="inline-flex items-center gap-1.5 rounded-lg bg-slate-900 px-3.5 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                    <path d="M12 5v14M5 12h14"/>
                </svg>
                Baru
            </a>
        </div>
    </div>

    <form method="GET" class="mb-4 space-y-2">
        <div class="relative">
            <svg class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            </svg>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama project…"
                   class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pr-3 pl-9 text-base shadow-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none">
        </div>

        <div class="-mx-4 flex gap-2 overflow-x-auto px-4 pb-1">
            <button name="status" value=""
                    class="shrink-0 rounded-full px-3 py-1.5 text-sm font-medium {{ request('status') ? 'bg-white text-slate-600 ring-1 ring-slate-300' : 'bg-slate-900 text-white' }}">
                Semua
            </button>
            @foreach ($statuses as $value => $label)
                <button name="status" value="{{ $value }}"
                        class="shrink-0 rounded-full px-3 py-1.5 text-sm font-medium {{ request('status') === $value ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-300' }}">
                    {{ $label }}
                </button>
            @endforeach
        </div>
    </form>

    @forelse ($projects as $project)
        <a href="{{ route('projects.show', $project) }}"
           class="mb-2.5 block rounded-xl bg-white p-4 shadow-sm hover:bg-slate-50">
            <div class="flex items-start justify-between gap-3">
                <h2 class="font-medium text-slate-900">{{ $project->name }}</h2>
                <x-status-badge :status="$project->status" class="shrink-0" />
            </div>
            <p class="mt-1.5 flex items-center gap-1.5 text-sm text-slate-500">
                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                    <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/>
                </svg>
                @if ($project->deadline)
                    {{ $project->deadline->translatedFormat('d M Y') }}
                @else
                    Tanpa deadline
                @endif
            </p>
        </a>
    @empty
        <div class="rounded-xl border border-dashed border-slate-300 bg-white/60 p-8 text-center">
            <p class="text-sm text-slate-500">
                {{ request('q') || request('status') ? 'Tidak ada project yang cocok.' : 'Belum ada project. Tekan "Baru" untuk membuat.' }}
            </p>
        </div>
    @endforelse
</x-app-layout>
