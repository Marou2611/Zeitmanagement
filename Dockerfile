FROM php:8.3-fpm

WORKDIR /var/www/html

RUN apt-get update && apt-get install -y \
    nginx \
    libpng-dev libjpeg-dev libfreetype6-dev \
    zip unzip git curl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

RUN printf 'server {\n\
    listen 80 default_server;\n\
    root /var/www/html/public;\n\
    index index.php index.html;\n\
    client_max_body_size 50M;\n\
\n\
    location / {\n\
        try_files $uri $uri/ /index.php?$query_string;\n\
    }\n\
\n\
    location ~ \\.php$ {\n\
        include fastcgi_params;\n\
        fastcgi_pass 127.0.0.1:9000;\n\
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;\n\
        fastcgi_index index.php;\n\
    }\n\
\n\
    location ~ /\\.ht {\n\
        deny all;\n\
    }\n\
}\n' > /etc/nginx/sites-available/default

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY . /var/www/html

RUN composer install --no-dev --optimize-autoloader && composer dump-autoload --optimize


RUN npm install && npm run build

RUN mkdir -p storage/logs bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 storage bootstrap/cache

RUN { \
    echo '#!/bin/bash'; \
    echo 'set -e'; \
    echo 'printenv | grep -E "^(APP_|DB_|MAIL_|SESSION_|QUEUE_|CACHE_|LOG_|BROADCAST_|FILESYSTEM_|VITE_|BCRYPT_)" > /var/www/html/.env'; \
    echo 'echo "SESSION_LIFETIME=120" >> /var/www/html/.env'; \
    echo 'echo "SESSION_ENCRYPT=false" >> /var/www/html/.env'; \
    echo 'echo "SESSION_PATH=/" >> /var/www/html/.env'; \
    echo 'echo "SESSION_DOMAIN=null" >> /var/www/html/.env'; \
    echo 'php artisan config:clear'; \
    echo 'php artisan cache:clear'; \
    echo 'php artisan migrate --force'; \
    echo 'php artisan schedule:work &'; \
    echo 'php-fpm -D'; \
    echo 'exec nginx -g "daemon off;"'; \
} > /start.sh && chmod +x /start.sh

EXPOSE 80

CMD ["/bin/bash", "/start.sh"]
