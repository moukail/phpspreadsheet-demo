FROM php:8.2-cli-alpine3.17

RUN apk --update --no-cache add bash gcc g++ make autoconf linux-headers libzip-dev libpng-dev
RUN docker-php-ext-install -j$(nproc) gd zip
RUN pecl install xdebug && docker-php-ext-enable xdebug

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/bin/ --filename=composer

# Symfony CLI
RUN wget https://get.symfony.com/cli/installer -O - | bash && mv /root/.symfony5/bin/symfony /usr/bin/symfony

RUN echo $'zend_extension=xdebug.so \n\
xdebug.mode=debug \n\
xdebug.start_with_request=trigger \n\
xdebug.client_host=host.docker.internal'\
> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

WORKDIR /var/www/backend

ADD docker-init.sh /home/
RUN chmod +x /home/docker-init.sh
CMD bash /home/docker-init.sh