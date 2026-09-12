<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Batas Unduhan Google Drive
    |--------------------------------------------------------------------------
    |
    | Ukuran maksimal (MB) file yang boleh disalin dari Google Drive ke server.
    | Turunkan kalau hosting memutus proses sebelum unduhan besar selesai;
    | file di atas batas ini tetap bisa dicatat lewat tombol "Simpan link".
    |
    */

    'drive_max_mb' => (int) env('DRIVE_MAX_MB', 200),

];
