from flask import Flask, request, jsonify
from flask_cors import CORS
import random
from hearty.preguntas import FLUJO_PREGUNTAS
from hearty.respuestas import (
    RESPUESTAS, PALABRAS_CLAVE, CONSEJOS_GENERALES,
    NOMBRES_TECNICAS, RUTAS_TECNICAS
)

app = Flask(__name__)
CORS(app, origins=["http://127.0.0.1:8000", "http://localhost:8000"])


def detectar_emocion_texto_libre(texto: str) -> dict | None:
    """Detecta la emoción en texto libre usando palabras clave."""
    texto_lower = texto.lower()
    for emocion, datos in PALABRAS_CLAVE.items():
        for palabra in datos["palabras"]:
            if palabra in texto_lower:
                return {
                    "emocion":   datos["emocion"],
                    "intensidad": datos["intensidad"],
                    "respuesta": datos["respuesta"],
                    "tecnicas":  datos.get("tecnicas", [])
                }
    return None


def construir_respuesta_con_memoria(mensaje: str, contexto: dict, historial: list) -> dict:
    """
    Construye la respuesta de Hearty combinando el diccionario de respuestas
    con la detección de texto libre y el contexto de sesiones anteriores.
    """
    # 1. Buscar respuesta en el diccionario de opciones predefinidas
    respuesta_data = RESPUESTAS.get(mensaje)

    # 2. Si no está en el diccionario, intentar detección por texto libre
    if not respuesta_data:
        deteccion = detectar_emocion_texto_libre(mensaje)
        if deteccion:
            respuesta_data = {
                "respuesta":        deteccion["respuesta"],
                "tecnicas":         deteccion["tecnicas"],
                "emocion_detectada": deteccion["emocion"],
                "intensidad":        deteccion["intensidad"],
            }

    # 3. Si sigue sin encontrar nada, respuesta genérica empática
    if not respuesta_data:
        respuesta_data = {
            "respuesta": _respuesta_generica_con_memoria(historial),
            "tecnicas":  [],
            "emocion_detectada": None,
            "intensidad": None,
        }

    # 4. Personalizar respuesta con el historial si existe
    respuesta_texto = respuesta_data.get("respuesta", "")
    if historial and len(historial) > 0:
        emocion_anterior = historial[-1].get("emocion") if historial else None
        emocion_actual   = respuesta_data.get("emocion_detectada")
        if emocion_anterior and emocion_actual and emocion_anterior != emocion_actual:
            if emocion_actual in ["alegría", "calma"] and emocion_anterior in ["ansiedad", "tristeza"]:
                respuesta_texto = f"Noto que la última vez te sentías {emocion_anterior} y hoy pareces mejor 😊 ¡Sigue cuidándote! " + respuesta_texto
            elif emocion_actual in ["ansiedad", "tristeza"] and emocion_anterior in ["alegría", "calma"]:
                respuesta_texto = f"La última vez que hablamos te sentías {emocion_anterior}. Hoy parece que las cosas están más difíciles 💙 Estoy aquí. " + respuesta_texto

    # 5. Formatear técnicas sugeridas
    tecnicas_ids = respuesta_data.get("tecnicas", [])
    tecnicas_formateadas = [
        {
            "id":     t,
            "nombre": NOMBRES_TECNICAS.get(t, t),
            "ruta":   RUTAS_TECNICAS.get(t, "/")
        }
        for t in tecnicas_ids
    ]

    return {
        "respuesta":        respuesta_texto,
        "tecnicas":         tecnicas_formateadas,
        "emocion_detectada": respuesta_data.get("emocion_detectada"),
        "intensidad":        respuesta_data.get("intensidad"),
        "consejo":          random.choice(CONSEJOS_GENERALES) if tecnicas_ids else None,
        "accion":           respuesta_data.get("accion"),
    }


def _respuesta_generica_con_memoria(historial: list) -> str:
    """Genera una respuesta genérica personalizada según el historial."""
    if not historial:
        return "Gracias por compartir eso conmigo 💙 ¿Hay algo más que quieras contarme? Estoy aquí para escucharte."

    sesiones = len(historial)
    if sesiones >= 5:
        return f"Llevamos {sesiones} conversaciones juntos 💚 Confío en que puedo ayudarte. Cuéntame más sobre cómo te sientes."
    elif sesiones >= 2:
        return "Ya nos conocemos un poco 😊 Puedes contarme lo que necesites con total confianza. ¿Qué te ronda por la cabeza?"
    return "Gracias por compartir eso conmigo 💙 ¿Hay algo más que quieras contarme?"


