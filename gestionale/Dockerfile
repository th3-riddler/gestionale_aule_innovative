# Use the official PHP with Apache image
FROM php:apache

# Enable Apache modules
RUN a2enmod rewrite

# Disable PHP Warnings
RUN echo "display_errors = Off" >> /usr/local/etc/php/php.ini

# Install mysqli extension
RUN docker-php-ext-install mysqli

# Copy the PHP files into the container
COPY src/ /var/www/html/