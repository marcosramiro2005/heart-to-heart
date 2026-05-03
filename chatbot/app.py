from flask import Flask, request, jsonify
from flask_cors import CORS
import random
from collections import Counter

from hearty.respuestas import FRASES_MOTIVACIONALES, CONSEJOS_GENERALES

app = Flask(__name__)
CORS(app, origins=["http://127.0.0.1:8000", "http://localhost:8000", "http://192.168.1.40:8000"])

# ── Emociones, términos clave y técnicas recomendadas ─────────────────────────
EMOCIONES = {
    "crisis": {
        "terminos": [
            "suicidio", "morir", "matarme", "hacerme daño", "no quiero vivir",
            "quitarme la vida", "acabar con todo", "no quiero seguir", "mejor sin mí",
            "desaparecer para siempre", "no tiene sentido vivir", "me quiero morir",
            "quiero morir", "desaparecer", "hacerme daño", "autolesion", "autolesión",
            "cortarme", "hacerme algo", "no quiero estar aquí", "no merece la pena vivir",
        ],
    },
    "ansiedad": {
        "terminos": [
            "ansios", "angustia", "pánico", "nervios", "agobiad", "preocup",
            "miedo", "inquiet", "intranquil", "tenso", "no puedo respirar",
            "corazón acelerado", "temblor", "catastróf", "me da vueltas", "fobia",
            "terror", "me ahogo", "opresión", "y si pasa", "y si me",
            "no paro de pensar", "pensamientos", "obsesion", "obsesión",
            "parálisis", "bloqueado", "no puedo parar", "acelerado",
            "me late muy fuerte", "taquicardia", "sudor", "mareo",
        ],
        "tecnicas": [
            {"nombre": "🫁 Respiración 4-7-8",   "ruta": "/respiracion",      "por_que": "Activa el sistema nervioso parasimpático en menos de 2 minutos"},
            {"nombre": "🌍 Grounding 5-4-3-2-1", "ruta": "/tecnica-5-4-3-2-1","por_que": "Ancla tu mente al presente y detiene el ciclo de pensamientos"},
            {"nombre": "🧘 Meditación guiada",    "ruta": "/meditacion",       "por_que": "Calma los pensamientos acelerados"},
            {"nombre": "👆 EFT Tapping",          "ruta": "/tapping",          "por_que": "Libera la tensión emocional a través de puntos de acupresión"},
        ],
    },
    "tristeza": {
        "terminos": [
            "triste", "llorar", "llorando", "deprimid", "vacío", "sin ganas",
            "no me importa", "para qué", "inútil", "fracas", "nada tiene sentido",
            "no vale la pena", "soy un desastre", "todo mal", "nadie me quiere",
            "estoy solo", "abandonad", "pérdida", "echo de menos", "extraño",
            "me siento fatal", "hundid", "sin esperanza", "oscuro",
            "no tengo ilusión", "no me ilusiona", "no disfruto", "anhedonia",
            "lloré", "me he puesto a llorar", "llanto", "desolado",
            "no tengo ganas de nada", "todo me da igual", "me da igual todo",
        ],
        "tecnicas": [
            {"nombre": "🏃 Ejercicio terapéutico", "ruta": "/ejercicio",     "por_que": "Libera endorfinas y serotonina, los antidepresivos naturales"},
            {"nombre": "📓 Diario emocional",      "ruta": "/diario",        "por_que": "Escribir lo que sientes ayuda a procesar y liberar la tristeza"},
            {"nombre": "💗 Autocompasión",         "ruta": "/autocompasion", "por_que": "Aprender a tratarte con amabilidad como lo harías con un amigo"},
            {"nombre": "🎶 Musicoterapia",         "ruta": "/musicoterapia", "por_que": "La música regula las emociones a nivel neurológico"},
        ],
    },
    "estres": {
        "terminos": [
            "estresad", "presión", "no doy abasto", "demasiado", "saturad",
            "no puedo más", "al límite", "quemad", "no llego", "todo a la vez",
            "colapso", "agobio", "trabajo", "examen", "deadlin", "plazo",
            "responsabilidad", "carga", "peso", "abrumad", "desbordad",
            "no tengo tiempo", "sin tiempo", "me está matando", "no paro",
            "no descanso", "sobrecargad", "demasiadas cosas", "entrega",
            "no puedo con todo", "todo encima", "me supera",
            "no me concentro", "no puedo concentrarme", "no consigo concentrarme",
            "me cuesta concentrarme", "no me entra", "no me entra nada",
            "bloqueo", "bloqueado", "bloqueada", "mente en blanco",
            "no puedo estudiar", "me cuesta estudiar", "no rindo estudiando",
            "concentracion", "concentración", "no me sale", "procrastin",
        ],
        "tecnicas": [
            {"nombre": "💆 Relajación muscular",  "ruta": "/relajacion-muscular", "por_que": "Libera la tensión física que acumula el estrés"},
            {"nombre": "🫁 Respiración de caja",  "ruta": "/respiracion",         "por_que": "Técnica usada por militares para controlar el estrés agudo"},
            {"nombre": "🏃 Ejercicio liberador",  "ruta": "/ejercicio",            "por_que": "Quema el exceso de cortisol y adrenalina"},
            {"nombre": "🎯 Modo Focus",           "ruta": "/focus",                "por_que": "Organiza tu tiempo con Pomodoro y recupera el control"},
        ],
    },
    "cansancio": {
        "terminos": [
            "cansad", "agotad", "sin energía", "exhausto", "no tengo fuerzas",
            "sin motivación", "todo me cuesta", "pesadez", "no me levanto",
            "duermo mal", "duermo poco", "no descanso", "burnout", "lentitud",
            "no tengo ganas de nada", "apatía", "sin vida", "no puedo más",
            "no me apetece nada", "sin pilas", "vacío de energía",
            "me arrastra", "no rindo", "no funciono",
            "no duermo", "insomnio", "me desvelo", "me despierto",
            "no pego ojo", "mal descansado", "mal descansada",
        ],
        "tecnicas": [
            {"nombre": "🍵 Infusiones relajantes", "ruta": "/infusiones",    "por_que": "Ritual de autocuidado que restaura el sistema nervioso"},
            {"nombre": "🎵 Sonidos relajantes",    "ruta": "/sonidos",       "por_que": "Ambiente sonoro que mejora la calidad del descanso"},
            {"nombre": "🤸 Yoga suave",            "ruta": "/yoga",          "por_que": "Movimiento suave que restaura la energía sin agotar"},
            {"nombre": "🌈 Visualización guiada",  "ruta": "/visualizacion", "por_que": "Recupera energía mental con un viaje mental relajante"},
        ],
    },
    "enfado": {
        "terminos": [
            "enfadad", "rabia", "ira", "furioso", "frustrad", "harto",
            "no aguanto", "odio", "me revienta", "me saca de quicio", "injusto",
            "me han hecho", "no soporto", "exploto", "me quema", "indignado",
            "cabreado", "me tiene harto", "me tiene cansado", "me hierve",
            "me tiene frito", "me da asco", "me parece fatal", "qué rabia",
            "qué asco", "no puede ser", "es un asco", "me encabrona",
        ],
        "tecnicas": [
            {"nombre": "🏃 Ejercicio intenso",   "ruta": "/ejercicio",           "por_que": "Canaliza la energía del enfado de forma constructiva"},
            {"nombre": "💆 Relajación muscular", "ruta": "/relajacion-muscular",  "por_que": "Libera la tensión física que genera el enfado"},
            {"nombre": "📝 Journaling",          "ruta": "/journaling",           "por_que": "Expresar el enfado en papel lo desactiva"},
            {"nombre": "🫁 Respiración guiada",  "ruta": "/respiracion",          "por_que": "Baja la activación fisiológica del enfado"},
        ],
    },
    "soledad": {
        "terminos": [
            "solo", "soledad", "nadie me entiende", "no tengo a nadie", "aislad",
            "desconectad", "incomprendid", "no encajo", "no tengo amigos",
            "me siento invisible", "nadie me escucha", "me ignoran", "paso desapercibid",
            "nadie cuenta conmigo", "no le importo a nadie", "sin nadie",
            "me siento extraño", "no pertenezco", "rechazad", "marginad",
        ],
        "tecnicas": [
            {"nombre": "👥 Comunidad HTH",    "ruta": "/comunidad", "por_que": "Conecta con personas que entienden cómo te sientes"},
            {"nombre": "📓 Diario emocional", "ruta": "/diario",    "por_que": "Escribir acompaña la soledad y clarifica pensamientos"},
        ],
    },
    "alegria": {
        "terminos": [
            "bien", "genial", "feliz", "contento", "alegre", "animad", "positivo",
            "ilusionad", "emocionad", "orgullos", "logré", "conseguí", "éxito",
            "celebrar", "satisfech", "estupend", "fantástic", "increíble",
            "muy bien", "super bien", "maravillos", "estoy mejor", "me encuentro bien",
            "ha ido bien", "salió bien", "me alegra",
        ],
        "tecnicas": [],
    },
}
FLUJO_PREGUNTAS = [
    {
        "id": "bienvenida",
        "mensaje": "¡Hola! Soy Hearty, tu guía emocional 💚 Estoy aquí para acompañarte. ¿Cómo te sientes hoy?",
        "opciones": ["😊 Bien", "😌 Tranquilo/a", "😰 Ansioso/a", "😢 Triste", "😠 Enfadado/a", "😴 Cansado/a", "😤 Estresado/a", "😔 Solo/a"],
        "siguiente": "profundizar",
    },
    {
        "id": "profundizar",
        "mensaje": "Gracias por contármelo. ¿Hace cuánto tiempo te sientes así?",
        "opciones": ["Hoy solamente", "Unos días", "Varias semanas", "Hace mucho tiempo"],
        "siguiente": "causa",
    },
    {
        "id": "causa",
        "mensaje": "Entiendo. ¿Crees que hay algo concreto que esté influyendo en cómo te sientes?",
        "opciones": ["El trabajo o estudios", "Mis relaciones personales", "Mi salud", "No lo sé", "Varias cosas a la vez"],
        "siguiente": "apoyo",
    },
    {
        "id": "apoyo",
        "mensaje": "¿Qué tipo de apoyo te vendría mejor ahora mismo?",
        "opciones": ["Técnicas para calmarme", "Recursos informativos", "Solo quiero desahogarme", "Consejos prácticos"],
        "siguiente": "final",
    },
]

