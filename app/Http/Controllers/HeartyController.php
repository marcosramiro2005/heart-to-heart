<?php

namespace App\Http\Controllers;

use App\Models\ChatMessage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Inertia\Inertia;

class HeartyController extends Controller
{
    private $chatbotUrl = 'http://127.0.0.1:5000';

    // Muestra la página del chat
    public function index()
    {
        $mensajes = ChatMessage::where('user_id', auth()->id())
            ->orderBy('created_at', 'asc')
            ->get();

        return Inertia::render('Hearty/Index', [
            'mensajes' => $mensajes
        ]);
    }

    // Obtiene el mensaje de bienvenida de Hearty
    public function inicio()
    {
        try {
            $response = Http::get("{$this->chatbotUrl}/inicio");
            return response()->json($response->json());
        } catch (\Exception $e) {
            return response()->json([
                'mensaje' => '¡Hola! Soy Hearty 💚 ¿Cómo te sientes hoy?',
                'opciones' => ['😊 Bien', '😰 Ansioso/a', '😢 Triste', '😴 Cansado/a'],
                'pregunta_id' => 'bienvenida'
            ]);
        }
    }

    // Envía mensaje al chatbot y guarda en BD
    public function chat(Request $request)
    {
        $request->validate([
            'mensaje' => 'required|string|max:500',
            'pregunta_actual' => 'nullable|string',
        ]);

        // Guardar mensaje del usuario
        ChatMessage::create([
            'user_id' => auth()->id(),
            'sender' => 'user',
            'message' => $request->mensaje,
        ]);

        try {
            // Enviar al chatbot Python
            $response = Http::post("{$this->chatbotUrl}/chat", [
                'mensaje' => $request->mensaje,
                'pregunta_actual' => $request->pregunta_actual ?? 'bienvenida',
                'contexto' => []
            ]);

            $data = $response->json();

            // Guardar respuesta de Hearty
            ChatMessage::create([
                'user_id' => auth()->id(),
                'sender' => 'hearty',
                'message' => $data['respuesta'],
                'emotion_detected' => $request->mensaje,
            ]);

            return response()->json($data);

        } catch (\Exception $e) {
            $respuesta = "Lo siento, tengo problemas para conectarme ahora mismo 💙 Pero recuerda: estoy aquí para ti.";

            ChatMessage::create([
                'user_id' => auth()->id(),
                'sender' => 'hearty',
                'message' => $respuesta,
            ]);

            return response()->json(['respuesta' => $respuesta]);
        }
    }
}