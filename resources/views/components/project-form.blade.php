@props(['project' => null, 'statuses'])

@if ($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<label for="name" class="field-label">Nama project</label>
<input id="name" name="name" type="text" required maxlength="255"
       value="{{ old('name', $project?->name) }}" class="field-input mb-4">

<div class="mb-5 grid gap-4 sm:grid-cols-2">
    <div>
        <label for="deadline" class="field-label">
            Deadline <span class="font-normal text-zinc-400 dark:text-zinc-600">opsional</span>
        </label>
        <input id="deadline" name="deadline" type="date"
               value="{{ old('deadline', $project?->deadline?->format('Y-m-d')) }}" class="field-input font-mono text-[13px]">
    </div>

    <div>
        <label for="status" class="field-label">Status</label>
        <select id="status" name="status" required class="field-input">
            @foreach ($statuses as $value => $label)
                <option value="{{ $value }}" @selected(old('status', $project?->status) === $value)>{{ $label }}</option>
            @endforeach
        </select>
    </div>
</div>
