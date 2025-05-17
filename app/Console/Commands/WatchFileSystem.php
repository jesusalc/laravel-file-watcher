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

    //   Spatie
    //   public function handle(
    //       DispatcherService $dispatcher,
    //       MemeRestorerService $memeRestorer
    //   ) {
    //     $this->logStartup($dispatcher);
    //     $watchPath = storage_path('app/watched');
    //     Watch::path(storage_path('app/watched'))
    //       ->onFileCreated(function ($path) use ($dispatcher) {
    //           Log::channel('watcher')->info("Received {$path}");
    //           $dispatcher->handleCreate($path);
    //       })
    //       ->onFileDeleted(function ($path) use ($memeRestorer) {
    //           Log::channel('watcher')->info("File deleted: {$path}");
    //           $memeRestorer->handle($path);
    //       })
    //       ->start();
    //  }


    public function handle(DispatcherService $dispatcher, MemeRestorerService $memeRestorer)
    {
        if (!function_exists('inotify_init')) {
            $this->error("inotify extension not installed.");
            return Command::FAILURE;
        }

        $this->logStartup($dispatcher);

        $watchPath = storage_path('app/watched');
        $inotify = inotify_init();
        stream_set_blocking($inotify, false);

        $watchDescriptor = inotify_add_watch($inotify, $watchPath, IN_CREATE | IN_MODIFY | IN_DELETE);

        Log::channel('watcher')->info("Watching folder via inotify: {$watchPath}");

        while (true) {
            $events = inotify_read($inotify);
            if ($events) {
                $logged_already = 0;
                foreach ($events as $event) {
                    $filePath = $watchPath . '/' . $event['name'];
                    $mask = $event['mask'];
                    if ($logged_already === 0) {
                        Log::channel('watcher')->info("INOTIFY EVENT: {$event['mask']} on {$filePath}");
                        $logged_already++;
                    }
                    if ($mask & IN_CREATE) {
                        // Log::channel('watcher')->info("Event: IN_CREATE - {$filePath}");
                        $dispatcher->handleCreate($filePath);
                    }

                    if ($mask & IN_MODIFY) {
                        // Log::channel('watcher')->info("Event: IN_MODIFY - {$filePath}");
                        $dispatcher->handleModify($filePath);
                    }

                    if ($mask & IN_DELETE) {
                        // Log::channel('watcher')->info("Event: IN_DELETE - {$filePath}");
                        $memeRestorer->handle($filePath);
                    }
                }
            }

            usleep(250000); // 250ms polling delay to reduce CPU
        }

        inotify_rm_watch($inotify, $watchDescriptor);
        fclose($inotify);
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

        Log::channel('watcher')->info("\033[38;5;164m # THIS SCRIPT NAME\033[38;5;251m: \033[38;5;151m{$app}");
        Log::channel('watcher')->info("\033[38;5;164m # THIS SCRIPT VERSION\033[38;5;251m: \033[38;5;151m{$version}");
        Log::channel('watcher')->info("\033[38;5;164m # THIS SCRIPT RELATIVE PATH\033[38;5;251m: \033[38;5;151m./{$app}/");
        Log::channel('watcher')->info("\033[38;5;164m # THIS SCRIPT ABSOLUTE PATH\033[38;5;251m: \033[38;5;241m\"\033[38;5;151m{$path}/{$app}\033[38;5;241m\"");
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
