<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectFile;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectFileController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $request->validate([
            'files' => ['required', 'array'],
            'files.*' => [
                'file',
                'max:40960',
                'extensions:'.implode(',', ProjectFile::allowedExtensions()),
            ],
        ], [
            'files.*.extensions' => 'Hanya file Excel, Word, PDF, foto (.jpg, .png, .webp), dan gambar CAD (.dwg, .dxf, .skp) yang didukung.',
            'files.*.max' => 'Ukuran file maksimal 40 MB.',
        ]);

        foreach ($request->file('files') as $file) {
            $extension = strtolower($file->getClientOriginalExtension());

            $project->files()->create([
                'category' => ProjectFile::categoryFor($extension),
                'original_name' => $file->getClientOriginalName(),
                'file_path' => $file->store("projects/{$project->id}", 'local'),
                'extension' => $extension,
                'size' => $file->getSize(),
            ]);
        }

        return back()->with('status', 'File berhasil diunggah.');
    }

    public function download(ProjectFile $file): StreamedResponse
    {
        Gate::authorize('view', $file->project);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless($disk->exists($file->file_path), 404);

        return $disk->download($file->file_path, $file->original_name);
    }

    public function destroy(ProjectFile $file): RedirectResponse
    {
        Gate::authorize('update', $file->project);

        Storage::disk('local')->delete($file->file_path);
        $file->delete();

        return back()->with('status', 'File berhasil dihapus.');
    }
}
