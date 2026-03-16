FROM php:8.3-apache

WORKDIR /var/www/html

# System-Pakete installieren
RUN apt-get update && apt-get install -y \
    libpng-dev libjpeg-dev libfreetype6-dev \
    zip unzip git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# MPM-Konflikt dauerhaft lösen (direkt Dateien entfernen)
RUN rm -f /etc/apache2/mods-enabled/mpm_event.conf \
          /etc/apache2/mods-enabled/mpm_event.load \
          /etc/apache2/mods-enabled/mpm_worker.conf \
          /etc/apache2/mods-enabled/mpm_worker.load

# Module aktivieren
RUN a2enmod mpm_prefork rewrite headers

# Apache Virtual Host
RUN printf '<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
        Options -Indexes +FollowSymLinks\n\
    </Directory>\n\
</VirtualHost>\n' > /etc/apache2/sites-available/000-default.conf

# Composer installieren
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Projekt kopieren
COPY . /var/www/html

# PHP-Pakete installieren
RUN composer install --no-dev --optimize-autoloader

# Berechtigungen setzen
RUN mkdir -p storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache \
    && chmod +x /var/www/html/docker-start.sh

EXPOSE 80

CMD ["/bin/bash", "/var/www/html/docker-start.sh"]
