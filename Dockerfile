FROM php:8.2-cli

# Dependências para extensões
RUN apt-get update && apt-get install -y \
    git unzip zip \
    libpng-dev libjpeg-dev libfreetype6-dev \
    libzip-dev libicu-dev libonig-dev libxml2-dev \
 && docker-php-ext-configure gd --with-freetype --with-jpeg \
 && docker-php-ext-install -j$(nproc) gd pdo pdo_mysql zip intl \
 && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . /app

# Instala dependências PHP
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Porta padrão (Railway injeta $PORT)
ENV PORT=8080

# PREPARE tudo no start:
# - cria pastas de sessão/cache/views
# - ajusta permissões
# - limpa config
# - roda migrações
# - cria storage:link
# - sobe servidor embutido apontando para /public
CMD sh -lc '\
  echo "=== BOOT v3: $(php -v | head -n1) ===" && \
  mkdir -p storage/framework/cache storage/framework/sessions storage/framework/views bootstrap/cache && \
  chmod -R 777 storage bootstrap/cache && \
  php artisan optimize:clear && \
  php artisan migrate --force || true && \
  php artisan storage:link || true && \
  php -S 0.0.0.0:${PORT:-8080} -t public public/index.php'