FROM php:8.2-apache


WORKDIR /var/www/html

# Install required dependencies
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install gd pdo pdo_mysql \
    && docker-php-ext-install pcntl

# Enable rewrite mode for URL maniplutaion
RUN a2enmod rewrite

# Install composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer
COPY composer*  ./
RUN composer install --no-scripts --no-autoloader

# copy files
COPY . .

# composer dump & autoload
RUN composer dump-autoload --optimize

# set the apache document root path
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf
RUN sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Generate laravel key
RUN php artisan key:generate

# Set correct permissions for Laravel
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html

EXPOSE 80 9000

CMD ["apache2-foreground"]



