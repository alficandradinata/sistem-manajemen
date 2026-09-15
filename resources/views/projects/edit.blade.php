<x-app-layout title="Ubah Project">
    <a href="{{ route('projects.show', $project) }}" class="back-link">
        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        {{ $project->name }}
    </a>

    <h1 class="page-title mb-6">Ubah Project</h1>

    <form method="POST" action="{{ route('projects.update', $project) }}" class="panel-pad mb-7 max-w-xl">
        @csrf
        @method('PUT')
        <x-project-form :project="$project" :statuses="$statuses" />
        <button type="submit" class="btn btn-primary">Simpan perubahan</button>
    </form>

    <h2 class="section-title">Zona bahaya</h2>

    <form method="POST" action="{{ route('projects.destroy', $project) }}" class="panel-pad max-w-xl"
          onsubmit="return confirm('Hapus project ini beserta semua berkasnya?')">
        @csrf
        @method('DELETE')
        <p class="field-hint">
            Seluruh berkas dan catatan link di project ini ikut terhapus permanen.
            Daftar harga tidak terpengaruh.
        </p>
        <button type="submit" class="btn btn-danger">Hapus project</button>
    </form>
</x-app-layout>
