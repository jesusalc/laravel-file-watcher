set -xuEe
sudo dnf install jpegoptim optipng pngquant gifsicle -y
npm install -g svgo
pecl install inotify
php -i | grep php.ini
echo "extension=inotify" >> "${HOME}/../linuxbrew/.linuxbrew/etc/php/8.4/php.ini"
sudo dnf install php-pecl-inotify -y
sudo dnf install -y imagemagick

#
