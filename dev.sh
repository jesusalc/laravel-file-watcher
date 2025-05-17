#!/usr/bin/env bash


set -xuEe

function on_exit(){
  kill $(ps aux | grep queue:work | grep -v grep  | awk '{print $2}' | xargs)
  kill $(ps aux | grep  fs:watch| grep -v grep | awk '{print $2}' | xargs)

} # end on_exit

trap 'on_exit' ERR INT EXIT



typeset -i _pidt=0
typeset -i _pidw=0

tail -f storare/logs/fs-watcher.log &
_pidt=$!
php artisan queue:work &
_pidw=$!

php artisan fs:watch
pkill tail
kill ${_pidt}
kill ${_pidw}
kill $(ps aux | grep queue:work | grep -v grep | awk '{print $2}' | xargs)
kill $(ps aux | grep  fs:watch | grep -v grep | awk '{print $2}' | xargs)


#
