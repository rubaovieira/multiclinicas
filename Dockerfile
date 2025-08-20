FROM php:8.2-cli

# Dependências de sistema para extensões
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

# Instalar dependências PHP (sem dev) e otimizar autoloader
RUN composer install --no-dev --prefer-dist --optimize-autoloader

# Servir a app (usa o $PORT fornecido pelo Railway)
ENV PORT=8080
CMD php artisan config:clear && php artisan serve --host=0.0.0.0 --port=${PORT}