RESPUESTA_EMOCION_MAP = {
    "😊 bien": "alegria",
    "😌 tranquilo": "alegria",
    "😰 ansioso": "ansiedad",
    "😢 triste": "tristeza",
    "😠 enfadado": "enfado",
    "😴 cansado": "cansancio",
    "😤 estresado": "estres",
    "😔 solo": "soledad",
}


def get_pregunta_por_id(pregunta_id):
    return next((p for p in FLUJO_PREGUNTAS if p["id"] == pregunta_id), None)


def inferir_emocion_por_opcion(texto):
    if not texto:
        return None
    t = texto.lower()
    for termino, emocion in RESPUESTA_EMOCION_MAP.items():
        if termino in t:
            return emocion
    if "tranquil" in t or "bien" in t:
        return "alegria"
    if "ansios" in t or "ansiedad" in t or "ansioso" in t:
        return "ansiedad"
    if "triste" in t:
        return "tristeza"
    if "enfad" in t or "ira" in t:
        return "enfado"
    if "cansad" in t:
        return "cansancio"
    if "estres" in t or "estrés" in t:
        return "estres"
    if "solo" in t or "soledad" in t:
        return "soledad"
    return detectar_emocion(texto)


def generar_resumen_final(flow_answers, nombre):
    n = _n(nombre)
    emocion = inferir_emocion_por_opcion(flow_answers.get("bienvenida", ""))
    if not emocion:
        emocion = detectar_emocion(" ".join(flow_answers.values()) or "")

    apoyo = flow_answers.get("apoyo", "")
    cuando = flow_answers.get("profundizar", "")
    cause = flow_answers.get("causa", "")

    descripcion = {
        "ansiedad": "ansioso/a, con la mente acelerada y la sensación de que algo puede salir mal",
        "tristeza": "triste, como si te pesara el corazón y te costara encontrar ánimo",
        "estres": "estresado/a, con demasiadas responsabilidades y sensación de no llegar",
        "cansancio": "agotado/a, con poco impulso y falta de energía para lo cotidiano",
        "enfado": "enfadado/a, con mucha tensión y ganas de soltar lo que te molesta",
        "soledad": "solo/a, desconectado/a y con ganas de comprensión",
        "alegria": "bien, con una sensación de calma o felicidad que te acompaña",
    }.get(emocion, "como estás ahora mismo")

    texto = f"Gracias por responder{n}. Veo que te sientes {descripcion}."
    if cuando:
        texto += f" Lo notas desde {cuando.lower()}."
    if cause:
        texto += f" Parece que está vinculado a {cause.lower()}."

    if apoyo == "Técnicas para calmarme":
        texto += " Te recomiendo empezar con ejercicios prácticos de regulación como la respiración, el grounding o la relajación muscular."
    elif apoyo == "Recursos informativos":
        texto += " Buscar información y entender mejor lo que te pasa puede ayudarte a sentir más control."
    elif apoyo == "Solo quiero desahogarme":
        texto += " A veces lo mejor es dejar salir lo que llevas dentro y no forzarte a solucionar nada aún."
    elif apoyo == "Consejos prácticos":
        texto += " Un pequeño paso concreto hoy puede marcar la diferencia: prioriza una cosa y hazla sin juzgarte."
    else:
        texto += " Estoy aquí para ayudarte a cuidar de ti paso a paso."

    if emocion == "alegria":
        texto = f"Gracias por compartir{n}. Me alegra saber que te sientes bien. Aprovecha este momento para reforzar las buenas rutinas y seguir cuidándote."
        tecnicas = [
            {"nombre": "📓 Diario de gratitud", "ruta": "/diario", "por_que": "Anotar lo positivo ayuda a que esos momentos buenos duren más."},
            {"nombre": "🌿 Paseo consciente", "ruta": "/ejercicio", "por_que": "Aprovecha la energía positiva para conectar con el presente."},
        ]
    else:
        tecnicas = EMOCIONES.get(emocion, {}).get("tecnicas", [])[:3]
        if not tecnicas:
            tecnicas = [
                {"nombre": "💆 Autocuidado sencillo", "ruta": "/autocompasion", "por_que": "Cuidarte con pequeños gestos ayuda a recuperar estabilidad emocional."},
                {"nombre": "📝 Escribir lo que sientes", "ruta": "/diario", "por_que": "Sacar en palabras lo que te pasa reduce la carga mental."},
            ]

    return texto, emocion, tecnicas


def procesar_flujo_preguntas(pregunta_actual, respuesta, flow_answers, nombre):
    if not pregunta_actual:
        return None

    pregunta = get_pregunta_por_id(pregunta_actual)
    if not pregunta:
        return None

    flujo = dict(flow_answers or {})
    flujo[pregunta_actual] = respuesta
    siguiente_id = pregunta.get("siguiente")

    if siguiente_id == "final":
        resumen, emocion, tecnicas = generar_resumen_final(flujo, nombre)
        return {
            "respuesta": resumen,
            "emocion_detectada": emocion,
            "es_crisis": False,
            "tecnicas_sugeridas": tecnicas,
            "frase_motivacional": None,
            "opciones": [],
            "pregunta_actual": None,
        }

    siguiente = get_pregunta_por_id(siguiente_id)
    if not siguiente:
        return None

    emocion_detectada = inferir_emocion_por_opcion(flujo.get("bienvenida", ""))
    return {
        "respuesta": siguiente["mensaje"].format(nombre=_n(nombre)),
        "emocion_detectada": emocion_detectada,
        "es_crisis": False,
        "tecnicas_sugeridas": [],
        "frase_motivacional": None,
        "opciones": siguiente["opciones"],
        "pregunta_actual": siguiente_id,
    }

