FROM php:8.2-fpm

COPY php.ini /usr/local/etc/php/

RUN apt-get update \
	&& apt-get install -y \
	libzip-dev \
	zlib1g-dev \
	libonig-dev \
	pkg-config \
	&& docker-php-ext-install pdo_mysql mbstring bcmath zip \
	&& apt-get clean \
	&& rm -rf /var/lib/apt/lists/*

RUN curl -sS https://getcomposer.org/installer | php \
	&& mv composer.phar /usr/local/bin/composer

WORKDIR /var/www
