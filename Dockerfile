FROM php:7.4-apache

# Install system dependencies
RUN apt-get update && apt-get install -y     libpng-dev     libjpeg-dev     libfreetype6-dev     zip     unzip     libcurl4-openssl-dev     && rm -rf /var/lib/apt/lists/*

# Install PHP extensions
RUN docker-php-ext-configure gd --with-freetype --with-jpeg
RUN docker-php-ext-install gd mysqli curl

# Enable Apache rewrite module
RUN a2enmod rewrite

# Set the document root to the public directory
ENV APACHE_DOCUMENT_ROOT /var/www/html

# Copy the application code
COPY . /var/www/html