# ── Banco de respuestas ────────────────────────────────────────────────────────
RESPUESTAS = {

    # ── Sin emoción detectada ──────────────────────────────────────────────────
    "inicio_sin_emocion": [
        "¡Hola{nombre}! 💚 Este es tu espacio seguro. Aquí puedes hablar de lo que quieras sin miedo a ser juzgado/a.\n\nEstoy aquí para escucharte de verdad. ¿Cómo te sientes hoy?",
        "¡Hola{nombre}! Me alegra que hayas venido 💙 No tienes que tenerlo todo ordenado para contarme algo — a veces ni sabemos bien cómo nos sentimos hasta que lo decimos en voz alta.\n\n¿Cómo estás ahora mismo?",
        "Hola{nombre} 💚 Soy Hearty, tu espacio para desahogarte sin filtros ni juicios.\n\n¿Qué tal va el día?",
        "¡Hola{nombre}! 🌱 Aquí siempre hay tiempo y espacio para ti. Puedes contarme cualquier cosa — lo grande, lo pequeño, lo que sea.\n\n¿Cómo te encuentras?",
        "Hola{nombre} 💙 Me alegra verte por aquí. A veces solo necesitamos un sitio donde poder decir lo que sentimos.\n\n¿Cómo llevas el día?",
    ],

    "generica": [
        "Gracias por contarme eso{nombre} 💙 Estoy aquí para escucharte, sin juzgar nada de lo que digas.\n\n¿Puedes contarme un poco más sobre lo que está pasando?",
        "Te escucho{nombre} 💚 ¿Quieres contarme más sobre lo que sientes? Cuanto más me cuentes, mejor puedo acompañarte.",
        "Entiendo{nombre} 🌱 A veces es difícil saber exactamente qué sentimos. Tómate el tiempo que necesites.\n\n¿Hay algo concreto que te esté pesando ahora mismo?",
        "Estoy aquí contigo{nombre} 💙 No tienes que pasar por esto solo/a.\n\n¿Cómo llevas el día en general?",
        "Gracias por compartir eso{nombre} 💚 Me importa saber cómo estás.\n\n¿Hay algo en lo que pueda ayudarte ahora mismo?",
        "Lo que dices tiene mucho valor{nombre} 🤍 Estoy escuchándote con toda mi atención.\n\n¿Quieres que hablemos más sobre esto?",
    ],

    # ── Ansiedad ──────────────────────────────────────────────────────────────
    "ansiedad_primera": [
        "Entiendo que te sientes ansioso/a{nombre} 💙 La ansiedad puede ser muy agotadora, especialmente cuando los pensamientos no paran y el cuerpo se pone en alerta.\n\nQuiero que sepas que lo que sientes es real y merece atención. Estoy aquí.\n\n¿Puedes contarme qué es lo que más te preocupa ahora mismo?",
        "Lo que describes suena muy intenso{nombre} 💙 La ansiedad tiene esa capacidad de apoderarse de todo y hacer que las cosas parezcan más grandes de lo que son.\n\n¿Cuándo empezaste a sentirte así? ¿Fue de repente o lleva tiempo?",
        "Noto que estás pasando por un momento muy difícil{nombre} 💙 La ansiedad agota muchísimo, tanto mental como físicamente — es normal que te sientas así de cargado/a.\n\n¿Es algo concreto lo que te genera esa sensación, o es más una angustia general?",
        "Estar así de ansioso/a es agotador{nombre} 💙 El cuerpo y la mente van a mil cuando la ansiedad aparece, y eso consume mucha energía.\n\nAntes de nada: ¿cómo estás respirando ahora mismo? ¿Notas la respiración acelerada?",
        "La ansiedad es una de las emociones que más desgastan porque no te da tregua{nombre} 💙 Entiendo que estás pasándola mal.\n\n¿Es algo que te ha venido ahora de repente o llevas un tiempo sintiéndote así?",
        "Que reconozcas lo que sientes ya es un paso importante{nombre} 💙 Muchas personas pasan la ansiedad en silencio sin atreverse a decirlo.\n\n¿Hay algo que haya pasado recientemente que pueda estar detrás de esto?",
    ],
    "ansiedad_profundizar": [
        "Entiendo{nombre}... La ansiedad sostenida agota muchísimo 💙\n\nPrueba algo ahora mismo: inhala contando hasta 4, mantén el aire 4 segundos, exhala lentamente en 4. Solo tres ciclos. Es la respiración de caja — ¿puedes intentarlo mientras seguimos hablando?\n\n¿Cuándo notaste que empezaba a ponerse peor?",
        "Lo que describes tiene mucho sentido{nombre} 💙 Cuando la ansiedad lleva tiempo presente, el cuerpo aprende a estar en alerta aunque no haya peligro real — se convierte en el estado por defecto.\n\n¿Hay algún momento del día en que se sienta más intensa?",
        "Gracias por seguir contándome{nombre} 💚 Sé que no siempre es fácil poner palabras a lo que se siente.\n\n¿Hay algo que normalmente te ayude a calmarte aunque sea un poco? ¿Algo que hayas probado antes?",
        "La ansiedad que describes me dice que tu sistema nervioso lleva mucho tiempo en modo emergencia{nombre} 💙 El cuerpo no distingue entre un peligro real y uno imaginado — reacciona igual.\n\nUna pregunta importante: ¿estás durmiendo bien? El sueño tiene un impacto enorme en la ansiedad.",
        "Entiendo{nombre} 💙 Cuando la ansiedad se cronifica es más difícil de gestionar, pero también es cuando más importante es empezar a trabajarla.\n\n¿La ansiedad afecta a tu vida diaria? ¿Te impide hacer cosas?",
        "Está bien que lo cuentes{nombre} 💚 A veces la ansiedad nos hace sentir que estamos solos con algo que nadie más puede entender — pero mucha gente pasa por lo mismo.\n\n¿Tienes a alguien de confianza con quien puedas hablar de esto además de conmigo?",
    ],
    "ansiedad_consejo": [
        "Algo que funciona muy bien para la ansiedad{nombre} 💙 es el grounding — anclar la mente al momento presente. Prueba esto: nombra 5 cosas que puedes VER ahora mismo, 4 que puedes TOCAR, 3 que puedes OÍR. Eso corta el ciclo de pensamientos.\n\n¿Lo intentamos?",
        "Para la ansiedad hay una técnica que usan hasta los navy seals{nombre} — la respiración cuadrada: inhala 4 segundos, aguanta 4, exhala 4, aguanta 4. Cuatro ciclos. Activa el sistema parasimpático casi de inmediato 💙\n\n¿Quieres probarla ahora?",
        "Cuando la ansiedad llega con fuerza{nombre}, lo primero es sacar a la mente del futuro y traerla al presente 💚 El cuerpo solo puede estar aquí y ahora — es la mente la que viaja a los «¿y si...?».\n\nUna forma de hacerlo: fíjate en la sensación de tus pies en el suelo. ¿Los notas?",
    ],

    # ── Tristeza ──────────────────────────────────────────────────────────────
    "tristeza_primera": [
        "Gracias por contarme cómo te sientes{nombre} 💙 La tristeza merece ser escuchada, no ignorada ni apresurada.\n\nNo tienes que «ponerte bien» rápido. Estoy aquí sin juzgarte.\n\n¿Quieres contarme qué ha pasado?",
        "Lo que sientes es completamente válido{nombre} 🤍 La tristeza no es debilidad — es una emoción humana que nos dice que algo importa o que algo duele.\n\n¿La tristeza tiene una razón concreta, o aparece sin saber muy bien por qué?",
        "Me alegra que hayas venido a contarme esto{nombre} 💚 Cargar con la tristeza en silencio la hace más pesada. Aquí no tienes que guardar nada.\n\n¿Hace cuánto tiempo te sientes así?",
        "Sentir tristeza duele, y eso que describes no es pequeño{nombre} 🤍 A veces el cuerpo necesita un tiempo para procesar cosas que nos han afectado más de lo que creíamos.\n\n¿Hay algo o alguien que haya desencadenado esto?",
        "Estoy aquí contigo{nombre} 💙 La tristeza a veces viene sola sin aviso — y otras veces viene cargada de razones. Las dos son igual de válidas.\n\n¿Cómo describirías tú lo que sientes ahora mismo?",
        "Gracias por compartir eso{nombre} 🤍 Lo que estás sintiendo es real, y merece que alguien lo escuche.\n\n¿Tienes ganas de contarme un poco más de lo que está pasando?",
    ],
    "tristeza_profundizar": [
        "Gracias por seguir contándome{nombre} 🤍 Sé que a veces es difícil poner en palabras lo que se siente — que lo intentes ya dice mucho.\n\n¿Hay alguien en tu vida con quien puedas hablar de esto además de conmigo?",
        "Lo que describes me importa mucho{nombre} 💙 ¿Qué es lo que más te pesa ahora mismo? ¿Hay algo concreto que sientas que no puedes soltar?",
        "La tristeza que describes suena profunda{nombre} 💙 Cuando llevamos tiempo así, a veces el cuerpo también lo nota.\n\n¿Has podido dormir bien? ¿Y comer? Cuando estamos tristes muchas veces descuidamos lo más básico.",
        "Entiendo{nombre} 🤍 La tristeza cansa, y cuando ya llevas un tiempo así es normal que se sienta como si no fuera a cambiar nunca. Pero cambia.\n\n¿Hubo algún momento en el pasado en que te sintieras mejor? ¿Qué era diferente entonces?",
        "Gracias por la confianza{nombre} 💚 No es fácil abrirse así. ¿Tienes espacios en tu vida donde puedas desconectar un poco? ¿Algo que antes te gustara hacer y que ahora sientas que no te apetece?",
        "Lo que cuentas tiene mucho sentido{nombre} 🤍 La tristeza a veces hace que todo parezca más gris de lo que es — los problemas se agrandan y las cosas buenas se vuelven invisibles.\n\n¿Hay algo pequeño que puedas hacer hoy para cuidarte un poco?",
    ],
    "tristeza_consejo": [
        "Para la tristeza{nombre}, el movimiento es uno de los mejores aliados 💙 No tiene que ser nada intenso — un paseo de 20 minutos activa la serotonina y dopamina de forma natural. El cuerpo en movimiento cambia la química del cerebro.\n\n¿Cuándo fue la última vez que saliste a caminar?",
        "Una cosa que ayuda mucho cuando estamos tristes{nombre} 🤍 es escribir. No para resolver nada — solo para sacar lo que llevamos dentro. El diario emocional funciona porque externaliza lo que de otra forma da vueltas en la cabeza.\n\n¿Has probado alguna vez escribir cómo te sientes?",
        "La tristeza nos dice que algo importa{nombre} 💙 Una pregunta que a veces ayuda: ¿qué necesitas ahora mismo? No lo que deberías hacer — lo que necesitas. Puede ser llorar, puede ser silencio, puede ser hablar. Todo vale.",
    ],

    # ── Estrés ────────────────────────────────────────────────────────────────
    "estres_primera": [
        "El estrés que describes suena agotador{nombre} 💙 Cuando todo llega a la vez es muy difícil saber por dónde empezar — el cerebro entra en modo pánico y eso lo bloquea todo más.\n\nPero no tienes que resolver todo hoy. ¿Qué es lo que más estrés te está generando ahora mismo?",
        "Entiendo que estás al límite{nombre} 💙 El estrés sostenido tiene un coste real, tanto en el cuerpo como en la mente. No es «exagerar» — es que literalmente el sistema nervioso está sobreactivado.\n\n¿Es estrés de trabajo, de relaciones, personal... o todo mezclado?",
        "Lo que describes suena a mucha presión acumulada{nombre} 💙 ¿Cuándo fue la última vez que te sentiste tranquilo/a de verdad, sin nada urgente encima?",
        "El estrés que sientes es una señal de que llevas demasiado peso{nombre} 💙 El cuerpo lo dice antes que la mente — tensión en el cuello, dificultad para dormir, irritabilidad.\n\n¿Notas esas señales físicas?",
        "Estar así de saturado/a es muy duro{nombre} 💙 Muchas veces el estrés aparece cuando sentimos que no controlamos lo que pasa.\n\n¿Hay algo en tu situación que sientas que sí puedes controlar?",
        "Entiendo{nombre} 💚 El estrés a veces se acumula tan despacio que no nos damos cuenta hasta que ya no podemos más. Que lo estés nombrando es importante.\n\n¿Hace cuánto tiempo llevas sintiéndote así?",
    ],
    "estres_profundizar": [
        "Gracias por seguir contándome{nombre} 💙 Parece que el estrés lleva tiempo acumulándose — eso desgasta muchísimo.\n\n¿Qué pasaría si hoy, solo hoy, dejaras de intentar controlarlo todo? No para siempre — solo hoy.",
        "Entiendo{nombre} 💚 Cuando hay mucho encima, a veces ayuda priorizar: ¿qué es lo que MÁS urge de todo lo que tienes? Solo esa cosa. Una a la vez.\n\n¿Cuál sería para ti la prioridad número uno ahora mismo?",
        "Lo que describes tiene mucho sentido{nombre} 💙 El cerebro bajo estrés pierde perspectiva — todo parece igual de urgente e importante, y eso lo paraliza.\n\nUna técnica que funciona bien: hacer una lista de todo lo que tienes en la cabeza. Sacarlo fuera libera espacio mental. ¿Lo intentamos?",
        "El estrés crónico agota a niveles que muchas veces no vemos hasta que ya no podemos más{nombre} 💙 ¿Estás durmiendo? ¿Comiendo? El cuerpo bajo estrés a veces descuida lo más básico.",
        "Entiendo que sientes que no hay salida{nombre} 💚 Pero hay algo importante: el estrés puede gestionarse, y hay formas concretas de bajarlo. No tienes que resolverlo todo de golpe.\n\n¿Qué es lo que más te cuesta delegar o soltar?",
    ],
    "estres_consejo": [
        "Para el estrés agudo{nombre} 💙 una de las técnicas más efectivas es la relajación muscular progresiva: tensas un grupo de músculos durante 5 segundos y sueltas. Empiezas por los pies y subes. Eso libera la tensión física que acumula el estrés.\n\n¿Tienes 5 minutos ahora mismo para probarlo?",
        "Algo que cambia mucho el estrés{nombre} es la técnica Pomodoro 💚 Trabajas 25 minutos y descansas 5. No es solo productividad — le dice al cerebro que el modo emergencia tiene un final. ¿Lo has probado alguna vez?",
        "Para el estrés{nombre} 💙 el ejercicio físico es uno de los mejores reguladores que existen — quema el cortisol y la adrenalina que se acumulan. No tiene que ser intenso: 20 minutos de caminar rápido ya marca la diferencia.",
    ],

    # ── Cansancio ─────────────────────────────────────────────────────────────
    "cansancio_primera": [
        "El cansancio profundo no siempre se soluciona durmiendo más{nombre} 💙 A veces es el alma la que está cansada, no solo el cuerpo. Y eso requiere otro tipo de descanso.\n\n¿Es cansancio físico, mental, emocional... o una mezcla de todo?",
        "Hay momentos en que el cuerpo dice «hasta aquí» y eso hay que escucharlo{nombre} 🤍 Ignorar el agotamiento solo lo hace más profundo.\n\n¿Cuándo fue la última vez que descansaste de verdad, sin pendientes en la cabeza?",
        "El agotamiento que describes es real y serio{nombre} 💚 No es flojera ni excusa — cuando el cuerpo y la mente están agotados, todo cuesta más y nada parece apetecer.\n\n¿Qué es lo que más energía te está drenando últimamente?",
        "Estar sin energía así es muy duro{nombre} 💙 Muchas veces el cansancio profundo viene de dar demasiado sin reponer — trabajo, preocupaciones, responsabilidades que no paran.\n\n¿Sientes que tienes tiempo para ti mismo/a?",
        "Entiendo{nombre} 🤍 El burnout y el agotamiento emocional son reales — tan reales como una lesión física. El cuerpo tiene sus límites y a veces los ignoramos hasta que ya no podemos.\n\n¿Llevas mucho tiempo sintiéndote así?",
        "Ese cansancio que describes{nombre} 💙 suena a que llevas mucho tiempo exigiéndote sin parar. El cuerpo lo aguanta un tiempo, pero llega un punto en que dice basta.\n\n¿Hay algo que estés haciendo por obligación y que te quite mucha energía?",
    ],
    "cansancio_profundizar": [
        "Gracias por seguir contándome{nombre} 💙 El cansancio profundo muchas veces no se ve desde fuera — y eso lo hace más duro, porque parece que tenemos que seguir igual aunque por dentro no podemos más.\n\n¿Hay algo que puedas retirar de tu lista para aligerar un poco la carga?",
        "Lo que describes{nombre} 🤍 suena a que llevas un tiempo en modo supervivencia. No es sostenible. ¿Qué necesitas para empezar a recuperarte, aunque sea un poco?",
        "Entiendo{nombre} 💚 El cansancio también puede ser señal de que algo en tu vida necesita cambiar — un ritmo, una dinámica, una exigencia que ya no puedes sostener.\n\n¿Qué es lo que más energía te roba cada día?",
    ],
    "cansancio_consejo": [
        "Para el cansancio profundo{nombre} 🍵 a veces lo que más ayuda no es dormir más, sino crear momentos de desconexión real. Una infusión sin pantallas, 10 minutos de silencio. Parece poco, pero el sistema nervioso agradece esos respiros.\n\n¿Tienes algún ritual de descanso que hayas probado?",
        "El yoga suave{nombre} 🤸 es especialmente bueno para el agotamiento porque restaura energía sin exigir esfuerzo. Hay sesiones de 10-15 minutos que cambian completamente cómo te sientes.\n\n¿Te apetecería probarlo?",
        "Para el cansancio mental{nombre} 💙 los sonidos relajantes funcionan muy bien porque el cerebro asocia ciertos sonidos (lluvia, olas, bosque) con seguridad y descanso — y eso baja la activación del sistema nervioso.",
    ],

    # ── Enfado ────────────────────────────────────────────────────────────────
    "enfado_primera": [
        "Entiendo que estás enfadado/a{nombre} 💙 El enfado es una emoción válida — nos dice que algo no está bien, que se ha cruzado un límite o que algo es injusto.\n\n¿Qué ha pasado que te ha puesto así?",
        "Tiene sentido que te sientas así{nombre} 🌱 El enfado a veces es la única forma que tiene el cuerpo de pedir que algo cambie. No hay que ignorarlo.\n\n¿Es algo que ha pasado ahora o lleva tiempo acumulándose?",
        "Lo que describes suena a mucha frustración acumulada{nombre} 💙 ¿Quieres contarme qué ha pasado? Estoy aquí para escucharte sin juzgar.",
        "El enfado que sientes{nombre} 💙 tiene una razón, aunque a veces no sepamos bien cuál. ¿Puedes contarme un poco más de lo que está pasando?",
        "Entiendo{nombre} 💚 El enfado puede ser muy intenso, y cuando uno está así es difícil pensar con claridad. Primero cuéntame qué ha pasado — después podemos ver qué hacer con eso.",
        "Tiene todo el sentido que te sientas así{nombre} 🤍 Cuando algo nos parece injusto o nos hace daño, el enfado es una respuesta natural.\n\n¿Quieres contarme qué ha pasado?",
    ],
    "enfado_profundizar": [
        "Gracias por seguir contándome{nombre} 💙 El enfado acumulado pesa mucho — y muchas veces no es solo por lo que ha pasado ahora, sino por todo lo que llevaba tiempo guardado.\n\n¿Sientes que este enfado viene solo de esta situación o hay cosas de antes también?",
        "Lo que describes tiene mucho sentido{nombre} 💚 A veces el enfado nos protege de otras emociones más difíciles — como el dolor o la decepción. ¿Crees que detrás del enfado hay algo más?",
        "Entiendo{nombre} 💙 El enfado es válido, pero cuando se queda dentro durante mucho tiempo también daña. ¿Has podido hablar con alguien sobre esto, aparte de conmigo?",
    ],
    "enfado_consejo": [
        "Para el enfado{nombre} 💙 el ejercicio físico intenso es uno de los mejores liberadores — correr, golpear un saco, saltar. El cuerpo necesita descargar la energía que acumula el enfado.\n\n¿Tienes alguna actividad física que te guste?",
        "Cuando el enfado está muy activo{nombre} 💙 escribirlo ayuda muchísimo. No para mandárselo a nadie — solo para sacarlo fuera. El journaling libera lo que de otra forma da vueltas en la cabeza.\n\n¿Lo has intentado alguna vez?",
        "La respiración guiada{nombre} 💙 es muy efectiva para bajar la activación del enfado porque actúa directamente sobre el sistema nervioso. Exhalar lentamente (más tiempo que la inhalación) activa el sistema parasimpático.",
    ],

    # ── Soledad ───────────────────────────────────────────────────────────────
    "soledad_primera": [
        "La soledad es una de las emociones más difíciles de sostener{nombre} 💙 Me alegra que estés aquí, porque aquí siempre hay alguien.\n\n¿Es una soledad de estar físicamente solo/a, o más de sentirte incomprendido/a aunque haya gente?",
        "Sentirse solo/a duele mucho{nombre} 🤍 Y muchas veces la soledad más difícil no es la de estar sin gente, sino la de estar con gente y sentirse igual de solo.\n\n¿Cómo es tu soledad?",
        "Que lo cuentes ya es importante{nombre} 💙 La soledad muchas veces hace que uno sienta que es el único que está así — pero hay mucha gente que pasa por lo mismo en silencio.\n\n¿Hay algo que haya cambiado recientemente que haya hecho que te sientas más solo/a?",
        "Gracias por contarme eso{nombre} 🤍 Sentirse invisible o incomprendido/a es muy duro. Nadie merece sentirse así.\n\n¿Hay alguien en tu vida con quien sientas que puedes ser tú mismo/a?",
        "La soledad que describes{nombre} 💙 suena muy profunda. No es solo estar físicamente solo — es esa sensación de que nadie te ve de verdad.\n\n¿Ha habido algún momento en que te hayas sentido conectado/a con alguien?",
        "Entiendo{nombre} 🤍 La soledad puede aparecer aunque estés rodeado de gente — y eso la hace más confusa todavía.\n\n¿Qué es lo que más echas de menos ahora mismo?",
    ],
    "soledad_profundizar": [
        "Gracias por seguir aquí{nombre} 💙 Sé que hablar de la soledad no es fácil. ¿Hay algo que hayas intentado para conectar con los demás que no haya funcionado?",
        "Lo que describes{nombre} 🤍 suena muy duro. La desconexión de los demás a veces viene de dentro — de sentir que uno no encaja o que los demás no van a entender.\n\n¿Crees que algo tuyo dificulta esa conexión, o sientes que los demás directamente no te ven?",
        "Entiendo{nombre} 💙 La soledad crónica afecta mucho al bienestar emocional. Una pregunta: ¿hay actividades o espacios donde puedas estar con personas que compartan algo contigo, aunque sea pequeño?",
    ],
    "soledad_consejo": [
        "Para la soledad{nombre} 💙 la comunidad HTH puede ser un buen primer paso — conectar con personas que entienden lo que sientes, aunque sea a distancia, puede marcar la diferencia.\n\n¿Te apetecería explorar eso?",
        "El diario emocional{nombre} 🤍 no sustituye la conexión humana, pero sí acompaña la soledad. Escribir te pone en contacto contigo mismo/a, y eso es también una forma de no estar tan solo.\n\n¿Has probado alguna vez escribir lo que sientes?",
    ],

    # ── Alegría ───────────────────────────────────────────────────────────────
    "alegria": [
        "¡Me alegra mucho escucharlo{nombre}! 😊 Ese estado positivo es valioso y merece ser celebrado.\n\n¿Qué ha pasado hoy que te hace sentir así de bien?",
        "¡Genial{nombre}! 🌟 Los momentos buenos también merecen atención — y aprovecharlos bien marca la diferencia.\n\n¿Quieres explorar alguna técnica para «guardar» este estado y poder volver a él cuando lo necesites?",
        "¡Qué bien{nombre}! 💚 Me encanta saber cuándo estás bien. Estos momentos son importantes.\n\n¿Qué es lo que ha cambiado o ha pasado?",
        "¡Eso es genial{nombre}! 🌱 Cuando estamos bien es el mejor momento para reforzar hábitos positivos — el cerebro aprende mejor desde la calma.\n\n¿Hay algo que quieras hacer con este buen momento?",
        "Qué bien{nombre} 💚 Me alegra escucharlo. ¿Hay algo que hayas hecho o que haya pasado que haya contribuido a sentirte así?",
    ],

    # ── Mejora ────────────────────────────────────────────────────────────────
    "mejora": [
        "¡Me alegra mucho que estés mejor{nombre}! 😊 Eso es un gran avance, de verdad.\n\n¿Qué ha cambiado? Me encanta saber qué ha ayudado.",
        "¡Qué bien{nombre}! 💚 Es muy importante notar los progresos, aunque sean pequeños. El hecho de que estés mejor dice mucho de ti.\n\n¿Cómo te sientes ahora comparado con antes?",
        "Me alegra mucho escuchar eso{nombre} 🌱 El camino hacia el bienestar no es lineal — y que estés mejor es una señal real de que algo está funcionando.\n\n¿Qué crees que más ha ayudado?",
    ],

    # ── Crisis ────────────────────────────────────────────────────────────────
    "crisis": [
        "Lo que me estás contando es muy serio y quiero que sepas que no estás solo/a 💙\n\nAhora mismo lo más importante es que estés a salvo. Por favor llama al **024** — es gratuito, confidencial y están disponibles ahora mismo las 24 horas.\n\n¿Estás en un lugar seguro en este momento?",
        "Gracias por contarme esto. Sé que no es fácil decirlo y lo valoro mucho 🤍\n\nLo que sientes tiene solución, aunque ahora no lo parezca. Por favor llama al **024** ahora mismo — es gratuito y están ahí para ti.\n\n¿Hay alguien cercano a quien puedas llamar también?",
        "Lo que me cuentas me importa mucho y te tomo muy en serio 💙\n\nNecesito que llames ahora al **024** — es el teléfono de atención a la conducta suicida, gratuito y confidencial, disponible 24 horas. Ellos saben cómo ayudarte en este momento.\n\n¿Estás a salvo ahora mismo?",
    ],

    # ── Seguimiento ───────────────────────────────────────────────────────────
    "seguimiento_ansiedad": [
        "¿Cómo estás ahora{nombre} comparado con antes? ¿La ansiedad sigue igual de intensa o ha bajado algo? 💙",
        "¿Cómo llevas la ansiedad ahora{nombre}? ¿Hay algún cambio desde que hablamos? 💙",
        "Antes me contabas que te sentías ansioso/a{nombre} 💙 ¿Cómo estás ahora?",
    ],
    "seguimiento_tristeza": [
        "¿Sigues sintiéndote igual de triste{nombre} o hay algún cambio? 💙",
        "¿Cómo estás ahora{nombre}? La tristeza que me contabas me tiene pensando en ti 🤍",
        "Hace un momento me contabas que estabas triste{nombre} 💙 ¿Cómo te encuentras ahora?",
    ],
    "seguimiento_estres": [
        "¿El estrés sigue igual{nombre} o ha bajado un poco? 💙",
        "¿Cómo llevas ahora todo lo que me contabas{nombre}? ¿Ha cambiado algo? 💚",
        "Antes hablábamos de que estabas muy estresado/a{nombre} 💙 ¿Cómo te sientes ahora?",
    ],
    "seguimiento_cansancio": [
        "¿Sigues sintiéndote igual de cansado/a{nombre} o hay algún cambio? 🤍",
        "¿Cómo llevas el cansancio ahora{nombre}? 💙",
    ],
    "seguimiento_enfado": [
        "¿Cómo estás con ese enfado ahora{nombre}? ¿Sigue igual o ha cambiado algo? 💙",
        "Antes estabas bastante enfadado/a{nombre} 💙 ¿Cómo te encuentras ahora?",
    ],
    "seguimiento_soledad": [
        "¿Sigues sintiéndote así de solo/a{nombre} o ha cambiado algo? 💙",
        "¿Cómo estás ahora{nombre} con esa sensación de soledad? 🤍",
    ],
}

