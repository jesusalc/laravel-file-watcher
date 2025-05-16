<?php // app/Services/FileWatchers/DispatcherService.php

namespace App\Services\FileWatchers;

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

	public function handleCreate(string $path): void
	{
		if (!is_file($path)) return;

		$ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

		foreach ($this->services as $service) {
			if (in_array($ext, $service->fileTypes())) {
				$service->handle($path);
				break;
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
