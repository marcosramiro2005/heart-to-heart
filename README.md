<div align="center">

# 💚 Heart to Heart

### Plataforma web de bienestar emocional y salud mental

[![Laravel](https://img.shields.io/badge/Laravel-13-FF2D20?style=for-the-badge&logo=laravel&logoColor=white)](https://laravel.com)
[![Vue.js](https://img.shields.io/badge/Vue.js-3-4FC08D?style=for-the-badge&logo=vue.js&logoColor=white)](https://vuejs.org)
[![Inertia.js](https://img.shields.io/badge/Inertia.js-2-9553E9?style=for-the-badge&logo=inertia&logoColor=white)](https://inertiajs.com)
[![SQLite](https://img.shields.io/badge/SQLite-003B57?style=for-the-badge&logo=sqlite&logoColor=white)](https://sqlite.org)
[![Docker](https://img.shields.io/badge/Docker-2496ED?style=for-the-badge&logo=docker&logoColor=white)](https://docker.com)
[![Fly.io](https://img.shields.io/badge/Fly.io-8B5CF6?style=for-the-badge&logo=fly.io&logoColor=white)](https://fly.io)

**Trabajo de Fin de Grado — Ciclo Superior en Desarrollo de Aplicaciones Web · 2025/2026**

🌐 **[heart-to-heart-marcos.fly.dev](https://heart-to-heart-marcos.fly.dev)**

</div>

---

## 📖 Sobre el proyecto

Heart to Heart es una aplicación web pensada para acompañar a las personas en su camino hacia el bienestar emocional. Ofrece herramientas prácticas, seguimiento del estado de ánimo y un espacio de comunidad, todo integrado en una interfaz cuidada y accesible.

---

## ✨ Funcionalidades

| Módulo | Descripción |
|--------|-------------|
| 🤖 **Hearty** | Asistente de IA empático disponible 24/7 (Python + Flask) |
| 💬 **Foro** | Comunidad de apoyo con posts, comentarios, likes y modo anónimo |
| 🧘 **Técnicas de bienestar** | 14 técnicas: respiración, meditación, yoga, journaling, tapping, grounding, visualización, autocompasión,musicoterapia, relajación muscular, ejercicio e infusiones |
| 📊 **Dashboard emocional** | Registro y seguimiento del estado de ánimo con gráficas y calendario |
| 📓 **Diario personal** | Espacio privado de reflexión con entradas diarias |
| 🏆 **Retos y logros** | Sistema de retos, badges, niveles y notificaciones |
| 📅 **Plan semanal** | Plan de bienestar personalizado |
| 🧪 **Test PHQ-9** | Test de bienestar semanal con seguimiento de resultados |
| ⏱️ **Modo Focus** | Sesiones de concentración con temporizador Pomodoro |
| 📚 **Biblioteca** | Recursos de salud mental con opción de guardar favoritos |
| 📰 **Noticias** | Noticias de salud mental con buscador |
| 🆘 **SOS** | Recursos de crisis y acceso directo a la línea 024 |
| 👤 **Perfil** | Avatar, bio, actividad, logros y seguridad |
| 🎉 **Onboarding** | Flujo de bienvenida personalizado para nuevos usuarios |

---

## 🛠️ Stack tecnológico

| Capa | Tecnología |
|------|-----------|
| Backend | Laravel 13 (PHP 8.3) |
| Frontend | Vue 3 + Inertia.js |
| Estilos | CSS personalizado + Tailwind CSS |
| Build | Vite 8 |
| Base de datos | SQLite |
| Autenticación | Laravel Breeze |
| Gráficas | Chart.js + vue-chartjs |
| Chatbot IA | Python + Flask |
| Email | Resend |
| Despliegue | Docker + Fly.io |

---

## 🚀 Instalación local

### Requisitos
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

# 2. Instalar dependencias
composer install
npm install

# 3. Configurar el entorno
cp .env.example .env
php artisan key:generate

# 4. Base de datos
php artisan migrate --seed

# 5. Compilar assets y arrancar
npm run build
php artisan serve
```

> La app estará disponible en `http://localhost:8000`

Para el chatbot Hearty:

```bash
cd chatbot
pip install -r requirements.txt
python app.py
```

---

## ⚙️ Variables de entorno

| Variable | Descripción |
|----------|-------------|
| `APP_KEY` | Clave de cifrado (`php artisan key:generate`) |
| `APP_URL` | URL de la app |
| `DB_DATABASE` | Ruta al fichero SQLite |
| `MAIL_*` | Configuración del servidor de correo (Resend) |

---

## 📁 Estructura del proyecto

```
heart-to-heart/
├── app/
│   ├── Http/Controllers/     # Controladores
│   ├── Models/               # Modelos Eloquent
│   └── Rules/                # Reglas de validación
├── chatbot/                  # Chatbot Hearty (Python + Flask)
├── database/
│   └── migrations/
├── resources/
│   └── js/Pages/
│       ├── Tecnicas/         # 14 técnicas de bienestar
│       ├── Forum/
│       ├── Hearty/
│       ├── EmotionalDashboard/
│       ├── Diary/
│       ├── Challenges/
│       ├── WellnessPlan/
│       ├── WellnessTest/
│       ├── Achievements/
│       ├── Focus/
│       ├── Resources/
│       ├── News/
│       ├── SOS/
│       ├── Profile/
│       └── Auth/
├── Dockerfile
├── fly.toml
└── .env.example
```

---

<div align="center">

Desarrollado por **Marcos** · TFG DAW 2025/2026

</div>