# ── Situaciones concretas: respuestas contextuales específicas ───────────────
# Se comprueban ANTES de la detección de emoción genérica para dar respuestas
# relevantes a lo que el usuario realmente ha dicho.
SITUACIONES = {
    "concentracion": {
        "terminos": [
            "no me concentro", "no puedo concentrarme", "no consigo concentrarme",
            "me cuesta concentrarme", "no me entra", "no me entra nada",
            "mente en blanco", "no puedo estudiar", "me cuesta estudiar",
            "no rindo estudiando", "no rindo en clase", "me distraigo",
            "no me sale nada", "bloqueo al estudiar", "bloqueo mental",
            "concentrándome", "concentrarme estudiando", "concentrar estudiando",
        ],
        "emocion": "estres",
        "respuestas": [
            "La falta de concentración al estudiar es muy frustrante{nombre} 💙 El cerebro cuando está bajo presión o cansado literalmente no puede retener información — no es que seas torpe, es que el sistema nervioso está sobreactivado.\n\n¿Llevas mucho tiempo así? ¿O ha empezado hace poco?",
            "Eso que describes{nombre} pasa mucho cuando hay estrés o cansancio acumulado 💙 El cerebro necesita estar en calma para aprender — si está en modo alerta, usa sus recursos en otra cosa y estudiar se vuelve casi imposible.\n\n¿Cómo tienes el descanso últimamente?",
            "No poder concentrarte estudiando genera mucha angustia{nombre} 💙 Especialmente si hay exámenes cerca o la sensación de que el tiempo se acaba.\n\n¿Qué pasa exactamente cuando intentas estudiar? ¿La mente se va a otro lado o estás tan bloqueado/a que no puedes ni empezar?",
            "Entiendo{nombre} 💙 Cuando no podemos concentrarnos se monta un círculo vicioso — nos preocupamos por no rendir, esa preocupación nos bloquea más, y así. Es agotador.\n\n¿Tienes exámenes o entregas pronto que te estén generando presión?",
        ],
    },
    "sueno": {
        "terminos": [
            "no duermo", "no puedo dormir", "duermo mal", "me desvelo",
            "me despierto de noche", "me despierto a las", "insomnio",
            "no descanso nada", "no pego ojo", "me cuesta dormir",
            "no consigo dormir", "no concilio el sueño", "doy vueltas en la cama",
        ],
        "emocion": "cansancio",
        "respuestas": [
            "No poder dormir bien es agotador{nombre}, y encima genera un círculo difícil de romper — el cansancio aumenta la ansiedad, y la ansiedad impide dormir 💙\n\n¿Sabes qué es lo que te mantiene despierto/a? ¿Son pensamientos que no paran, o es que el cuerpo no se relaja?",
            "El insomnio es uno de los problemas que más afecta al bienestar emocional{nombre} 💙 Cuando no descansamos bien, todo se hace más difícil — las emociones se intensifican y la mente pierde perspectiva.\n\n¿Llevas mucho tiempo sin dormir bien?",
            "Eso que describes suena muy duro{nombre} 🤍 No descansar bien afecta a todo — el ánimo, la concentración, las relaciones... El cuerpo no puede funcionar sin sueño.\n\n¿Cuándo fue la última vez que dormiste bien de verdad?",
        ],
    },
    "relaciones_ruptura": {
        "terminos": [
            "me ha dejado", "hemos roto", "ruptura", "se ha ido", "me dejó",
            "terminé con", "terminamos", "lo hemos dejado", "cortamos",
            "ya no estamos juntos", "mi ex", "me han dejado",
        ],
        "emocion": "tristeza",
        "respuestas": [
            "Una ruptura duele muchísimo{nombre} 💙 No importa cómo haya sido — perder a alguien importante siempre deja un hueco. No hay que minimizarlo.\n\n¿Quieres contarme qué ha pasado?",
            "Lo siento mucho{nombre} 🤍 Las rupturas son uno de los dolores más intensos que existen — mezcla tristeza, confusión, soledad... a veces todo a la vez.\n\n¿Hace cuánto ha pasado?",
            "Eso es muy duro{nombre} 💙 Después de una ruptura el mundo puede sentirse completamente distinto. Es normal que duela así de fuerte.\n\n¿Cómo estás llevándolo?",
        ],
    },
    "relaciones_conflicto": {
        "terminos": [
            "pelea con", "peleé con", "peleado con", "discutí con", "discusión con",
            "me ha hecho daño", "me ha fallado", "me han traicionado",
            "me ha mentido", "no me entiende", "me ignora", "me trata mal",
            "problema con mi", "conflicto con",
        ],
        "emocion": "enfado",
        "respuestas": [
            "Los conflictos con personas cercanas son especialmente dolorosos{nombre} 💙 Cuando alguien que importa nos falla, el enfado y la tristeza se mezclan.\n\n¿Qué ha pasado exactamente?",
            "Entiendo{nombre} 💙 Cuando hay una pelea o conflicto con alguien importante, es difícil saber cómo gestionarlo — y las emociones pueden ser muy intensas.\n\n¿Quieres contarme qué ha pasado?",
            "Eso suena muy duro{nombre} 🤍 Las relaciones que más nos importan son también las que más pueden hacernos daño cuando algo falla.\n\n¿Es algo que ha pasado ahora o lleva tiempo acumulándose?",
        ],
    },
    "presion_academica": {
        "terminos": [
            "tengo examen", "tengo exámenes", "tengo un examen", "mañana tengo examen",
            "suspendí", "suspendí el", "he suspendido", "he suspendido el",
            "me he cargado el examen", "saqué una mala nota", "saqué mala nota",
            "tengo que entregar", "entrega mañana", "entrega esta semana",
            "trabajo de fin", "tfg", "tfm", "tesis", "selectividad", "evau",
        ],
        "emocion": "estres",
        "respuestas": [
            "La presión académica puede ser muy intensa{nombre} 💙 Y cuando hay exámenes encima, la ansiedad y el bloqueo se disparan aunque uno quiera estudiar.\n\n¿Cómo te encuentras ahora mismo? ¿Más bloqueado/a, más nervioso/a...?",
            "Eso genera mucho estrés{nombre} 💙 La presión de los exámenes y las entregas a veces hace que el cerebro entre en modo pánico, justo lo contrario de lo que necesita para rendir.\n\n¿Cómo llevas la preparación?",
            "Entiendo{nombre} 💚 Los exámenes y entregas crean una presión muy real. No es solo nerviosismo — el cuerpo lo vive como una amenaza y eso afecta a todo.\n\n¿Qué es lo que más te preocupa ahora mismo de esto?",
        ],
    },
    "autoestima": {
        "terminos": [
            "soy un fracasado", "soy una fracasada", "no valgo nada", "no sirvo para nada",
            "soy un inútil", "soy una inútil", "soy lo peor", "soy un desastre",
            "me odio", "me odio a mí mismo", "no me aguanto", "soy horrible",
            "no soy suficiente", "nunca hago nada bien", "todo lo hago mal",
            "soy el peor", "soy la peor",
        ],
        "emocion": "tristeza",
        "respuestas": [
            "Lo que dices de ti mismo/a me llama mucho la atención{nombre} 💙 Cuando estamos pasando por un momento difícil, la mente tiende a ser muy cruel con nosotros — dice cosas que nunca le diríamos a un amigo.\n\n¿Por qué sientes eso ahora mismo? ¿Qué ha pasado?",
            "Escucho mucho dolor en lo que dices{nombre} 🤍 Esa forma de hablarte a ti mismo/a dice que estás sufriendo mucho, no que sea verdad lo que piensas.\n\n¿Qué es lo que te ha llevado a sentirte así?",
            "Eso que dices no es un hecho — es lo que la tristeza o el agotamiento te hace creer{nombre} 💙 Cuando estamos mal, la mente distorsiona las cosas y todo se ve más oscuro de lo que es.\n\n¿Qué ha pasado que te hace sentir así?",
        ],
    },
    "trabajo_presion": {
        "terminos": [
            "me han despedido", "me han echado del trabajo", "he perdido el trabajo",
            "no encuentro trabajo", "busco trabajo", "sin trabajo", "en paro",
            "jefe", "mi jefe", "el trabajo me", "en el trabajo",
            "compañeros de trabajo", "ambiente en el trabajo",
        ],
        "emocion": "estres",
        "respuestas": [
            "Los problemas de trabajo afectan a muchos más ámbitos de la vida de lo que parece{nombre} 💙 Consume energía, genera estrés y a veces hace que todo lo demás se tambalee.\n\n¿Qué es lo que está pasando exactamente?",
            "Eso suena muy estresante{nombre} 💙 El trabajo ocupa una parte enorme de nuestra vida, así que cuando algo falla ahí se nota en todo lo demás.\n\n¿Quieres contarme más?",
            "Entiendo{nombre} 💚 Las situaciones laborales difíciles son muy desgastantes — no solo por el trabajo en sí, sino por lo que representan: seguridad, identidad, rutina...\n\n¿Qué es lo que más te pesa de todo esto?",
        ],
    },
    "soledad_social": {
        "terminos": [
            "no tengo amigos", "no tengo a nadie", "mis amigos no me hacen caso",
            "me he quedado sin amigos", "no encajo", "no tengo con quien hablar",
            "nadie me llama", "nadie me escribe", "me siento excluido", "me siento excluida",
            "me han dejado de lado", "no me invitan",
        ],
        "emocion": "soledad",
        "respuestas": [
            "Sentirse sin nadie con quien hablar es uno de los dolores más duros{nombre} 💙 Y muchas veces nadie lo ve desde fuera.\n\n¿Qué ha pasado? ¿Siempre ha sido así o ha cambiado algo?",
            "Eso que describes duele mucho{nombre} 🤍 Sentirse excluido/a o sin conexión real con los demás es una de las experiencias más difíciles.\n\n¿Hay algo que haya cambiado recientemente en tus relaciones?",
            "Lo entiendo{nombre} 💙 La soledad social es muy dolorosa — especialmente cuando ves que los demás parecen tener esas conexiones que tú sientes que no tienes.\n\n¿Cómo describirías cómo es tu situación ahora mismo?",
        ],
    },
    "sintomas_fisicos": {
        "terminos": [
            "me duele la cabeza", "dolor de cabeza", "jaqueca", "migraña",
            "me duele el estómago", "tensión en el cuello", "me duele el cuello",
            "me tiemblan las manos", "nudo en el estómago", "me aprieta el pecho",
            "presión en el pecho", "me cuesta respirar", "hiperventilo",
        ],
        "emocion": "ansiedad",
        "respuestas": [
            "Los síntomas físicos que describes{nombre} 💙 muchas veces son la forma que tiene el cuerpo de expresar lo que la mente está viviendo. El estrés y la ansiedad se manifiestan físicamente — no es imaginación.\n\n¿Cuándo suelen aparecer? ¿En momentos concretos o a lo largo del día?",
            "El cuerpo habla mucho cuando hay tensión emocional{nombre} 💙 Lo que describes puede ser una respuesta física al estrés o la ansiedad acumulada.\n\n¿Cómo te encuentras emocionalmente además de estos síntomas físicos?",
            "Lo que describes suena incómodo{nombre} 💙 A veces el cuerpo nos avisa de que algo no está bien antes de que la mente lo reconozca. ¿Está pasando algo que te esté generando tensión o estrés?",
        ],
    },
}

