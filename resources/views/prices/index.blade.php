<x-app-layout title="Daftar Harga">
    <div class="mb-6 flex items-end justify-between gap-4">
        <div class="min-w-0">
            <h1 class="page-title">Daftar Harga</h1>
            <p class="page-subtitle">Catatan harga barang, berlaku lintas project.</p>
        </div>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary shrink-0">Project</a>
    </div>

    <form method="GET" class="mb-5">
        <div class="relative">
            <svg class="pointer-events-none absolute top-1/2 left-2.5 size-4 -translate-y-1/2 text-zinc-400 dark:text-zinc-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            </svg>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama barang"
                   class="field-input pl-8">
        </div>
    </form>

    <details class="panel-pad mb-5" @if ($errors->any() || ($prices->isEmpty() && ! request('q'))) open @endif>
        <summary class="flex cursor-pointer list-none items-center gap-2 text-[13px] font-medium text-zinc-700 dark:text-zinc-300">
            <svg class="size-3.5 text-zinc-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Tambah harga
        </summary>

        <form method="POST" action="{{ route('prices.store') }}" class="mt-4">
            @csrf

            @if ($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <div class="grid gap-2 sm:grid-cols-[1fr_180px]">
                <input name="name" type="text" required maxlength="255" placeholder="Nama barang"
                       value="{{ old('name') }}" class="field-input">
                <input name="price" type="number" step="0.01" min="0" required placeholder="Harga"
                       value="{{ old('price') }}" class="field-input field-input-num">
            </div>

            <button type="submit" class="btn btn-primary mt-3">Tambah</button>
        </form>
    </details>

    <div class="mb-1.5 flex items-baseline justify-between">
        <h2 class="section-title mb-0">Barang</h2>
        <span class="figure">{{ $prices->count() }}</span>
    </div>

    <div class="panel overflow-hidden">
        @forelse ($prices as $price)
            <details class="group border-b border-zinc-100 last:border-0 dark:border-zinc-800/80">
                <summary class="flex cursor-pointer list-none items-center justify-between gap-4 px-4 py-2.5 transition-colors hover:bg-zinc-50 dark:hover:bg-zinc-800/40">
                    <span class="min-w-0 truncate text-[14px] text-zinc-800 dark:text-zinc-200">{{ $price->name }}</span>
                    <span class="flex shrink-0 items-center gap-2.5">
                        <span class="font-mono text-[14px] text-zinc-900 dark:text-zinc-100">
                            {{ number_format($price->price, 0, ',', '.') }}
                        </span>
                        <svg class="size-3.5 text-zinc-300 transition-transform group-open:rotate-180 dark:text-zinc-600" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="m6 9 6 6 6-6"/>
                        </svg>
                    </span>
                </summary>

                <div class="border-t border-zinc-100 bg-zinc-50/60 px-4 py-3.5 dark:border-zinc-800/80 dark:bg-zinc-800/20">
                    <form method="POST" action="{{ route('prices.update', $price) }}">
                        @csrf
                        @method('PUT')
                        <div class="grid gap-2 sm:grid-cols-[1fr_180px]">
                            <input name="name" type="text" required maxlength="255" value="{{ $price->name }}"
                                   class="field-input">
                            <input name="price" type="number" step="0.01" min="0" required value="{{ $price->price }}"
                                   class="field-input field-input-num">
                        </div>

                        <div class="mt-3 flex gap-2">
                            <button type="submit" class="btn btn-primary">Simpan</button>
                            <button type="submit" form="hapus-{{ $price->id }}" class="btn btn-danger">Hapus</button>
                        </div>
                    </form>

                    <form id="hapus-{{ $price->id }}" method="POST" action="{{ route('prices.destroy', $price) }}"
                          onsubmit="return confirm('Hapus {{ $price->name }}?')">
                        @csrf
                        @method('DELETE')
                    </form>
                </div>
            </details>
        @empty
            <p class="empty-state">
                {{ request('q') ? 'Tidak ada barang yang cocok.' : 'Belum ada harga yang dicatat.' }}
            </p>
        @endforelse
    </div>
</x-app-layout>
