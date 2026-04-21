from flask import Flask, request, jsonify
from flask_cors import CORS
import random
import os
from hearty.respuestas import FRASES_MOTIVACIONALES, CONSEJOS_GENERALES

app = Flask(__name__)
CORS(app, origins=["http://127.0.0.1:8000","http://localhost:8000","http://192.168.1.40:8000"])

EMOCIONES = {
    "crisis": {
        "terminos": ["suicidio","morir","matarme","hacerme daño","no quiero vivir","quitarme la vida","acabar con todo","no quiero seguir","mejor sin mí","desaparecer para siempre","no tiene sentido vivir","me quiero morir"],
    },
    "ansiedad": {
        "terminos": ["ansios","angustia","pánico","nervios","agobiad","preocup","miedo","inquiet","intranquil","tenso","no puedo respirar","corazón acelerado","temblor","catastróf","me da vueltas","fobia","terror","me ahogo","opresión","y si pasa","y si me","no paro de pensar","pensamientos","obsesion","obsesión"],
        "tecnicas": [
            {"nombre":"🫁 Respiración 4-7-8","ruta":"/respiracion","por_que":"Activa el sistema nervioso parasimpático en menos de 2 minutos"},
            {"nombre":"🌍 Grounding 5-4-3-2-1","ruta":"/tecnica-5-4-3-2-1","por_que":"Ancla tu mente al presente y detiene el ciclo de pensamientos"},
            {"nombre":"🧘 Meditación guiada","ruta":"/meditacion","por_que":"Calma los pensamientos acelerados"},
            {"nombre":"👆 EFT Tapping","ruta":"/tapping","por_que":"Libera la tensión emocional a través de puntos de acupresión"},
        ],
    },
    "tristeza": {
        "terminos": ["triste","llorar","llorando","deprimid","vacío","sin ganas","no me importa","para qué","inútil","fracas","nada tiene sentido","no vale la pena","soy un desastre","todo mal","nadie me quiere","estoy solo","abandonad","pérdida","echo de menos","extraño","me siento fatal","hundid","sin esperanza","oscuro","no tengo ilusión","no me ilusiona","no disfruto","anhedonia"],
        "tecnicas": [
            {"nombre":"🏃 Ejercicio terapéutico","ruta":"/ejercicio","por_que":"Libera endorfinas y serotonina, los antidepresivos naturales"},
            {"nombre":"📓 Diario emocional","ruta":"/diario","por_que":"Escribir lo que sientes ayuda a procesar y liberar la tristeza"},
            {"nombre":"💗 Autocompasión","ruta":"/autocompasion","por_que":"Aprender a tratarte con amabilidad como lo harías con un amigo"},
            {"nombre":"🎶 Musicoterapia","ruta":"/musicoterapia","por_que":"La música regula las emociones a nivel neurológico"},
        ],
    },
    "estres": {
        "terminos": ["estresad","presión","no doy abasto","demasiado","saturad","no puedo más","al límite","quemad","no llego","todo a la vez","colapso","agobio","trabajo","examen","deadlin","plazo","responsabilidad","carga","peso","abrumad","desbordad","no tengo tiempo","sin tiempo"],
        "tecnicas": [
            {"nombre":"💆 Relajación muscular","ruta":"/relajacion-muscular","por_que":"Libera la tensión física que acumula el estrés"},
            {"nombre":"🫁 Respiración de caja","ruta":"/respiracion","por_que":"Técnica usada por militares para controlar el estrés agudo"},
            {"nombre":"🏃 Ejercicio liberador","ruta":"/ejercicio","por_que":"Quema el exceso de cortisol y adrenalina"},
            {"nombre":"🎯 Modo Focus","ruta":"/focus","por_que":"Organiza tu tiempo con Pomodoro y recupera el control"},
        ],
    },
    "cansancio": {
        "terminos": ["cansad","agotad","sin energía","exhausto","no tengo fuerzas","sin motivación","todo me cuesta","pesadez","no me levanto","duermo mal","duermo poco","no descanso","burnout","lentitud","no tengo ganas de nada","apatía","sin vida"],
        "tecnicas": [
            {"nombre":"🍵 Infusiones relajantes","ruta":"/infusiones","por_que":"Ritual de autocuidado que restaura el sistema nervioso"},
            {"nombre":"🎵 Sonidos relajantes","ruta":"/sonidos","por_que":"Ambiente sonoro que mejora la calidad del descanso"},
            {"nombre":"🤸 Yoga suave","ruta":"/yoga","por_que":"Movimiento suave que restaura la energía sin agotar"},
            {"nombre":"🌈 Visualización guiada","ruta":"/visualizacion","por_que":"Recupera energía mental con un viaje mental relajante"},
        ],
    },
    "enfado": {
        "terminos": ["enfadad","rabia","ira","furioso","frustrad","harto","no aguanto","odio","me revienta","me saca de quicio","injusto","me han hecho","no soporto","exploto","me quema","indignado","cabreado","me tiene harto","me tiene cansado"],
        "tecnicas": [
            {"nombre":"🏃 Ejercicio intenso","ruta":"/ejercicio","por_que":"Canaliza la energía del enfado de forma constructiva"},
            {"nombre":"💆 Relajación muscular","ruta":"/relajacion-muscular","por_que":"Libera la tensión física que genera el enfado"},
            {"nombre":"📝 Journaling","ruta":"/journaling","por_que":"Expresar el enfado en papel lo desactiva"},
            {"nombre":"🫁 Respiración guiada","ruta":"/respiracion","por_que":"Baja la activación fisiológica del enfado"},
        ],
    },
    "soledad": {
        "terminos": ["solo","soledad","nadie me entiende","no tengo a nadie","aislad","desconectad","incomprendid","no encajo","no tengo amigos","me siento invisible","nadie me escucha","me ignoran","paso desapercibid"],
        "tecnicas": [
            {"nombre":"👥 Comunidad HTH","ruta":"/comunidad","por_que":"Conecta con personas que entienden cómo te sientes"},
            {"nombre":"📓 Diario emocional","ruta":"/diario","por_que":"Escribir acompaña la soledad y clarifica pensamientos"},
        ],
    },
    "alegria": {
        "terminos": ["bien","genial","feliz","contento","alegre","animad","positivo","ilusionad","emocionad","orgullos","logré","conseguí","éxito","celebrar","satisfech","estupend","fantástic","increíble","muy bien","super bien"],
        "tecnicas": [],
    },
}