# ── Palabras que indican preguntas sobre técnicas o consejos ─────────────────
PALABRAS_CONSEJO = [
    "qué hago", "que hago", "qué puedo hacer", "cómo lo hago", "cómo mejoro",
    "qué me recomiendas", "dame un consejo", "ayúdame", "ayudame",
    "cómo puedo", "como puedo", "qué técnica", "que tecnica",
    "cómo gestiono", "como gestiono", "cómo manejo", "como manejo",
    "algo para", "algo que me ayude", "algo que ayude",
]

# ── Palabras que expresan incomprensión o deseo de ser escuchado ─────────────
PALABRAS_ESCUCHA = [
    "no me entiendes", "no entiendes", "no me escuchas", "repite", "no sé",
    "no se", "no lo sé", "no lo se", "ni yo sé", "ni yo se",
    "no sé qué decir", "no sé cómo explicarlo",
]


# ── Helpers ────────────────────────────────────────────────────────────────────
def es_continuacion(mensaje):
    """True cuando el mensaje es una respuesta corta/vaga sin contenido propio.
    Solo en ese caso tiene sentido usar el 'seguimiento' de la emoción anterior."""
    t = mensaje.strip().lower()
    if len(t) <= 20:
        return True
    respuestas_vagas = {
        "sí", "si", "no", "igual", "más o menos", "mas o menos",
        "un poco", "bastante", "mucho", "no sé", "no se", "no lo sé",
        "no lo se", "sigue igual", "ahí va", "ahí está", "bien", "mal",
        "más o menos bien", "mas o menos bien", "fatal", "regular",
        "sigo igual", "no ha cambiado", "igual que antes",
    }
    return t in respuestas_vagas


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
        if em == "crisis":
            continue
        p = sum(1 for term in datos.get("terminos", []) if term in t)
        if p > 0:
            puntos[em] = p
    return max(puntos, key=puntos.get) if puntos else None


