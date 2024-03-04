FROM php:8.1-fpm

# Mettre à jour les paquets et installer les dépendances requises
# RUN apt-get update && apt-get install -y \
#         git \
#         unzip \
#         libzip-dev \
#         libpq-dev \
#         && docker-php-ext-install zip \
#         && docker-php-ext-install pdo pdo_pgsql \
#         && rm -rf /var/lib/apt/lists/*

RUN apt-get update && apt-get install -y \
        git \
        unzip \
        libzip-dev \
        libpq-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libpng-dev \
        libwebp-dev \
        && rm -rf /var/lib/apt/lists/* \
        && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
        && docker-php-ext-install -j$(nproc) gd \
        && docker-php-ext-install zip \
        && docker-php-ext-install pdo pdo_pgsql

# Installer Composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php \
        && php composer-setup.php --install-dir=/usr/local/bin --filename=composer \
        && rm composer-setup.php

# Installer Node.js et npm
RUN curl -sL https://deb.nodesource.com/setup_14.x | bash - && \
        apt-get install -y nodejs npm -y

WORKDIR /var/www/symfony

# Assurez-vous que le répertoire des logs peut être écrit par l'application
RUN mkdir -p /var/www/symfony/var/log
RUN chmod -R 777 /var/www/symfony/var/log
RUN chown -R www-data:www-data /var/www/symfony/var/log

CMD bash -c "php-fpm"

# Optional: Expose port 9000 for fpm
EXPOSE 9000