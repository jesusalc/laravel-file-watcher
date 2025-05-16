# Laravel File System Watcher


A modular Laravel 10+ application that monitors a filesystem directory for file changes and performs dynamic actions based on file type.

---
### ✅Run

./dev.sh



## 📦 Features

- ✅ Monitor file **creation** and **deletion**
- 🖼 Automatically optimize `.jpg` / `.jpeg` files for web
- 🥓 Append Bacon Ipsum to `.txt` files
- 🌐 Send `.json` files to an HTTP endpoint
- 📦 Extract `.zip` files
- 😂 Replace deleted files with meme images from the Meme API
- 🧪 Fully unit-tested with service-level isolation
- 🔁 Cleanly extendable for future file types

---

## 🧠 Conceptual Overview

This application was designed around the following principles:

### ✅ Modular Service Structure

Each file type is handled by its own dedicated service class. Every service implements a common interface:

```php
interface FileWatchServiceInterface {
    public function fileTypes(): array;
    public function handle(string $path): void;
}
This makes it trivial to add new file watchers without altering existing logic.
```


### ✅Structure:
```
app/
├── Console/
│   └── Commands/
│       └── WatchFileSystem.php
├── Services/
│   └── FileWatchers/
│       ├── DispatcherService.php
│       ├── ImageOptimizerService.php
│       ├── JsonWebhookService.php
│       ├── TxtAppenderService.php
│       ├── ZipExtractorService.php
│       └── MemeRestorerService.php
```
### ✅ Testing:
```
./tests.bash

tests/
└── Unit/
    └── Services/
        └── FileWatchers/
            ├── ImageOptimizerServiceTest.php
            ├── JsonWebhookServiceTest.php
            ├── TxtAppenderServiceTest.php
            ├── ZipExtractorServiceTest.php
            └── MemeRestorerServiceTest.php

```
### ✅ Central Dispatcher

A single DispatcherService is responsible for routing file events to the correct service based on file extension. This separation of concerns keeps the watcher logic thin and maintainable.

### ✅ Clear Logging

A custom Monolog channel (watcher) provides structured, timestamped log entries that are easy to tail or parse


### ⚠️ Challenges Encountered

## 1. File Modification Tracking

Spatie's file-system-watcher only supports onFileCreated() and onFileDeleted() — no native onFileModified().

Solution: Simulated file modification detection was scoped out for now. It can be added later using:

    php-inotify (Linux-only)

    Scheduled checksum-based polling

    Replacing the watcher backend


## 2. Handling Unstable APIs

APIs like Meme API and Bacon Ipsum may be slow, fail, or return invalid data.

Solution: All API calls:

    Use timeout + structured error logging

    Include response duration in logs

    Validate expected JSON fields

## 3. Optimizing Very Small Images

Some JPEGs were too small to optimize (e.g. 10×10 test images).

Solution: Tests use realistic image sizes and assert size reductions only when measurable.


### 🔧 Extending the Watcher

To add a new file watcher (e.g. for .csv, .mp4, .pdf):

    Create a New Service
```php
class CsvArchiverService implements FileWatchServiceInterface {
    public function fileTypes(): array { return ['csv']; }
    public function handle(string $path): void {
        // your logic here
    }
}
```
    Register It in DispatcherService
```php
$this->services = [
    $image, $json, $txt, $zip, $csv
];

```



### 🚀 Future Enhancements

    Modify detection support (via polling or inotify)

    Watcher service auto-discovery via tagged services

    .meta.json metadata tracking per file

    File quarantine mode

    Web UI dashboard for logs/status

### ✅ Running the Watcher
```
php artisan fs:watch
```
Logs output to:
```
storage/logs/fs-watcher.log
```
### 🧪 Running Tests
```
php artisan test
```
### 👥 Credits

Built with Laravel 10, Spatie File Watcher, Guzzle, and clean architectural separation.
### 📝 License

MIT
