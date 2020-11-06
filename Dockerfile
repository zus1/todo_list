FROM php:7.4-apache
COPY . /var/www/html
RUN DEBIAN_FRONTEND=noninteractive apt-get update && apt-get install -y curl
RUN DEBIAN_FRONTEND=noninteractive apt-get install -y \
    lsb-release \
    git \
    nano \
    python \
    locales \
    libsnmp-dev \
    libzip-dev \
    && docker-php-ext-install -j$(nproc) pdo_mysql zip
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
ADD ./conf/vhost.conf /etc/apache2/sites-available/000-default.conf
RUN a2enmod rewrite && \
    service apache2 restart
EXPOSE 80 3306 443