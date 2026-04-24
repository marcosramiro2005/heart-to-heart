<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\HeartySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Carbon\Carbon;

/**
 * HeartyController
 *
 * Gestiona el chatbot emocional "Hearty", que se comunica con un servidor Flask
 * corriendo en local (puerto 5000) mediante peticiones HTTP.
 *
 * Flujo general:
 * 1. El usuario abre /hearty → se carga el historial de mensajes y la sesión
 * 2. El frontend llama a /hearty/inicio para obtener el saludo inicial de Hearty
 * 3. Cada mensaje del usuario se envía a /hearty/chat, que lo reenvía al Flask
 * 4. Flask responde con texto, emoción detectada y técnicas sugeridas
 * 5. La sesión de Hearty se actualiza con la emoción detectada
 */
class HeartyController extends Controller
{
    /**
     * URL base del servidor Flask (chatbot de Python).
     * Debe estar ejecutándose localmente para que funcione el chat.
     */
    private $chatbotUrl = 'http://127.0.0.1:5000';

    /**
     * Muestra la página principal del chatbot.
     * Carga los últimos 30 mensajes del historial y crea la sesión si no existe.
     */
    public function index()
    {
        $user   = auth()->user();
        $userId = $user->id;

        // Obtener los últimos 30 mensajes ordenados cronológicamente para mostrar la conversación
        $mensajes = ChatMessage::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->take(30)
            ->get();

        // firstOrCreate: si el usuario ya tiene sesión la reutiliza, si no la crea nueva
        $heartySession = HeartySession::firstOrCreate(
            ['user_id' => $userId],
            [
                'session_count'    => 0,
                'emotions_history' => [],
                'last_session_at'  => now(),
            ]
        );

        // Cada vez que el usuario entra, se cuenta como una nueva sesión
        $heartySession->increment('session_count');
        $heartySession->update(['last_session_at' => now()]);

        return Inertia::render('Hearty/Index', [
            'mensajes'      => $mensajes,
            'heartySession' => [
                'session_count'    => $heartySession->session_count,
                'dominant_emotion' => $heartySession->dominant_emotion,
                'emotions_history' => $heartySession->emotions_history ?? [],
                'last_summary'     => $heartySession->last_summary,
            ],
            'userName' => $user->name,
        ]);
    }

