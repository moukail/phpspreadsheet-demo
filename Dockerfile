FROM php:8.1-cli-alpine3.16

RUN apk --update --no-cache add bash gcc g++ make autoconf libzip-dev libpng-dev
RUN docker-php-ext-install -j$(nproc) gd zip
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && mv /root/.symfony5/bin/symfony /usr/bin/symfony

RUN echo $'zend_extension=xdebug.so \n\
xdebug.mode=debug,profile,coverage,develop \n\
xdebug.start_with_request=trigger \n\
xdebug.discover_client_host=true \n\
xdebug.client_host=host.docker.internal \n\
xdebug.idekey=PHPSTORM \n\
xdebug.file_link_format="phpstorm://open?file=%f&line=%l" '\
> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/backend

ADD docker-init.sh /home/
RUN chmod +x /home/docker-init.sh
CMD bash /home/docker-init.sh