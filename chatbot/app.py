from flask import Flask, request, jsonify
from flask_cors import CORS
import anthropic
import random
import os
from hearty.respuestas import CONSEJOS_GENERALES, FRASES_MOTIVACIONALES

app = Flask(__name__)
CORS(app, origins=[
    "http://127.0.0.1:8000",
    "http://localhost:8000",
    "http://192.168.1.40:8000",
])

# ── Cliente Anthropic ──
cliente = anthropic.Anthropic(
    api_key=os.environ.get("ANTHROPIC_API_KEY", "")
)

SYSTEM_PROMPT = """Eres Hearty, el asistente emocional de Heart to Heart, una aplicación de bienestar mental.

Tu personalidad:
- Eres cálido, empático y genuinamente comprensivo
- Hablas en español, de forma natural y cercana, nunca robótica
- Usas "tú" para dirigirte al usuario
- Eres directo pero amable, nunca condescendiente
- Usas emojis con moderación (1-2 por mensaje máximo)

Tu función:
- Escuchar activamente y validar las emociones del usuario
- Hacer preguntas que ayuden a profundizar y reflexionar
- Sugerir técnicas de bienestar cuando sea apropiado
- Detectar señales de crisis y responder con recursos reales
- Recordar el contexto de la conversación

Técnicas disponibles en la app que puedes recomendar:
- Respiración guiada (/respiracion) — para ansiedad inmediata
- Meditación (/meditacion) — para calmar la mente
- Sonidos relajantes (/sonidos) — para el ambiente
- Diario de gratitud (/diario) — para cambiar perspectiva
- EFT Tapping (/tapping) — para liberar estrés
- Visualización guiada (/visualizacion) — para relajación profunda
- Yoga suave (/yoga) — para liberar tensión corporal
- Journaling (/journaling) — para procesar emociones
- Grounding 5-4-3-2-1 (/tecnica-5-4-3-2-1) — para ataques de pánico
- Relajación muscular (/relajacion-muscular) — para tensión física
- Musicoterapia (/musicoterapia) — para regular emociones
- Ejercicio terapéutico (/ejercicio) — para liberar endorfinas

Reglas importantes:
1. NUNCA digas que eres una IA o un chatbot. Eres Hearty.
2. Si el usuario menciona pensamientos de hacerse daño, responde con mucha empatía y proporciona el número 024 (España) de forma natural.
3. Tus respuestas deben tener entre 2-4 párrafos. Nunca muy cortas ni muy largas.
4. Cuando sugieras una técnica, explica brevemente por qué esa técnica ayuda para lo que siente el usuario.
5. Termina siempre con una pregunta abierta o una invitación a continuar.
6. Si el contexto incluye historial emocional del usuario, úsalo para personalizar tu respuesta.
"""

def construir_mensajes_sistema(historial_emocional: list, sesiones: int, nombre: str) -> str:
    contexto = SYSTEM_PROMPT

    if nombre:
        contexto += f"\n\nEl nombre del usuario es {nombre}."

    if sesiones > 0:
        contexto += f"\n\nEsta es la sesión número {sesiones + 1} del usuario con Hearty."

    if historial_emocional:
        ultimas = historial_emocional[-5:]
        resumen = ", ".join([f"{h.get('emocion', 'desconocida')} (intensidad {h.get('intensidad', '?')})" for h in ultimas])
        contexto += f"\n\nHistorial emocional reciente del usuario: {resumen}."

        emociones = [h.get('emocion') for h in historial_emocional if h.get('emocion')]
        if emociones:
            from collections import Counter
            predominante = Counter(emociones).most_common(1)[0][0]
            contexto += f" Su emoción predominante ha sido {predominante}."

    return contexto


@app.route('/health', methods=['GET'])
def health():
    tiene_key = bool(os.environ.get("ANTHROPIC_API_KEY"))
    return jsonify({
        "status": "ok",
        "chatbot": "Hearty Ultra Pro 💚",
        "claude_api": "conectada" if tiene_key else "sin clave API"
    })


