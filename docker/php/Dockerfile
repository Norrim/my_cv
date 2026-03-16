FROM php:8.4-fpm-alpine

# Variables utiles pour Claude Code sur Alpine
ENV USE_BUILTIN_RIPGREP=0
ENV PATH="/root/.local/bin:${PATH}"

# Paquets système & extensions PHP courantes (PostgreSQL + intl + zip)
# + dépendances requises pour Claude Code sur Alpine
RUN apk add --no-cache \
      git unzip curl bash \
      icu-dev icu-data-full icu-libs \
      libzip-dev oniguruma-dev postgresql-dev \
      libgcc libstdc++ ripgrep \
  && docker-php-ext-configure intl \
  && docker-php-ext-install -j$(nproc) intl opcache pdo pdo_pgsql zip

# Installation officielle de Claude Code
RUN curl -fsSL https://claude.ai/install.sh | bash

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

# Préparer le dossier d'uploads
RUN mkdir -p /var/www/html/public/uploads \
 && chown -R www-data:www-data /var/www/html/public/uploads

CMD ["php-fpm", "-F"]
