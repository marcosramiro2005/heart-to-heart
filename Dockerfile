FROM php:8.3-apache

# Activar mod_rewrite para que funcionen las rutas de Laravel
RUN a2enmod rewrite

# Dependencias del sistema + extensiones PHP necesarias
RUN apt-get update && apt-get install -y \
    git curl zip unzip \
    libpq-dev libonig-dev libxml2-dev \
    libsqlite3-dev sqlite3 \
    && docker-php-ext-install \
        pdo pdo_mysql pdo_sqlite \
        mbstring bcmath xml \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Node.js 20 para compilar los assets con Vite
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Apuntar Apache al directorio public/ de Laravel
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' \
    /etc/apache2/sites-available/*.conf \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

# Permitir que el .htaccess de Laravel funcione
RUN sed -ri -e 's/AllowOverride None/AllowOverride All/g' \
    /etc/apache2/apache2.conf \
    /etc/apache2/conf-available/*.conf

WORKDIR /var/www/html

# Instalar dependencias PHP (sin devDependencies)
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-scripts --no-autoloader

# Instalar dependencias Node
COPY package.json package-lock.json ./
RUN npm ci

# Copiar el resto del código
COPY . .

# Compilar assets de Vue/Vite para producción
RUN npm run build

# Optimizar autoloader de Composer
RUN composer dump-autoload --optimize

# Permisos correctos para Laravel
RUN chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Directorio para SQLite (el volumen de Fly.io se monta aquí)
RUN mkdir -p /var/www/html/database \
    && chown -R www-data:www-data /var/www/html/database

COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh
RUN chmod +x /usr/local/bin/docker-entrypoint.sh

EXPOSE 80

CMD ["docker-entrypoint.sh"]