RESPUESTAS = {
    "inicio_sin_emocion": [
        "¡Hola{nombre}! Soy Hearty 💚\n\nEste es tu espacio seguro. Puedes contarme cualquier cosa, sin juzgarte y sin prisa. Estoy aquí para escucharte de verdad.\n\n¿Cómo te sientes hoy?",
        "¡Hola{nombre}! Me alegra que estés aquí 💙\n\nPuedes hablarme de lo que sea — cómo te sientes, qué te preocupa, qué te alegra... Todo vale. ¿Qué tal estás?",
        "Hola{nombre} 💚 Soy Hearty, tu espacio para desahogarte sin filtros.\n\n¿Cómo está yendo el día?",
    ],
    "ansiedad_primera": [
        "Entiendo que te sientes ansioso/a{nombre} 💙 La ansiedad puede ser muy agotadora, especialmente cuando los pensamientos no paran.\n\nQuiero que sepas que lo que sientes es real y merece atención. Estoy aquí.\n\n¿Puedes contarme qué es lo que más te preocupa ahora mismo?",
        "Lo que describes suena muy intenso{nombre} 💙 La ansiedad tiene esa capacidad de apoderarse de todo y hacerlo parecer más grande de lo que es.\n\n¿Cuándo empezaste a sentirte así? ¿Fue de repente o lleva tiempo?",
        "Noto que estás pasando por un momento difícil{nombre} 💙 La ansiedad agota muchísimo, tanto mental como físicamente.\n\n¿Hay algo concreto que te genera esa sensación, o es más una angustia general sin saber muy bien por qué?",
    ],
    "ansiedad_profundizar": [
        "Entiendo{nombre}... La ansiedad sostenida es agotadora 💙\n\nPrueba esto ahora mismo conmigo: inhala contando hasta 4, mantén 7 segundos, exhala lentamente en 8. Solo tres ciclos. ¿Puedes intentarlo mientras seguimos hablando?",
        "Lo que describes tiene mucho sentido{nombre} 💙 Cuando la ansiedad lleva tiempo presente, el cuerpo se acostumbra a estar en alerta aunque no haya peligro real.\n\n¿Hay algún momento del día en que se sienta más intensa?",
        "Gracias por seguir contándome{nombre} 💚 ¿Hay algo que normalmente te ayude a calmarte, aunque sea un poco?",
    ],
    "tristeza_primera": [
        "Gracias por contarme cómo te sientes{nombre} 💙 La tristeza merece ser escuchada, no ignorada ni apresurada.\n\nNo tienes que «ponerte bien» rápido. Estoy aquí sin juzgarte.\n\n¿Quieres contarme qué ha pasado?",
        "Lo que sientes es completamente válido{nombre} 🤍 La tristeza no es debilidad, es una emoción que nos dice que algo importa.\n\n¿La tristeza tiene una razón concreta o aparece sin saber muy bien por qué?",
        "Me alegra que hayas venido a contarme esto{nombre} 💚 Cargar con la tristeza en silencio la hace más pesada.\n\n¿Hace cuánto tiempo te sientes así?",
    ],
    "tristeza_profundizar": [
        "Gracias por seguir contándome{nombre} 🤍 Sé que a veces es difícil poner en palabras lo que se siente.\n\n¿Hay alguien en tu vida con quien puedas hablar de esto además de conmigo?",
        "Lo que describes me importa mucho{nombre} 💙 ¿Qué es lo que más te pesa ahora mismo?",
        "La tristeza que describes suena profunda{nombre} 💙\n\n¿Has dormido bien últimamente? ¿Y has podido comer? A veces cuando estamos tristes descuidamos lo más básico.",
    ],
    "estres_primera": [
        "El estrés que describes suena agotador{nombre} 💙 Cuando todo llega a la vez es muy difícil saber por dónde empezar.\n\nPero no tienes que resolver todo hoy. ¿Qué es lo que más estrés te está generando ahora mismo?",
        "Entiendo que estás al límite{nombre} 💙 El estrés sostenido tiene un coste real en el cuerpo y la mente.\n\n¿Es estrés de trabajo, de relaciones, personal... o una mezcla de todo?",
        "Lo que describes suena muy intenso{nombre} 💙 ¿Cuándo fue la última vez que te sentiste tranquilo/a de verdad?",
    ],
    "estres_profundizar": [
        "Gracias por seguir contándome{nombre} 💙 Parece que el estrés lleva tiempo acumulándose.\n\n¿Qué pasaría si hoy, solo hoy, dejaras de intentar controlarlo todo?",
        "Entiendo{nombre} 💚 ¿Qué es lo que más urge resolver de todo lo que tienes encima? A veces ayuda priorizar una sola cosa.",
    ],
    "cansancio_primera": [
        "El cansancio profundo no siempre se soluciona durmiendo más{nombre} 💙 A veces es el alma la que está cansada, no solo el cuerpo.\n\n¿Es cansancio físico, mental, emocional... o una mezcla de todo?",
        "Hay momentos en que el cuerpo dice «hasta aquí» y eso hay que escucharlo{nombre} 🤍\n\n¿Cuándo fue la última vez que descansaste de verdad, sin pendientes en la cabeza?",
        "El agotamiento emocional es real y serio{nombre} 💚 No es flojera ni excusa.\n\n¿Qué es lo que más energía te está drenando últimamente?",
    ],
    "enfado_primera": [
        "Entiendo que estás enfadado/a{nombre} 💙 El enfado es una emoción válida que nos dice que algo no está bien.\n\n¿Qué ha pasado que te ha puesto así?",
        "Tiene sentido que te sientas así{nombre} 🌱 El enfado a veces es la única forma que tiene el cuerpo de pedir que algo cambie.\n\n¿Es algo puntual o lleva tiempo acumulándose?",
    ],
    "soledad_primera": [
        "La soledad es una de las emociones más difíciles de sostener{nombre} 💙 Me alegra que estés aquí, porque aquí siempre hay alguien.\n\n¿Es una soledad de estar físicamente solo/a, o más de sentirte incomprendido/a?",
        "Sentirse solo/a duele mucho{nombre} 🤍 Y muchas veces la soledad más difícil no es la de estar sin gente, sino la de estar con gente y sentirse igualmente solo.\n\n¿Cómo es tu soledad?",
    ],
    "alegria": [
        "¡Me alegra mucho escucharlo{nombre}! 😊 Ese estado positivo merece ser celebrado.\n\n¿Qué ha pasado que te hace sentir así de bien?",
        "¡Genial{nombre}! 🌟 Los momentos buenos también merecen atención.\n\n¿Quieres aprovechar este buen momento para hacer algo que te ayude a mantenerlo?",
        "¡Qué bien{nombre}! 💚 Me encanta saber cuándo estás bien.\n\n¿Qué es lo que ha pasado hoy?",
    ],
    "mejora": [
        "¡Me alegra mucho que estés mejor{nombre}! 😊 Eso es un gran avance.\n\n¿Qué ha cambiado? Me encanta saber qué te ha ayudado.",
        "¡Qué bien{nombre}! 💚 Es importante notar los progresos, aunque sean pequeños.\n\n¿Cómo te sientes comparado con antes?",
    ],
    "crisis": [
        "Lo que me estás contando es muy serio y quiero que sepas que no estás solo/a 💙\n\nAhora mismo lo más importante es que estés a salvo. Por favor llama al **024** — es gratuito, confidencial y están disponibles ahora mismo las 24 horas.\n\n¿Estás en un lugar seguro en este momento?",
        "Gracias por contarme esto. Sé que no es fácil decirlo y lo valoro mucho 🤍\n\nLo que sientes tiene solución, aunque ahora no lo parezca. Por favor llama al **024** ahora mismo.\n\n¿Hay alguien cercano a quien puedas llamar?",
    ],
    "generica": [
        "Gracias por contarme eso{nombre} 💙 Estoy aquí para escucharte sin juzgarte.\n\n¿Hay algo más que quieras contarme?",
        "Entiendo{nombre} 💚 ¿Puedes contarme un poco más? Cuanto más me cuentes mejor puedo acompañarte.",
        "Estoy aquí contigo{nombre} 🌱 ¿Cómo llevas el día en general?",
        "Gracias por compartir eso{nombre} 💙\n\n¿Hay algo concreto en lo que pueda ayudarte ahora mismo?",
        "Te escucho{nombre} 💚 ¿Quieres contarme más sobre lo que está pasando?",
    ],
    "seguimiento_ansiedad": [
        "¿Cómo estás ahora{nombre} comparado con antes? ¿La ansiedad sigue igual de intensa? 💙",
        "¿Cómo llevas la ansiedad ahora{nombre}? 💙",
    ],
    "seguimiento_tristeza": [
        "¿Sigues sintiéndote igual de triste{nombre} o hay algún cambio? 💙",
        "¿Cómo estás ahora{nombre}? La tristeza que me contabas me tiene pensando en ti 🤍",
    ],
    "seguimiento_estres": [
        "¿El estrés sigue igual{nombre} o ha bajado un poco? 💙",
        "¿Cómo llevas ahora todo lo que me contabas{nombre}? 💚",
    ],
}

