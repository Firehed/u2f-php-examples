FROM composer:1.10.8 as dependencies
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-progress \
    --optimize-autoloader \
    --prefer-dist

FROM php:7.4.7-cli-alpine as server
WORKDIR /var/www/html
# Add any system dependencies here

# Set up runtime user
RUN addgroup php-www-users && adduser -D -G php-www-users php-www

# Copy code into runtime
COPY --chown=php-www:php-www-users --from=dependencies /app/vendor ./vendor
COPY --chown=php-www:php-www-users . .

# Run
USER php-www
ENV PORT=8000
CMD php -S 0.0.0.0:$PORT -t public/
