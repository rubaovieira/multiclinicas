FROM php:8.2-cli

# Dependências de sistema e extensões PHP
RUN apt-get update && apt-get install -y \
    git unzip zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev libicu-dev libonig-dev libxml2-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip intl \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Código
WORKDIR /app
COPY . /app

# Dependências PHP
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Railway injeta $PORT
ENV PORT=8080

# Start: prepara pastas e inicia o servidor embutido
CMD sh -lc '\
  mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache && \
  chmod -R 775 storage bootstrap/cache && \
  php artisan migrate --force || true && \
  php artisan storage:link || true && \
  php -S 0.0.0.0:${PORT:-8080} -t public public/index.php'