def detectar_situacion(texto):
    """Devuelve (clave_situacion, emocion) si el mensaje encaja en una situación concreta."""
    t = texto.lower()
    for clave, datos in SITUACIONES.items():
        if any(term in t for term in datos["terminos"]):
            return clave, datos["emocion"]
    return None, None


def detectar_pide_consejo(texto):
    t = texto.lower()
    return any(p in t for p in PALABRAS_CONSEJO)


def detectar_pide_escucha(texto):
    t = texto.lower()
    return any(p in t for p in PALABRAS_ESCUCHA)


def historial_emocion_predominante(historial):
    emociones = [
        detectar_emocion(m.get("texto", ""))
        for m in historial
        if m.get("sender") == "user"
    ]
    emociones = [e for e in emociones if e and e != "crisis"]
    return Counter(emociones).most_common(1)[0][0] if emociones else None


def num_mensajes_usuario(historial):
    return sum(1 for m in historial if m.get("sender") == "user")


def _fmt(respuesta, emocion, tecnicas):
    return {
        "respuesta": respuesta,
        "emocion_detectada": emocion,
        "es_crisis": False,
        "tecnicas_sugeridas": tecnicas,
        "frase_motivacional": (
            random.choice(FRASES_MOTIVACIONALES)
            if emocion and emocion not in ["alegria"]
            else None
        ),
    }


