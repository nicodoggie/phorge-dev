FROM php:8.0-fpm

WORKDIR /srv
ENV TZ=UTC

COPY --chown=www-data:www-data ./phorge /srv/phorge
COPY --chown=www-data:www-data ./arcanist /srv/arcanist

RUN rm -r /usr/local/etc/php-fpm.d

COPY --chown=root:root ./conf/php/php-fpm.d /usr/local/etc/php-fpm.d 
COPY --chown=root:root ./conf/php/php.ini /usr/local/etc/php/php.ini 

RUN apt-get update && apt-get install -y \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  libzip-dev \
  ca-certificates \
  python3-pygments \
  && rm -rf /var/lib/apt/lists \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd opcache mysqli zip

ADD https://pecl.php.net/get/apcu-5.1.21.tgz /
RUN pecl install file:///apcu-5.1.21.tgz \
  && docker-php-ext-enable apcu \
  && rm /apcu-5.1.21.tgz

USER www-data

CMD [ "php-fpm", "-F" ]
