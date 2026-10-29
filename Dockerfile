FROM php:8.2-apache

WORKDIR /var/www/html
COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html
RUN mkdir -p /var/lib/siged && chown www-data:www-data /var/lib/siged
RUN a2enmod rewrite && printf '<Directory /var/www/html>\n    AllowOverride All\n    Require all granted\n</Directory>\n' > /etc/apache2/conf-available/segep.conf && a2enconf segep

EXPOSE 80
