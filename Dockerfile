FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    libpng-dev \
    && docker-php-ext-install mysqli \
    && docker-php-ext-enable mysqli \
    && apt-get clean

WORKDIR /var/www/html

COPY . .

EXPOSE 8080

CMD ["php", "-S", "0.0.0.0:8080"]
