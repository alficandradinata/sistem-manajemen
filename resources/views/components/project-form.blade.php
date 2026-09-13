@props(['project' => null, 'statuses'])

@if ($errors->any())
    <div class="alert alert-error">{{ $errors->first() }}</div>
@endif

<label for="name" class="field-label">Nama project</label>
<input id="name" name="name" type="text" required maxlength="255"
       value="{{ old('name', $project?->name) }}" class="field-input mb-4">

<label for="deadline" class="field-label">
    Deadline <span class="font-normal text-stone-400">— opsional</span>
</label>
<input id="deadline" name="deadline" type="date"
       value="{{ old('deadline', $project?->deadline?->format('Y-m-d')) }}" class="field-input mb-4">

<label for="status" class="field-label">Status</label>
<select id="status" name="status" required class="field-input mb-5">
    @foreach ($statuses as $value => $label)
        <option value="{{ $value }}" @selected(old('status', $project?->status) === $value)>{{ $label }}</option>
    @endforeach
</select>
