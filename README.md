# Heart to Heart

Plataforma web de bienestar emocional y salud mental desarrollada como Trabajo de Fin de Grado (TFG) del Ciclo Superior en Desarrollo de Aplicaciones Web.

**Aplicacion en produccion:** https://heart-to-heart-marcos.fly.dev

**Repositorio:** https://github.com/marcosramiro2005/heart-to-heart

---

## Descripcion

Heart to Heart es una aplicacion web pensada para acompanar a las personas en su camino hacia el bienestar emocional. Proporciona herramientas practicas, seguimiento del estado de animo y un espacio de comunidad, todo integrado en una interfaz cuidada y accesible.

---

## Funcionalidades

| Modulo | Descripcion |
|--------|-------------|
| **Hearty** | Asistente de IA empatico disponible 24/7 (chatbot con Python + Flask) |
| **Foro** | Comunidad de apoyo entre usuarios con posts, comentarios, likes y modo anonimo |
| **Tecnicas de bienestar** | Respiracion, meditacion, yoga, journaling, visualizacion, tapping, grounding, autocompasion, musicoterapia, relajacion muscular, ejercicio e infusiones |
| **Dashboard emocional** | Registro y seguimiento del estado de animo con graficas (Chart.js) y calendario |
| **Diario personal** | Espacio privado de reflexion con entradas diarias |
| **Retos** | Sistema de retos de bienestar con progreso y rachas |
| **Plan semanal** | Plan de bienestar personalizado semanal |
| **Test PHQ-9** | Test de bienestar semanal con seguimiento de resultados |
| **Logros y badges** | Sistema de logros, niveles y notificaciones toast |
| **Modo Focus** | Sesiones de concentracion con temporizador Pomodoro |
| **Biblioteca** | Articulos y recursos de salud mental con opcion de guardar favoritos |
| **Noticias** | Noticias de salud mental con buscador |
| **SOS** | Recursos de crisis y acceso directo a la linea 024 |
| **Perfil** | Avatar, bio, actividad, logros y configuracion de seguridad |
| **Onboarding** | Flujo de bienvenida personalizado para nuevos usuarios |

---

## Stack tecnologico

| Capa | Tecnologia |
|------|-----------|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend | Vue 3 + Inertia.js |
| Estilos | CSS personalizado + Tailwind CSS |
| Build | Vite 8 |
| Base de datos | SQLite |
| Autenticacion | Laravel Breeze |
| Graficas | Chart.js + vue-chartjs |
| Chatbot IA | Python + Flask |
| Email | Resend |
| Desplegue | Docker + Fly.io |

---

## Instalacion local

### Requisitos previos
- PHP 8.3+
- Composer 2
- Node.js 20+
- npm
- Python 3 + pip (para el chatbot Hearty)

### Pasos

```bash
# 1. Clonar el repositorio
git clone https://github.com/marcosramiro2005/heart-to-heart.git
cd heart-to-heart

# 2. Instalar dependencias PHP
composer install

# 3. Instalar dependencias Node
npm install

# 4. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 5. Crear la base de datos y ejecutar migraciones con datos de prueba
php artisan migrate --seed

# 6. Compilar assets
npm run build

# 7. Arrancar el servidor de desarrollo
php artisan serve
```

La app estara disponible en `http://localhost:8000`.

Para arrancar el chatbot Hearty en local:

```bash
cd chatbot
pip install -r requirements.txt
python app.py
```

---

## Despliegue en produccion (Fly.io)

### Requisitos
- Cuenta en [fly.io](https://fly.io)
- [flyctl](https://fly.io/docs/flyctl/install/) instalado

### Primera vez

```bash
# 1. Iniciar sesion
fly auth login

# 2. Crear la app
fly apps create nombre-de-tu-app

# 3. Crear volumen persistente para la base de datos
fly volumes create heart_data --region mad --size 1

# 4. Configurar variables secretas
fly secrets set APP_KEY=$(php artisan key:generate --show)
fly secrets set APP_NAME="Heart to Heart"
fly secrets set MAIL_MAILER=resend
fly secrets set MAIL_HOST=smtp.resend.com
fly secrets set MAIL_PORT=465
fly secrets set MAIL_USERNAME=resend
fly secrets set MAIL_PASSWORD=tu_api_key_de_resend
fly secrets set MAIL_FROM_ADDRESS=noreply@tudominio.com

# 5. Desplegar
fly deploy

# 6. Abrir la app
fly open
```

### Redesplegar tras cambios

```bash
git add .
git commit -m "descripcion del cambio"
fly deploy
```

---

## Variables de entorno principales

| Variable | Descripcion |
|----------|-------------|
| `APP_KEY` | Clave de cifrado (generada con `php artisan key:generate`) |
| `APP_URL` | URL publica de la app |
| `DB_DATABASE` | Ruta al fichero SQLite |
| `MAIL_*` | Configuracion del servidor de correo (Resend) |

---

## Estructura del proyecto

```
heart-to-heart/
├── app/
│   ├── Http/Controllers/     # Controladores (Forum, Hearty, Emotions, Challenges...)
│   ├── Models/               # Modelos Eloquent
│   └── Rules/                # Reglas de validacion personalizadas
├── chatbot/                  # Chatbot Hearty (Python + Flask)
├── database/
│   └── migrations/           # Migraciones de base de datos
├── resources/
│   └── js/
│       └── Pages/            # Componentes Vue por seccion
│           ├── Tecnicas/     # 14 tecnicas de bienestar
│           ├── Forum/        # Foro de comunidad
│           ├── Hearty/       # Chatbot IA
│           ├── EmotionalDashboard/
│           ├── Diary/
│           ├── Challenges/
│           ├── WellnessPlan/
│           ├── WellnessTest/
│           ├── Achievements/
│           ├── Focus/
│           ├── Resources/
│           ├── News/
│           ├── SOS/
│           ├── Profile/
│           └── Auth/
├── routes/
│   └── web.php               # Definicion de rutas
├── Dockerfile                # Imagen Docker para produccion
├── fly.toml                  # Configuracion de Fly.io
└── .env.example              # Variables de entorno de ejemplo
```

---

## Autor

Marcos — TFG DAW · 2025/2026
