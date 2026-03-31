FROM php:8.4-cli

# Instalamos dependencias del sistema
RUN apt-get update && apt-get install -y \
    git curl zip unzip libpq-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring bcmath

# Traemos Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copiamos solo los archivos de composer primero (esto acelera el build)
COPY composer.json composer.lock ./

# Instalamos dependencias ignorando chequeos de plataforma por ahora
RUN composer install --no-scripts --no-autoloader --ignore-platform-reqs

# Ahora copiamos el resto del código
COPY . .

# Generamos el autoloader final
RUN composer dump-autoload --optimize

EXPOSE 8000
CMD ["php", "artisan", "serve", "--host=0.0.0.0", "--port=8000"]