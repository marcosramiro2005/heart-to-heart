from flask import Flask, request, jsonify
from flask_cors import CORS
import random
from hearty.preguntas import FLUJO_PREGUNTAS
from hearty.respuestas import (
    RESPUESTAS,
    CONSEJOS_GENERALES,
    NOMBRES_TECNICAS,
    RUTAS_TECNICAS
)

app = Flask(__name__)
CORS(app)

def construir_respuesta(mensaje_usuario, contexto):
    """Genera la respuesta de Hearty basándose en el mensaje del usuario."""

    respuesta_data = RESPUESTAS.get(mensaje_usuario, None)

    if not respuesta_data:
        # Respuesta por defecto si no reconoce el mensaje
        return {
            "mensaje": "Gracias por compartir eso conmigo 💙 ¿Hay algo más que quieras contarme?",
            "tecnicas": [],
            "consejo": random.choice(CONSEJOS_GENERALES),
            "accion": None
        }

    tecnicas = respuesta_data.get("tecnicas", [])
    tecnicas_formateadas = [
        {
            "id": t,
            "nombre": NOMBRES_TECNICAS.get(t, t),
            "ruta": RUTAS_TECNICAS.get(t, "/")
        }
        for t in tecnicas
    ]

    return {
        "mensaje": respuesta_data.get("respuesta", ""),
        "tecnicas": tecnicas_formateadas,
        "consejo": random.choice(CONSEJOS_GENERALES) if tecnicas else None,
        "accion": respuesta_data.get("accion", None)
    }


@app.route('/health', methods=['GET'])
def health():
    """Endpoint para verificar que el chatbot está activo."""
    return jsonify({"status": "ok", "chatbot": "Hearty activo 💚"})


@app.route('/inicio', methods=['GET'])
def inicio():
    """Devuelve el mensaje y opciones de bienvenida."""
    primera_pregunta = FLUJO_PREGUNTAS[0]
    return jsonify({
        "mensaje": primera_pregunta["mensaje"],
        "opciones": primera_pregunta["opciones"],
        "pregunta_id": primera_pregunta["id"]
    })


@app.route('/chat', methods=['POST'])
def chat():
    """Procesa el mensaje del usuario y devuelve la respuesta de Hearty."""
    data = request.get_json()

    if not data or "mensaje" not in data:
        return jsonify({"error": "Mensaje requerido"}), 400

    mensaje_usuario = data.get("mensaje", "").strip()
    contexto = data.get("contexto", {})
    pregunta_actual = data.get("pregunta_actual", "bienvenida")

    # Construir respuesta basada en el mensaje
    respuesta = construir_respuesta(mensaje_usuario, contexto)

    # Determinar siguiente pregunta del flujo
    siguiente_pregunta = None
    for i, pregunta in enumerate(FLUJO_PREGUNTAS):
        if pregunta["id"] == pregunta_actual and i + 1 < len(FLUJO_PREGUNTAS):
            sig = FLUJO_PREGUNTAS[i + 1]
            siguiente_pregunta = {
                "mensaje": sig["mensaje"],
                "opciones": sig["opciones"],
                "pregunta_id": sig["id"]
            }
            break

    return jsonify({
        "respuesta": respuesta["mensaje"],
        "tecnicas_sugeridas": respuesta["tecnicas"],
        "consejo_del_dia": respuesta["consejo"],
        "accion": respuesta["accion"],
        "siguiente_pregunta": siguiente_pregunta
    })


@app.route('/consejo', methods=['GET'])
def consejo_aleatorio():
    """Devuelve un consejo aleatorio."""
    return jsonify({"consejo": random.choice(CONSEJOS_GENERALES)})


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)