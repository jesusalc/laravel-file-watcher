<?php
namespace App\Services\FileWatchers;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class MemeRestorerService
{
    public function handle(string $deletedPath): void
    {
        $start = microtime(true);
        $api = 'https://meme-api.com/gimme';

        try {
            Log::channel('watcher')->info("Fetching meme for deleted file: {$deletedPath}");

            $response = Http::timeout(5)->get($api);

            if (!$response->successful()) {
                Log::channel('watcher')->error("Meme API error: HTTP " . $response->status());
                return;
            }

            $data = $response->json();

            $url = $data['url'] ?? null;
            $title = $data['title'] ?? '(untitled)';
            $subreddit = $data['subreddit'] ?? 'unknown';

            if (!$url || !preg_match('/\.(jpg|jpeg|png|gif)$/i', $url)) {
                Log::channel('watcher')->error("Meme API returned no valid image URL");
                return;
            }

            $imageData = Http::timeout(10)->get($url)->body();

            file_put_contents($deletedPath, $imageData);

            $duration = round((microtime(true) - $start) * 1000);
            Log::channel('watcher')->info("Restored meme to {$deletedPath} from r/{$subreddit} - \"{$title}\" ({$duration}ms)");
        } catch (\Throwable $e) {
            $duration = round((microtime(true) - $start) * 1000);
            Log::channel('watcher')->error("Failed to fetch meme image ({$duration}ms): " . $e->getMessage());
        }
    }
}
