# Basis-Image mit PHP und Apache
FROM php:8.3-apache

# Arbeitsverzeichnis festlegen
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
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer installieren
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Projektdateien kopieren
COPY . /var/www/html

# Abhängigkeiten installieren
RUN composer install --no-dev --optimize-autoloader

# Rechte für Laravel-Verzeichnisse setzen
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Umgebungsvariablen für die .env-Datei
ENV APACHE_DOCUMENT_ROOT /var/www/html/public

# Artisan Key generieren
RUN php artisan key:generate

# Apache-Konfiguration anpassen
RUN sed -ri -e 's!/var/www/html!/var/www/html/public!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!/var/www/html/public!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Port freigeben
EXPOSE 80

# Startbefehl für Apache
