<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class FileWatcher extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:file-watcher';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
		public function handle()
    {
        Watch::path(storage_path('app/watched'))
          ->onFileCreated(function ($path) {
            $this->dispatcher->dispatch($path);
          })
          ->onFileDeleted(fn ($path) => $this->memeRestorer->restore($path))
          ->start();
    }
    public function dispatch(string $path): void
    {
        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

        match($ext) {
            'jpg', 'jpeg' => $this->imageOptimizer->optimize($path),
            'json' => $this->jsonWebhook->send($path),
            'txt' => $this->txtAppender->append($path),
            'zip' => $this->zipExtractor->extract($path),
            default => null,
        };
    }

}