@app.route('/inicio', methods=['GET'])
def inicio():
    historial_str = request.args.get('historial', '')
    sesiones      = int(request.args.get('sesiones', 0))
    nombre        = request.args.get('nombre', '')

    historial = [{"emocion": e} for e in historial_str.split(',') if e]

    # Generar bienvenida con Claude
    try:
        if not os.environ.get("ANTHROPIC_API_KEY"):
            raise Exception("Sin API key")

        prompt_bienvenida = f"El usuario se llama {nombre or 'amigo/a'}. "
        if sesiones == 0:
            prompt_bienvenida += "Es la primera vez que usa la app. Salúdale con calidez y pregúntale cómo se siente hoy."
        elif sesiones < 5:
            prompt_bienvenida += f"Ha usado la app {sesiones} veces antes. Salúdale de vuelta con familiaridad."
        else:
            prompt_bienvenida += f"Es un usuario frecuente con {sesiones} sesiones. Reconoce su constancia."

        if historial:
            ultima = historial[-1].get('emocion')
            if ultima:
                prompt_bienvenida += f" La última vez se sintió {ultima}."

        prompt_bienvenida += " El saludo debe ser breve, de 2-3 frases máximo."

        mensaje = cliente.messages.create(
            model="claude-sonnet-4-20250514",
            max_tokens=200,
            system=construir_mensajes_sistema(historial, sesiones, nombre),
            messages=[{"role": "user", "content": prompt_bienvenida}]
        )

        texto = mensaje.content[0].text

    except Exception as e:
        # Fallback si no hay API key
        if sesiones == 0:
            texto = f"¡Hola{', ' + nombre if nombre else ''}! Soy Hearty 💚 Estoy aquí para acompañarte. ¿Cómo te sientes hoy?"
        else:
            texto = f"¡Hola de nuevo{', ' + nombre if nombre else ''}! Me alegra que hayas vuelto 😊 ¿Cómo estás hoy?"

    return jsonify({
        "mensaje":     texto,
        "opciones":    ["😊 Bien", "😌 Tranquilo/a", "😰 Ansioso/a", "😢 Triste", "😠 Enfadado/a", "😴 Cansado/a"],
        "pregunta_id": "bienvenida",
        "sesiones":    sesiones,
    })


@app.route('/chat', methods=['POST'])
def chat():
    data = request.get_json()
    if not data or "mensaje" not in data:
        return jsonify({"error": "Mensaje requerido"}), 400

    mensaje         = data.get("mensaje", "").strip()
    historial_chat  = data.get("historial_chat", [])
    historial_emoc  = data.get("historial_emocional", [])
    nombre          = data.get("nombre", "")
    sesiones        = data.get("sesiones", 0)

    try:
        if not os.environ.get("ANTHROPIC_API_KEY"):
            raise Exception("Sin API key")

        # Construir historial de mensajes para Claude
        mensajes_claude = []
        for msg in historial_chat[-10:]:  # últimos 10 mensajes
            rol = "user" if msg.get("sender") == "user" else "assistant"
            mensajes_claude.append({
                "role":    rol,
                "content": msg.get("texto", "")
            })

        # Añadir mensaje actual
        mensajes_claude.append({
            "role":    "user",
            "content": mensaje
        })

        respuesta = cliente.messages.create(
            model="claude-sonnet-4-20250514",
            max_tokens=600,
            system=construir_mensajes_sistema(historial_emoc, sesiones, nombre),
            messages=mensajes_claude
        )

        texto = respuesta.content[0].text

        # Detectar si hay crisis en el mensaje
        palabras_crisis = ["suicidio", "morir", "hacerme daño", "no quiero vivir", "quitarme la vida"]
        es_crisis = any(p in mensaje.lower() for p in palabras_crisis)

        # Detectar técnicas mencionadas en la respuesta
        tecnicas_detectadas = []
        mapa_tecnicas = {
            "/respiracion":        ("🫁 Respiración guiada",   "/respiracion"),
            "/meditacion":         ("🧘 Meditación",            "/meditacion"),
            "/sonidos":            ("🎵 Sonidos relajantes",    "/sonidos"),
            "/diario":             ("📓 Diario de gratitud",    "/diario"),
            "/tapping":            ("👆 EFT Tapping",           "/tapping"),
            "/visualizacion":      ("🌈 Visualización guiada",  "/visualizacion"),
            "/yoga":               ("🤸 Yoga suave",            "/yoga"),
            "/journaling":         ("📝 Journaling",            "/journaling"),
            "/tecnica-5-4-3-2-1":  ("🌍 Grounding 5-4-3-2-1",  "/tecnica-5-4-3-2-1"),
            "/relajacion-muscular":("💆 Relajación muscular",   "/relajacion-muscular"),
            "/ejercicio":          ("🏃 Ejercicio terapéutico", "/ejercicio"),
        }

        for ruta, (nombre_tec, ruta_tec) in mapa_tecnicas.items():
            if ruta in texto:
                tecnicas_detectadas.append({
                    "id":     ruta.strip("/"),
                    "nombre": nombre_tec,
                    "ruta":   ruta_tec
                })

        return jsonify({
            "respuesta":          texto,
            "tecnicas_sugeridas": tecnicas_detectadas,
            "emocion_detectada":  None,
            "intensidad":         None,
            "es_crisis":          es_crisis,
            "frase_motivacional": random.choice(FRASES_MOTIVACIONALES) if not es_crisis else None,
            "consejo_del_dia":    random.choice(CONSEJOS_GENERALES) if not es_crisis else None,
        })

    except Exception as e:
        # Fallback sin API key
        return jsonify({
            "respuesta": _respuesta_fallback(mensaje),
            "tecnicas_sugeridas": [],
            "emocion_detectada":  None,
            "es_crisis":          False,
            "frase_motivacional": random.choice(FRASES_MOTIVACIONALES),
        })


