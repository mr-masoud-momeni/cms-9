# مرحله اول: نصب Apache و PHP
FROM php:8.2.4-apache

# نصب Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# نصب Node.js و npm
RUN apt-get update && apt-get install -y \
    curl \
    lsb-release \
    gnupg2 \
    ca-certificates \
    && curl -sL https://deb.nodesource.com/setup_16.x | bash - \
    && apt-get install -y nodejs \
    && apt-get install -y git

# نصب MySQLnd برای پشتیبانی از MySQL
RUN docker-php-ext-install mysqli pdo pdo_mysql

# فعال کردن mod_rewrite برای Apache
RUN a2enmod rewrite

# تنظیمات دایرکتوری پروژه
WORKDIR /var/www/html

# کپی کردن پروژه به داخل کانتینر
COPY ./src /var/www/html

# نصب بسته‌های npm
RUN npm install

# اجرای Apache
CMD ["apache2-foreground"]
