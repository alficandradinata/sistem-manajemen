<x-app-layout title="Project Baru">
    <a href="{{ route('projects.index') }}"
       class="mb-4 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Kembali
    </a>

    <h1 class="page-title mb-4">Project Baru</h1>

    <form method="POST" action="{{ route('projects.store') }}" class="card-pad">
        @csrf
        <x-project-form :statuses="$statuses" />
        <button type="submit" class="btn btn-primary btn-block">Simpan</button>
    </form>
</x-app-layout>
