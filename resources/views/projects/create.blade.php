<x-app-layout title="Project Baru">
    <a href="{{ route('projects.index') }}" class="mb-3 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Kembali
    </a>

    <h1 class="mb-4 text-xl font-semibold">Project Baru</h1>

    <form method="POST" action="{{ route('projects.store') }}" class="rounded-2xl bg-white p-5 shadow-sm">
        @csrf
        <x-project-form :statuses="$statuses" />

        <button type="submit"
                class="w-full rounded-lg bg-slate-900 px-4 py-3 text-base font-medium text-white hover:bg-slate-800">
            Simpan
        </button>
    </form>
</x-app-layout>
