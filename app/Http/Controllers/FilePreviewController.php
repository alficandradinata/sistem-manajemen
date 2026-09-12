<?php

namespace App\Http\Controllers;

use App\Models\ProjectFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use PhpOffice\PhpSpreadsheet\IOFactory as SpreadsheetReader;
use PhpOffice\PhpWord\Element\Table as WordTable;
use PhpOffice\PhpWord\Element\Text as WordText;
use PhpOffice\PhpWord\Element\TextRun as WordTextRun;
use PhpOffice\PhpWord\IOFactory as WordReader;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Throwable;

class FilePreviewController extends Controller
{
    private const MAX_ROWS = 300;

    private const MAX_COLUMNS = 30;

    public function show(Request $request, ProjectFile $file): View
    {
        Gate::authorize('view', $file->project);

        $data = ['file' => $file, 'error' => null];

        try {
            $data += match ($file->preview_kind) {
                'sheet' => $this->readSheet($file, $request->integer('sheet')),
                'document' => $this->readDocument($file),
                default => [],
            };
        } catch (Throwable $e) {
            $data['error'] = 'File ini tidak bisa ditampilkan. Kemungkinan formatnya tidak standar atau filenya rusak — silakan unduh dan buka di aplikasi aslinya.';
            report($e);
        }

        return view('files.preview', $data);
    }

    public function raw(ProjectFile $file): BinaryFileResponse
    {
        Gate::authorize('view', $file->project);

        abort_if($file->inline_mime_type === null, 404);
        abort_unless(Storage::disk('local')->exists($file->file_path), 404);

        return response()->file(Storage::disk('local')->path($file->file_path), [
            'Content-Type' => $file->inline_mime_type,
            'Content-Disposition' => 'inline; filename="'.addslashes($file->original_name).'"',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function readSheet(ProjectFile $file, int $sheetIndex): array
    {
        $reader = SpreadsheetReader::createReaderForFile($this->absolutePath($file));
        $spreadsheet = $reader->load($this->absolutePath($file));

        $names = $spreadsheet->getSheetNames();
        $sheetIndex = max(0, min($sheetIndex, count($names) - 1));

        $rows = $spreadsheet->getSheet($sheetIndex)->toArray(null, true, true, false);
        $truncated = count($rows) > self::MAX_ROWS;

        $rows = array_map(
            fn (array $row) => array_slice($row, 0, self::MAX_COLUMNS),
            array_slice($rows, 0, self::MAX_ROWS),
        );

        while ($rows !== [] && $this->isBlank(end($rows))) {
            array_pop($rows);
        }

        return [
            'rows' => $rows,
            'sheetNames' => $names,
            'sheetIndex' => $sheetIndex,
            'truncated' => $truncated,
        ];
    }

    private function readDocument(ProjectFile $file): array
    {
        $word = WordReader::load($this->absolutePath($file));
        $blocks = [];

        foreach ($word->getSections() as $section) {
            foreach ($section->getElements() as $element) {
                if ($element instanceof WordTable) {
                    $blocks[] = ['type' => 'table', 'rows' => $this->tableRows($element)];

                    continue;
                }

                $text = $this->elementText($element);

                if (trim($text) !== '') {
                    $blocks[] = ['type' => 'paragraph', 'text' => $text];
                }
            }
        }

        return ['blocks' => $blocks];
    }

    private function tableRows(WordTable $table): array
    {
        $rows = [];

        foreach ($table->getRows() as $row) {
            $cells = [];

            foreach ($row->getCells() as $cell) {
                $text = '';

                foreach ($cell->getElements() as $element) {
                    $text .= $this->elementText($element).' ';
                }

                $cells[] = trim($text);
            }

            $rows[] = $cells;
        }

        return $rows;
    }

    private function elementText(object $element): string
    {
        if ($element instanceof WordText) {
            return $element->getText();
        }

        if ($element instanceof WordTextRun) {
            return implode('', array_map($this->elementText(...), $element->getElements()));
        }

        if (method_exists($element, 'getText')) {
            $text = $element->getText();

            return is_string($text) ? $text : '';
        }

        return '';
    }

    private function isBlank(array $row): bool
    {
        return implode('', array_map(fn ($cell) => trim((string) $cell), $row)) === '';
    }

    private function absolutePath(ProjectFile $file): string
    {
        abort_unless(Storage::disk('local')->exists($file->file_path), 404);

        return Storage::disk('local')->path($file->file_path);
    }
}
