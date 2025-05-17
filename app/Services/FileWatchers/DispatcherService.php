<?php // app/Services/FileWatchers/DispatcherService.php

namespace App\Services\FileWatchers;

use Illuminate\Support\Facades\Log;
class DispatcherService
{
	/** @var FileWatchServiceInterface[] */
	protected array $services;

	public function __construct(
		protected ImageOptimizerService $image,
		protected JsonWebhookService $json,
		protected TxtAppenderService $txt,
		protected ZipExtractorService $zip,
	) {
		$this->services = [$image, $json, $txt, $zip];
	}

	public function handleModify(string $path): void
	{
			Log::channel('watcher')->info("Modified file: {$path}");

			foreach ($this->services as $service) {
					if (in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), $service->fileTypes())) {
							$service->handle($path);
					}
			}
	}

	protected array $hashes = [];

	public function handleCreate(string $path): void
	{
			if (!is_file($path)) return;

			Log::channel('watcher')->info("Created file: {$path}");

			$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
			$hash = md5_file($path);

			// Check if file existed before with different content
			if (isset($this->hashes[$path]) && $this->hashes[$path] !== $hash) {
					$this->handleModify($path);
			}

			$this->hashes[$path] = $hash;

			foreach ($this->services as $service) {
					if (in_array($ext, $service->fileTypes())) {
							$service->handle($path);
					}
			}
	}


	public function supportedFileTypes(): array
	{
		return collect($this->services)
			->flatMap(fn($s) => $s->fileTypes())
			->unique()
			->values()
			->all();
	}

}
