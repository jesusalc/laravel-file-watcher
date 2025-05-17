#!/usr/bin/env bash

set -uEex
clear

php artisan queue:work &


function on_exit(){
  kill $(ps aux | grep queue:work | grep -v grep | awk '{print $2}' | xargs)
	kill $(ps aux | grep  fs:watch | grep -v grep  | awk '{print $2}' | xargs)

} # end on_exit

trap 'on_exit' ERR INT EXIT

php artisan test --filter=TxtAppenderServiceTest
php artisan test --filter=JsonWebhookServiceTest
php artisan test --filter=MemeRestorerServiceTest
printf '\xFF\xD8\xFF\xDB' > tests/Unit/Services/FileWatchers/test.jpg
php artisan test --filter=ImageOptimizerServiceTest
rm -rf tests/Unit/Services/FileWatchers/test.jpg
php artisan test --filter=ZipExtractorServiceTest



#
