set -uEex
clear
php artisan test --filter=TxtAppenderServiceTest
php artisan test --filter=JsonWebhookServiceTest
php artisan test --filter=MemeRestorerServiceTest
printf '\xFF\xD8\xFF\xDB' > tests/Unit/Services/FileWatchers/test.jpg
php artisan test --filter=ImageOptimizerServiceTest
rm -rf tests/Unit/Services/FileWatchers/test.jpg
php artisan test --filter=ZipExtractorServiceTest
