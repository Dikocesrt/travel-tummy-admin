FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git unzip curl libzip-dev zip libicu-dev \
    && docker-php-ext-install zip pdo pdo_mysql intl

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /app

# Copy all files
COPY . .

# Install PHP dependencies
RUN composer install --no-interaction --prefer-dist --optimize-autoloader

# Expose port (opsional, hanya untuk dokumentasi)
EXPOSE 8000

# Command default (misalnya pakai php artisan serve)
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]