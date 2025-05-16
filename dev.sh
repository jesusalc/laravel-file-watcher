tail -f storage/logs/fs-watcher.log &
php artisan fs:watch
pkill tail
