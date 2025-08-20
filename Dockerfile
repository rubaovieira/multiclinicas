FROM php:8.2-cli

# Extensões necessárias (mpdf precisa de gd)
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

# Dependências PHP
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Porta padrão (Railway injeta $PORT; usamos 8080 se não houver)
ENV PORT=8080

# Iniciar sem o "artisan serve" (usa servidor embutido do PHP apontando para /public)
CMD sh -lc "php artisan config:clear && php -S 0.0.0.0:${PORT:-8080} -t public public/index.php"