def _respuesta_fallback(mensaje: str) -> str:
    mensaje_lower = mensaje.lower()
    if any(p in mensaje_lower for p in ["ansios", "angustia", "pánico", "nervios"]):
        return "Noto que estás pasando por un momento de ansiedad 💙 Vamos a trabajar juntos. Prueba ahora mismo la respiración 4-7-8: inspira 4 segundos, mantén 4 y suelta en 6. ¿Puedes contarme qué está generando esa ansiedad?"
    elif any(p in mensaje_lower for p in ["triste", "llorar", "mal", "fatal", "depri"]):
        return "Gracias por contarme cómo te sientes 💙 La tristeza es válida y merece ser escuchada. No tienes que estar solo/a con esto. ¿Quieres contarme qué ha pasado, o prefieres que te sugiera algo que pueda ayudarte ahora mismo?"
    elif any(p in mensaje_lower for p in ["bien", "genial", "feliz", "contento"]):
        return "¡Me alegra mucho escucharlo! 😊 Ese estado positivo es valioso. ¿Quieres aprovechar este buen momento para practicar algo que te ayude a mantenerlo?"
    elif any(p in mensaje_lower for p in ["cansad", "agotad", "sin energía"]):
        return "El cansancio es una señal importante 😴 Tu cuerpo y mente necesitan recuperarse. ¿Es cansancio físico, mental o emocional? Cada tipo necesita un descanso diferente."
    else:
        return "Gracias por compartir eso conmigo 💙 Estoy aquí para escucharte. ¿Hay algo más que quieras contarme sobre cómo te sientes?"


@app.route('/analizar', methods=['POST'])
def analizar():
    data  = request.get_json()
    texto = data.get("texto", "")

    palabras = {
        "ansiedad":  ["ansios", "angustia", "pánico", "nervios", "agobiad"],
        "tristeza":  ["triste", "llorar", "depri", "fatal", "mal", "vacío"],
        "alegría":   ["bien", "genial", "feliz", "contento", "alegre"],
        "cansancio": ["cansad", "agotad", "sin energía", "exhausto"],
        "enfado":    ["enfadad", "furioso", "rabia", "frustrad", "harto"],
    }

    texto_lower = texto.lower()
    for emocion, terminos in palabras.items():
        if any(t in texto_lower for t in terminos):
            return jsonify({"detectado": True, "emocion": emocion})

    return jsonify({"detectado": False})


@app.route('/consejo', methods=['GET'])
def consejo():
    return jsonify({"consejo": random.choice(CONSEJOS_GENERALES)})


if __name__ == '__main__':
    api_key = os.environ.get("ANTHROPIC_API_KEY")
    if api_key:
        print("✅ Claude API conectada — Hearty Ultra Pro activo")
    else:
        print("⚠️  Sin ANTHROPIC_API_KEY — usando modo fallback")
    app.run(host='0.0.0.0', port=5000, debug=True)