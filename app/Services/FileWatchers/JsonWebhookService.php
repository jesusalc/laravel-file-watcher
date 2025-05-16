<?php

namespace App\Services\FileWatchers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Throwable;
use App\Services\FileWatchers\FileWatchServiceInterface;

class JsonWebhookService implements FileWatchServiceInterface
{
    protected string $endpoint;

    public function __construct()
    {
        $this->endpoint = config('services.fswatcher.webhook');
    }

    public function fileTypes(): array
    {
        return ['json'];
    }

    public function handle(string $path): void
    {
        if (!is_readable($path)) {
            Log::channel('watcher')->warning("JSON file unreadable: {$path}");
            return;
        }

        $json = file_get_contents($path);
        $start = microtime(true);

        try {
            Log::channel('watcher')->info("Sending JSON to {$this->endpoint} from {$path}");

            $response = Http::timeout(5)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post($this->endpoint, $json);

            $duration = round((microtime(true) - $start) * 1000);

            if ($response->successful()) {
                Log::channel('watcher')->info("JSON sent successfully from {$path} ({$duration}ms)");
            } else {
                Log::channel('watcher')->error("HTTP {$response->status()} error from {$this->endpoint} ({$duration}ms)");
            }

        } catch (Throwable $e) {
            $duration = round((microtime(true) - $start) * 1000);
            Log::channel('watcher')->error("Failed to send JSON from {$path} ({$duration}ms): " . $e->getMessage());
        }
    }
}
