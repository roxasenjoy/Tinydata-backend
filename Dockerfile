FROM php:7.4-apache
 
RUN a2enmod rewrite
 
RUN apt-get update \
  && apt-get install -y libzip-dev libpng-dev git wget cron --no-install-recommends \
  && apt-get clean \
  && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

ENV TZ=Europe/Paris
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone
 
RUN docker-php-ext-install pdo mysqli pdo_mysql zip gd opcache;
 
RUN wget https://getcomposer.org/download/2.0.9/composer.phar \
    && mv composer.phar /usr/bin/composer && chmod +x /usr/bin/composer
 
COPY ./apache.conf /etc/apache2/sites-enabled/000-default.conf
COPY  php.ini /usr/local/etc/php/

COPY ./app /var/www
RUN chmod 777 -R /var/www/public


 
WORKDIR /var/www
RUN echo DATABASE_TINYCOACHING_URL=mysql://root:root@127.0.0.1:3306/bdd > .env
RUN composer install
 
CMD ["apache2-foreground"]