# ── Motor de conversación ─────────────────────────────────────────────────────
def construir_respuesta(mensaje, emocion, nombre, historial, sesiones):
    n = _n(nombre)
    num_msgs = num_mensajes_usuario(historial)
    emocion_previa = historial_emocion_predominante(historial)
    pide_consejo = detectar_pide_consejo(mensaje)
    pide_escucha = detectar_pide_escucha(mensaje)
    situacion, emocion_situacion = detectar_situacion(mensaje)

    def r(clave):
        opciones = RESPUESTAS.get(clave, RESPUESTAS["generica"])
        return random.choice(opciones).format(nombre=n)

    # ── CRISIS ──────────────────────────────────────────────────────────────
    if emocion == "crisis":
        return {
            "respuesta": r("crisis"),
            "emocion_detectada": "crisis",
            "es_crisis": True,
            "tecnicas_sugeridas": [],
            "frase_motivacional": None,
        }

    # ── PIDE ESCUCHA ─────────────────────────────────────────────────────────
    if pide_escucha:
        resp = (
            f"Perdona si no te he entendido bien{n} 💙 Cuéntamelo de otra forma — estoy aquí, sin prisa.\n\n¿Cómo lo describirías tú con tus palabras?"
            if "no me entiendes" in mensaje.lower() or "no entiendes" in mensaje.lower()
            else f"Tómate el tiempo que necesites{n} 💚 No hay que tenerlo claro para hablar. Empieza por donde puedas.\n\n¿Qué es lo primero que te viene a la cabeza ahora mismo?"
        )
        emocion_efectiva = emocion or emocion_previa
        return _fmt(resp, emocion_efectiva, [])

    # ── SITUACIÓN CONCRETA — tiene prioridad sobre la detección genérica ─────
    # Si detectamos una situación específica siempre respondemos a ella
    if situacion:
        emocion_final = emocion or emocion_situacion
        tecnicas = EMOCIONES.get(emocion_final, {}).get("tecnicas", [])[:2] if emocion_final else []
        respuestas_sit = SITUACIONES[situacion]["respuestas"]
        resp = random.choice(respuestas_sit).format(nombre=n)
        if tecnicas:
            resp += "\n\nMientras hablamos, hay algo que puede ayudarte ahora mismo 👇"
        return _fmt(resp, emocion_final, tecnicas)

    tecnicas = EMOCIONES.get(emocion, {}).get("tecnicas", [])[:2] if emocion else []
    emocion_efectiva = emocion or emocion_previa

    # ── PRIMERA VEZ (sin historial previo) ──────────────────────────────────
    if num_msgs <= 1:
        if emocion and emocion != "alegria":
            clave = f"{emocion}_primera"
            resp = r(clave) if clave in RESPUESTAS else r("generica")
        elif emocion == "alegria":
            resp = r("alegria")
        else:
            resp = r("inicio_sin_emocion")
        return _fmt(resp, emocion, tecnicas)

    # ── MEJORA DETECTADA ────────────────────────────────────────────────────
    if emocion == "alegria" and emocion_previa and emocion_previa != "alegria":
        return _fmt(r("mejora"), "alegria", [])

    if emocion == "alegria":
        return _fmt(r("alegria"), "alegria", [])

    # ── PIDE CONSEJO / TÉCNICA ──────────────────────────────────────────────
    if pide_consejo and emocion_efectiva and emocion_efectiva != "alegria":
        clave_consejo = f"{emocion_efectiva}_consejo"
        if clave_consejo in RESPUESTAS:
            resp = r(clave_consejo)
            all_tecnicas = EMOCIONES.get(emocion_efectiva, {}).get("tecnicas", [])[:2]
            return _fmt(resp, emocion_efectiva, all_tecnicas)

    # ── EMOCIÓN DETECTADA ────────────────────────────────────────────────────
    if emocion:
        if emocion == emocion_previa and num_msgs >= 2:
            clave = f"{emocion}_profundizar"
            resp = r(clave) if clave in RESPUESTAS else r(f"{emocion}_primera")
        else:
            clave = f"{emocion}_primera"
            resp = r(clave) if clave in RESPUESTAS else r("generica")

        if tecnicas:
            resp += "\n\nMientras hablamos, hay algo que puede ayudarte ahora mismo 👇"

        return _fmt(resp, emocion, tecnicas)

    # ── SIN EMOCIÓN — SEGUIMIENTO solo si el mensaje es corto/vago ──────────
    # Si el usuario dice algo concreto pero no detectamos emoción, respondemos
    # de forma genérica contextual — NO preguntamos por la emoción anterior.
    if emocion_previa and es_continuacion(mensaje):
        clave = f"seguimiento_{emocion_previa}"
        if clave in RESPUESTAS:
            return _fmt(r(clave), emocion_previa, [])

    return _fmt(r("generica"), None, [])


