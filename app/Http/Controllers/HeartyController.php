<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use App\Models\HeartySession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;
use Carbon\Carbon;

class HeartyController extends Controller
{
    private $chatbotUrl = 'http://127.0.0.1:5000';

    public function index()
    {
        $user   = auth()->user();
        $userId = $user->id;

        // Últimos 30 mensajes del historial
        $mensajes = ChatMessage::where('user_id', $userId)
            ->orderBy('created_at', 'asc')
            ->take(30)
            ->get();

        // Obtener o crear la sesión de Hearty
        $heartySession = HeartySession::firstOrCreate(
            ['user_id' => $userId],
            [
                'session_count'    => 0,
                'emotions_history' => [],
                'last_session_at'  => now(),
            ]
        );

        // Incrementar contador de sesiones
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

    public function inicio(Request $request)
    {
        $userId        = auth()->id();
        $heartySession = HeartySession::where('user_id', $userId)->first();

        $historialEmociones = collect($heartySession?->emotions_history ?? [])
            ->pluck('emocion')
            ->filter()
            ->implode(',');

        try {
            $response = Http::timeout(5)->get("{$this->chatbotUrl}/inicio", [
                'historial' => $historialEmociones,
                'sesiones'  => $heartySession?->session_count ?? 0,
                'nombre'    => explode(' ', auth()->user()->name)[0], // solo el primer nombre
            ]);

            if ($response->successful()) {
                return response()->json($response->json());
            }
            throw new \Exception('Flask no responde');

        } catch (\Exception $e) {
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

    public function chat(Request $request)
    {
        $request->validate([
            'mensaje'        => 'required|string|max:500',
            'pregunta_actual'=> 'nullable|string',
        ]);

        $userId = auth()->id();

        // Guardar mensaje del usuario
        ChatMessage::create([
            'user_id' => $userId,
            'sender'  => 'user',
            'message' => $request->mensaje,
        ]);

        // Obtener historial de emociones para el contexto
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

            // Guardar respuesta de Hearty
            ChatMessage::create([
                'user_id'          => $userId,
                'sender'           => 'hearty',
                'message'          => $data['respuesta'],
                'emotion_detected' => $data['emocion_detectada'] ?? null,
            ]);

            // Actualizar la sesión de Hearty con la emoción detectada
            if (!empty($data['emocion_detectada'])) {
                $this->actualizarSesionHearty($userId, $data['emocion_detectada'], $data['intensidad'] ?? null);
            }

            return response()->json($data);

        } catch (\Exception $e) {
            \Log::error('Error Hearty: ' . $e->getMessage());
            $respuesta = "Lo siento, tengo problemas técnicos ahora mismo 💙 Pero recuerda: estoy aquí para ti.";

            ChatMessage::create([
                'user_id' => $userId,
                'sender'  => 'hearty',
                'message' => $respuesta,
            ]);

            return response()->json(['respuesta' => $respuesta, 'tecnicas_sugeridas' => []]);
        }
    }

    public function limpiarChat()
    {
        ChatMessage::where('user_id', auth()->id())->delete();
        return back()->with('success', 'Conversación borrada');
    }

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

        // Mantener solo los últimos 20 registros
        if (count($historial) > 20) {
            $historial = array_slice($historial, -20);
        }

        // Calcular emoción dominante
        $conteo = array_count_values(array_column($historial, 'emocion'));
        arsort($conteo);
        $dominante = array_key_first($conteo);

        // Calcular intensidad media
        $intensidades = array_filter(array_column($historial, 'intensidad'));
        $mediaInt     = count($intensidades) > 0
            ? round(array_sum($intensidades) / count($intensidades), 1)
            : null;

        // Generar resumen de la sesión
        $resumen = $this->generarResumen($emocion, $dominante, count($historial));

        $session->update([
            'emotions_history'    => $historial,
            'dominant_emotion'    => $dominante,
            'avg_intensity'       => $mediaInt,
            'last_summary'        => $resumen,
        ]);
    }

    private function generarResumen(string $emocionActual, string $emocionDominante, int $totalSesiones): string
    {
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