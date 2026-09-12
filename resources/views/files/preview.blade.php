<x-app-layout :title="$file->original_name">
    <a href="{{ route('projects.show', $file->project) }}" class="mb-3 inline-flex items-center gap-1 text-sm text-slate-500 hover:text-slate-900">
        <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <path d="m15 18-6-6 6-6"/>
        </svg>
        {{ $file->project->name }}
    </a>

    <div class="mb-4 rounded-2xl bg-white p-4 shadow-sm">
        <div class="flex items-start justify-between gap-3">
            <div class="min-w-0">
                <p class="truncate font-medium text-slate-900">{{ $file->original_name }}</p>
                <p class="mt-0.5 text-xs text-slate-400">{{ $file->category_label }} · {{ $file->readable_size }}</p>
            </div>
            <a href="{{ route('files.download', $file) }}"
               class="flex size-10 shrink-0 items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-900"
               aria-label="Unduh">
                <svg class="size-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4M7 10l5 5 5-5M12 15V3"/>
                </svg>
            </a>
        </div>
    </div>

    @if ($error)
        <div class="rounded-2xl border border-amber-200 bg-amber-50 p-5 text-sm text-amber-800">
            {{ $error }}
        </div>
    @elseif ($file->preview_kind === 'image')
        <img src="{{ route('files.raw', $file) }}" alt="{{ $file->original_name }}"
             class="w-full rounded-2xl bg-white shadow-sm">

    @elseif ($file->preview_kind === 'pdf')
        <object data="{{ route('files.raw', $file) }}" type="application/pdf"
                class="h-[70vh] w-full rounded-2xl bg-white shadow-sm">
            <div class="rounded-2xl bg-white p-5 text-center shadow-sm">
                <p class="text-sm text-slate-500">Browser ini tidak bisa menampilkan PDF langsung.</p>
                <a href="{{ route('files.raw', $file) }}" target="_blank" rel="noopener"
                   class="mt-3 inline-block rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                    Buka di tab baru
                </a>
            </div>
        </object>

    @elseif ($file->preview_kind === 'sheet')
        @if (count($sheetNames) > 1)
            <div class="-mx-4 mb-3 flex gap-2 overflow-x-auto px-4">
                @foreach ($sheetNames as $i => $name)
                    <a href="{{ route('files.preview', [$file, 'sheet' => $i]) }}"
                       class="shrink-0 rounded-full px-3 py-1.5 text-sm font-medium {{ $i === $sheetIndex ? 'bg-slate-900 text-white' : 'bg-white text-slate-600 ring-1 ring-slate-300' }}">
                        {{ $name }}
                    </a>
                @endforeach
            </div>
        @endif

        @if ($rows === [])
            <div class="rounded-2xl bg-white p-8 text-center text-sm text-slate-500 shadow-sm">Lembar ini kosong.</div>
        @else
            <div class="overflow-x-auto rounded-2xl bg-white shadow-sm">
                <table class="min-w-full text-sm">
                    <tbody>
                        @foreach ($rows as $r => $row)
                            <tr class="{{ $r === 0 ? 'bg-slate-50 font-medium' : '' }} border-b border-slate-100 last:border-0">
                                @foreach ($row as $cell)
                                    <td class="px-3 py-2 whitespace-nowrap text-slate-700">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif

        <p class="mt-3 text-xs text-slate-400">
            Rumus ditampilkan sebagai hasil hitungnya. Warna, garis, dan sel gabungan tidak ikut terbawa.
            @if ($truncated) Hanya {{ count($rows) }} baris pertama yang ditampilkan. @endif
        </p>

    @elseif ($file->preview_kind === 'document')
        @if ($blocks === [])
            <div class="rounded-2xl bg-white p-8 text-center text-sm text-slate-500 shadow-sm">Dokumen ini kosong.</div>
        @else
            <div class="space-y-3 rounded-2xl bg-white p-5 shadow-sm">
                @foreach ($blocks as $block)
                    @if ($block['type'] === 'paragraph')
                        <p class="text-sm leading-relaxed text-slate-700">{{ $block['text'] }}</p>
                    @else
                        <div class="overflow-x-auto">
                            <table class="min-w-full text-sm">
                                <tbody>
                                    @foreach ($block['rows'] as $row)
                                        <tr class="border-b border-slate-100 last:border-0">
                                            @foreach ($row as $cell)
                                                <td class="px-3 py-2 text-slate-700">{{ $cell }}</td>
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

        <p class="mt-3 text-xs text-slate-400">Tampilan teks saja — tata letak dan gambar tidak ikut terbawa.</p>

    @else
        <div class="rounded-2xl bg-white p-8 text-center shadow-sm">
            <p class="text-sm text-slate-500">
                File {{ $file->category_label }} tidak bisa ditampilkan di browser.
                Unduh dan buka di aplikasi aslinya.
            </p>
            <a href="{{ route('files.download', $file) }}"
               class="mt-4 inline-block rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                Unduh
            </a>
        </div>
    @endif
</x-app-layout>
