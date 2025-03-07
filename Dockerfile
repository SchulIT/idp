FROM php:8.3-fpm-alpine AS base

ADD --chmod=0755 https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions /usr/local/bin/

RUN apk add supervisor nginx

RUN install-php-extensions apcu pdo_mysql pcntl intl zip gd xsl redis

# Copy php.ini
RUN cp /usr/local/etc/php/php.ini-production /usr/local/etc/php.ini

# Set memory limit
RUN echo "memory_limit=512M" > /usr/local/etc/php/conf.d/memory-limit.ini

# Set settings for uploading files
RUN echo "upload_max_filesize = 128M" >> /usr/local/etc/php/conf.d/uploads.ini
RUN echo "post_max_size = 128M" >> /usr/local/etc/php/conf.d/uploads.ini

# Set maximum execution time
RUN echo "max_execution_time = 90" > /usr/local/etc/php/conf.d/execution-time.ini

# Do not expose PHP
RUN echo "expose_php = Off" > /usr/local/etc/php/conf.d/expose-php.ini

# Install composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
ENV COMPOSER_ALLOW_SUPERUSER=1

# Set working directory
WORKDIR /var/www/html

# Copy whole project into image
COPY . .

# Run composer install
RUN composer install --no-dev --classmap-authoritative --no-scripts

# Install Assets
FROM node:22-alpine as assets
WORKDIR /var/www/html
COPY --from=base /var/www/html /var/www/html

# Install dependencies
RUN npm install \
    && npm run build \
    && rm -rf node_modules

FROM base as runner
WORKDIR /var/www/html
COPY --from=assets /var/www/html/public/build /var/www/html/public/build

# Install assets (copy 3rd party stuff)
RUN php bin/console assets:install

# Install nginx configuration
COPY .docker/nginx.conf /etc/nginx/sites-enabled/default

# Remove the .htaccess file because we are using Nginx
RUN rm -rf ./public/.htaccess

# Install supervisor configuration
COPY .docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Export HTTP port
EXPOSE 80

# Install cronjob
RUN crontab -l | { cat; echo "*/2 * * * * php /var/www/html/bin/console shapecode:cron:run"; } | crontab -

# Copy startup.sh
COPY .docker/startup.sh startup.sh
RUN chmod +x startup.sh

CMD ["./startup.sh"]
