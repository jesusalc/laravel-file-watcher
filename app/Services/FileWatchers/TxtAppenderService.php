<?php

namespace App\Services\FileWatchers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;

class TxtAppenderService implements FileWatchServiceInterface
{
    public function fileTypes(): array
    {
        return ['txt'];
    }

    public function handle(string $path): void
    {
        if (!is_writable($path)) {
            Log::channel('watcher')->warning("TXT file not writable: {$path}");
            return;
        }

        $start = microtime(true);
				$api = config('services.bacon.url');

        try {
            Log::channel('watcher')->info("Requesting Bacon Ipsum for {$path}");

            $response = Http::timeout(5)->get($api);

            if (!$response->successful()) {
                Log::channel('watcher')->error("Bacon Ipsum API returned HTTP {$response->status()}");
                return;
            }

            $data = $response->json();

            if (!is_array($data)) {
                Log::channel('watcher')->error("Bacon Ipsum API response invalid format");
                return;
            }

            $appendText = "\n\n" . implode("\n\n", $data) . "\n";
            file_put_contents($path, $appendText, FILE_APPEND);

            $duration = round((microtime(true) - $start) * 1000);
            Log::channel('watcher')->info("Appended Bacon Ipsum to {$path} ({$duration}ms)");
        } catch (Throwable $e) {
            $duration = round((microtime(true) - $start) * 1000);
            Log::channel('watcher')->error("Failed to append Bacon Ipsum ({$duration}ms): " . $e->getMessage());
        }
    }
}
