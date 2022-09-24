#!/usr/bin/env bash

sudo dnf makecache --refresh
sudo dnf -y update
sudo dnf -y install unzip libxml2-devel sqlite-devel libcurl-devel libpng-devel  #libsodium-devel
sudo dnf --enablerepo=crb -y install libzip-devel oniguruma-devel

wget http://nl1.php.net/distributions/php-8.1.10.tar.gz
tar -xzf php-8.1.10.tar.gz
cd php-8.1.10

./configure --sysconfdir=/etc/php/8.1/cli --with-config-file-path=/etc/php/8.1/cli --with-config-file-scan-dir=/etc/php/8.1/cli/conf.d \
    --disable-cgi --enable-cli --prefix=/usr --with-openssl --with-curl --with-zip --with-zlib --enable-mbstring --enable-gd --enable-ftp # --with-sodium

make -j$(nproc) && sudo make install

sudo mv /usr/bin/php /usr/bin/php8.1 && sudo ln -s /usr/bin/php8.1 /usr/bin/php
sudo mkdir -p /etc/php/8.1/cli/conf.d
cp php.ini-production /etc/php/8.1/cli/php.ini

cd .. && rm -rf php-8.1.10 php-8.1.10.tar.gz

php -v
# Composer
curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Symfony CLI
#  wget https://github.com/symfony-cli/symfony-cli/releases/latest/download/symfony-cli_linux_amd64.tar.gz
wget https://get.symfony.com/cli/installer -O - | bash && sudo mv ~/.symfony5/bin/symfony /usr/bin/symfony
