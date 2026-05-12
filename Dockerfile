# ============================================================
#  FinEdu Backend - Dockerfile (Railway)
# ============================================================

FROM php:8.2-apache

# Install ekstensi mysqli
RUN docker-php-ext-install mysqli && docker-php-ext-enable mysqli

# Aktifkan mod_rewrite untuk .htaccess
RUN a2enmod rewrite headers

# Izinkan .htaccess override
RUN sed -i 's/AllowOverride None/AllowOverride All/g' /etc/apache2/apache2.conf

# Copy semua file backend ke web root
COPY . /var/www/html/

# Set permission
RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

# Expose port (Railway akan override lewat env PORT)
EXPOSE 80

CMD ["apache2-foreground"]
