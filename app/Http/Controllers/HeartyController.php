<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class HeartyController extends Controller
{
    private $chatbotUrl = 'http://localhost:5000';

    public function index()
    {
        $mensajes = ChatMessage::where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Hearty/Index', [
            'mensajes' => $mensajes
        ]);
    }

    public function inicio()
    {
        try {
            $response = Http::timeout(5)->get("{$this->chatbotUrl}/inicio");

            if ($response->successful()) {
                return response()->json($response->json());
            }

            throw new \Exception('Flask no responde');

        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => '¡Hola! Soy Hearty 💚 ¿Cómo te sientes hoy?',
                'opciones' => ['😊 Bien', '😌 Tranquilo/a', '😰 Ansioso/a', '😢 Triste', '😠 Enfadado/a', '😴 Cansado/a'],
                'pregunta_id' => 'bienvenida'
            ]);
        }
    }

    public function chat(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:500',
            'pregunta_actual' => 'nullable|string',
        ]);

        // Guardar mensaje del usuario en BD
        ChatMessage::create([
            'user_id' => auth()->id(),
            'sender' => 'user',
            'message' => $request->mensaje,
        ]);

        try {
            $response = Http::timeout(10)
                ->withHeaders(['Content-Type' => 'application/json'])
                ->post("{$this->chatbotUrl}/chat", [
                    'mensaje' => $request->mensaje,
                    'pregunta_actual' => $request->pregunta_actual ?? 'bienvenida',
                    'contexto' => []
                ]);

            if (!$response->successful()) {
                throw new \Exception('Error en Flask: ' . $response->status());
            }

            $data = $response->json();

            // Guardar respuesta de Hearty en BD
            ChatMessage::create([
                'user_id' => auth()->id(),
                'sender' => 'hearty',
                'message' => $data['respuesta'],
                'emotion_detected' => $request->mensaje,
            ]);

            return response()->json($data);

        } catch (\Exception $e) {
            \Log::error('Error Hearty: ' . $e->getMessage());

            $respuesta = "Lo siento, tengo problemas técnicos ahora mismo 💙 Pero recuerda: estoy aquí para ti.";

            ChatMessage::create([
                'user_id' => auth()->id(),
                'sender' => 'hearty',
                'message' => $respuesta,
            ]);

            return response()->json([
                'respuesta' => $respuesta,
                'tecnicas_sugeridas' => [],
                'error_detalle' => $e->getMessage()
            ]);
        }
    }
}