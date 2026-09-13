<x-app-layout :title="$project->name">
    <a href="{{ route('projects.index') }}"
       class="mb-4 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Semua project
    </a>

    <div class="card-pad mb-6">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <h1 class="text-xl font-semibold tracking-tight text-slate-900">{{ $project->name }}</h1>
                <p class="mt-1.5 flex items-center gap-1.5 text-sm text-slate-500">
                    <svg class="size-3.5 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                        <rect x="3" y="5" width="18" height="16" rx="2"/><path d="M8 3v4M16 3v4M3 11h18"/>
                    </svg>
                    {{ $project->deadline ? 'Deadline '.$project->deadline->translatedFormat('d M Y') : 'Tanpa deadline' }}
                </p>
                <x-status-badge :status="$project->status" class="mt-3" />
            </div>
            <a href="{{ route('projects.edit', $project) }}" class="icon-btn" aria-label="Ubah project">
                <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
                </svg>
            </a>
        </div>
    </div>

    <h2 class="section-title mb-2.5">Tambah file</h2>

    <form method="POST" action="{{ route('projects.files.store', $project) }}" enctype="multipart/form-data"
          class="card-pad mb-3">
        @csrf

        @if ($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
        @endif

        <label for="files" class="field-label">Unggah dari perangkat</label>
        <p class="field-hint">
            Excel, Word, PDF, foto, dan gambar CAD. Kategori ditentukan otomatis.
            Maksimal {{ \App\Support\UploadLimits::readable(\App\Support\UploadLimits::maxFileBytes()) }} per file,
            {{ \App\Support\UploadLimits::maxFiles() }} file sekali kirim.
        </p>
        <input id="files" name="files[]" type="file" multiple required
               accept=".xls,.xlsx,.xlsm,.csv,.doc,.docx,.dwg,.dxf,.skp,.pdf,.jpg,.jpeg,.png,.webp"
               class="mb-4 block w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2.5 file:text-sm file:font-medium file:text-white hover:file:bg-slate-700">

        <button type="submit" class="btn btn-primary btn-block">Unggah</button>
    </form>

    <form method="POST" action="{{ route('projects.files.drive', $project) }}" class="card-pad mb-6">
        @csrf

        <label for="drive_url" class="field-label">Ambil dari Google Drive</label>
        <p class="field-hint">
            Tempel link file satuan yang disetel &ldquo;siapa saja yang punya link&rdquo;.
            <span class="font-medium text-slate-700">Ambil file</span> menyalinnya ke sini agar bisa dipratinjau;
            <span class="font-medium text-slate-700">Simpan link</span> hanya mencatat alamatnya — pilih itu untuk
            file CAD besar yang memang dibuka di AutoCAD atau SketchUp.
        </p>

        @if ($errors->drive->any())
            <div class="alert alert-warn">{{ $errors->drive->first() }}</div>
        @endif

        <input id="drive_url" name="drive_url" type="url" required maxlength="2000"
               placeholder="https://drive.google.com/file/d/..."
               value="{{ old('drive_url') }}" class="field-input mb-3">

        <div class="grid grid-cols-2 gap-2">
            <button type="submit" name="mode" value="download" class="btn btn-primary btn-block">Ambil file</button>
            <button type="submit" name="mode" value="link" class="btn btn-secondary btn-block">Simpan link</button>
        </div>
    </form>

    <h2 class="section-title mb-2.5">Berkas project</h2>

    @foreach ($categoryLabels as $key => $label)
        @php $files = $filesByCategory[$key] ?? collect(); @endphp

        <section class="card mb-2 overflow-hidden">
            <header class="flex items-center justify-between border-b border-slate-100 px-4 py-2.5">
                <h3 class="text-sm font-medium text-slate-900">{{ $label }}</h3>
                <span class="text-xs text-slate-400">{{ $files->count() }}</span>
            </header>

            @forelse ($files as $file)
                <div class="flex items-center gap-3 border-b border-slate-100 px-4 py-3 last:border-0">
                    @if ($file->preview_kind === 'image')
                        <a href="{{ route('files.preview', $file) }}" class="shrink-0">
                            <img src="{{ route('files.raw', $file) }}" alt=""
                                 class="size-10 rounded-md border border-slate-200 bg-slate-100 object-cover">
                        </a>
                    @endif

                    <div class="min-w-0 flex-1">
                        <p class="truncate text-sm text-slate-800">{{ $file->original_name }}</p>
                        <p class="mt-0.5 flex items-center gap-1.5 text-xs text-slate-400">
                            @if ($file->size)
                                <span>{{ $file->readable_size }}</span>
                            @endif
                            @if ($file->is_link)
                                <span class="inline-flex items-center gap-1 rounded border border-slate-200 px-1.5 py-px text-[11px] text-slate-500">
                                    <svg class="size-3" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                                        <path d="M10 13a5 5 0 0 0 7 0l3-3a5 5 0 0 0-7-7l-1 1M14 11a5 5 0 0 0-7 0l-3 3a5 5 0 0 0 7 7l1-1"/>
                                    </svg>
                                    di Drive
                                </span>
                            @endif
                        </p>
                    </div>

                    @if ($file->is_link)
                        <a href="{{ $file->drive_url }}" target="_blank" rel="noopener noreferrer"
                           class="icon-btn" aria-label="Buka {{ $file->original_name }} di Drive">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>
                            </svg>
                        </a>
                    @endif

                    @if ($file->preview_kind)
                        <a href="{{ route('files.preview', $file) }}" class="icon-btn"
                           aria-label="Lihat {{ $file->original_name }}">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                            </svg>
                        </a>
                    @endif

                    @unless ($file->is_link)
                        <a href="{{ route('files.download', $file) }}" class="icon-btn"
                           aria-label="Unduh {{ $file->original_name }}">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                            </svg>
                        </a>
                    @endunless

                    <form method="POST" action="{{ route('files.destroy', $file) }}"
                          onsubmit="return confirm('Hapus {{ $file->original_name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="icon-btn hover:bg-red-50 hover:text-red-600"
                                aria-label="Hapus {{ $file->original_name }}">
                            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/>
                            </svg>
                        </button>
                    </form>
                </div>
            @empty
                <p class="px-4 py-3 text-sm text-slate-400">Belum ada file.</p>
            @endforelse
        </section>
    @endforeach
</x-app-layout>
