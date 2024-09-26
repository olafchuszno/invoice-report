FROM php:8.2-apache

# Install php pdo extension
RUN docker-php-ext-install pdo pdo_mysql

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/local/bin/composer

WORKDIR /var/www/html

COPY . .

RUN composer install

# Copy the setup.sh script
COPY setup.sh /usr/local/bin/setup.sh
RUN chmod +x /usr/local/bin/setup.sh

# Run the setup.sh script (when the container starts)
ENTRYPOINT ["/usr/local/bin/setup.sh"]