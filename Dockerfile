# Use the official PHP image with Apache
FROM php:8.2-apache

# Install system dependencies and PHP extensions
# - libpng-dev & libzip-dev: Required for gd and zip extensions
# - mysqli: Required for your database connection
# - pdo_mysql: Good practice to have
# - gd: For image processing (if used)
# - zip: For Composer
RUN apt-get update && apt-get install -y \
    libpng-dev \
    libzip-dev \
    unzip \
    libssl-dev \
    pkg-config \
    && pecl install mongodb \
    && docker-php-ext-enable mongodb \
    && docker-php-ext-install mysqli pdo_mysql gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Enable Apache mod_rewrite
# This is CRITICAL for your .htaccess routing to work
RUN a2enmod rewrite

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www/html

# Copy composer files first to leverage Docker cache
COPY composer.json ./

# Install PHP dependencies
# --no-dev: Don't install development packages
# --optimize-autoloader: Optimize for production
RUN composer install --no-dev --optimize-autoloader

# Copy the rest of the application code
COPY . .

# Set permissions
# Apache runs as www-data user, so it needs ownership of the files
RUN chown -R www-data:www-data /var/www/html

# Update Apache configuration to use the PORT environment variable provided by Render
# Render sets a PORT env var (usually 10000), but standard Apache listens on 80.
# We change ports.conf to listen on $PORT
RUN sed -i 's/80/${PORT}/g' /etc/apache2/sites-available/000-default.conf /etc/apache2/ports.conf

# Expose port 80 (standard documentation, though Render overrides this)
EXPOSE 80

# Start Apache in foreground
CMD ["apache2-foreground"]
