@props(['status'])

@php
    $styles = [
        'berjalan' => 'bg-blue-50 text-blue-700 ring-blue-200',
        'pending' => 'bg-amber-50 text-amber-700 ring-amber-200',
        'selesai' => 'bg-emerald-50 text-emerald-700 ring-emerald-200',
        'batal' => 'bg-slate-100 text-slate-500 ring-slate-200',
    ];
@endphp

<span {{ $attributes->merge([
    'class' => 'inline-flex items-center rounded-md px-2 py-0.5 text-xs font-medium ring-1 ring-inset '.$styles[$status],
]) }}>
    {{ \App\Models\Project::STATUSES[$status] }}
</span>
