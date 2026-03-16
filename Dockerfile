FROM php:8.3-apache

WORKDIR /var/www/html

# System-Abhängigkeiten installieren
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

# Composer-Pakete installieren
RUN composer install --no-dev --optimize-autoloader

# Berechtigungen setzen
RUN mkdir -p storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

# Apache Module aktivieren
RUN a2enmod rewrite headers

# Apache Virtual Host konfigurieren
RUN cat > /etc/apache2/sites-available/000-default.conf << 'APACHE'
<VirtualHost *:80>
    DocumentRoot /var/www/html/public

    <Directory /var/www/html/public>
        AllowOverride All
        Require all granted
        Options -Indexes +FollowSymLinks
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/error.log
    CustomLog ${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
APACHE

# Startup-Script erstellen
RUN cat > /start.sh << 'SCRIPT'
#!/bin/bash
set -e

echo "🔧 Erstelle .env Datei..."
cat > /var/www/html/.env << EOF
APP_NAME=${APP_NAME:-Zeitmanagement}
APP_ENV=${APP_ENV:-production}
APP_KEY=${APP_KEY}
APP_DEBUG=${APP_DEBUG:-false}
APP_URL=${APP_URL:-http://localhost}

DB_CONNECTION=${DB_CONNECTION:-mysql}
DB_HOST=${DB_HOST}
DB_PORT=${DB_PORT:-3306}
DB_DATABASE=${DB_DATABASE}
DB_USERNAME=${DB_USERNAME}
DB_PASSWORD=${DB_PASSWORD}

SESSION_DRIVER=${SESSION_DRIVER:-database}
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null

BROADCAST_CONNECTION=log
FILESYSTEM_DISK=local
QUEUE_CONNECTION=${QUEUE_CONNECTION:-database}
CACHE_STORE=${CACHE_STORE:-database}
CACHE_PREFIX=

LOG_CHANNEL=${LOG_CHANNEL:-stderr}
LOG_LEVEL=debug

MAIL_MAILER=${MAIL_MAILER:-smtp}
MAIL_HOST=${MAIL_HOST}
MAIL_PORT=${MAIL_PORT:-587}
MAIL_USERNAME=${MAIL_USERNAME}
MAIL_PASSWORD=${MAIL_PASSWORD}
MAIL_ENCRYPTION=${MAIL_ENCRYPTION:-tls}
MAIL_FROM_ADDRESS=${MAIL_FROM_ADDRESS:-noreply@example.com}
MAIL_FROM_NAME=${MAIL_FROM_NAME:-Zeitmanagement}

VITE_APP_NAME=Zeitmanagement
EOF

echo "✅ .env erstellt"

php artisan config:clear
php artisan cache:clear
php artisan migrate --force

echo "🚀 Starte Apache..."
exec apache2-foreground
SCRIPT

RUN chmod +x /start.sh

EXPOSE 80

CMD ["/bin/bash", "/start.sh"]
