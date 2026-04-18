from flask import Flask, request, jsonify
from flask_cors import CORS
import random
from hearty.preguntas import FLUJO_PREGUNTAS
from hearty.respuestas import (
    RESPUESTAS, PALABRAS_CLAVE, CONSEJOS_GENERALES,
    FRASES_MOTIVACIONALES, NOMBRES_TECNICAS, RUTAS_TECNICAS
)

app = Flask(__name__)
CORS(app, origins=["http://127.0.0.1:8000", "http://localhost:8000",
                   "http://192.168.1.40:8000", "http://0.0.0.0:8000"])


def detectar_emocion_texto_libre(texto: str) -> dict | None:
    texto_lower = texto.lower()
    # Prioridad: crisis primero
    for emocion in ["crisis", "ansiedad", "tristeza", "soledad", "cansancio",
                    "enfado", "autoestima", "trabajo", "dormir", "pareja",
                    "familia", "alegría", "calma"]:
        if emocion not in PALABRAS_CLAVE:
            continue
        datos = PALABRAS_CLAVE[emocion]
        for palabra in datos["palabras"]:
            if palabra in texto_lower:
                return {
                    "emocion":    datos["emocion"],
                    "intensidad": datos["intensidad"],
                    "respuesta":  datos["respuesta"],
                    "tecnicas":   datos.get("tecnicas", []),
                    "es_crisis":  datos.get("es_crisis", False),
                }
    return None


def respuesta_generica(historial: list) -> str:
    sesiones = len(historial) if historial else 0
    if sesiones >= 5:
        return f"Llevamos {sesiones} conversaciones juntos 💚 Confío en que puedo ayudarte. Cuéntame más sobre cómo te sientes."
    elif sesiones >= 2:
        return "Ya nos conocemos un poco 😊 Puedes contarme lo que necesites con total confianza. ¿Qué te ronda por la cabeza?"
    return "Gracias por compartir eso conmigo 💙 ¿Hay algo más que quieras contarme? Estoy aquí para escucharte."


def construir_respuesta(mensaje: str, historial: list) -> dict:
    respuesta_data = RESPUESTAS.get(mensaje)
    es_crisis = False

    if not respuesta_data:
        deteccion = detectar_emocion_texto_libre(mensaje)
        if deteccion:
            es_crisis = deteccion.get("es_crisis", False)
            respuesta_data = {
                "respuesta":         deteccion["respuesta"],
                "tecnicas":          deteccion["tecnicas"],
                "emocion_detectada": deteccion["emocion"],
                "intensidad":        deteccion["intensidad"],
                "es_crisis":         es_crisis,
            }

    if not respuesta_data:
        respuesta_data = {
            "respuesta":         respuesta_generica(historial),
            "tecnicas":          [],
            "emocion_detectada": None,
            "intensidad":        None,
        }

    respuesta_texto = respuesta_data.get("respuesta", "")

    # Personalizar con historial si no es crisis
    if historial and not es_crisis:
        emocion_anterior = historial[-1].get("emocion") if historial else None
        emocion_actual   = respuesta_data.get("emocion_detectada")
        if emocion_anterior and emocion_actual and emocion_anterior != emocion_actual:
            if emocion_actual in ["alegría", "calma"] and emocion_anterior in ["ansiedad", "tristeza", "enfado"]:
                respuesta_texto = "Noto que estás mejor que la última vez que hablamos 😊 Eso me alegra mucho. " + respuesta_texto
            elif emocion_actual in ["ansiedad", "tristeza"] and emocion_anterior in ["alegría", "calma"]:
                respuesta_texto = "Parece que las cosas han cambiado desde la última vez 💙 Estoy aquí. " + respuesta_texto

    tecnicas_ids = respuesta_data.get("tecnicas", [])
    tecnicas_formateadas = [
        {
            "id":     t,
            "nombre": NOMBRES_TECNICAS.get(t, t),
            "ruta":   RUTAS_TECNICAS.get(t, "/")
        }
        for t in tecnicas_ids
    ]

    frase = None
    if not es_crisis:
        frase = respuesta_data.get("frase_motivacional") or random.choice(FRASES_MOTIVACIONALES)

    return {
        "respuesta":          respuesta_texto,
        "tecnicas":           tecnicas_formateadas,
        "emocion_detectada":  respuesta_data.get("emocion_detectada"),
        "intensidad":         respuesta_data.get("intensidad"),
        "consejo":            random.choice(CONSEJOS_GENERALES) if tecnicas_ids and not es_crisis else None,
        "frase_motivacional": frase,
        "accion":             respuesta_data.get("accion"),
        "es_crisis":          es_crisis,
    }


