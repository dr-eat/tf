FROM php:8.2-apache
RUN docker-php-ext-install mysqli
RUN curl -O https://phar.phpunit.de/phpunit-10.5.0.phar
RUN chmod +x phpunit-10.5.0.phar && mv phpunit-10.5.0.phar /usr/local/bin/phpunit
RUN a2enmod rewrite