def _n(nombre):
    n = nombre.split()[0] if nombre else ""
    return f", {n}" if n else ""

def detectar_emocion(texto):
    t = texto.lower()
    for term in EMOCIONES["crisis"]["terminos"]:
        if term in t:
            return "crisis"
    puntos = {}
    for em, datos in EMOCIONES.items():
        if em == "crisis": continue
        p = sum(1 for term in datos.get("terminos", []) if term in t)
        if p > 0:
            puntos[em] = p
    return max(puntos, key=puntos.get) if puntos else None

def historial_emocion_predominante(historial):
    from collections import Counter
    emociones = []
    for m in historial:
        if m.get("sender") == "user":
            em = detectar_emocion(m.get("texto", ""))
            if em and em != "crisis":
                emociones.append(em)
    if not emociones:
        return None
    return Counter(emociones).most_common(1)[0][0]

def num_mensajes_usuario(historial):
    return sum(1 for m in historial if m.get("sender") == "user")

def construir_respuesta(mensaje, emocion, nombre, historial, sesiones):
    n = _n(nombre)
    num_msgs = num_mensajes_usuario(historial)
    emocion_previa = historial_emocion_predominante(historial)

    def r(clave, **kwargs):
        opciones = RESPUESTAS.get(clave, RESPUESTAS["generica"])
        return random.choice(opciones).format(nombre=n, **kwargs)

    # ── CRISIS ──
    if emocion == "crisis":
        return {
            "respuesta": r("crisis"),
            "emocion_detectada": "crisis",
            "es_crisis": True,
            "tecnicas_sugeridas": [],
            "frase_motivacional": None,
        }

    tecnicas = EMOCIONES.get(emocion, {}).get("tecnicas", [])[:2] if emocion else []

    # ── PRIMERA VEZ (sin historial) ──
    if num_msgs == 0:
        if emocion and emocion != "alegria":
            clave = f"{emocion}_primera"
            if clave in RESPUESTAS:
                resp = r(clave)
            else:
                resp = r("generica")
        elif emocion == "alegria":
            resp = r("alegria")
        else:
            resp = r("inicio_sin_emocion")
        return _fmt(resp, emocion, tecnicas)

    # ── DETECTA MEJORA ──
    if emocion == "alegria" and emocion_previa and emocion_previa not in ["alegria"]:
        return _fmt(r("mejora"), "alegria", [])

    if emocion == "alegria":
        return _fmt(r("alegria"), "alegria", [])

    # ── EMOCIÓN DETECTADA ──
    if emocion:
        if emocion == emocion_previa and num_msgs >= 2:
            # Profundizar
            clave = f"{emocion}_profundizar"
            resp  = r(clave) if clave in RESPUESTAS else r(f"{emocion}_primera")
        else:
            clave = f"{emocion}_primera"
            resp  = r(clave) if clave in RESPUESTAS else r("generica")

        if tecnicas:
            resp += f"\n\nMientras hablamos, hay algo que puede ayudarte ahora mismo 👇"

        return _fmt(resp, emocion, tecnicas)

    # ── SIN EMOCIÓN — RESPUESTA CONTEXTUAL ──
    if emocion_previa:
        clave = f"seguimiento_{emocion_previa}"
        if clave in RESPUESTAS:
            return _fmt(r(clave), emocion_previa, [])

    return _fmt(r("generica"), None, [])

