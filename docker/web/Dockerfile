FROM docker.io/php:7.3-apache
# FROM php:5.6-apache

RUN apt-get update -y && apt-get install -y git
RUN install -o nobody -g nogroup -m 0755 -d /var/www/bms/public/ && \
    install -o nobody -g nogroup -m 0755 -d /var/www/bms/fuel/ && \
    install -o nobody -g nogroup -m 0755 -d /var/www/bms/fuel/app/ && \
    install -o nobody -g nogroup -m 0755 -d /var/www/bms/fuel/app/cache/ && \
    ls -lhtr "$PHP_INI_DIR"

# TODO: copy php.ini
COPY apprepo/conf/bms.ini $PHP_INI_DIR/conf.d/

# COPY apprepo/conf/httpd/virtualhost-00.conf   /etc/apache2/sites-enabled/
# COPY apprepo/conf/httpd/virtualhost-bms.conf /etc/apache2/sites-enabled/
COPY etc/httpd/virtualhost-00.conf   /etc/apache2/sites-enabled/
COPY etc/httpd/virtualhost-bms.conf /etc/apache2/sites-enabled/

COPY apprepo/fuel/             /var/www/bms/fuel/
COPY apprepo/public/           /var/www/bms/public/
COPY apprepo/public/index.php /var/www/bms/public/index.php
COPY apprepo/CHANGELOG.md     /var/www/bms/CHANGELOG.md
COPY apprepo/oil              /var/www/bms/oil

# Install composer
WORKDIR /var/www/bms
RUN curl -s https://getcomposer.org/installer | php
COPY apprepo/composer.json /var/www/bms
RUN sed -i.bak -e '/php-coveralls/d' /var/www/bms/composer.json && \
    php composer.phar update --no-dev --prefer-dist && \
    php composer.phar install --no-dev
# NOTE: php composer.phar update --no-dev --prefer-dist
# COPY src/ /var/www/html/

# Use the default production configuration
RUN mv "$PHP_INI_DIR/php.ini-development" "$PHP_INI_DIR/php.ini" && \
    ls -lhtr "$PHP_INI_DIR/conf.d"
