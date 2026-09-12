<x-app-layout title="Daftar Harga">
    <div class="mb-5 flex items-start justify-between gap-3">
        <div>
            <h1 class="page-title">Daftar Harga</h1>
            <p class="muted mt-1">Catatan harga barang, berlaku untuk semua project.</p>
        </div>
        <a href="{{ route('projects.index') }}" class="btn btn-secondary shrink-0">Project</a>
    </div>

    <form method="GET" class="mb-3">
        <div class="relative">
            <svg class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            </svg>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama barang"
                   class="field-input pl-9">
        </div>
    </form>

    <details class="card mb-5"
             @if ($errors->any() || ($prices->isEmpty() && ! request('q'))) open @endif>
        <summary class="flex cursor-pointer list-none items-center gap-2 px-4 py-3 text-sm font-medium text-slate-900">
            <svg class="size-4 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.25" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Tambah harga
        </summary>

        <form method="POST" action="{{ route('prices.store') }}" class="border-t border-slate-100 p-4">
            @csrf

            @if ($errors->any())
                <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif

            <input name="name" type="text" required maxlength="255" placeholder="Nama barang"
                   value="{{ old('name') }}" class="field-input mb-2">
            <input name="price" type="number" step="0.01" min="0" required placeholder="Harga"
                   value="{{ old('price') }}" class="field-input mb-3">

            <button type="submit" class="btn btn-primary btn-block">Tambah</button>
        </form>
    </details>

    @if ($prices->isNotEmpty())
        <div class="card overflow-hidden">
            @foreach ($prices as $price)
                <details class="group border-b border-slate-100 last:border-0">
                    <summary class="flex cursor-pointer list-none items-center justify-between gap-3 px-4 py-3 hover:bg-slate-50">
                        <span class="min-w-0 truncate text-sm text-slate-800">{{ $price->name }}</span>
                        <span class="flex shrink-0 items-center gap-2">
                            <span class="text-sm font-medium text-slate-900">
                                Rp{{ number_format($price->price, 0, ',', '.') }}
                            </span>
                            <svg class="size-4 text-slate-400 transition-transform group-open:rotate-180" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.75" stroke-linecap="round" stroke-linejoin="round">
                                <path d="m6 9 6 6 6-6"/>
                            </svg>
                        </span>
                    </summary>

                    <div class="border-t border-slate-100 bg-slate-50/60 p-4">
                        <form method="POST" action="{{ route('prices.update', $price) }}">
                            @csrf
                            @method('PUT')
                            <input name="name" type="text" required maxlength="255" value="{{ $price->name }}"
                                   class="field-input mb-2">
                            <input name="price" type="number" step="0.01" min="0" required value="{{ $price->price }}"
                                   class="field-input mb-3">
                            <button type="submit" class="btn btn-primary btn-block">Simpan</button>
                        </form>

                        <form method="POST" action="{{ route('prices.destroy', $price) }}" class="mt-2"
                              onsubmit="return confirm('Hapus {{ $price->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-block">Hapus</button>
                        </form>
                    </div>
                </details>
            @endforeach
        </div>
    @else
        <p class="empty-state">
            {{ request('q') ? 'Tidak ada barang yang cocok.' : 'Belum ada harga yang dicatat.' }}
        </p>
    @endif
</x-app-layout>
