FROM php:8.3-fpm-alpine

# Paquets système & extensions PHP courantes (PostgreSQL + intl + zip)
RUN apk add --no-cache \
      git unzip curl bash \
      icu-dev icu-data-full icu-libs \
      libzip-dev oniguruma-dev postgresql-dev \
  && docker-php-ext-configure intl \
  && docker-php-ext-install -j$(nproc) intl opcache pdo pdo_pgsql zip

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Opcache (dev-friendly)
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.enable_cli=1"; \
      echo "opcache.validate_timestamps=1"; \
      echo "opcache.revalidate_freq=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

# Préparer le dossier d'uploads (le volume uploads-data sera monté ici)
# On met www-data propriétaire pour éviter "not writable" côté PHP-FPM.
RUN mkdir -p /var/www/html/public/uploads \
 && chown -R www-data:www-data /var/www/html/public/uploads

CMD ["php-fpm", "-F"]
