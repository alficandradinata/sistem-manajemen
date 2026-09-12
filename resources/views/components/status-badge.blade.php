@props(['status'])

@php
    $styles = [
        'berjalan' => 'bg-blue-100 text-blue-800',
        'pending' => 'bg-amber-100 text-amber-800',
        'selesai' => 'bg-emerald-100 text-emerald-800',
        'batal' => 'bg-slate-200 text-slate-600',
    ];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium '.$styles[$status]]) }}>
    {{ \App\Models\Project::STATUSES[$status] }}
</span>
