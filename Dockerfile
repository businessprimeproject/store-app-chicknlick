FROM php:8.3-apache

RUN apt-get update \
    && apt-get install -y --no-install-recommends git unzip libsqlite3-dev \
    && docker-php-ext-install pdo_sqlite \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html
COPY . .

RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader \
    && chown -R www-data:www-data storage bootstrap/cache \
    && printf '%s\n' '<VirtualHost *:80>' '    DocumentRoot /var/www/html/public' '    <Directory /var/www/html/public>' '        AllowOverride All' '        Require all granted' '    </Directory>' '</VirtualHost>' > /etc/apache2/sites-available/000-default.conf

ENV APP_ENV=production
ENV APP_DEBUG=false
ENV DB_CONNECTION=sqlite
ENV DB_DATABASE=/var/www/html/database/database.sqlite

RUN touch database/database.sqlite \
    && chown www-data:www-data database/database.sqlite \
    && php artisan storage:link || true

EXPOSE 80

CMD ["sh", "-c", "php artisan migrate --force && apache2-foreground"]
