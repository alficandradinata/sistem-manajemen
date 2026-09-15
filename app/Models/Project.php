<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Project extends Model
{
    use HasFactory;

    public const STATUSES = [
        'berjalan' => 'Berjalan',
        'pending' => 'Pending',
        'selesai' => 'Selesai',
        'batal' => 'Batal',
    ];

    protected $fillable = [
        'user_id',
        'name',
        'deadline',
        'status',
    ];

    protected $casts = [
        'deadline' => 'date',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function files(): HasMany
    {
        return $this->hasMany(ProjectFile::class);
    }

    /**
     * Project yang sudah selesai atau batal tidak lagi dikejar deadline-nya.
     */
    public function getTracksDeadlineAttribute(): bool
    {
        return $this->deadline !== null
            && ! in_array($this->status, ['selesai', 'batal'], true);
    }

    /**
     * Selisih hari ke deadline: negatif berarti sudah lewat.
     */
    public function getDaysToDeadlineAttribute(): ?int
    {
        if (! $this->tracks_deadline) {
            return null;
        }

        return (int) now()->startOfDay()->diffInDays($this->deadline->startOfDay(), false);
    }

    public function getDeadlineLabelAttribute(): ?string
    {
        $days = $this->days_to_deadline;

        if ($days === null) {
            return null;
        }

        return match (true) {
            $days < 0 => 'lewat '.abs($days).' hari',
            $days === 0 => 'hari ini',
            $days === 1 => 'besok',
            $days <= 7 => $days.' hari lagi',
            default => null,
        };
    }

    public function getDeadlineToneAttribute(): ?string
    {
        $days = $this->days_to_deadline;

        return match (true) {
            $days === null => null,
            $days <= 0 => 'overdue',
            $days <= 7 => 'soon',
            default => null,
        };
    }
}