def generar_bienvenida_personalizada(historial: list, sesiones: int, nombre: str = None) -> str:
    """Genera el mensaje de bienvenida adaptado al historial del usuario."""
    saludo = f"¡Hola{', ' + nombre if nombre else ''}!"

    if sesiones == 0 or not historial:
        return f"{saludo} Soy Hearty, tu guía emocional 💚 Estoy aquí para acompañarte. ¿Cómo te sientes hoy?"

    ultima_emocion = historial[-1].get("emocion") if historial else None

    if sesiones == 1:
        base = f"{saludo} ¡Qué bueno verte de nuevo! 😊 La última vez hablamos sobre cómo te sentías."
    elif sesiones < 5:
        base = f"{saludo} Ya llevamos {sesiones} conversaciones juntos 💚 Me alegra que sigas aquí."
    elif sesiones < 10:
        base = f"{saludo} ¡{sesiones} conversaciones ya! 🌟 Eres un ejemplo de constancia en el cuidado personal."
    else:
        base = f"{saludo} ¡Qué alegría! Ya llevamos {sesiones} conversaciones 🏆 Eres increíble por seguir cuidándote."

    if ultima_emocion:
        endings = {
            "ansiedad":  f" La última vez te sentías algo ansioso/a. ¿Cómo estás hoy? ¿Ha mejorado?",
            "tristeza":  f" La última vez te sentías triste. Espero que hoy las cosas estén un poco mejor 💙 ¿Cómo te encuentras?",
            "alegría":   f" La última vez estabas de buen humor 😊 ¿Cómo te sientes hoy?",
            "calma":     f" La última vez te sentías tranquilo/a. ¿Has podido mantener esa calma? ¿Cómo estás?",
            "cansancio": f" La última vez estabas bastante cansado/a. Espero que hayas podido descansar 😴 ¿Cómo te encuentras hoy?",
            "enfado":    f" La última vez estabas pasando un momento difícil 💪 ¿Cómo estás ahora? ¿Ha mejorado la situación?",
        }
        base += endings.get(ultima_emocion, " ¿Cómo te sientes hoy?")
    else:
        base += " ¿Cómo te sientes hoy?"

    return base


# ──────────────────────────────────────────
# ENDPOINTS
# ──────────────────────────────────────────

@app.route('/health', methods=['GET'])
def health():
    return jsonify({"status": "ok", "chatbot": "Hearty activo 💚"})


@app.route('/inicio', methods=['GET'])
def inicio():
    """
    Acepta contexto del usuario (historial, número de sesiones, nombre)
    para generar un mensaje de bienvenida personalizado.
    """
    historial = request.args.get('historial', '').split(',') if request.args.get('historial') else []
    sesiones  = int(request.args.get('sesiones', 0))
    nombre    = request.args.get('nombre', None)

    # Reconstruir historial como lista de dicts simplificada
    historial_parsed = [{"emocion": e} for e in historial if e]

    mensaje = generar_bienvenida_personalizada(historial_parsed, sesiones, nombre)
    primera_pregunta = FLUJO_PREGUNTAS[0]

    return jsonify({
        "mensaje":     mensaje,
        "opciones":    primera_pregunta["opciones"],
        "pregunta_id": primera_pregunta["id"],
        "sesiones":    sesiones,
    })


@app.route('/chat', methods=['POST'])
def chat():
    """Procesa el mensaje del usuario con memoria y detección avanzada."""
    data = request.get_json()
    if not data or "mensaje" not in data:
        return jsonify({"error": "Mensaje requerido"}), 400

    mensaje        = data.get("mensaje", "").strip()
    pregunta_actual = data.get("pregunta_actual", "bienvenida")
    historial      = data.get("historial", [])   # lista de {"emocion": "...", "fecha": "..."}
    contexto       = data.get("contexto", {})

    # Construir respuesta con memoria
    respuesta = construir_respuesta_con_memoria(mensaje, contexto, historial)

    # Determinar siguiente pregunta del flujo
    siguiente_pregunta = None
    for i, pregunta in enumerate(FLUJO_PREGUNTAS):
        if pregunta["id"] == pregunta_actual and i + 1 < len(FLUJO_PREGUNTAS):
            sig = FLUJO_PREGUNTAS[i + 1]
            siguiente_pregunta = {
                "mensaje":     sig["mensaje"],
                "opciones":    sig["opciones"],
                "pregunta_id": sig["id"]
            }
            break

    return jsonify({
        "respuesta":         respuesta["respuesta"],
        "tecnicas_sugeridas": respuesta["tecnicas"],
        "consejo_del_dia":   respuesta["consejo"],
        "emocion_detectada": respuesta["emocion_detectada"],
        "intensidad":        respuesta["intensidad"],
        "accion":            respuesta["accion"],
        "siguiente_pregunta": siguiente_pregunta,
    })


@app.route('/analizar', methods=['POST'])
def analizar():
    """
    Analiza un texto libre y devuelve la emoción detectada
    sin generar respuesta conversacional. Útil para autocompletado.
    """
    data   = request.get_json()
    texto  = data.get("texto", "")
    result = detectar_emocion_texto_libre(texto)

    if result:
        return jsonify({
            "emocion":    result["emocion"],
            "intensidad": result["intensidad"],
            "detectado":  True
        })
    return jsonify({"detectado": False})


@app.route('/consejo', methods=['GET'])
def consejo_aleatorio():
    return jsonify({"consejo": random.choice(CONSEJOS_GENERALES)})


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)