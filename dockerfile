FROM php:7.4-apache

# Install ekstensi mysqli
RUN docker-php-ext-install mysqli

# Aktifkan mod_rewrite
RUN a2enmod rewrite

# Ubah AllowOverride supaya .htaccess aktif
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Salin semua file project
COPY . /var/www/html/

# Set permission buat Apache
RUN chown -R www-data:www-data /var/www/html
