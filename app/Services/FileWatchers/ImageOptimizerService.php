<?php


namespace App\Services\FileWatchers;

use Illuminate\Support\Facades\Log;
use Spatie\ImageOptimizer\OptimizerChainFactory;
use Throwable;

class ImageOptimizerService implements FileWatchServiceInterface
{
    protected array $spatieSupported = ['jpg', 'jpeg', 'png'];
    protected array $imagemagickSupported = ['webp', 'gif', 'svg', 'ico', 'tiff', 'tif', 'bmp'];

    public function fileTypes(): array
    {
        return array_merge($this->spatieSupported, $this->imagemagickSupported);
    }

    public function handle(string $path): void
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $start = microtime(true);

        try {
            if (in_array($ext, $this->spatieSupported, true)) {
                $optimizer = OptimizerChainFactory::create();
                $optimizer->optimize($path);
                $this->logSuccess($path, $start, 'Spatie');
            } elseif (in_array($ext, $this->imagemagickSupported, true)) {
                $escaped = escapeshellarg($path);
                $cmd = "magick $escaped -strip -interlace Plane -gaussian-blur 0.05 -quality 85% $escaped";
                exec($cmd, $output, $code);

                if ($code === 0) {
                    $this->logSuccess($path, $start, 'ImageMagick');
                } else {
                    Log::channel('watcher')->error("ImageMagick failed for: {$path}");
                }
            } else {
                Log::channel('watcher')->warning("Skipped unsupported image format: {$path}");
            }
        } catch (Throwable $e) {
            Log::channel('watcher')->error("Image optimization failed for {$path}: " . $e->getMessage());
        }
    }

    protected function logSuccess(string $path, float $start, string $tool): void
    {
        $duration = round((microtime(true) - $start) * 1000);
        Log::channel('watcher')->info("Optimized ({$tool}) {$path} in {$duration}ms");
    }
}

