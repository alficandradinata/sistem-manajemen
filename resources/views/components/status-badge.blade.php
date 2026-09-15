@props(['status'])

@php
    $dot = [
        'berjalan' => 'bg-blue-500',
        'pending' => 'bg-amber-500',
        'selesai' => 'bg-emerald-500',
        'batal' => 'bg-zinc-300 dark:bg-zinc-600',
    ][$status];

    $text = [
        'berjalan' => 'text-zinc-700 dark:text-zinc-300',
        'pending' => 'text-zinc-700 dark:text-zinc-300',
        'selesai' => 'text-zinc-700 dark:text-zinc-300',
        'batal' => 'text-zinc-400 dark:text-zinc-500',
    ][$status];
@endphp

<span {{ $attributes->merge(['class' => 'status '.$text]) }}>
    <span class="status-dot {{ $dot }}"></span>
    {{ \App\Models\Project::STATUSES[$status] }}
</span>
