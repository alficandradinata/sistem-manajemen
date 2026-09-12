<?php

namespace App\Http\Controllers;

use App\Exceptions\DriveImportException;
use App\Models\Project;
use App\Models\ProjectFile;
use App\Services\GoogleDriveImporter;
use App\Support\UploadLimits;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ProjectFileController extends Controller
{
    public function store(Request $request, Project $project): RedirectResponse
    {
        Gate::authorize('update', $project);

        $request->validate([
            'files' => ['required', 'array', 'max:'.UploadLimits::maxFiles()],
            'files.*' => [
                'file',
                'max:'.UploadLimits::maxFileKilobytes(),
                'extensions:'.implode(',', ProjectFile::allowedExtensions()),
            ],
        ], [
            'files.*.extensions' => 'Hanya file Excel, Word, PDF, foto (.jpg, .png, .webp), dan gambar CAD (.dwg, .dxf, .skp) yang didukung.',
            'files.*.max' => 'Ukuran file maksimal '.UploadLimits::readable(UploadLimits::maxFileBytes())
                .'. Untuk file lebih besar, pakai link Google Drive di bawah.',
            'files.max' => 'Maksimal '.UploadLimits::maxFiles().' file sekali unggah.',
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

    public function storeFromDrive(Request $request, Project $project, GoogleDriveImporter $importer): RedirectResponse
    {
        Gate::authorize('update', $project);

        $validated = $request->validate([
            'drive_url' => ['required', 'string', 'max:2000'],
            'mode' => ['required', 'in:download,link'],
        ]);

        $url = trim($validated['drive_url']);

        try {
            $file = $validated['mode'] === 'link'
                ? $importer->describe($url)
                : $importer->fetch($url);

            $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            $category = ProjectFile::categoryFor($extension);

            if ($category === null) {
                throw new DriveImportException("File \"{$file['name']}\" formatnya belum didukung.");
            }

            $path = null;

            if ($validated['mode'] === 'download') {
                $path = "projects/{$project->id}/".Str::random(40).'.'.$extension;
                Storage::disk('local')->writeStream($path, fopen($file['path'], 'r'));
            }
        } catch (DriveImportException $e) {
            throw ValidationException::withMessages(['drive_url' => $e->getMessage()])
                ->errorBag('drive');
        } finally {
            if (isset($file['path'])) {
                @unlink($file['path']);
            }
        }

        $project->files()->create([
            'category' => $category,
            'original_name' => $file['name'],
            'file_path' => $path,
            'drive_url' => $path === null ? $url : null,
            'extension' => $extension,
            'size' => $file['size'],
        ]);

        return back()->with('status', $path === null
            ? "Link \"{$file['name']}\" berhasil disimpan."
            : "\"{$file['name']}\" berhasil diambil dari Drive.");
    }

    public function download(ProjectFile $file): StreamedResponse
    {
        Gate::authorize('view', $file->project);

        abort_if($file->is_link, 404);

        /** @var \Illuminate\Filesystem\FilesystemAdapter $disk */
        $disk = Storage::disk('local');

        abort_unless($disk->exists($file->file_path), 404);

        return $disk->download($file->file_path, $file->original_name);
    }

    public function destroy(ProjectFile $file): RedirectResponse
    {
        Gate::authorize('update', $file->project);

        if (! $file->is_link) {
            Storage::disk('local')->delete($file->file_path);
        }

        $file->delete();

        return back()->with('status', 'File berhasil dihapus.');
    }
}
