FROM php:8.1-apache

# نصب افزونه‌های مورد نیاز برای PHP
RUN docker-php-ext-install pdo pdo_mysql

# کپی کردن فایل کانفیگ آپاچی به داخل کانتینر
COPY apache.conf /etc/apache2/sites-available/000-default.conf

# فعال کردن ماژول‌های مورد نیاز آپاچی
RUN a2enmod rewrite
