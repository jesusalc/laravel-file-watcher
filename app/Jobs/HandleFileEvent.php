<?php

namespace App\Jobs;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use App\Services\FileWatchers\DispatcherService;
use App\Services\FileWatchers\MemeRestorerService;

class HandleFileEvent implements ShouldQueue
{
    use Queueable;
    protected string $path;
    protected string $event;
    /**
     * Create a new job instance.
     */
    public function __construct(string $event, string $path)
    {
        $this->event = $event;
        $this->path = $path;
    }

    /**
     * Execute the job.
     */
    public function  handle(DispatcherService $dispatcher, MemeRestorerService $memeRestorer)
    {
        match ($this->event) {
            'create' => $dispatcher->handleCreate($this->path),
            'modify' => $dispatcher->handleModify($this->path),
            'delete' => $memeRestorer->handle($this->path),
            default => null,
        };
    }
}
