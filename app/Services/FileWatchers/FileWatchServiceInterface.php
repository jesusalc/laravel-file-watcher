<?php

namespace App\Services\FileWatchers;

interface FileWatchServiceInterface
{
    public function fileTypes(): array;
    public function handle(string $path): void;
}
