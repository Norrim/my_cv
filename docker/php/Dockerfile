FROM php:8.3-fpm-alpine

# Paquets système & extensions PHP courantes (PostgreSQL + intl + zip)
RUN apk add --no-cache \
      git unzip curl bash icu-dev libzip-dev oniguruma-dev postgresql-dev \
  && docker-php-ext-configure intl \
  && docker-php-ext-install -j$(nproc) intl opcache pdo pdo_pgsql zip

# (Optionnel) GD si tu en as besoin pour des images dans les exports
# RUN apk add --no-cache freetype-dev libpng-dev libjpeg-turbo-dev \
#   && docker-php-ext-configure gd --with-freetype --with-jpeg \
#   && docker-php-ext-install -j$(nproc) gd

# Composer (copié depuis l'image officielle)
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Astuce: on ne lance pas "composer install" ici pour éviter l'échec si le projet n'est pas encore initialisé.
# Tu pourras exécuter "composer install" via: docker compose exec php composer install

# Opcache de base pour le dev (à ajuster en prod)
RUN { \
      echo "opcache.enable=1"; \
      echo "opcache.enable_cli=1"; \
      echo "opcache.validate_timestamps=1"; \
      echo "opcache.revalidate_freq=0"; \
    } > /usr/local/etc/php/conf.d/opcache.ini

CMD ["php-fpm", "-F"]
