<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;

// Middleware de Inertia.js que se ejecuta en cada petición HTTP.
// Su función principal es compartir datos globales con TODOS los componentes Vue de la app.
// Los datos compartidos aquí están disponibles en cualquier componente mediante usePage().props
class HandleInertiaRequests extends Middleware
{
    // Plantilla Blade raíz que carga la aplicación en la primera visita (resources/views/app.blade.php).
    // En esta plantilla se monta el div #app donde Vue renderiza los componentes.
    protected $rootView = 'app';

    // Devuelve la versión de los assets para forzar recarga del cliente al desplegar nuevas versiones.
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    // Define los props globales compartidos con todos los componentes Vue de la aplicación.
    // Estos datos se envían en cada respuesta de Inertia y son accesibles desde cualquier página.
    public function share(Request $request): array
    {
        return [
            // Incluye los props compartidos por defecto de Inertia (errors de validación, etc.)
            ...parent::share($request),

            // Datos del usuario autenticado (null si no hay sesión activa)
            // Solo se comparte lo estrictamente necesario para la barra de navegación
            'auth' => [
                'user' => $request->user() ? [
                    'id'     => $request->user()->id,
                    'name'   => $request->user()->name,
                    'email'  => $request->user()->email,
                    'avatar' => $request->user()->avatar ?? '👤',
                ] : null,
            ],

            // Mensajes flash de sesión para mostrar notificaciones tras redirecciones
            // (ej: 'Emoción registrada correctamente' tras enviar el formulario)
            'flash' => [
                'success' => session('success'),
                'error'   => session('error'),
                'info'    => session('info'),
            ],

            // Si el usuario no ha completado el onboarding, los layouts pueden redirigirle
            'onboarding_pendiente' => $request->user() && !$request->user()->onboarding_completado,
        ];
    }


}


