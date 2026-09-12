<x-app-layout :title="$project->name">
    <a href="{{ route('projects.index') }}" class="mb-3 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Semua project
    </a>

    <div class="rounded-2xl bg-white p-5 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div>
                <h1 class="text-xl font-semibold">{{ $project->name }}</h1>
                <p class="mt-1.5 flex items-center gap-1.5 text-sm text-slate-500">
                    <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/>
                    </svg>
                    @if ($project->deadline)
                        Deadline {{ $project->deadline->translatedFormat('d M Y') }}
                    @else
                        Tanpa deadline
                    @endif
                </p>
                <x-status-badge :status="$project->status" class="mt-2.5" />
            </div>
            <a href="{{ route('projects.edit', $project) }}"
               class="flex size-10 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900"
               aria-label="Ubah project">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                </svg>
            </a>
        </div>
    </div>

    <form method="POST" action="{{ route('projects.files.store', $project) }}" enctype="multipart/form-data"
          class="mt-4 mb-4 rounded-2xl bg-white p-5 shadow-sm">
        @csrf

        @if ($errors->any())
            <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                {{ $errors->first() }}
            </div>
        @endif

        <label for="files" class="block text-sm font-medium text-slate-700">Unggah file</label>
        <p class="mt-0.5 mb-2 text-xs text-slate-500">
            Excel, Word, PDF, foto (.jpg, .png, .webp), gambar CAD (.dwg, .dxf, .skp). Kategori ditentukan otomatis.
        </p>
        <input id="files" name="files[]" type="file" multiple required
               accept=".xls,.xlsx,.xlsm,.csv,.doc,.docx,.dwg,.dxf,.skp,.pdf,.jpg,.jpeg,.png,.webp"
               class="mb-4 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-white hover:file:bg-slate-800">

        <button type="submit"
                class="w-full rounded-lg bg-slate-900 px-4 py-3 text-base font-medium text-white hover:bg-slate-800">
            Unggah
        </button>
    </form>

    @foreach ($categoryLabels as $key => $label)
        @php $files = $filesByCategory[$key] ?? collect(); @endphp

        <section class="mb-3 overflow-hidden rounded-2xl bg-white shadow-sm">
            <h2 class="flex items-center justify-between border-b border-slate-100 px-4 py-3">
                <span class="text-sm font-semibold text-slate-900">{{ $label }}</span>
                <span class="text-xs text-slate-400">{{ $files->count() }} file</span>
            </h2>

            @forelse ($files as $file)
                <div class="flex items-center gap-3 border-b border-slate-50 px-4 py-3 last:border-0">
                    @if ($file->preview_kind === 'image')
                        <a href="{{ route('files.preview', $file) }}" class="shrink-0">
                            <img src="{{ route('files.raw', $file) }}" alt=""
                                 class="size-11 rounded-lg bg-slate-100 object-cover">
                        </a>
                    @endif

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm text-slate-800">{{ $file->original_name }}</p>
                        <p class="text-xs text-slate-400">{{ $file->readable_size }}</p>
                    </div>

                    @if ($file->preview_kind)
                        <a href="{{ route('files.preview', $file) }}"
                           class="flex size-10 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900"
                           aria-label="Lihat {{ $file->original_name }}">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                    @endif

                    <a href="{{ route('files.download', $file) }}"
                       class="flex size-10 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900"
                       aria-label="Unduh {{ $file->original_name }}">
                        <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                        </svg>
                    </a>

                    <form method="POST" action="{{ route('files.destroy', $file) }}"
                          onsubmit="return confirm('Hapus {{ $file->original_name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="flex size-10 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-600"
                                aria-label="Hapus {{ $file->original_name }}">
                            <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @empty
                <p class="px-4 py-4 text-sm text-slate-400">Belum ada file.</p>
            @endforelse
        </section>
    @endforeach
</x-app-layout>
