<x-app-layout :title="$file->original_name">
    <a href="{{ route('projects.show', $file->project) }}" class="back-link">
        <svg class="size-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        {{ $file->project->name }}
    </a>

    <div class="mb-5 flex items-start justify-between gap-4 border-b border-zinc-200 pb-4 dark:border-zinc-800">
        <div class="min-w-0">
            <h1 class="truncate text-[17px] font-semibold tracking-tight text-zinc-900 dark:text-zinc-50">
                {{ $file->original_name }}
            </h1>
            <p class="figure mt-1">{{ $file->category_label }} · {{ $file->readable_size }}</p>
        </div>
        <a href="{{ route('files.download', $file) }}" class="btn btn-secondary shrink-0">Unduh</a>
    </div>

    @if ($error)
        <p class="alert alert-warn">{{ $error }}</p>

    @elseif ($file->preview_kind === 'image')
        <img src="{{ route('files.raw', $file) }}" alt="{{ $file->original_name }}"
             class="w-full rounded-md border border-zinc-200 dark:border-zinc-800">

    @elseif ($file->preview_kind === 'pdf')
        <object data="{{ route('files.raw', $file) }}" type="application/pdf"
                class="h-[72vh] w-full rounded-md border border-zinc-200 bg-white dark:border-zinc-800 dark:bg-zinc-900">
            <div class="panel-pad text-center">
                <p class="muted">Browser ini tidak bisa menampilkan PDF langsung.</p>
                <a href="{{ route('files.raw', $file) }}" target="_blank" rel="noopener"
                   class="btn btn-primary mt-3">Buka di tab baru</a>
            </div>
        </object>

    @elseif ($file->preview_kind === 'sheet')
        @if (count($sheetNames) > 1)
            <div class="mb-3 flex gap-1.5 overflow-x-auto">
                @foreach ($sheetNames as $i => $name)
                    <a href="{{ route('files.preview', [$file, 'sheet' => $i]) }}"
                       class="chip {{ $i === $sheetIndex ? 'chip-active' : '' }}">{{ $name }}</a>
                @endforeach
            </div>
        @endif

        @if ($rows === [])
            <p class="panel empty-state">Lembar ini kosong.</p>
        @else
            <div class="panel overflow-x-auto">
                <table class="min-w-full font-mono text-[12px]">
                    <tbody>
                        @foreach ($rows as $r => $row)
                            <tr class="border-b border-zinc-100 last:border-0 dark:border-zinc-800/80 {{ $r === 0 ? 'bg-zinc-50 font-medium text-zinc-900 dark:bg-zinc-800/40 dark:text-zinc-100' : '' }}">
                                @foreach ($row as $cell)
                                    <td class="border-r border-zinc-100 px-2.5 py-1.5 whitespace-nowrap text-zinc-700 last:border-0 dark:border-zinc-800/80 dark:text-zinc-300">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p class="mt-2.5 text-[12px] text-zinc-400 dark:text-zinc-600">
            Rumus ditampilkan sebagai hasil hitungnya. Warna, garis, dan sel gabungan tidak ikut terbawa.
            @if ($truncated) Hanya {{ count($rows) }} baris pertama yang ditampilkan. @endif
        </p>

    @elseif ($file->preview_kind === 'document')
        @if ($blocks === [])
            <p class="panel empty-state">Dokumen ini kosong.</p>
        @else
            <div class="panel-pad space-y-3">
                @foreach ($blocks as $block)
                    @if ($block['type'] === 'paragraph')
                        <p class="text-[14px] leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $block['text'] }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full font-mono text-[12px]">
                                <tbody>
                                    @foreach ($block['rows'] as $row)
                                        <tr class="border-b border-zinc-100 last:border-0 dark:border-zinc-800/80">
                                            @foreach ($row as $cell)
                                                <td class="px-2.5 py-1.5 text-zinc-700 dark:text-zinc-300">{{ $cell }}</td>
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

        <p class="mt-2.5 text-[12px] text-zinc-400 dark:text-zinc-600">Tampilan teks saja — tata letak dan gambar tidak ikut terbawa.</p>

    @else
        <div class="panel-pad text-center">
            <p class="muted">
                Berkas {{ $file->category_label }} tidak bisa ditampilkan di browser.
                Unduh dan buka di aplikasi aslinya.
            </p>
            <a href="{{ route('files.download', $file) }}" class="btn btn-primary mt-4">Unduh</a>
        </div>
    @endif
</x-app-layout>
