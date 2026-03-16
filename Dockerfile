# Basis-Image mit PHP und Apache
FROM php:8.3-apache

WORKDIR /var/www/html

# Systemabhängigkeiten installieren
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

# Composer installieren
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Projektdateien kopieren
COPY . /var/www/html

# Abhängigkeiten installieren
RUN composer install --no-dev --optimize-autoloader

# Verzeichnisse und Berechtigungen
RUN mkdir -p /var/www/html/storage/logs /var/www/html/bootstrap/cache \
    && chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# .env erstellen und Key generieren
RUN cp .env.example .env && php artisan key:generate

# Apache Document Root anpassen
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Startup Script erstellen das MPM beim Start repariert
RUN echo '#!/bin/bash\n\
a2dismod mpm_event mpm_worker 2>/dev/null || true\n\
a2enmod mpm_prefork 2>/dev/null || true\n\
apache2-foreground' > /start.sh \
    && chmod +x /start.sh

EXPOSE 80

# Startup Script verwenden statt direkt apache2-foreground
CMD ["/bin/bash", "/start.sh"]
