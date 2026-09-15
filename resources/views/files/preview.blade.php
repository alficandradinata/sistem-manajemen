<x-app-layout :title="$file->original_name">
    <a href="{{ route('projects.show', $file->project) }}"
       class="back-link">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        {{ $file->project->name }}
    </a>

    <div class="card mb-4 flex items-center justify-between gap-3 px-4 py-3">
        <div class="min-w-0">
            <p class="truncate font-medium text-slate-900 dark:text-slate-100">{{ $file->original_name }}</p>
            <p class="mt-0.5 text-xs text-slate-400 dark:text-slate-500">{{ $file->category_label }} &middot; {{ $file->readable_size }}</p>
        </div>
        <a href="{{ route('files.download', $file) }}" class="icon-btn" aria-label="Unduh">
            <svg class="size-4.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
            </svg>
        </a>
    </div>

    @if ($error)
        <p class="alert alert-warn">{{ $error }}</p>

    @elseif ($file->preview_kind === 'image')
        <img src="{{ route('files.raw', $file) }}" alt="{{ $file->original_name }}"
             class="w-full rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">

    @elseif ($file->preview_kind === 'pdf')
        <object data="{{ route('files.raw', $file) }}" type="application/pdf"
                class="h-[70vh] w-full rounded-xl border border-slate-200 bg-white dark:border-slate-800 dark:bg-slate-900">
            <div class="card-pad text-center">
                <p class="muted">Browser ini tidak bisa menampilkan PDF langsung.</p>
                <a href="{{ route('files.raw', $file) }}" target="_blank" rel="noopener"
                   class="btn btn-primary mt-3">Buka di tab baru</a>
            </div>
        </object>

    @elseif ($file->preview_kind === 'sheet')
        @if (count($sheetNames) > 1)
            <div class="-mx-4 mb-3 flex gap-2 overflow-x-auto px-4">
                @foreach ($sheetNames as $i => $name)
                    <a href="{{ route('files.preview', [$file, 'sheet' => $i]) }}"
                       class="chip {{ $i === $sheetIndex ? 'chip-active' : '' }}">{{ $name }}</a>
                @endforeach
            </div>
        @endif

        @if ($rows === [])
            <p class="empty-state">Lembar ini kosong.</p>
        @else
            <div class="card overflow-x-auto">
                <table class="min-w-full text-sm">
                    <tbody>
                        @foreach ($rows as $r => $row)
                            <tr class="border-b border-slate-100 last:border-0 dark:border-slate-800 {{ $r === 0 ? 'bg-slate-50 font-medium text-slate-900 dark:bg-slate-800 dark:text-slate-100' : '' }}">
                                @foreach ($row as $cell)
                                    <td class="px-3 py-2 whitespace-nowrap text-slate-700 dark:text-slate-300">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p class="mt-2.5 text-xs text-slate-400 dark:text-slate-500">
            Rumus ditampilkan sebagai hasil hitungnya. Warna, garis, dan sel gabungan tidak ikut terbawa.
            @if ($truncated) Hanya {{ count($rows) }} baris pertama yang ditampilkan. @endif
        </p>

    @elseif ($file->preview_kind === 'document')
        @if ($blocks === [])
            <p class="empty-state">Dokumen ini kosong.</p>
        @else
            <div class="card-pad space-y-3">
                @foreach ($blocks as $block)
                    @if ($block['type'] === 'paragraph')
                        <p class="text-sm leading-relaxed text-slate-700 dark:text-slate-300">{{ $block['text'] }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <tbody>
                                    @foreach ($block['rows'] as $row)
                                        <tr class="border-b border-slate-100 last:border-0 dark:border-slate-800">
                                            @foreach ($row as $cell)
                                                <td class="px-3 py-2 text-slate-700 dark:text-slate-300">{{ $cell }}</td>
                                            @endforeach
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                @endforeach
            </div>
        @endif

        <p class="mt-2.5 text-xs text-slate-400 dark:text-slate-500">Tampilan teks saja — tata letak dan gambar tidak ikut terbawa.</p>

    @else
        <div class="card-pad text-center">
            <p class="muted">
                File {{ $file->category_label }} tidak bisa ditampilkan di browser.
                Unduh dan buka di aplikasi aslinya.
            </p>
            <a href="{{ route('files.download', $file) }}" class="btn btn-primary mt-4">Unduh</a>
        </div>
    @endif
</x-app-layout>
