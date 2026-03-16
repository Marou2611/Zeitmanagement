FROM php:8.3-apache

WORKDIR /var/www/html

# System-Pakete + curl für Node.js
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Node.js 20 installieren (für npm run build)
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# MPM-Konflikt lösen
RUN rm -f /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
          /etc/apache2/mods-enabled/mpm_worker.load

# Apache Module aktivieren
RUN a2enmod mpm_prefork rewrite headers

# Apache Virtual Host konfigurieren
RUN printf '<VirtualHost *:80>\n    DocumentRoot /var/www/html/public\n    <Directory /var/www/html/public>\n        AllowOverride All\n        Require all granted\n        Options -Indexes +FollowSymLinks\n    </Directory>\n</VirtualHost>\n' \
    > /etc/apache2/sites-available/000-default.conf

# Composer installieren
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Projekt kopieren
COPY . /var/www/html

# PHP-Pakete installieren
RUN composer install --no-dev --optimize-autoloader

# Frontend-Assets bauen (Vite/Tailwind)
RUN npm install && npm run build

# Berechtigungen setzen
RUN mkdir -p storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Startup-Skript erstellen
RUN { \
    echo '#!/bin/bash'; \
    echo 'set -e'; \
    echo 'echo "Schreibe .env..."'; \
    echo 'printenv | grep -E "^(APP_|DB_|MAIL_|SESSION_|QUEUE_|CACHE_|LOG_|BROADCAST_|FILESYSTEM_|VITE_|BCRYPT_)" > /var/www/html/.env'; \
    echo 'echo "SESSION_LIFETIME=120" >> /var/www/html/.env'; \
    echo 'echo "SESSION_ENCRYPT=false" >> /var/www/html/.env'; \
    echo 'echo "SESSION_PATH=/" >> /var/www/html/.env'; \
    echo 'echo "SESSION_DOMAIN=null" >> /var/www/html/.env'; \
    echo 'echo ".env Inhalt (ohne Passwörter):"'; \
    echo 'grep -vE "PASSWORD|KEY|SECRET" /var/www/html/.env || true'; \
    echo 'php artisan config:clear'; \
    echo 'php artisan cache:clear'; \
    echo 'php artisan migrate --force'; \
    echo 'echo "Apache startet..."'; \
    echo 'exec apache2-foreground'; \
} > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/bin/bash", "/start.sh"]