def _fmt(respuesta, emocion, tecnicas):
    return {
        "respuesta": respuesta,
        "emocion_detectada": emocion,
        "es_crisis": False,
        "tecnicas_sugeridas": tecnicas,
        "frase_motivacional": random.choice(FRASES_MOTIVACIONALES) if emocion and emocion not in ["alegria"] else None,
    }

@app.route('/health', methods=['GET'])
def health():
    return jsonify({"status": "ok", "chatbot": "Hearty Pro Max v5 💚"})

@app.route('/inicio', methods=['GET'])
def inicio():
    nombre   = request.args.get('nombre', '')
    sesiones = int(request.args.get('sesiones', 0))
    n        = nombre.split()[0] if nombre else "amigo/a"

    if sesiones == 0:
        msg = f"¡Hola, {n}! Soy Hearty 💚\n\nEste es tu espacio seguro. Puedes contarme lo que sea — cómo te sientes, qué te preocupa, qué te alegra... Sin juzgarte y sin prisa.\n\n¿Cómo estás hoy?"
    elif sesiones < 5:
        msg = f"¡Hola de nuevo, {n}! 😊 Qué bien verte por aquí.\n\n¿Cómo estás hoy?"
    elif sesiones < 20:
        msg = f"¡{n}! Me alegra que hayas vuelto 💚\n\n¿Cómo llevas el día?"
    else:
        msg = f"¡Hola, {n}! 🌟 Tu constancia en cuidarte es admirable.\n\n¿Cómo te sientes hoy?"

    return jsonify({
        "mensaje":  msg,
        "opciones": ["😊 Bien","😌 Tranquilo/a","😰 Ansioso/a","😢 Triste","😠 Enfadado/a","😴 Cansado/a","😤 Estresado/a","😔 Solo/a"],
    })