def bienvenida_personalizada(historial: list, sesiones: int, nombre: str = None) -> str:
    saludo = f"¡Hola{', ' + nombre if nombre else ''}!"

    if sesiones == 0 or not historial:
        return f"{saludo} Soy Hearty, tu guía emocional 💚 Estoy aquí para acompañarte en cualquier momento. ¿Cómo te sientes hoy?"

    ultima_emocion = historial[-1].get("emocion") if historial else None

    if sesiones == 1:
        base = f"{saludo} ¡Qué bueno verte de nuevo! 😊"
    elif sesiones < 5:
        base = f"{saludo} Ya llevamos {sesiones} conversaciones juntos 💚"
    elif sesiones < 10:
        base = f"{saludo} ¡{sesiones} conversaciones ya! 🌟 Eres un ejemplo de cuidado personal."
    else:
        base = f"{saludo} ¡{sesiones} conversaciones! 🏆 Es un honor acompañarte en este camino."

    endings = {
        "ansiedad":  " La última vez te sentías algo ansioso/a. ¿Cómo estás hoy?",
        "tristeza":  " La última vez estabas pasando un momento difícil 💙 Espero que hoy esté mejor. ¿Cómo te encuentras?",
        "alegría":   " La última vez estabas de buen humor 😊 ¿Sigues igual de bien?",
        "calma":     " La última vez te sentías tranquilo/a. ¿Has podido mantener esa calma?",
        "cansancio": " La última vez estabas bastante cansado/a. ¿Has podido descansar?",
        "enfado":    " La última vez estabas pasando un momento difícil. ¿Cómo estás ahora?",
    }

    base += endings.get(ultima_emocion, " ¿Cómo te sientes hoy?")
    return base


@app.route('/health', methods=['GET'])
def health():
    return jsonify({"status": "ok", "chatbot": "Hearty Pro Max activo 💚"})


@app.route('/inicio', methods=['GET'])
def inicio():
    historial_str = request.args.get('historial', '')
    sesiones      = int(request.args.get('sesiones', 0))
    nombre        = request.args.get('nombre', None)

    historial = [{"emocion": e} for e in historial_str.split(',') if e]

    mensaje          = bienvenida_personalizada(historial, sesiones, nombre)
    primera_pregunta = FLUJO_PREGUNTAS[0]

    return jsonify({
        "mensaje":     mensaje,
        "opciones":    primera_pregunta["opciones"],
        "pregunta_id": primera_pregunta["id"],
        "sesiones":    sesiones,
    })


@app.route('/chat', methods=['POST'])
def chat():
    data = request.get_json()
    if not data or "mensaje" not in data:
        return jsonify({"error": "Mensaje requerido"}), 400

    mensaje        = data.get("mensaje", "").strip()
    pregunta_actual = data.get("pregunta_actual", "bienvenida")
    historial      = data.get("historial", [])

    respuesta = construir_respuesta(mensaje, historial)

    # Siguiente pregunta del flujo
    siguiente = None
    for i, p in enumerate(FLUJO_PREGUNTAS):
        if p["id"] == pregunta_actual and i + 1 < len(FLUJO_PREGUNTAS):
            sig = FLUJO_PREGUNTAS[i + 1]
            siguiente = {
                "mensaje":     sig["mensaje"],
                "opciones":    sig["opciones"],
                "pregunta_id": sig["id"]
            }
            break

    return jsonify({
        "respuesta":          respuesta["respuesta"],
        "tecnicas_sugeridas": respuesta["tecnicas"],
        "consejo_del_dia":    respuesta["consejo"],
        "frase_motivacional": respuesta["frase_motivacional"],
        "emocion_detectada":  respuesta["emocion_detectada"],
        "intensidad":         respuesta["intensidad"],
        "accion":             respuesta["accion"],
        "es_crisis":          respuesta["es_crisis"],
        "siguiente_pregunta": siguiente,
    })


@app.route('/analizar', methods=['POST'])
def analizar():
    data   = request.get_json()
    texto  = data.get("texto", "")
    result = detectar_emocion_texto_libre(texto)
    if result:
        return jsonify({"emocion": result["emocion"], "intensidad": result["intensidad"], "detectado": True})
    return jsonify({"detectado": False})


@app.route('/consejo', methods=['GET'])
def consejo():
    return jsonify({"consejo": random.choice(CONSEJOS_GENERALES)})


@app.route('/frase', methods=['GET'])
def frase():
    return jsonify({"frase": random.choice(FRASES_MOTIVACIONALES)})


if __name__ == '__main__':
    app.run(host='0.0.0.0', port=5000, debug=True)