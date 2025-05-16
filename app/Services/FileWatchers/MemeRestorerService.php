<?php

namespace App\Services\FileWatchers;

use Illuminate\Support\Facades\Http;

class MemeRestorerService
{
    public function handle(string $deletedPath): void
    {
        $response = Http::get('https://meme-api.com/gimme');
        $url = $response->json()['url'] ?? null;

        if ($url) {
            $img = Http::get($url)->body();
            file_put_contents($deletedPath, $img);
        }
    }
}
