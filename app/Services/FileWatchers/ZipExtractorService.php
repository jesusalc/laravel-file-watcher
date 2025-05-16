<?php

namespace App\Services\FileWatchers;

use ZipArchive;

class ZipExtractorService
{
	public function fileTypes(): array
	{
		return ['zip'];
	}

	public function extract(string $path): void
	{
		$zip = new ZipArchive();
		if ($zip->open($path) === true) {
			$zip->extractTo(dirname($path));
			$zip->close();
		}
	}
}
