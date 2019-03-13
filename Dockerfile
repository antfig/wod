FROM php:7.2-cli

# install dependencies
RUN apt-get update \
    && apt-get install -y zip unzip git

# install debug
RUN pecl install xdebug-2.6.0 \
 && docker-php-ext-enable xdebug

# install composer
RUN curl -o /tmp/composer-setup.php https://getcomposer.org/installer \
  && curl -o /tmp/composer-setup.sig https://composer.github.io/installer.sig \
  && php -r "if (hash('SHA384', file_get_contents('/tmp/composer-setup.php')) !== trim(file_get_contents('/tmp/composer-setup.sig'))) { unlink('/tmp/composer-setup.php'); echo 'Invalid installer' . PHP_EOL; exit(1); }" \
  && php /tmp/composer-setup.php --no-ansi --install-dir=/usr/local/bin --filename=composer  && rm -rf /tmp/composer-setup.php

# volumes
WORKDIR /app

#CMD ["ls"]
CMD ["php", "wod.php"]
