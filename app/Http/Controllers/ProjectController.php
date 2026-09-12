<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ProjectController extends Controller
{
    public function index(Request $request): View
    {
        $projects = $request->user()->projects()
            ->when($request->string('q')->trim()->value(), function ($query, $search) {
                $query->where('name', 'like', "%{$search}%");
            })
            ->when($request->string('status')->value(), function ($query, $status) {
                $query->where('status', $status);
            })
            ->orderByRaw('deadline is null')
            ->orderBy('deadline')
            ->latest('id')
            ->get();

        return view('projects.index', [
            'projects' => $projects,
            'statuses' => Project::STATUSES,
        ]);
    }

    public function create(): View
    {
        return view('projects.create', ['statuses' => Project::STATUSES]);
    }

    public function store(Request $request): RedirectResponse
    {
        $project = $request->user()->projects()->create($this->validated($request));

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'Project berhasil dibuat.');
    }

    public function show(Project $project): View
    {
        Gate::authorize('view', $project);

        $project->load('files');

        return view('projects.show', [
            'project' => $project,
            'filesByCategory' => $project->files->groupBy('category'),
            'categoryLabels' => ProjectFile::CATEGORY_LABELS,
        ]);
    }

    public function edit(Project $project): View
    {
        Gate::authorize('update', $project);

        return view('projects.edit', [
            'project' => $project,
            'statuses' => Project::STATUSES,
        ]);
    }

    public function update(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $project->update($this->validated($request));

        return redirect()
            ->route('projects.show', $project)
            ->with('status', 'Project berhasil diperbarui.');
    }

    public function destroy(Project $project): RedirectResponse
    {
        Gate::authorize('delete', $project);

        Storage::disk('local')->deleteDirectory("projects/{$project->id}");
        $project->delete();

        return redirect()
            ->route('projects.index')
            ->with('status', 'Project berhasil dihapus.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'deadline' => ['nullable', 'date'],
            'status' => ['required', 'in:'.implode(',', array_keys(Project::STATUSES))],
        ]);
    }
}
