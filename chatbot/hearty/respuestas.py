RESPUESTAS = {
    "😊 Bien": {
        "respuesta": "¡Me alegra mucho escucharlo! 😊 Mantener ese estado positivo es muy importante. ¿Quieres explorar alguna técnica para seguir cuidando tu bienestar?",
        "tecnicas": ["diario", "ejercicio"],
        "emocion_detectada": "alegría",
        "intensidad": 3
    },
    "😌 Tranquilo/a": {
        "respuesta": "Qué bien que te sientas tranquilo/a 😌 Es un estado muy valioso. Podemos aprovecharlo para practicar algo de mindfulness.",
        "tecnicas": ["respiracion", "sonidos"],
        "emocion_detectada": "calma",
        "intensidad": 2
    },
    "😰 Ansioso/a": {
        "respuesta": "Entiendo que la ansiedad puede ser agotadora 💙 No estás solo/a en esto. Tengo algunas técnicas que pueden ayudarte a calmarte ahora mismo.",
        "tecnicas": ["respiracion", "sonidos", "infusiones"],
        "emocion_detectada": "ansiedad",
        "intensidad": 7
    },
    "😢 Triste": {
        "respuesta": "Siento mucho que estés pasando por un momento difícil 💙 La tristeza es válida y merece ser escuchada. Estoy aquí contigo.",
        "tecnicas": ["diario", "ejercicio"],
        "emocion_detectada": "tristeza",
        "intensidad": 6
    },
    "😠 Enfadado/a": {
        "respuesta": "El enfado es una emoción completamente válida 💪 Lo importante es encontrar una salida saludable. Vamos a trabajar en eso juntos.",
        "tecnicas": ["ejercicio", "respiracion"],
        "emocion_detectada": "enfado",
        "intensidad": 7
    },
    "😴 Cansado/a": {
        "respuesta": "El cansancio acumulado afecta mucho a nuestro bienestar 😴 Tu cuerpo y mente necesitan descanso. Vamos a ver qué puede ayudarte.",
        "tecnicas": ["sonidos", "infusiones"],
        "emocion_detectada": "cansancio",
        "intensidad": 5
    },
    "Hoy solamente": {
        "respuesta": "Que sea algo puntual de hoy es una buena señal. A veces el día a día nos pasa factura. Vamos a ver cómo recuperarte.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Unos días": {
        "respuesta": "Llevar unos días así puede ser agotador. Es importante que te cuides ahora antes de que se acumule más.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Varias semanas": {
        "respuesta": "Varias semanas es bastante tiempo cargando con eso. Me alegra que estés aquí buscando apoyo. Vamos paso a paso.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Hace mucho tiempo": {
        "respuesta": "Llevar mucho tiempo sintiéndote así requiere atención especial. Además de lo que trabajemos juntos, considera hablar con un profesional si no lo has hecho 💙",
        "emocion_detectada": None,
        "intensidad": 8
    },
    "El trabajo o estudios": {
        "respuesta": "El estrés académico y laboral es muy común hoy en día. La clave está en establecer límites y momentos de desconexión.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Mis relaciones personales": {
        "respuesta": "Las relaciones son parte fundamental de nuestro bienestar. Es normal que nos afecten profundamente cuando no van bien.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Mi salud": {
        "respuesta": "La salud física y mental están muy conectadas. Cuidar una ayuda a la otra. Vamos a ver qué podemos hacer.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "No lo sé": {
        "respuesta": "No siempre tenemos que tener una respuesta clara. A veces simplemente nos sentimos mal y está bien no saber por qué.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Varias cosas a la vez": {
        "respuesta": "Cuando todo se acumula puede ser abrumador. Lo importante es ir de una en una, sin presión.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Técnicas para calmarme": {
        "respuesta": "Perfecto, tengo varias técnicas que pueden ayudarte ahora mismo 🌿",
        "tecnicas": ["respiracion", "sonidos", "infusiones"],
        "emocion_detectada": None,
        "intensidad": None,
        "accion": "mostrar_tecnicas"
    },
    "Recursos informativos": {
        "respuesta": "Aquí tienes algunos recursos que pueden orientarte 📚",
        "emocion_detectada": None,
        "intensidad": None,
        "accion": "mostrar_recursos"
    },
    "Solo quiero desahogarme": {
        "respuesta": "Estoy aquí para escucharte 💙 Cuéntame lo que necesites, sin juicios. Aquí es un espacio seguro.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Consejos prácticos": {
        "respuesta": "Te doy algunos consejos concretos que puedes aplicar hoy mismo 💡",
        "tecnicas": ["ejercicio", "diario"],
        "emocion_detectada": None,
        "intensidad": None,
        "accion": "mostrar_consejos"
    },
}

# ── Detección por palabras clave en texto libre ──
PALABRAS_CLAVE = {
    "ansiedad": {
        "palabras": ["ansioso", "ansiosa", "ansiedad", "nervioso", "nerviosa", "angustia",
                     "angustiado", "pánico", "agobiado", "agobiada", "preocupado", "preocupada",
                     "estrés", "estresado", "estresada", "intranquilo", "intranquila"],
        "respuesta": "Noto que estás pasando por un momento de ansiedad 💙 Es completamente normal sentirse así. Vamos a trabajar juntos para que te sientas mejor.",
        "tecnicas": ["respiracion", "sonidos", "infusiones"],
        "emocion": "ansiedad",
        "intensidad": 7
    },
    "tristeza": {
        "palabras": ["triste", "tristeza", "llorar", "llorando", "deprimido", "deprimida",
                     "mal", "horrible", "fatal", "vacío", "vacía", "solo", "sola",
                     "abandonado", "abandonada", "desesperado", "desesperada"],
        "respuesta": "Siento que estás pasando por un momento difícil 💙 Gracias por contármelo. Estoy aquí contigo y vamos a encontrar algo que te ayude.",
        "tecnicas": ["diario", "ejercicio", "sonidos"],
        "emocion": "tristeza",
        "intensidad": 6
    },
    "alegría": {
        "palabras": ["bien", "genial", "feliz", "contento", "contenta", "alegre",
                     "estupendo", "estupenda", "perfecto", "perfecta", "increíble",
                     "fantástico", "fantástica", "positivo", "positiva", "motivado", "motivada"],
        "respuesta": "¡Me encanta que te sientas así! 😊 Ese estado de ánimo positivo es muy valioso. ¿Quieres aprovecharlo para practicar algo de bienestar?",
        "tecnicas": ["diario", "ejercicio"],
        "emocion": "alegría",
        "intensidad": 2
    },
    "cansancio": {
        "palabras": ["cansado", "cansada", "agotado", "agotada", "sin energía", "exhausto",
                     "exhausta", "sin fuerzas", "duermo mal", "no duermo", "insomnio",
                     "dormido", "dormida", "somnoliento", "somnolencia"],
        "respuesta": "El agotamiento es una señal de que tu cuerpo y mente necesitan descanso 😴 Vamos a ver qué técnicas pueden ayudarte a recuperarte.",
        "tecnicas": ["sonidos", "infusiones", "respiracion"],
        "emocion": "cansancio",
        "intensidad": 5
    },
    "enfado": {
        "palabras": ["enfadado", "enfadada", "furioso", "furiosa", "rabia", "ira",
                     "odio", "frustrado", "frustrada", "irritado", "irritada",
                     "molesto", "molesta", "harto", "harta", "desesperado"],
        "respuesta": "El enfado también forma parte de nosotros 💪 Lo importante es canalizarlo de forma saludable. Tengo algo que puede ayudarte.",
        "tecnicas": ["ejercicio", "respiracion"],
        "emocion": "enfado",
        "intensidad": 7
    },
    "calma": {
        "palabras": ["tranquilo", "tranquila", "calmado", "calmada", "sereno", "serena",
                     "relajado", "relajada", "en paz", "equilibrado", "equilibrada"],
        "respuesta": "Qué bien que te sientas en ese estado de calma 😌 Es perfecto para practicar algo de mindfulness y profundizar en ese bienestar.",
        "tecnicas": ["respiracion", "sonidos"],
        "emocion": "calma",
        "intensidad": 2
    }
}

CONSEJOS_GENERALES = [
    "💧 Bebe agua. La deshidratación afecta directamente al estado de ánimo.",
    "🌬️ Practica la respiración 4-4-6: inspira 4 segundos, mantén 4, espira 6.",
    "🚶 Date un paseo corto de 10 minutos. El movimiento libera endorfinas.",
    "📵 Desconéctate del móvil al menos 30 minutos antes de dormir.",
    "✍️ Escribe 3 cosas por las que estar agradecido/a hoy.",
    "🍵 Prepárate una infusión caliente de manzanilla o tila.",
    "🎵 Pon música que te guste y simplemente escúchala sin hacer nada más.",
    "🌿 Sal al exterior aunque sea 5 minutos. La naturaleza calma el sistema nervioso.",
    "😴 Intenta acostarte y levantarte a la misma hora cada día.",
    "🤗 Comparte cómo te sientes con alguien de confianza.",
]

NOMBRES_TECNICAS = {
    "respiracion": "🫁 Respiración guiada",
    "sonidos":     "🎵 Sonidos relajantes",
    "diario":      "📓 Diario de gratitud",
    "infusiones":  "🍵 Infusiones",
    "ejercicio":   "🏃 Ejercicio suave",
}

RUTAS_TECNICAS = {
    "respiracion": "/respiracion",
    "sonidos":     "/sonidos",
    "diario":      "/diario",
    "infusiones":  "/infusiones",
    "ejercicio":   "/ejercicio",
}