FROM php:8.1.12-zts-alpine3.16 AS yolocms
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
EXPOSE 80
WORKDIR /home/www-data
ENTRYPOINT ["php","-S","0.0.0.0:80"]