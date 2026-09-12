<?php

namespace App\Services;

use App\Exceptions\DriveImportException;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Http;

class GoogleDriveImporter
{
    public static function maxBytes(): int
    {
        return config('datalaila.drive_max_mb') * 1_048_576;
    }

    /**
     * Downloads to a temp file; the caller is responsible for deleting it.
     *
     * @return array{name: string, path: string, size: int}
     */
    public function fetch(string $url): array
    {
        ['name' => $name, 'size' => $declaredSize, 'downloadUrl' => $downloadUrl] = $this->describe($url);

        if ($declaredSize > self::maxBytes()) {
            throw new DriveImportException(
                'File berukuran '.$this->readable($declaredSize).', melebihi batas '
                .$this->readable(self::maxBytes()).'. Simpan link-nya saja.'
            );
        }

        $temp = tempnam(sys_get_temp_dir(), 'drive');

        $response = $this->request()->sink($temp)->get($downloadUrl);

        if (! $response->successful()) {
            @unlink($temp);

            throw new DriveImportException('Gagal mengunduh file dari Drive. Coba lagi sebentar lagi.');
        }

        $size = filesize($temp);

        if ($size === 0) {
            @unlink($temp);

            throw new DriveImportException('File yang diambil kosong.');
        }

        if ($size > self::maxBytes()) {
            @unlink($temp);

            throw new DriveImportException(
                'File berukuran '.$this->readable($size).', melebihi batas '
                .$this->readable(self::maxBytes()).'.'
            );
        }

        return ['name' => $name, 'path' => $temp, 'size' => $size];
    }

    /**
     * Reads name and size without transferring the file.
     *
     * @return array{name: string, size: int, downloadUrl: string}
     */
    public function describe(string $url): array
    {
        [$type, $id] = $this->parse($url);
        $downloadUrl = $this->downloadUrl($type, $id);

        $head = $this->request()->head($downloadUrl);

        $this->guardResponse($head);

        return [
            'name' => $this->filename($head->header('Content-Disposition')),
            'size' => (int) $head->header('Content-Length'),
            'downloadUrl' => $downloadUrl,
        ];
    }

    private function request(): \Illuminate\Http\Client\PendingRequest
    {
        return Http::timeout(600)
            ->connectTimeout(30)
            ->withHeaders(['User-Agent' => 'Mozilla/5.0'])
            ->throw(function () {
                throw new DriveImportException('Gagal menghubungi Google Drive. Periksa koneksi internet lalu coba lagi.');
            });
    }

    private function guardResponse(\Illuminate\Http\Client\Response $response): void
    {
        if (! $response->successful()) {
            throw new DriveImportException('File tidak bisa diambil dari Drive. Pastikan link-nya disetel "siapa saja yang punya link".');
        }

        if (str_contains((string) $response->header('Content-Type'), 'text/html')) {
            throw new DriveImportException('Google menolak mengunduh file ini. Biasanya karena aksesnya dibatasi — ubah setelan berbagi jadi "siapa saja yang punya link", atau unduh manual lalu unggah dari HP.');
        }
    }

    /**
     * @return array{0: string, 1: string}
     */
    private function parse(string $url): array
    {
        $patterns = [
            'sheet' => '#docs\.google\.com/spreadsheets/d/([a-zA-Z0-9_-]{10,})#',
            'doc' => '#docs\.google\.com/document/d/([a-zA-Z0-9_-]{10,})#',
            'binary' => '#drive\.google\.com/file/d/([a-zA-Z0-9_-]{10,})#',
        ];

        foreach ($patterns as $type => $pattern) {
            if (preg_match($pattern, $url, $matches)) {
                return [$type, $matches[1]];
            }
        }

        if (preg_match('#drive\.google\.com/(?:open|uc)\?.*\bid=([a-zA-Z0-9_-]{10,})#', $url, $matches)) {
            return ['binary', $matches[1]];
        }

        if (preg_match('#drive\.google\.com/drive/folders/#', $url)) {
            throw new DriveImportException('Ini link folder. Sistem baru bisa mengambil link file satuan — buka foldernya di Drive, lalu salin link tiap filenya.');
        }

        if (preg_match('#docs\.google\.com/presentation/d/#', $url)) {
            throw new DriveImportException('Google Slides belum didukung. Unduh sebagai PDF lalu unggah manual.');
        }

        throw new DriveImportException('Link tidak dikenali sebagai link Google Drive.');
    }

    private function downloadUrl(string $type, string $id): string
    {
        return match ($type) {
            'sheet' => "https://docs.google.com/spreadsheets/d/{$id}/export?format=xlsx",
            'doc' => "https://docs.google.com/document/d/{$id}/export?format=docx",
            'binary' => "https://drive.usercontent.google.com/download?id={$id}&export=download&confirm=t",
        };
    }

    private function filename(?string $disposition): string
    {
        if ($disposition !== null
            && preg_match("#filename\*=UTF-8''([^;]+)#i", $disposition, $matches)) {
            return basename(urldecode(trim($matches[1], '"')));
        }

        if ($disposition !== null
            && preg_match('#filename="?([^";]+)"?#i', $disposition, $matches)) {
            return basename(trim($matches[1]));
        }

        throw new DriveImportException('Nama file tidak terbaca dari Drive. Unduh manual lalu unggah dari HP.');
    }

    private function readable(int $bytes): string
    {
        return round($bytes / 1_048_576).' MB';
    }
}
