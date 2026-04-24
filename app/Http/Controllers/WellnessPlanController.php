<?php

namespace App\Http\Controllers;

use App\Models\WellnessPlan;
use Illuminate\Http\Request;
use Inertia\Inertia;

/**
 * WellnessPlanController
 *
 * Gestiona el plan semanal de bienestar personalizado:
 * - Muestra el plan activo de la semana actual y el historial de las últimas 4 semanas
 * - Genera un nuevo plan de 7 actividades según el objetivo del usuario
 * - Permite marcar el día actual como completado
 *
 * Cada plan tiene 7 actividades (una por día) adaptadas al objetivo elegido
 * (ansiedad, tristeza, estrés, sueño o general).
 */
class WellnessPlanController extends Controller
{
    /**
     * Muestra el plan de bienestar semanal del usuario.
     * Si existe un plan activo para la semana actual lo muestra; si no, invita a generar uno.
     * También muestra el historial de los últimos 4 planes.
     */
    public function index()
    {
        $userId = auth()->id();
        $user   = auth()->user();

        // El inicio de semana en lunes (Carbon: 1 = lunes)
        $inicioSemana = now()->startOfWeek(1)->toDateString();

        // Buscar el plan activo de esta semana (semana_inicio >= lunes de esta semana)
        $planActual = WellnessPlan::where('user_id', $userId)
            ->where('semana_inicio', '>=', $inicioSemana)
            ->orderByDesc('created_at')
            ->first();

        // Historial de los últimos 4 planes para mostrar el progreso semanal
        $historial = WellnessPlan::where('user_id', $userId)
            ->orderByDesc('semana_inicio')
            ->take(4)
            ->get()
            ->map(fn($p) => [
                'id'               => $p->id,
                'objetivo'         => $p->objetivo,
                'semana_inicio'    => $p->semana_inicio->format('d/m/Y'),
                'dias_completados' => $p->dias_completados,
                'completado'       => $p->completado,
                // Progreso como porcentaje sobre 7 días
                'progreso'         => round(($p->dias_completados / 7) * 100),
            ]);

        return Inertia::render('WellnessPlan/Index', [
            'planActual' => $planActual ? [
                'id'               => $planActual->id,
                'objetivo'         => $planActual->objetivo,
                'actividades'      => $planActual->actividades, // Array con las 7 actividades del plan
                'dias_check'       => $planActual->dias_check ?? [],
                'dias_completados' => $planActual->dias_completados,
                'completado'       => $planActual->completado,
                'semana_inicio'    => $planActual->semana_inicio->format('d/m/Y'),
                'progreso'         => round(($planActual->dias_completados / 7) * 100),
            ] : null,
            'historial' => $historial,
            // Objetivo principal del usuario para preseleccionar el tipo de plan
            'objetivo'  => $user->objetivo_principal ?? 'general',
        ]);
    }

    /**
     * Genera un nuevo plan semanal según el objetivo indicado.
     * Elimina el plan de la semana actual (si existe) antes de crear el nuevo.
     */
    public function generar(Request $request)
    {
        $request->validate([
            'objetivo' => 'required|string',
        ]);

        $userId       = auth()->id();
        $inicioSemana = now()->startOfWeek(1)->toDateString();

        // Eliminar el plan existente de esta semana para no tener duplicados
        WellnessPlan::where('user_id', $userId)
            ->where('semana_inicio', '>=', $inicioSemana)
            ->delete();

        // Generar las 7 actividades según el objetivo elegido
        $actividades = $this->generarActividades($request->objetivo);

        WellnessPlan::create([
            'user_id'       => $userId,
            'objetivo'      => $request->objetivo,
            'actividades'   => $actividades,
            'semana_inicio' => $inicioSemana,
            'dias_check'    => [], // Array vacío: ningún día completado aún
        ]);

        return redirect()->route('plan.index')->with('success', '🌱 Plan semanal generado');
    }

    /**
     * Marca el día actual como completado en el plan.
     * Si se completan los 7 días, el plan se marca como finalizado.
     * Solo el propietario del plan puede completar sus días.
     */
    public function completarDia(WellnessPlan $wellnessPlan)
    {
        if ($wellnessPlan->user_id !== auth()->id()) abort(403);

        $hoy  = now()->toDateString();
        $dias = $wellnessPlan->dias_check ?? [];

        // Evitar marcar el mismo día dos veces
        if (in_array($hoy, $dias)) {
            return back()->with('info', 'Ya completaste el día de hoy');
        }

        // Añadir el día de hoy y actualizar contadores
        $dias[] = $hoy;
        $wellnessPlan->dias_check       = $dias;
        $wellnessPlan->dias_completados = count($dias);
        // El plan se considera completado al marcar los 7 días
        $wellnessPlan->completado       = count($dias) >= 7;
        $wellnessPlan->save();

        $msg = $wellnessPlan->completado
            ? '🏆 ¡Semana completada! Increíble constancia.'
            : '✅ ¡Día completado! Sigue así.';

        return redirect()->route('plan.index')->with('success', $msg);
    }

