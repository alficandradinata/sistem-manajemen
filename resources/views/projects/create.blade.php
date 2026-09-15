<x-app-layout title="Project Baru">
    <a href="{{ route('projects.index') }}" class="back-link">
        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Semua project
    </a>

    <h1 class="page-title mb-6">Project Baru</h1>

    <form method="POST" action="{{ route('projects.store') }}" class="panel-pad max-w-xl">
        @csrf
        <x-project-form :statuses="$statuses" />
        <button type="submit" class="btn btn-primary">Simpan</button>
    </form>
</x-app-layout>
