<?php

namespace App\Services\FileWatchers;

use Illuminate\Support\Facades\Http;

class TxtAppenderService
{
	public function fileTypes(): array
	{
		return ['txt'];
	}

	public function append(string $path): void
	{
		$response = Http::get('https://baconipsum.com/api/?type=meat-and-filler&paras=1');
		$text = $response->json()[0] ?? '';

		file_put_contents($path, "\n" . $text, FILE_APPEND);
	}
}
