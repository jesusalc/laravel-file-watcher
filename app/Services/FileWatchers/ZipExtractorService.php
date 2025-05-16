<?php

namespace App\Services\FileWatchers;

use ZipArchive;
use Illuminate\Support\Facades\Log;

class ZipExtractorService implements FileWatchServiceInterface
{
    public function fileTypes(): array
    {
        return ['zip'];
    }

    public function handle(string $path): void
    {
        if (!is_readable($path)) {
            Log::channel('watcher')->warning("ZIP file unreadable: {$path}");
            return;
        }

        $zip = new ZipArchive();
        $start = microtime(true);

        if ($zip->open($path) === true) {
            $extractTo = pathinfo($path, PATHINFO_DIRNAME);
            $zip->extractTo($extractTo);
            $zip->close();

            $duration = round((microtime(true) - $start) * 1000);
            Log::channel('watcher')->info("Extracted ZIP: {$path} to {$extractTo} ({$duration}ms)");
        } else {
            Log::channel('watcher')->error("Failed to open ZIP file: {$path}");
        }
    }
}
