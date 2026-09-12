@props(['project' => null, 'statuses'])

@if ($errors->any())
    <div class="mb-4 rounded-lg border border-red-200 bg-red-50 px-3 py-2 text-sm text-red-700">
        {{ $errors->first() }}
    </div>
@endif

<label for="name" class="block text-sm font-medium text-slate-700">Nama project</label>
<input id="name" name="name" type="text" required maxlength="255"
       value="{{ old('name', $project?->name) }}"
       class="mt-1 mb-4 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base shadow-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none">

<label for="deadline" class="block text-sm font-medium text-slate-700">Deadline <span class="font-normal text-slate-400">(opsional)</span></label>
<input id="deadline" name="deadline" type="date"
       value="{{ old('deadline', $project?->deadline?->format('Y-m-d')) }}"
       class="mt-1 mb-4 block w-full rounded-lg border border-slate-300 px-3 py-2.5 text-base shadow-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none">

<label for="status" class="block text-sm font-medium text-slate-700">Status</label>
<select id="status" name="status" required
        class="mt-1 mb-5 block w-full rounded-lg border border-slate-300 bg-white px-3 py-2.5 text-base shadow-sm focus:border-slate-900 focus:ring-1 focus:ring-slate-900 focus:outline-none">
    @foreach ($statuses as $value => $label)
        <option value="{{ $value }}" @selected(old('status', $project?->status) === $value)>{{ $label }}</option>
    @endforeach
</select>
