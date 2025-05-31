FROM php:8.2-cli

# Installe les extensions nécessaires pour Symfony et MongoDB
RUN apt-get update && apt-get install -y \
    git unzip libzip-dev libicu-dev libonig-dev libxml2-dev libcurl4-openssl-dev pkg-config libssl-dev \
    libpng-dev libjpeg-dev libfreetype6-dev gnupg libyaml-dev libpq-dev libcurl4-openssl-dev libmongodb-dev \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install zip intl mbstring xml curl

# Installe Composer
RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer

WORKDIR /app

COPY . .

RUN composer install --no-interaction

EXPOSE 10000

CMD ["php", "-S", "0.0.0.0:10000", "-t", "public"]
