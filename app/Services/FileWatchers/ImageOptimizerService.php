<?php


namespace App\Services\FileWatchers;

use Illuminate\Support\Facades\Log;
use Spatie\ImageOptimizer\OptimizerChainFactory;

class ImageOptimizerService
{
	protected array $supported = ['jpg', 'jpeg', 'png', 'gif', 'svg'];

	public function fileTypes(): array
	{
			return $this->supported;
	}

	public function handle(string $path): void
	{
			$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

			if (!in_array($ext, $this->supported, true)) {
					Log::channel('watcher')->error("Image type '{$ext}' not supported for optimization: {$path}");
					return;
			}

			$optimizer = OptimizerChainFactory::create();
			$optimizer->optimize($path);
	}
}
