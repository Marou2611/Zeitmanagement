FROM php:8.3-apache

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader

RUN mkdir -p /var/www/html/storage/logs /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Startup Script - Migrate läuft hier mit echten Railway Variablen!
RUN printf '#!/bin/bash\n\
cp .env.example .env\n\
php artisan key:generate\n\
php artisan config:clear\n\
php artisan migrate --force\n\
a2dismod mpm_event mpm_worker 2>/dev/null || true\n\
a2enmod mpm_prefork 2>/dev/null || true\n\
apache2-foreground\n' > /start.sh \
    && chmod +x /start.sh

EXPOSE 80

CMD ["/bin/bash", "/start.sh"]
