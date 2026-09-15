<x-app-layout :title="$project->name">
    <a href="{{ route('projects.index') }}" class="back-link">
        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        Semua project
    </a>

    <div class="mb-7 flex items-start justify-between gap-4 border-b border-zinc-200 pb-5 dark:border-zinc-800">
        <div class="min-w-0">
            <h1 class="page-title">{{ $project->name }}</h1>
            <div class="mt-2 flex flex-wrap items-center gap-x-4 gap-y-1">
                <x-status-badge :status="$project->status" />
                <span class="figure">
                    {{ $project->deadline ? 'Deadline '.$project->deadline->translatedFormat('d M Y') : 'Tanpa deadline' }}
                </span>
            </div>
        </div>
        <a href="{{ route('projects.edit', $project) }}" class="icon-btn" aria-label="Ubah project">
            <svg class="size-[18px]" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M12 20h9M16.5 3.5a2.12 2.12 0 0 1 3 3L7 19l-4 1 1-4Z"/>
            </svg>
        </a>
    </div>

    <h2 class="section-title">Tambah berkas</h2>

    <div class="mb-7 grid gap-3 sm:grid-cols-2">
        <form method="POST" action="{{ route('projects.files.store', $project) }}" enctype="multipart/form-data"
              class="panel-pad">
            @csrf

            @if ($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <label for="files" class="field-label">Dari perangkat</label>
            <p class="field-hint">
                Excel, Word, PDF, foto, CAD. Maks
                <span class="font-mono">{{ \App\Support\UploadLimits::readable(\App\Support\UploadLimits::maxFileBytes()) }}</span>
                per berkas, <span class="font-mono">{{ \App\Support\UploadLimits::maxFiles() }}</span> berkas sekali kirim.
            </p>
            <input id="files" name="files[]" type="file" multiple required
                   accept=".xls,.xlsx,.xlsm,.csv,.doc,.docx,.dwg,.dxf,.skp,.pdf,.jpg,.jpeg,.png,.webp"
                   class="mb-3 block w-full text-[13px] text-zinc-500 file:mr-3 file:rounded file:border-0 file:bg-zinc-900 file:px-3 file:py-1.5 file:text-[13px] file:font-medium file:text-white hover:file:bg-zinc-800
                   dark:text-zinc-400 dark:file:bg-zinc-100 dark:file:text-zinc-900 dark:hover:file:bg-white">

            <button type="submit" class="btn btn-primary btn-block">Unggah</button>
        </form>

        <form method="POST" action="{{ route('projects.files.drive', $project) }}" class="panel-pad">
            @csrf

            <label for="drive_url" class="field-label">Dari Google Drive</label>
            <p class="field-hint">
                Link berkas satuan, disetel &ldquo;siapa saja yang punya link&rdquo;.
                <strong class="font-medium text-zinc-700 dark:text-zinc-300">Ambil</strong> menyalin ke sini agar bisa dipratinjau,
                <strong class="font-medium text-zinc-700 dark:text-zinc-300">Link</strong> hanya mencatat alamatnya.
            </p>

            @if ($errors->drive->any())
                <div class="alert alert-warn">{{ $errors->drive->first() }}</div>
            @endif

            <input id="drive_url" name="drive_url" type="url" required maxlength="2000"
                   placeholder="https://drive.google.com/file/d/..."
                   value="{{ old('drive_url') }}" class="field-input mb-3 font-mono text-[13px]">

            <div class="grid grid-cols-2 gap-2">
                <button type="submit" name="mode" value="download" class="btn btn-primary btn-block">Ambil</button>
                <button type="submit" name="mode" value="link" class="btn btn-secondary btn-block">Link</button>
            </div>
        </form>
    </div>

    <h2 class="section-title">Berkas</h2>

    <div class="panel divide-y divide-zinc-200 overflow-hidden dark:divide-zinc-800">
        @foreach ($categoryLabels as $key => $label)
            @php $files = $filesByCategory[$key] ?? collect(); @endphp

            <section>
                <header class="flex items-baseline justify-between bg-zinc-50/60 px-4 py-2 dark:bg-zinc-800/30">
                    <h3 class="text-[11px] font-semibold tracking-[0.1em] text-zinc-500 uppercase dark:text-zinc-400">{{ $label }}</h3>
                    <span class="figure text-[11px]">{{ $files->count() }}</span>
                </header>

                @forelse ($files as $file)
                    <div class="row">
                        @if ($file->preview_kind === 'image')
                            <a href="{{ route('files.preview', $file) }}" class="shrink-0">
                                <img src="{{ route('files.raw', $file) }}" alt=""
                                     class="size-9 rounded border border-zinc-200 bg-zinc-100 object-cover dark:border-zinc-700 dark:bg-zinc-800">
                            </a>
                        @endif

                        <div class="min-w-0 flex-1">
                            <p class="truncate text-[14px] text-zinc-800 dark:text-zinc-200">{{ $file->original_name }}</p>
                            <p class="figure mt-0.5 flex items-center gap-2 text-[12px]">
                                @if ($file->size)
                                    <span>{{ $file->readable_size }}</span>
                                @endif
                                @if ($file->is_link)
                                    <span class="text-zinc-400 dark:text-zinc-600">· Drive</span>
                                @endif
                            </p>
                        </div>

                        @if ($file->is_link)
                            <a href="{{ $file->drive_url }}" target="_blank" rel="noopener noreferrer"
                               class="icon-btn" aria-label="Buka {{ $file->original_name }} di Drive">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14 21 3"/>
                                </svg>
                            </a>
                        @endif

                        @if ($file->preview_kind)
                            <a href="{{ route('files.preview', $file) }}" class="icon-btn"
                               aria-label="Lihat {{ $file->original_name }}">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/>
                                </svg>
                            </a>
                        @endif

                        @unless ($file->is_link)
                            <a href="{{ route('files.download', $file) }}" class="icon-btn"
                               aria-label="Unduh {{ $file->original_name }}">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                                </svg>
                            </a>
                        @endunless

                        <form method="POST" action="{{ route('files.destroy', $file) }}"
                              onsubmit="return confirm('Hapus {{ $file->original_name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="icon-btn hover:bg-red-50 hover:text-red-600 dark:hover:bg-red-950/40 dark:hover:text-red-400"
                                    aria-label="Hapus {{ $file->original_name }}">
                                <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                    <path d="M3 6h18M8 6V4h8v2M19 6l-1 14H6L5 6M10 11v6M14 11v6"/>
                                </svg>
                            </button>
                        </form>
                    </div>
                @empty
                    <p class="px-4 py-2.5 text-[13px] text-zinc-300 dark:text-zinc-700">—</p>
                @endforelse
            </section>
        @endforeach
    </div>
</x-app-layout>
