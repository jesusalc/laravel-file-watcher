<?php // app/Console/Commands/WatchFileSystem.php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\Watcher\Watch;
use App\Services\FileWatchers\DispatcherService;
use App\Services\FileWatchers\MemeRestorerService;
use Illuminate\Support\Facades\Log;


class WatchFileSystem extends Command
{
    protected $signature = 'fs:watch';
    protected $description = 'Watch filesystem and trigger actions on file events';

    public function handle(
        DispatcherService $dispatcher,
        MemeRestorerService $memeRestorer
    ) {
			$this->logStartup($dispatcher);

      $watchPath = storage_path('app/watched');

      Watch::path(storage_path('app/watched'))
        ->onFileCreated(function ($path) use ($dispatcher) {
            Log::channel('watcher')->info("Received {$path}");
            $dispatcher->handleCreate($path);
        })
        ->onFileDeleted(function ($path) use ($memeRestorer) {
            Log::channel('watcher')->info("File deleted: {$path}");
            $memeRestorer->handle($path);
        })
        ->start();
   }

    protected function logStartup(DispatcherService $dispatcher): void
    {
        $app = config('app.name');
        $version = config('app.version');
        $pid = getmypid();
        $path = base_path();
        $envPath = base_path('.env');
        $corsPath = base_path('.env_cors');

        Log::channel('watcher')->info("Logger initialized");
        Log::channel('watcher')->info(".env loading at: {$envPath}");

        if (file_exists($corsPath)) {
            Log::channel('watcher')->info(".env_cors file found at: {$corsPath}");
        }

        Log::channel('watcher')->info("# THIS SCRIPT NAME: {$app}");
        Log::channel('watcher')->info("# THIS SCRIPT VERSION: {$version}");
        Log::channel('watcher')->info("# THIS SCRIPT RELATIVE PATH: ./{$app}/");
        Log::channel('watcher')->info("# THIS SCRIPT ABSOLUTE PATH: \"{$path}/{$app}\"");
        Log::channel('watcher')->info("PID: {$pid}");

				$corsFile = base_path('.env_cors');
				$origins = file_exists($corsFile)
				    ? array_filter(array_map('trim', file($corsFile)))
				    : [];

				Log::channel('watcher')->info("Allowed origins: " . json_encode($origins));
				Log::channel('watcher')->info("CORS origins loaded successfully.");
				Log::channel('watcher')->info("Allowed cors_origins: " . json_encode($origins));

        Log::channel('watcher')->info("File Observer starting with PID: {$pid}");
        Log::channel('watcher')->info("Starting 8 workers");
        Log::channel('watcher')->info("File Observer observing folder at " . storage_path('app/watched'));

		    $types = $dispatcher->supportedFileTypes();
				Log::channel('watcher')->info("File Observer observing file types at " . implode(' ', $types));

    }

}
