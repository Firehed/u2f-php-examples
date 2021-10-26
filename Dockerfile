FROM composer:2 AS dependencies
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist

FROM php:8.0-cli-alpine as server
WORKDIR /var/www/html
# Add any system dependencies here

# Set up runtime user
RUN addgroup php-www-users && adduser -D -G php-www-users php-www

# Copy code into runtime
COPY --chown=php-www:php-www-users --from=dependencies /app/vendor ./vendor
COPY --chown=php-www:php-www-users . .

# Run
USER php-www
# This value must be set to the domain name you're serving content from
ENV HOSTNAME=localhost
ENV PORT=8000
CMD php -S 0.0.0.0:$PORT -t public/
