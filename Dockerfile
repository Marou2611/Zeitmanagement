FROM php:8.3-apache

WORKDIR /var/www/html

# System-Pakete
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# MPM-Konflikt: Dateien direkt löschen (sicherer als a2dismod)
RUN rm -f /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
          /etc/apache2/mods-enabled/mpm_worker.load

# Module aktivieren
RUN a2enmod mpm_prefork rewrite headers

# Apache Virtual Host
RUN printf '<VirtualHost *:80>\n    DocumentRoot /var/www/html/public\n    <Directory /var/www/html/public>\n        AllowOverride All\n        Require all granted\n        Options -Indexes +FollowSymLinks\n    </Directory>\n</VirtualHost>\n' \
    > /etc/apache2/sites-available/000-default.conf

# Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Projekt kopieren
COPY . /var/www/html

# PHP-Pakete
RUN composer install --no-dev --optimize-autoloader

# Berechtigungen
RUN mkdir -p storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Startup-Skript inline erstellen (keine externe Datei nötig!)
RUN { \
    echo '#!/bin/bash'; \
    echo 'set -e'; \
    echo 'echo "Schreibe .env aus Railway-Variablen..."'; \
    echo 'printenv | grep -E "^(APP_|DB_|MAIL_|SESSION_|QUEUE_|CACHE_|LOG_|BROADCAST_|FILESYSTEM_|VITE_)" > /var/www/html/.env'; \
    echo 'echo "SESSION_LIFETIME=120" >> /var/www/html/.env'; \
    echo 'echo "SESSION_ENCRYPT=false" >> /var/www/html/.env'; \
    echo 'echo "SESSION_PATH=/" >> /var/www/html/.env'; \
    echo 'echo "SESSION_DOMAIN=null" >> /var/www/html/.env'; \
    echo 'echo ".env geschrieben:"'; \
    echo 'grep -v "PASSWORD\|KEY" /var/www/html/.env'; \
    echo 'php artisan config:clear'; \
    echo 'php artisan cache:clear'; \
    echo 'php artisan migrate --force'; \
    echo 'echo "Starte Apache..."'; \
    echo 'exec apache2-foreground'; \
} > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/bin/bash", "/start.sh"]
