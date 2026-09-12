<?php

namespace App\Support;

class UploadLimits
{
    /**
     * The largest single file PHP will actually accept on this server.
     */
    public static function maxFileBytes(): int
    {
        $limits = array_filter([
            self::bytes(ini_get('upload_max_filesize')),
            self::bytes(ini_get('post_max_size')),
        ]);

        return $limits === [] ? 2 * 1024 * 1024 : min($limits);
    }

    public static function maxFileKilobytes(): int
    {
        return intdiv(self::maxFileBytes(), 1024);
    }

    public static function maxFiles(): int
    {
        return (int) ini_get('max_file_uploads') ?: 20;
    }

    public static function readable(int $bytes): string
    {
        return $bytes >= 1_073_741_824
            ? round($bytes / 1_073_741_824, 1).' GB'
            : round($bytes / 1_048_576).' MB';
    }

    /**
     * PHP size shorthand ("40M", "1G", "512K") to bytes. Zero means unlimited.
     */
    private static function bytes(string|false $value): int
    {
        $value = trim((string) $value);

        if ($value === '') {
            return 0;
        }

        $number = (int) $value;

        return match (strtolower(substr($value, -1))) {
            'g' => $number * 1_073_741_824,
            'm' => $number * 1_048_576,
            'k' => $number * 1024,
            default => $number,
        };
    }
}