# ── Rutas Flask ───────────────────────────────────────────────────────────────
@app.route("/health", methods=["GET"])
def health():
    return jsonify({"status": "ok", "chatbot": "Hearty 💚"})


@app.route("/inicio", methods=["GET"])
def inicio():
    nombre = request.args.get("nombre", "")
    sesiones = int(request.args.get("sesiones", 0))
    n = nombre.split()[0] if nombre else "amigo/a"

    if sesiones == 0:
        msg = (
            f"¡Hola, {n}! Soy Hearty 💚\n\n"
            "Este es tu espacio seguro. Puedes contarme lo que quieras — "
            "cómo te sientes, qué te preocupa, qué te alegra... Sin juzgarte y sin prisa.\n\n"
            "¿Cómo estás hoy?"
        )
    elif sesiones < 5:
        msg = f"¡Hola de nuevo, {n}! 😊 Me alegra que hayas vuelto.\n\n¿Cómo estás hoy?"
    elif sesiones < 20:
        msg = f"¡{n}! Me alegra verte de nuevo 💚\n\n¿Cómo llevas el día?"
    else:
        msg = f"¡Hola, {n}! 🌟 Tu constancia en cuidarte es admirable.\n\n¿Cómo te sientes hoy?"

    return jsonify({
        "mensaje": msg,
        "opciones": [
            "😊 Bien", "😌 Tranquilo/a", "😰 Ansioso/a", "😢 Triste",
            "😠 Enfadado/a", "😴 Cansado/a", "😤 Estresado/a", "😔 Solo/a",
        ],
        "pregunta_id": "bienvenida",
    })


@app.route("/chat", methods=["POST"])
def chat():
    data = request.get_json()
    if not data or "mensaje" not in data:
        return jsonify({"error": "Mensaje requerido"}), 400

    mensaje = data.get("mensaje", "").strip()
    nombre = data.get("nombre", "")
    historial_chat = data.get("historial_chat", [])
    sesiones = data.get("sesiones", 0)
    pregunta_actual = data.get("pregunta_actual")
    flow_answers = data.get("flow_answers", {})

    if not mensaje:
        return jsonify({"error": "Mensaje vacío"}), 400

    print(f"💬 [{nombre}] {mensaje[:80]}")

    if pregunta_actual:
        resultado = procesar_flujo_preguntas(pregunta_actual, mensaje, flow_answers, nombre)
        if resultado:
            return jsonify(resultado)

    emocion = detectar_emocion(mensaje)
    resultado = construir_respuesta(mensaje, emocion, nombre, historial_chat, sesiones)
    return jsonify(resultado)


@app.route("/analizar", methods=["POST"])
def analizar():
    data = request.get_json()
    emocion = detectar_emocion(data.get("texto", ""))
    return jsonify({"detectado": emocion is not None, "emocion": emocion})


@app.route("/consejo", methods=["GET"])
def consejo():
    return jsonify({
        "consejo": random.choice(CONSEJOS_GENERALES),
        "motivacion": random.choice(FRASES_MOTIVACIONALES),
    })


if __name__ == "__main__":
    print("💚 Hearty arrancando...")
    app.run(host="0.0.0.0", port=5000, debug=True)