    /**
     * Genera el mensaje de bienvenida inicial de Hearty al abrir el chat.
     * Intenta obtenerlo del servidor Flask; si falla, usa un mensaje de fallback local.
     * Envía el historial emocional del usuario para que Flask personalice el saludo.
     */
    public function inicio(Request $request)
    {
        $userId        = auth()->id();
        $heartySession = HeartySession::where('user_id', $userId)->first();

        // Construir una cadena de emociones anteriores separadas por comas para el contexto
        $historialEmociones = collect($heartySession?->emotions_history ?? [])
            ->pluck('emocion')
            ->filter()
            ->implode(',');

        try {
            $response = Http::timeout(5)->get("{$this->chatbotUrl}/inicio", [
                'historial' => $historialEmociones,
                'sesiones'  => $heartySession?->session_count ?? 0,
                // Solo se envía el primer nombre para hacer el saludo más cercano
                'nombre'    => explode(' ', auth()->user()->name)[0],
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }
            throw new \Exception('Flask no responde');

        } catch (\Exception $e) {
            // Si Flask no está disponible, se devuelve un mensaje de bienvenida hardcoded
            $sesiones = $heartySession?->session_count ?? 0;
            $mensaje  = $sesiones > 1
                ? "¡Hola de nuevo! Soy Hearty 💚 Me alegra que hayas vuelto. ¿Cómo te sientes hoy?"
                : "¡Hola! Soy Hearty, tu guía emocional 💚 ¿Cómo te sientes hoy?";

            return response()->json([
                'mensaje'    => $mensaje,
                'opciones'   => ['😊 Bien', '😌 Tranquilo/a', '😰 Ansioso/a', '😢 Triste', '😠 Enfadado/a', '😴 Cansado/a'],
                'pregunta_id'=> 'bienvenida',
            ]);
        }
    }

    /**
     * Procesa un mensaje del usuario y devuelve la respuesta de Hearty.
     * Guarda tanto el mensaje del usuario como la respuesta del bot en la base de datos.
     * Si Flask detecta una emoción, actualiza el historial emocional de la sesión.
     */
    public function chat(Request $request)
    {
        $request->validate([
            'mensaje'        => 'required|string|max:500',
            'pregunta_actual'=> 'nullable|string',
        ]);

        $userId = auth()->id();

        // Persistir el mensaje del usuario antes de llamar a Flask
        ChatMessage::create([
            'user_id' => $userId,
            'sender'  => 'user',
            'message' => $request->mensaje,
        ]);

        // Obtener el historial emocional para dárselo a Flask como contexto
        $heartySession      = HeartySession::where('user_id', $userId)->first();
        $historialEmociones = $heartySession?->emotions_history ?? [];

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->chatbotUrl}/chat", [
                    'mensaje'        => $request->mensaje,
                    'pregunta_actual'=> $request->pregunta_actual ?? 'bienvenida',
                    'historial'      => $historialEmociones,
                    'contexto'       => [],
                ]);

            if (!$response->successful()) {
                throw new \Exception('Error Flask: ' . $response->status());
            }

            $data = $response->json();

            // Guardar la respuesta generada por Hearty, incluyendo la emoción detectada
            ChatMessage::create([
                'user_id'          => $userId,
                'sender'           => 'hearty',
                'message'          => $data['respuesta'],
                'emotion_detected' => $data['emocion_detectada'] ?? null,
            ]);

            // Si Flask detectó una emoción, actualizar el historial de la sesión
            if (!empty($data['emocion_detectada'])) {
                $this->actualizarSesionHearty($userId, $data['emocion_detectada'], $data['intensidad'] ?? null);
            }

            return response()->json($data);

        } catch (\Exception $e) {
            \Log::error('Error Hearty: ' . $e->getMessage());
            $respuesta = "Lo siento, tengo problemas técnicos ahora mismo 💙 Pero recuerda: estoy aquí para ti.";

            // Guardar el mensaje de error como respuesta de Hearty para mantener el historial
            ChatMessage::create([
                'user_id' => $userId,
                'sender'  => 'hearty',
                'message' => $respuesta,
            ]);

            return response()->json(['respuesta' => $respuesta, 'tecnicas_sugeridas' => []]);
        }
    }

    /**
     * Elimina todos los mensajes del chat del usuario actual.
     * Útil para empezar una conversación desde cero.
     */
    public function limpiarChat()
    {
        ChatMessage::where('user_id', auth()->id())->delete();
        return back()->with('success', 'Conversación borrada');
    }

    /**
     * Actualiza la sesión de Hearty con una nueva emoción detectada.
     * Mantiene un historial de las últimas 20 emociones para no crecer indefinidamente.
     * Recalcula la emoción dominante e intensidad media tras cada actualización.
     *
     * @param  int     $userId    ID del usuario
     * @param  string  $emocion   Emoción detectada por Flask
     * @param  float|null  $intensidad  Intensidad de la emoción (1-10) si la devuelve Flask
     */
    private function actualizarSesionHearty(int $userId, string $emocion, ?float $intensidad): void
    {
        $session = HeartySession::where('user_id', $userId)->first();
        if (!$session) return;

        $historial   = $session->emotions_history ?? [];
        $historial[] = [
            'emocion'    => $emocion,
            'intensidad' => $intensidad,
            'fecha'      => Carbon::now()->format('Y-m-d'),
        ];

        // Limitar el historial a las últimas 20 entradas para no crecer indefinidamente
        if (count($historial) > 20) {
            $historial = array_slice($historial, -20);
        }

        // Determinar la emoción que más veces ha aparecido (emoción dominante)
        $conteo    = array_count_values(array_column($historial, 'emocion'));
        arsort($conteo);
        $dominante = array_key_first($conteo);

        // Calcular la media de intensidad ignorando valores nulos
        $intensidades = array_filter(array_column($historial, 'intensidad'));
        $mediaInt     = count($intensidades) > 0
            ? round(array_sum($intensidades) / count($intensidades), 1)
            : null;

        // Generar un resumen textual para mostrar en el perfil de sesión
        $resumen = $this->generarResumen($emocion, $dominante, count($historial));

        $session->update([
            'emotions_history' => $historial,
            'dominant_emotion' => $dominante,
            'avg_intensity'    => $mediaInt,
            'last_summary'     => $resumen,
        ]);
    }

    /**
     * Genera una frase descriptiva del patrón emocional de las últimas sesiones.
     * Se almacena en la sesión como 'last_summary' para mostrarse en el perfil.
     *
     * @param  string  $emocionActual     Última emoción detectada
     * @param  string  $emocionDominante  Emoción más frecuente en el historial
     * @param  int     $totalSesiones     Número total de entradas en el historial
     * @return string  Frase descriptiva en lenguaje natural
     */
    private function generarResumen(string $emocionActual, string $emocionDominante, int $totalSesiones): string
    {
        // Mapeo de emociones a descripciones más naturales para el resumen
        $mapeo = [
            'ansiedad'  => 'momentos de ansiedad',
            'tristeza'  => 'períodos de tristeza',
            'alegría'   => 'estados de alegría',
            'calma'     => 'momentos de calma',
            'cansancio' => 'episodios de cansancio',
            'enfado'    => 'momentos de enfado',
        ];

        $descripcion = $mapeo[$emocionDominante] ?? $emocionDominante;
        return "En las últimas {$totalSesiones} conversaciones, el patrón emocional más frecuente han sido {$descripcion}.";
    }
}