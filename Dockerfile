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
# Força o caminho compilado das views do Blade
ENV VIEW_COMPILED_PATH=/app/storage/framework/views

# Start: prepara pastas, limpa caches e inicia o servidor embutido
CMD sh -lc '\
  mkdir -p storage/framework/{cache,sessions,views} bootstrap/cache && \
  chmod -R 777 storage bootstrap/cache && \
  rm -f bootstrap/cache/*.php || true && \
  php artisan config:clear || true && \
  php artisan view:clear || true && \
  php artisan migrate --force || true && \
  php artisan storage:link || true && \
  php -S 0.0.0.0:${PORT:-8080} -t public public/index.php'

