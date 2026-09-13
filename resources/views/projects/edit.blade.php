<x-app-layout title="Ubah Project">
    <a href="{{ route('projects.show', $project) }}"
       class="mb-4 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Kembali
    </a>

    <h1 class="page-title mb-4">Ubah Project</h1>

    <form method="POST" action="{{ route('projects.update', $project) }}" class="card-pad mb-6">
        @csrf
        @method('PUT')
        <x-project-form :project="$project" :statuses="$statuses" />
        <button type="submit" class="btn btn-primary btn-block">Simpan perubahan</button>
    </form>

    <h2 class="section-title mb-2.5">Hapus project</h2>

    <form method="POST" action="{{ route('projects.destroy', $project) }}" class="card-pad"
          onsubmit="return confirm('Hapus project ini beserta semua filenya?')">
        @csrf
        @method('DELETE')
        <p class="field-hint">
            Seluruh file dan catatan link di project ini ikut terhapus permanen. Daftar harga tidak terpengaruh.
        </p>
        <button type="submit" class="btn btn-danger btn-block">Hapus project</button>
    </form>
</x-app-layout>
