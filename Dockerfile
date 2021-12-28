FROM php:8.0-fpm

WORKDIR /srv

COPY --chown=www-data:www-data ./phorge /srv/phorge
COPY --chown=www-data:www-data ./arcanist /srv/arcanist

RUN rm -r /usr/local/etc/php-fpm.d

COPY --chown=root:root ./php/php-fpm.d /usr/local/etc/php-fpm.d 

RUN apt-get update && apt-get install -y \
  libfreetype6-dev \
  libjpeg62-turbo-dev \
  libpng-dev \
  && docker-php-ext-configure gd --with-freetype --with-jpeg \
  && docker-php-ext-install -j$(nproc) gd

USER www-data

CMD [ "php-fpm", "-F" ]
