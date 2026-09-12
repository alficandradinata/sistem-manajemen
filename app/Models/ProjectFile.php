<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProjectFile extends Model
{
    use HasFactory;

    public const CATEGORY_EXTENSIONS = [
        'bq_excel' => ['xls', 'xlsx', 'xlsm', 'csv'],
        'word' => ['doc', 'docx'],
        '2d' => ['dwg', 'dxf'],
        '3d' => ['skp'],
        'pdf' => ['pdf'],
        'foto' => ['jpg', 'jpeg', 'png', 'webp'],
    ];

    public const CATEGORY_LABELS = [
        'bq_excel' => 'BQ Excel',
        'word' => 'Word',
        '2d' => 'Gambar 2D',
        '3d' => 'Gambar 3D',
        'pdf' => 'PDF',
        'foto' => 'Foto',
    ];

    public const PREVIEW_KINDS = [
        'foto' => 'image',
        'pdf' => 'pdf',
        'bq_excel' => 'sheet',
        'word' => 'document',
    ];

    public const INLINE_MIME_TYPES = [
        'pdf' => 'application/pdf',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'png' => 'image/png',
        'webp' => 'image/webp',
    ];

    protected $fillable = [
        'project_id',
        'category',
        'original_name',
        'file_path',
        'extension',
        'size',
    ];

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class);
    }

    public static function categoryFor(string $extension): ?string
    {
        foreach (self::CATEGORY_EXTENSIONS as $category => $extensions) {
            if (in_array(strtolower($extension), $extensions, true)) {
                return $category;
            }
        }

        return null;
    }

    public static function allowedExtensions(): array
    {
        return array_merge(...array_values(self::CATEGORY_EXTENSIONS));
    }

    public function getCategoryLabelAttribute(): string
    {
        return self::CATEGORY_LABELS[$this->category];
    }

    public function getPreviewKindAttribute(): ?string
    {
        return self::PREVIEW_KINDS[$this->category] ?? null;
    }

    public function getInlineMimeTypeAttribute(): ?string
    {
        return self::INLINE_MIME_TYPES[$this->extension] ?? null;
    }

    public function getReadableSizeAttribute(): string
    {
        $bytes = (int) $this->size;

        foreach (['B', 'KB', 'MB', 'GB'] as $unit) {
            if ($bytes < 1024) {
                return round($bytes, $unit === 'B' ? 0 : 1).' '.$unit;
            }
            $bytes /= 1024;
        }

        return round($bytes, 1).' TB';
    }
}
