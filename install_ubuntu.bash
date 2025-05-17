set -xuEe
sudo apt-get install jpegoptim optipng pngquant gifsicle svgo -y
npm install -g svgo
pecl install inotify
php -i | grep php.ini
echo "extension=inotify" >> "${HOME}/../linuxbrew/.linuxbrew/etc/php/8.4/php.ini"
