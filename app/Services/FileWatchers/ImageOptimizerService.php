<?php


namespace App\Services\FileWatchers;

use Spatie\ImageOptimizer\OptimizerChainFactory;

class ImageOptimizerService
{
	public function fileTypes(): array
	{
		return ['jpg', 'jpeg'];
	}

	public function handle(string $path): void
	{
		$optimizer = OptimizerChainFactory::create();
		$optimizer->optimize($path);
	}
}
