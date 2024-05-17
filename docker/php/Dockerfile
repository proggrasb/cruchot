FROM php:8.2-fpm-alpine

ARG PHP_MAX_EXECUTION_TIME
ARG WORKDIR_SITE
ARG XDEBUG_REMOTE_HOST
ARG BIN_CONSOLE=$WORKDIR_SITE/bin/console

RUN apk add --update --no-cache curl git libxml2-dev build-base zlib-dev autoconf bash shadow libpq-dev linux-headers supervisor rabbitmq-c-dev \
  && docker-php-ext-install pdo_pgsql \
  && docker-php-ext-install soap

RUN apk --no-cache add pcre-dev ${PHPIZE_DEPS} \
      && pecl install redis \
      && docker-php-ext-enable redis \
      && pecl install amqp \
      && docker-php-ext-enable amqp \
      && apk del pcre-dev ${PHPIZE_DEPS} \
      && rm -rf /tmp/pear

RUN cp "/usr/local/etc/php/php.ini-development" "/usr/local/etc/php/php.ini"


COPY conf/supervisord.conf /etc/supervisord.conf
COPY conf/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

RUN sed -i \
    -e "s/;date.timezone =.*/date.timezone = UTC/" /usr/local/etc/php/php.ini \
    -e "s/;cgi.fix_pathinfo=1/cgi.fix_pathinfo=0/" /usr/local/etc/php/php.ini \
    -e "s/max_execution_time = .*/max_execution_time = ${PHP_MAX_EXECUTION_TIME}/" /usr/local/etc/php/php.ini \
    -e "s/pid =.*/pid = \/var\/run\/php-fpm.pid/" /usr/local/etc/php-fpm.conf \
    -e "s/error_log =.*/error_log = \/proc\/self\/fd\/2/" /usr/local/etc/php-fpm.conf \
    -e "s/;daemonize\s*=\s*yes/daemonize = no/g" /usr/local/etc/php-fpm.conf \
    -e "s/;catch_workers_output = .*/catch_workers_output = yes/" /usr/local/etc/php-fpm.d/www.conf \
    -e "s|\${BIN_CONSOLE}|$BIN_CONSOLE|" /etc/supervisor/conf.d/supervisord.conf \
    && mkdir -p /run/php

#RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
#    && pecl install xdebug \
#    && docker-php-ext-enable xdebug \
#    && apk del -f .build-deps
#
#RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.client_port=9003" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.client_host=${XDEBUG_REMOTE_HOST}" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini \
#    && echo "xdebug.idekey=PHPSTORM" >> /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

#USER www-data

COPY . $WORKDIR_SITE
WORKDIR $WORKDIR_SITE

CMD ["/usr/bin/supervisord", "-n", "-c", "/etc/supervisord.conf"]

EXPOSE 9000
