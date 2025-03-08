# Use the official PHP 8.2 image with FPM
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install required PHP extensions
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    zip \
    unzip \
    git \
    curl \
    npm \
    && docker-php-ext-install pdo pdo_mysql gd opcache


# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy Laravel project files
COPY . .

# Install dependencies
RUN composer install --ignore-platform-reqs

RUN composer install --no-dev --optimize-autoloader

RUN npm install && npm run build

# Set permissions for storage & cache
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache

RUN php artisan route:cache && php artisan view:cache && php artisan optimize

RUN php artisan config:clear;

RUN php artisan migrate:fresh --seed --force

# Expose PHP-FPM port
EXPOSE 9000
CMD ["php-fpm"]
