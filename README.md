# Heart to Heart 💚

Plataforma web de bienestar emocional y salud mental desarrollada como Trabajo de Fin de Grado (TFG) del Grado Superior en Desarrollo de Aplicaciones Web.

---

## Descripción

Heart to Heart es una aplicación web pensada para acompañar a las personas en su camino hacia el bienestar emocional. Ofrece:

- **Hearty** — Asistente de IA empático disponible 24/7
- **Comunidad** — Foro de apoyo entre usuarios (con moderación de contenido)
- **Técnicas de bienestar** — Respiración, meditación, yoga, journaling y más
- **Dashboard emocional** — Seguimiento del estado de ánimo a lo largo del tiempo
- **Retos y plan de bienestar** — Objetivos personalizados semanales
- **Modo Focus** — Sesiones de concentración con temporizador Pomodoro
- **SOS** — Recursos de crisis y línea de emergencias (024)
- **Diario personal** — Espacio privado de reflexión
- **Biblioteca de recursos** — Artículos y guías de salud mental

---

## Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend | Vue 3 + Inertia.js |
| Estilos | CSS personalizado + Tailwind CSS |
| Build | Vite 8 |
| Base de datos | SQLite |
| Autenticación | Laravel Breeze |
| Despliegue | Docker + Fly.io |

---

## Instalación local

### Requisitos previos
- PHP 8.3+
- Composer 2
- Node.js 20+
- npm

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/heart-to-heart.git
cd heart-to-heart

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias Node
npm install

# 4. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 5. Crear la base de datos y ejecutar migraciones
touch database/database.sqlite
php artisan migrate

# 6. Compilar assets
npm run dev

# 7. Arrancar el servidor de desarrollo
php artisan serve
```

La app estará disponible en `http://localhost:8000`.

---

## Despliegue en producción (Fly.io)

### Requisitos
- Cuenta en [fly.io](https://fly.io) (requiere tarjeta, pero el tier gratuito no cobra)
- [flyctl](https://fly.io/docs/flyctl/install/) instalado

### Primera vez

```bash
# 1. Iniciar sesión
fly auth login

# 2. Crear la app (elige un nombre único)
fly apps create heart-to-heart-marco

# 3. Editar fly.toml: reemplaza "heart-to-heart-app" por tu nombre de app

# 4. Crear volumen persistente para la base de datos
fly volumes create heart_data --region mad --size 1

# 5. Configurar variables secretas
fly secrets set APP_KEY=$(php artisan key:generate --show)
fly secrets set APP_NAME="Heart to Heart"
fly secrets set MAIL_MAILER=resend
fly secrets set MAIL_HOST=smtp.resend.com
fly secrets set MAIL_PORT=465
fly secrets set MAIL_USERNAME=resend
fly secrets set MAIL_PASSWORD=tu_api_key_de_resend
fly secrets set MAIL_FROM_ADDRESS=noreply@tudominio.com

# 6. Desplegar
fly deploy

# 7. Abrir la app
fly open
```

### Redesplegar tras cambios

```bash
git add .
git commit -m "descripción del cambio"
fly deploy
```

### Email en producción

Se recomienda [Resend](https://resend.com) — gratuito hasta 3.000 emails/mes:
1. Crea cuenta en resend.com
2. Genera un API Key
3. Úsalo como `MAIL_PASSWORD` en el comando `fly secrets set`

---

## Variables de entorno principales

| Variable | Descripción |
|----------|-------------|
| `APP_KEY` | Clave de cifrado (generada con `php artisan key:generate`) |
| `APP_URL` | URL pública de la app |
| `DB_DATABASE` | Ruta al fichero SQLite |
| `MAIL_*` | Configuración del servidor de correo |

---

## Estructura del proyecto

```
heart-to-heart/
├── app/
│   ├── Http/Controllers/     # Controladores (Forum, Hearty, Emotions...)
│   ├── Models/               # Modelos Eloquent
│   └── Rules/                # Reglas de validación personalizadas
├── database/
│   └── migrations/           # Migraciones de base de datos
├── resources/
│   └── js/
│       └── Pages/            # Componentes Vue por sección (~50 páginas)
├── routes/
│   └── web.php               # Definición de rutas
├── public/
│   └── images/               # Imágenes estáticas (logo, etc.)
├── Dockerfile                # Imagen Docker para producción
├── fly.toml                  # Configuración de Fly.io
└── .env.example              # Variables de entorno de ejemplo
```

---

## Autor

Marcos — TFG DAW · 2026