    /**
     * Devuelve las 7 actividades del plan según el objetivo indicado.
     * Cada actividad incluye día, emoji, nombre, ruta interna, duración y descripción.
     * Si el objetivo no existe en el mapa, se usa el plan 'general' por defecto.
     *
     * @param  string  $objetivo  Clave del objetivo: 'ansiedad', 'tristeza', 'estres', 'sueno', 'general'
     * @return array  Array de 7 actividades ordenadas de lunes a domingo
     */
    private function generarActividades(string $objetivo): array
    {
        $planes = [
            // Plan para gestionar la ansiedad: respiración, mindfulness y grounding
            'ansiedad' => [
                ['dia' => 'Lunes',     'emoji' => '🫁', 'nombre' => 'Respiración 4-7-8',   'ruta' => '/respiracion',        'duracion' => '5 min',  'desc' => 'Empieza la semana calmando el sistema nervioso'],
                ['dia' => 'Martes',    'emoji' => '📓', 'nombre' => 'Diario de gratitud',   'ruta' => '/diario',             'duracion' => '10 min', 'desc' => 'Escribe 3 cosas positivas del día'],
                ['dia' => 'Miércoles', 'emoji' => '🧘', 'nombre' => 'Meditación guiada',    'ruta' => '/meditacion',         'duracion' => '10 min', 'desc' => 'Sesión de mindfulness para calmar la mente'],
                ['dia' => 'Jueves',    'emoji' => '🌍', 'nombre' => 'Grounding 5-4-3-2-1',  'ruta' => '/tecnica-5-4-3-2-1', 'duracion' => '5 min',  'desc' => 'Ancla tu mente al presente'],
                ['dia' => 'Viernes',   'emoji' => '🎵', 'nombre' => 'Sonidos relajantes',   'ruta' => '/sonidos',            'duracion' => '15 min', 'desc' => 'Termina la semana laboral con calma'],
                ['dia' => 'Sábado',    'emoji' => '🍵', 'nombre' => 'Ritual de infusión',   'ruta' => '/infusiones',         'duracion' => '20 min', 'desc' => 'Ritual de autocuidado del fin de semana'],
                ['dia' => 'Domingo',   'emoji' => '🌈', 'nombre' => 'Visualización guiada', 'ruta' => '/visualizacion',      'duracion' => '10 min', 'desc' => 'Prepara tu mente para la nueva semana'],
            ],
            // Plan para la tristeza: movimiento, expresión emocional y autocompasión
            'tristeza' => [
                ['dia' => 'Lunes',     'emoji' => '🏃', 'nombre' => 'Ejercicio terapéutico', 'ruta' => '/ejercicio',        'duracion' => '15 min', 'desc' => 'Libera endorfinas y serotonina'],
                ['dia' => 'Martes',    'emoji' => '💬', 'nombre' => 'Hablar con Hearty',      'ruta' => '/hearty',           'duracion' => '10 min', 'desc' => 'Comparte cómo te sientes hoy'],
                ['dia' => 'Miércoles', 'emoji' => '🎶', 'nombre' => 'Musicoterapia',          'ruta' => '/musicoterapia',    'duracion' => '20 min', 'desc' => 'Usa la música para regular emociones'],
                ['dia' => 'Jueves',    'emoji' => '📝', 'nombre' => 'Journaling emocional',   'ruta' => '/journaling',       'duracion' => '15 min', 'desc' => 'Procesa tus emociones escribiendo'],
                ['dia' => 'Viernes',   'emoji' => '💗', 'nombre' => 'Autocompasión',          'ruta' => '/autocompasion',    'duracion' => '10 min', 'desc' => 'Trátate con la misma amabilidad que a otros'],
                ['dia' => 'Sábado',    'emoji' => '🤸', 'nombre' => 'Yoga suave',             'ruta' => '/yoga',             'duracion' => '20 min', 'desc' => 'Movimiento suave para liberar tensión'],
                ['dia' => 'Domingo',   'emoji' => '✨', 'nombre' => 'Gratitud visual',        'ruta' => '/gratitud-visual',  'duracion' => '15 min', 'desc' => 'Construye tu tablero de gratitud'],
            ],
            // Plan para el estrés: relajación muscular, respiración y tapping
            'estres' => [
                ['dia' => 'Lunes',     'emoji' => '💆', 'nombre' => 'Relajación muscular',  'ruta' => '/relajacion-muscular', 'duracion' => '15 min', 'desc' => 'Libera tensión acumulada del cuerpo'],
                ['dia' => 'Martes',    'emoji' => '🫁', 'nombre' => 'Respiración de caja',  'ruta' => '/respiracion',         'duracion' => '5 min',  'desc' => 'Técnica Navy SEAL para el estrés agudo'],
                ['dia' => 'Miércoles', 'emoji' => '🏃', 'nombre' => 'Ejercicio liberador',  'ruta' => '/ejercicio',           'duracion' => '15 min', 'desc' => 'Quema el exceso de cortisol y adrenalina'],
                ['dia' => 'Jueves',    'emoji' => '🧘', 'nombre' => 'Meditación antiestrés','ruta' => '/meditacion',          'duracion' => '10 min', 'desc' => 'Pausa activa en el día'],
                ['dia' => 'Viernes',   'emoji' => '👆', 'nombre' => 'EFT Tapping',          'ruta' => '/tapping',             'duracion' => '10 min', 'desc' => 'Libera el estrés de la semana'],
                ['dia' => 'Sábado',    'emoji' => '🌲', 'nombre' => 'Visualización bosque', 'ruta' => '/visualizacion',       'duracion' => '10 min', 'desc' => 'Evade mentalmente a la naturaleza'],
                ['dia' => 'Domingo',   'emoji' => '📓', 'nombre' => 'Reflexión semanal',    'ruta' => '/diario',              'duracion' => '15 min', 'desc' => 'Qué aprendiste esta semana'],
            ],
            // Plan para mejorar el sueño: rituales nocturnos y técnicas de relajación
            'sueno' => [
                ['dia' => 'Lunes',     'emoji' => '🍵', 'nombre' => 'Infusión de valeriana', 'ruta' => '/infusiones',          'duracion' => '20 min', 'desc' => 'Empieza el ritual nocturno'],
                ['dia' => 'Martes',    'emoji' => '💆', 'nombre' => 'Relajación muscular',   'ruta' => '/relajacion-muscular', 'duracion' => '15 min', 'desc' => 'Relaja el cuerpo antes de dormir'],
                ['dia' => 'Miércoles', 'emoji' => '🎵', 'nombre' => 'Sonidos para dormir',   'ruta' => '/sonidos',             'duracion' => '∞',      'desc' => 'Lluvia suave para conciliar el sueño'],
                ['dia' => 'Jueves',    'emoji' => '🌈', 'nombre' => 'Visualización playa',   'ruta' => '/visualizacion',       'duracion' => '10 min', 'desc' => 'Viaje mental relajante antes de dormir'],
                ['dia' => 'Viernes',   'emoji' => '🫁', 'nombre' => 'Respiración 4-7-8',     'ruta' => '/respiracion',         'duracion' => '5 min',  'desc' => 'La técnica más efectiva para dormirse'],
                ['dia' => 'Sábado',    'emoji' => '📓', 'nombre' => 'Diario nocturno',       'ruta' => '/diario',              'duracion' => '10 min', 'desc' => 'Vacía la mente antes de dormir'],
                ['dia' => 'Domingo',   'emoji' => '🧘', 'nombre' => 'Meditación del sueño',  'ruta' => '/meditacion',          'duracion' => '10 min', 'desc' => 'Meditación body scan para relajarse'],
            ],
            // Plan general: variedad de técnicas para bienestar global
            'general' => [
                ['dia' => 'Lunes',     'emoji' => '📊', 'nombre' => 'Registro emocional',   'ruta' => '/mis-emociones', 'duracion' => '5 min',  'desc' => 'Check-in emocional de la semana'],
                ['dia' => 'Martes',    'emoji' => '🫁', 'nombre' => 'Respiración guiada',   'ruta' => '/respiracion',   'duracion' => '5 min',  'desc' => 'Técnica de respiración diaria'],
                ['dia' => 'Miércoles', 'emoji' => '🏃', 'nombre' => 'Ejercicio moderado',   'ruta' => '/ejercicio',     'duracion' => '15 min', 'desc' => 'Mueve el cuerpo a mitad de semana'],
                ['dia' => 'Jueves',    'emoji' => '📓', 'nombre' => 'Diario de gratitud',   'ruta' => '/diario',        'duracion' => '10 min', 'desc' => '3 cosas positivas del día'],
                ['dia' => 'Viernes',   'emoji' => '🧘', 'nombre' => 'Meditación',           'ruta' => '/meditacion',    'duracion' => '10 min', 'desc' => 'Cierra la semana con calma'],
                ['dia' => 'Sábado',    'emoji' => '🌿', 'nombre' => 'Técnica libre',        'ruta' => '/respiracion',   'duracion' => '15 min', 'desc' => 'Elige la técnica que más te apetezca'],
                ['dia' => 'Domingo',   'emoji' => '💬', 'nombre' => 'Reflexión con Hearty', 'ruta' => '/hearty',        'duracion' => '10 min', 'desc' => 'Prepara tu mente para la nueva semana'],
            ],
        ];

        // Si el objetivo no existe en el mapa, usar el plan general como fallback
        return $planes[$objetivo] ?? $planes['general'];
    }
}