set -uEex
clear
php artisan test --filter=TxtAppenderServiceTest
php artisan test --filter=JsonWebhookServiceTest
php artisan test --filter=MemeRestorerServiceTest
php artisan test --filter=ImageOptimizerServiceTest
php artisan test --filter=ZipExtractorServiceTest