@app.route('/chat', methods=['POST'])
def chat():
    data = request.get_json()
    if not data or "mensaje" not in data:
        return jsonify({"error":"Mensaje requerido"}), 400

    mensaje        = data.get("mensaje","").strip()
    nombre         = data.get("nombre","")
    historial_chat = data.get("historial_chat", [])
    sesiones       = int(data.get("sesiones", 0))

    if not mensaje:
        return jsonify({"error":"Mensaje vacío"}), 400

    print(f"💬 [{nombre}] {mensaje} | historial: {len(historial_chat)} msgs")

    emocion   = detectar_emocion(mensaje)
    resultado = construir_respuesta(mensaje, emocion, nombre, historial_chat, sesiones)

    return jsonify(resultado)

@app.route('/analizar', methods=['POST'])
def analizar():
    data    = request.get_json()
    emocion = detectar_emocion(data.get("texto",""))
    return jsonify({"detectado": emocion is not None, "emocion": emocion})

@app.route('/consejo', methods=['GET'])
def consejo():
    return jsonify({"consejo": random.choice(CONSEJOS_GENERALES), "motivacion": random.choice(FRASES_MOTIVACIONALES)})

if __name__ == '__main__':
    print("💚 Hearty Pro Max v5")
    app.run(host='0.0.0.0', port=5000, debug=True)