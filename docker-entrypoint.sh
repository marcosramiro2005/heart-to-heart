#!/bin/bash
set -e

# Crear el fichero SQLite si no existe (primera vez o volumen vacío)
if [ ! -f /var/www/html/database/database.sqlite ]; then
    touch /var/www/html/database/database.sqlite
    chown www-data:www-data /var/www/html/database/database.sqlite
fi

# Enlace simbólico del storage público (para avatares y archivos subidos)
php artisan storage:link --force 2>/dev/null || true

# Ejecutar migraciones (--force acepta sin prompt en producción)
php artisan migrate --force

# Cachear configuración, rutas y vistas para mayor rendimiento
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Arrancar Apache en primer plano
exec apache2-foreground
