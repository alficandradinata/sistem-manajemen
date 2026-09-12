<x-app-layout title="Ubah Project">
    <a href="{{ route('projects.show', $project) }}" class="mb-3 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Kembali
    </a>

    <h1 class="mb-4 text-xl font-semibold">Ubah Project</h1>

    <form method="POST" action="{{ route('projects.update', $project) }}" class="rounded-2xl bg-white p-5 shadow-sm">
        @csrf
        @method('PUT')
        <x-project-form :project="$project" :statuses="$statuses" />

        <button type="submit"
                class="w-full rounded-lg bg-slate-900 px-4 py-3 text-base font-medium text-white hover:bg-slate-800">
            Simpan perubahan
        </button>
    </form>

    <form method="POST" action="{{ route('projects.destroy', $project) }}" class="mt-4"
          onsubmit="return confirm('Hapus project ini beserta semua file dan daftar barangnya?')">
        @csrf
        @method('DELETE')
        <button type="submit"
                class="w-full rounded-lg border border-red-200 bg-white px-4 py-3 text-base font-medium text-red-600 hover:bg-red-50">
            Hapus project
        </button>
    </form>
</x-app-layout>
