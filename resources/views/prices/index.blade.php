@php
    $inputClass = 'block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base shadow-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none';
@endphp

<x-app-layout title="Daftar Harga">
    <div class="mb-1 flex items-center justify-between gap-3">
        <h1 class="text-xl font-semibold">Daftar Harga</h1>
        <a href="{{ route('projects.index') }}"
           class="shrink-0 text-sm text-slate-500 hover:text-slate-900">Project</a>
    </div>
    <p class="mb-4 text-sm text-slate-500">Catatan harga barang, berlaku untuk semua project.</p>

    <form method="GET" class="mb-3">
        <div class="relative">
            <svg class="pointer-events-none absolute top-1/2 left-3 size-4 -translate-y-1/2 text-slate-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>
            </svg>
            <input type="search" name="q" value="{{ request('q') }}" placeholder="Cari nama barang…"
                   class="w-full rounded-lg border border-slate-300 bg-white py-2.5 pr-3 pl-9 text-base shadow-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none">
        </div>
    </form>

    <details class="mb-4 overflow-hidden rounded-2xl bg-white shadow-sm" @if ($errors->any() || ($prices->isEmpty() && ! request('q'))) open @endif>
        <summary class="flex cursor-pointer list-none items-center gap-2 px-5 py-4 text-sm font-semibold text-slate-900">
            <svg class="size-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round">
                <path d="M12 5v14M5 12h14"/>
            </svg>
            Tambah harga
        </summary>

        <form method="POST" action="{{ route('prices.store') }}" class="px-5 pb-5">
            @csrf

            @if ($errors->any())
                <div class="mb-3 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <input name="name" type="text" required maxlength="255" placeholder="Nama barang"
                   value="{{ old('name') }}" class="{{ $inputClass }} mb-2">
            <input name="price" type="number" step="0.01" min="0" required placeholder="Harga"
                   value="{{ old('price') }}" class="{{ $inputClass }} mb-3">

            <button type="submit"
                    class="w-full rounded-lg bg-slate-900 px-4 py-3 text-base font-medium text-white hover:bg-slate-800">
                Tambah
            </button>
        </form>
    </details>

    @forelse ($prices as $price)
        <div class="mb-2.5 rounded-2xl bg-white shadow-sm">
            <div class="flex items-start justify-between gap-3 p-4">
                <p class="min-w-0 font-medium text-slate-900">{{ $price->name }}</p>
                <p class="shrink-0 font-semibold text-slate-900">Rp{{ number_format($price->price, 0, ',', '.') }}</p>
            </div>

            <details class="group border-t border-slate-100">
                <summary class="flex cursor-pointer list-none items-center gap-4 px-4 py-2.5 text-sm text-slate-500 hover:text-slate-900">
                    <span class="group-open:hidden">Ubah</span>
                    <span class="hidden group-open:inline">Tutup</span>
                </summary>

                <div class="px-4 pb-4">
                    <form method="POST" action="{{ route('prices.update', $price) }}">
                        @csrf
                        @method('PUT')

                        <input name="name" type="text" required maxlength="255" value="{{ $price->name }}"
                               class="{{ $inputClass }} mb-2">
                        <input name="price" type="number" step="0.01" min="0" required value="{{ $price->price }}"
                               class="{{ $inputClass }} mb-3">

                        <button type="submit"
                                class="w-full rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-medium text-white hover:bg-slate-800">
                            Simpan
                        </button>
                    </form>

                    <form method="POST" action="{{ route('prices.destroy', $price) }}" class="mt-2"
                          onsubmit="return confirm('Hapus {{ $price->name }}?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="w-full rounded-lg border border-red-200 px-4 py-2.5 text-sm font-medium text-red-600 hover:bg-red-50">
                            Hapus
                        </button>
                    </form>
                </div>
            </details>
        </div>
    @empty
        <div class="rounded-xl border border-dashed border-slate-300 bg-white/60 p-8 text-center">
            <p class="text-sm text-slate-500">
                {{ request('q') ? 'Tidak ada barang yang cocok.' : 'Belum ada harga yang dicatat.' }}
            </p>
        </div>
    @endforelse
</x-app-layout>
