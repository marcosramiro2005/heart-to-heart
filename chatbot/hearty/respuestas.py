RESPUESTAS = {
    "😊 Bien": {
        "respuesta": "¡Me alegra mucho escucharlo! 😊 Mantener ese estado positivo es importante. ¿Quieres aprovechar este buen momento para practicar algo que te ayude a mantenerlo cuando las cosas se compliquen?",
        "tecnicas": ["diario", "ejercicio"],
        "emocion_detectada": "alegría",
        "intensidad": 3,
        "frase_motivacional": "La alegría de hoy es la energía que necesitas mañana. 🌟"
    },
    "😌 Tranquilo/a": {
        "respuesta": "Qué bien que te sientas tranquilo/a 😌 La calma es un estado muy valioso. Podemos aprovecharlo para hacer algo de mindfulness o simplemente disfrutarlo.",
        "tecnicas": ["respiracion", "sonidos", "meditacion"],
        "emocion_detectada": "calma",
        "intensidad": 2,
        "frase_motivacional": "La calma no es ausencia de problemas, es la capacidad de afrontarlos con serenidad. 🌊"
    },
    "😰 Ansioso/a": {
        "respuesta": "Entiendo que la ansiedad puede ser muy agotadora 💙 Lo que sientes es completamente válido. Vamos a trabajar juntos para que te sientas mejor ahora mismo.\n\nPrueba esto: pon una mano en el pecho y respira profundo durante 4 segundos, mantén 4, suelta 6. Repítelo tres veces. Este momento pasará.\n\n¿Puedes contarme qué está generando esa ansiedad?",
        "tecnicas": ["respiracion", "sonidos", "tapping", "infusiones"],
        "emocion_detectada": "ansiedad",
        "intensidad": 7,
        "frase_motivacional": "La ansiedad es una tormenta. Tú eres el cielo que permanece después. ⛅"
    },
    "😢 Triste": {
        "respuesta": "Gracias por contármelo 💙 La tristeza es una emoción completamente válida y merece ser escuchada, no ignorada. Está bien estar triste.\n\nEstoy aquí contigo. ¿Quieres contarme qué ha pasado, o prefieres que te sugiera algo que pueda ayudarte a sentirte un poco mejor?",
        "tecnicas": ["diario", "ejercicio", "visualizacion", "sonidos"],
        "emocion_detectada": "tristeza",
        "intensidad": 6,
        "frase_motivacional": "La tristeza es amor que todavía no ha encontrado su camino. 💙"
    },
    "😠 Enfadado/a": {
        "respuesta": "El enfado es una emoción completamente válida 💪 Algo que valoras ha sido amenazado o no respetado. No tienes que avergonzarte de sentirlo.\n\nLo importante es canalizarlo de forma saludable. El ejercicio físico es muy efectivo para liberar esa energía. ¿Quieres contarme qué ha pasado?",
        "tecnicas": ["ejercicio", "respiracion", "tapping"],
        "emocion_detectada": "enfado",
        "intensidad": 7,
        "frase_motivacional": "El enfado es fuego. Puedes usarlo para destruir o para forjar algo mejor. 🔥"
    },
    "😴 Cansado/a": {
        "respuesta": "El cansancio es una señal de que tu cuerpo y mente necesitan recuperarse 😴 En una sociedad que celebra la productividad constante, permitirte descansar es un acto de valentía.\n\n¿Es cansancio físico, mental o emocional? Cada tipo necesita un descanso diferente.",
        "tecnicas": ["sonidos", "infusiones", "respiracion", "yoga"],
        "emocion_detectada": "cansancio",
        "intensidad": 5,
        "frase_motivacional": "Descansar no es rendirse. Es recargar para seguir. 🌙"
    },
    "Hoy solamente": {
        "respuesta": "Que sea algo puntual es una buena señal 💙 A veces el día a día nos pasa factura y es completamente normal tener días difíciles. Lo importante es que lo estás reconociendo.\n\n¿Hay algo concreto que haya pasado hoy?",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Unos días": {
        "respuesta": "Llevar unos días así puede ser agotador, especialmente cuando intentas seguir con tu rutina normal 💙 Tu cuerpo y mente están enviando señales que merecen atención.\n\nEn estos días intenta ser más amable contigo mismo/a. Reduce las exigencias si puedes y prioriza el descanso.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Varias semanas": {
        "respuesta": "Llevar varias semanas así requiere atención especial 💙 Cuando algo se prolonga durante semanas, el cuerpo y la mente empiezan a adaptarse a ese estado y salir requiere más esfuerzo.\n\nAdemás de las herramientas que tenemos aquí, te animo a considerar hablar con alguien de confianza o con un profesional. Buscar ayuda no es debilidad, es exactamente lo contrario.\n\n¿Tienes personas cercanas con las que puedas hablar?",
        "emocion_detectada": None,
        "intensidad": 7
    },
    "Hace mucho tiempo": {
        "respuesta": "Llevar mucho tiempo cargando con esto es muy duro 💙 Lo que sientes tiene nombre y tiene solución. Nadie debería sentirse así de forma indefinida sin apoyo.\n\nTe pido que consideres seriamente hablar con un profesional de salud mental. En España puedes llamar al 024 (línea de atención psicológica gratuita, disponible 24h) o pedir cita con tu médico de cabecera.\n\n¿Tienes acceso a algún tipo de apoyo profesional actualmente?",
        "emocion_detectada": None,
        "intensidad": 8,
        "recurso_crisis": True
    },
    "El trabajo o estudios": {
        "respuesta": "El estrés académico y laboral es uno de los más comunes y también de los más normalizados, lo que hace que a veces lo ignoremos hasta que explota 💙\n\nAlgunas claves: establecer límites claros entre trabajo y descanso, hacer pausas regulares, y revisar si tus expectativas sobre ti mismo/a son realistas. La perfección no existe, pero el agotamiento sí.\n\n¿Qué aspecto concreto te está pesando más?",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Mis relaciones personales": {
        "respuesta": "Las relaciones personales son fuente de bienestar y también de dolor cuando no van bien 💙 Esto es así porque las personas que nos importan tienen el poder de afectarnos profundamente.\n\nAlgunas preguntas para reflexionar: ¿Es una relación en la que das más de lo que recibes? ¿Te sientes escuchado/a y valorado/a? ¿Hay algo que necesitas comunicar pero no te has atrevido?\n\n¿Quieres contarme un poco más sobre la situación?",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Mi salud": {
        "respuesta": "La salud física y mental están profundamente conectadas 💙 Cuando el cuerpo no está bien, la mente lo siente, y viceversa.\n\nSi estás pasando por un problema de salud físico, es completamente normal que afecte a tu estado emocional. El miedo, la incertidumbre y la sensación de pérdida de control son respuestas naturales.\n\n¿Quieres contarme más sobre lo que está pasando con tu salud?",
        "emocion_detectada": None,
        "intensidad": None
    },
    "No lo sé": {
        "respuesta": "No siempre tenemos que saber por qué nos sentimos como nos sentimos, y está completamente bien 💙 A veces el malestar no tiene una causa obvia.\n\nEse 'no sé' también es válido. Tu experiencia emocional no necesita una justificación para merecer atención.\n\nA veces escribir libremente ayuda a que emerjan cosas que no sabíamos que estaban ahí. ¿Te animarías a probarlo?",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Varias cosas a la vez": {
        "respuesta": "Cuando todo se acumula puede ser absolutamente abrumador 💙 La sensación de que hay demasiado a la vez y no sabes por dónde empezar es una de las más agotadoras.\n\nUn truco que ayuda: escribe en un papel todo lo que te preocupa, sin filtrar. Una vez fuera de tu cabeza, distingue: ¿qué puedo hacer yo ahora? ¿qué está fuera de mi control? Lo que no puedes controlar, intenta soltarlo. Lo que sí puedes, elige UNA cosa pequeña para hoy.\n\n¿Quieres contarme qué es lo que más te está pesando?",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Técnicas para calmarme": {
        "respuesta": "Perfecto, tengo varias técnicas probadas que pueden ayudarte ahora mismo 🌿\n\nPara calma inmediata: la respiración 4-7-8 es muy efectiva (inspira 4s, mantén 7s, expira 8s). También el grounding 5-4-3-2-1: nombra 5 cosas que ves, 4 que tocas, 3 que escuchas, 2 que hueles, 1 que saboreas. Esto ancla tu mente al presente.\n\nAquí tienes las herramientas:",
        "tecnicas": ["respiracion", "sonidos", "tapping", "infusiones"],
        "emocion_detectada": None,
        "intensidad": None,
        "accion": "mostrar_tecnicas"
    },
    "Recursos informativos": {
        "respuesta": "Informarse sobre salud mental es uno de los actos de autocuidado más importantes 📚 El conocimiento nos da herramientas y reduce el miedo.\n\nEn nuestra biblioteca encontrarás artículos sobre ansiedad, depresión, mindfulness, sueño, autoestima y relaciones. También tenemos noticias actualizadas sobre salud mental.",
        "emocion_detectada": None,
        "intensidad": None,
        "accion": "mostrar_recursos"
    },
    "Solo quiero desahogarme": {
        "respuesta": "Estoy aquí para escucharte, con toda mi atención 💙 Este es un espacio seguro, sin juicios y sin prisas.\n\nCuéntame lo que necesites. No tienes que ordenar los pensamientos ni explicarte bien. Solo escribe, y yo estaré aquí.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Consejos prácticos": {
        "respuesta": "Aquí tienes consejos concretos y basados en evidencia que puedes aplicar hoy 💡\n\n🌅 Mañana: 5 minutos de respiración consciente antes de mirar el móvil.\n🚶 Durante el día: una caminata de 10 minutos al aire libre libera serotonina.\n🌙 Noche: escribe 3 cosas buenas que han pasado hoy, por pequeñas que sean.\n💧 Siempre: hidratación. La deshidratación leve ya afecta al estado de ánimo.\n\n¿Cuál te parece más fácil de empezar hoy?",
        "tecnicas": ["ejercicio", "diario", "respiracion"],
        "emocion_detectada": None,
        "intensidad": None
    },
    "Quiero hablar más": {
        "respuesta": "Me alegra que quieras seguir 💙 Estoy aquí. ¿Sobre qué quieres hablar? Puedes contarme cualquier cosa: cómo fue tu día, algo que te preocupa, algo que te alegra, o simplemente lo que te pase por la cabeza.",
        "emocion_detectada": None,
        "intensidad": None
    },
    "Prefiero practicar una técnica": {
        "respuesta": "Perfecto, practicar es la mejor decisión 🌿 ¿Cuál te apetece más ahora mismo?",
        "tecnicas": ["respiracion", "meditacion", "yoga", "tapping", "visualizacion"],
        "emocion_detectada": None,
        "intensidad": None,
        "accion": "mostrar_tecnicas"
    },
    "Estoy bien, gracias": {
        "respuesta": "Me alegra mucho 😊 Recuerda que estoy aquí siempre que me necesites. Cuídate mucho y sigue así de bien. 💚",
        "emocion_detectada": "alegría",
        "intensidad": 2
    },
    "Tengo una pregunta concreta": {
        "respuesta": "Claro, dime. Puedo ayudarte con preguntas sobre salud mental, técnicas de bienestar, cómo gestionar emociones, recursos disponibles, o cualquier otra cosa relacionada con tu bienestar. ¿Qué quieres saber?",
        "emocion_detectada": None,
        "intensidad": None
    },
}

PALABRAS_CLAVE = {
    "crisis": {
        "palabras": [
            "no quiero vivir", "quiero morir", "hacerme daño", "suicidio",
            "suicidarme", "no puedo más", "desaparecer", "quitarme la vida",
            "no tiene sentido vivir", "estoy harto de todo", "mejor sin mí",
            "me quiero morir", "no vale la pena seguir"
        ],
        "respuesta": "Lo que me estás contando es muy serio y quiero que sepas que me importa profundamente lo que te está pasando 💙\n\nSi estás teniendo pensamientos de hacerte daño, por favor llama ahora al 024 — es la línea de atención a la conducta suicida en España, gratuita, confidencial y disponible las 24 horas. También puedes ir a urgencias de cualquier hospital.\n\nNo tienes que estar solo/a con esto. ¿Puedes llamar al 024 ahora mismo?",
        "tecnicas": [],
        "emocion": "crisis",
        "intensidad": 10,
        "es_crisis": True
    },
    "ansiedad": {
        "palabras": [
            "ansioso", "ansiosa", "ansiedad", "nervioso", "nerviosa", "angustia",
            "angustiado", "angustiada", "pánico", "ataque de pánico", "agobiado",
            "agobiada", "preocupado", "preocupada", "estrés", "estresado",
            "estresada", "intranquilo", "intranquila", "tensión", "tenso",
            "tensa", "inquieto", "inquieta", "asustado", "asustada", "miedo constante",
            "no puedo respirar", "me ahogo", "palpitaciones", "taquicardia"
        ],
        "respuesta": "Noto en lo que escribes señales de ansiedad 💙 Lo que sientes es real y válido.\n\nPrimero: respira. Pon una mano en el pecho y haz tres respiraciones profundas y lentas. No tienes que solucionar nada ahora mismo, solo respirar.\n\n¿Hay algo concreto que esté generando esa ansiedad, o es más una sensación general de malestar?",
        "tecnicas": ["respiracion", "sonidos", "tapping", "infusiones"],
        "emocion": "ansiedad",
        "intensidad": 7
    },
    "tristeza": {
        "palabras": [
            "triste", "tristeza", "llorar", "llorando", "lloro", "deprimido",
            "deprimida", "depresión", "mal", "horrible", "fatal", "vacío",
            "vacía", "solo", "sola", "soledad", "abandonado", "abandonada",
            "desesperado", "desesperada", "sin ganas", "no quiero nada",
            "para qué", "sin sentido", "perdido", "perdida", "hundido", "hundida",
            "no me importa nada", "todo me da igual", "me siento vacío"
        ],
        "respuesta": "Lo que describes me dice que estás pasando por un momento muy difícil 💙 Gracias por estar aquí y por contármelo.\n\nLa tristeza profunda puede hacer que todo parezca más pesado y sin sentido. Es una sensación muy dura de llevar.\n\n¿Estás teniendo pensamientos de hacerte daño? Puedes contármelo con total confianza.",
        "tecnicas": ["diario", "ejercicio", "visualizacion", "sonidos"],
        "emocion": "tristeza",
        "intensidad": 7
    },
    "soledad": {
        "palabras": [
            "solo", "sola", "soledad", "aislado", "aislada", "nadie me entiende",
            "nadie me escucha", "no tengo a nadie", "me siento invisible",
            "desconectado", "desconectada", "sin amigos", "incomprendido",
            "incomprendida", "nadie se preocupa por mí"
        ],
        "respuesta": "La soledad es una de las experiencias más dolorosas que existen 💙 Y es especialmente dura cuando hay personas alrededor pero aun así te sientes incomprendido/a.\n\nQuiero que sepas que el hecho de que estés aquí contándome cómo te sientes ya es un acto de valentía. No estás completamente solo/a en este momento.\n\nEl foro de la comunidad de Heart to Heart puede ser un primer paso para conectar con personas que quizás estén pasando por algo similar. ¿Te gustaría explorarlo?",
        "tecnicas": ["diario", "sonidos"],
        "emocion": "tristeza",
        "intensidad": 7
    },
    "cansancio": {
        "palabras": [
            "cansado", "cansada", "agotado", "agotada", "sin energía", "exhausto",
            "exhausta", "sin fuerzas", "duermo mal", "no duermo", "insomnio",
            "somnoliento", "somnolencia", "rendido", "rendida", "burnout",
            "quemado", "quemada", "no tengo energía", "todo me cuesta",
            "me pesa todo", "sin motivación"
        ],
        "respuesta": "El agotamiento que describes es una señal importante 😴 En una sociedad que te pide estar siempre al 100%, el cansancio crónico es muy común pero no debería normalizarse.\n\nHay diferentes tipos de cansancio: físico, mental y emocional. Cada uno necesita un descanso diferente.\n\n¿De qué tipo crees que es tu cansancio ahora mismo?",
        "tecnicas": ["sonidos", "infusiones", "respiracion", "yoga"],
        "emocion": "cansancio",
        "intensidad": 6
    },
    "enfado": {
        "palabras": [
            "enfadado", "enfadada", "furioso", "furiosa", "rabia", "ira",
            "odio", "frustrado", "frustrada", "irritado", "irritada",
            "molesto", "molesta", "harto", "harta", "indignado", "indignada",
            "cabreado", "cabreada", "no aguanto", "me tiene harto",
            "estoy hasta las narices", "me saca de quicio"
        ],
        "respuesta": "El enfado que sientes tiene una razón de ser 💪 Y reconocerlo es el primer paso para gestionarlo bien.\n\nEl enfado no es malo en sí mismo, es una señal de que algo que valoramos ha sido amenazado. El problema es cuando se acumula.\n\nEl ejercicio físico intenso es una de las formas más efectivas de liberar esa energía de forma saludable. ¿Tienes posibilidad de moverte un poco ahora?",
        "tecnicas": ["ejercicio", "respiracion", "tapping"],
        "emocion": "enfado",
        "intensidad": 7
    },
    "alegría": {
        "palabras": [
            "bien", "genial", "feliz", "contento", "contenta", "alegre",
            "estupendo", "estupenda", "perfecto", "perfecta", "increíble",
            "fantástico", "fantástica", "positivo", "positiva", "motivado",
            "motivada", "con energía", "ilusionado", "ilusionada", "emocionado",
            "emocionada", "agradecido", "agradecida", "satisfecho", "satisfecha",
            "muy bien", "excelente", "maravilloso"
        ],
        "respuesta": "¡Qué bien me hace saber que estás bien! 😊 Ese estado de ánimo positivo es muy valioso.\n\nEstar bien no significa que no haya nada en lo que trabajar. ¿Hay algo en lo que quieras seguir creciendo hoy, o algo que quieras celebrar?",
        "tecnicas": ["diario", "ejercicio"],
        "emocion": "alegría",
        "intensidad": 2
    },
    "calma": {
        "palabras": [
            "tranquilo", "tranquila", "calmado", "calmada", "sereno", "serena",
            "relajado", "relajada", "en paz", "equilibrado", "equilibrada",
            "sosegado", "sosegada", "estable", "centrado", "centrada"
        ],
        "respuesta": "Ese estado de calma es un regalo 😌 Disfrútalo y aprovéchalo.\n\nEs el momento ideal para practicar mindfulness o meditación, ya que tu mente está receptiva. También es buen momento para reflexionar en el diario.",
        "tecnicas": ["respiracion", "sonidos", "meditacion"],
        "emocion": "calma",
        "intensidad": 2
    },
    "autoestima": {
        "palabras": [
            "no valgo nada", "soy un fracaso", "no sirvo para nada",
            "me odio", "me da vergüenza", "soy lo peor", "no me quiero",
            "no me acepto", "inseguro", "insegura", "complejo", "feo", "fea",
            "no soy suficiente", "me siento inferior", "nadie me quiere"
        ],
        "respuesta": "Lo que describes sobre cómo te ves a ti mismo/a me preocupa 💙 Hablar con esa dureza sobre uno mismo es muy doloroso y merece atención.\n\nQuiero que sepas algo importante: la forma en que te hablas a ti mismo/a es un hábito que se puede cambiar. La autoestima no es algo fijo que se tiene o no se tiene, se trabaja y se fortalece.\n\n¿Desde cuándo te sientes así sobre ti mismo/a?",
        "tecnicas": ["diario", "visualizacion"],
        "emocion": "tristeza",
        "intensidad": 7
    },
    "trabajo": {
        "palabras": [
            "trabajo", "jefe", "jefa", "compañero", "compañera", "oficina",
            "reunión", "proyecto", "deadline", "plazo", "presión laboral",
            "me echan", "me despiden", "desempleo", "paro", "no encuentro trabajo"
        ],
        "respuesta": "Los problemas laborales generan un estrés muy particular porque afectan a nuestra seguridad económica y también a nuestra identidad 💙\n\nEs completamente normal sentirse así cuando el trabajo no va bien. ¿Qué es exactamente lo que está pasando? ¿Es la carga de trabajo, las relaciones con compañeros, o algo relacionado con la estabilidad del empleo?",
        "tecnicas": ["respiracion", "ejercicio"],
        "emocion": "ansiedad",
        "intensidad": 6
    },
    "dormir": {
        "palabras": [
            "no duermo", "duermo mal", "insomnio", "me desvelo", "pesadillas",
            "no puedo dormir", "me cuesta dormir", "me despierto de noche",
            "sueño interrumpido", "cansancio al despertar"
        ],
        "respuesta": "Los problemas de sueño afectan a todo: el estado de ánimo, la concentración, la energía y la salud física 😴 Es uno de los problemas más comunes y también de los que más impacto tienen.\n\nAlgunas preguntas: ¿Tienes dificultad para conciliar el sueño, para mantenerlo, o te despiertas muy temprano? Cada patrón tiene estrategias diferentes.\n\nMientras tanto, te sugiero la técnica de sonidos relajantes y la respiración guiada antes de dormir.",
        "tecnicas": ["sonidos", "respiracion", "infusiones"],
        "emocion": "cansancio",
        "intensidad": 5
    },
    "pareja": {
        "palabras": [
            "pareja", "novio", "novia", "marido", "mujer", "esposo", "esposa",
            "ruptura", "separación", "divorcio", "infidelidad", "me han dejado",
            "he dejado", "relación tóxica", "discusión de pareja"
        ],
        "respuesta": "Las rupturas y los problemas de pareja están entre las experiencias más dolorosas que existen 💙 Cuando una relación importante no va bien, afecta a todo lo demás.\n\n¿Estás pasando por una ruptura reciente o es una relación que está en dificultades? El tipo de apoyo que necesitas es diferente en cada caso.",
        "tecnicas": ["diario", "ejercicio", "sonidos"],
        "emocion": "tristeza",
        "intensidad": 7
    },
    "familia": {
        "palabras": [
            "familia", "padres", "madre", "padre", "hermano", "hermana",
            "hijos", "conflicto familiar", "problemas en casa", "discusión familiar",
            "me llevo mal con", "relación difícil con"
        ],
        "respuesta": "Los conflictos familiares son especialmente difíciles porque no podemos simplemente alejarnos de ellos como haríamos con otras personas 💙\n\nCada familia tiene su propia dinámica compleja. ¿Quieres contarme más sobre lo que está pasando? A veces ponerlo en palabras ya ayuda a verlo con más claridad.",
        "tecnicas": ["diario", "respiracion"],
        "emocion": "ansiedad",
        "intensidad": 6
    },
}

FRASES_MOTIVACIONALES = [
    "El progreso, no la perfección, es lo que importa. 🌱",
    "Cuidarte no es egoísta, es necesario. 💚",
    "Cada día que eliges seguir adelante es una victoria. 🏆",
    "No tienes que tenerlo todo resuelto. Solo da el siguiente paso. 👣",
    "Tu bienestar emocional es tan importante como tu salud física. 💙",
    "La vulnerabilidad no es debilidad. Es coraje. 💪",
    "Mereces el mismo cuidado que das a los demás. 🌸",
    "Los momentos difíciles no duran. Las personas fuertes, sí. ⭐",
    "Pedir ayuda es un signo de inteligencia emocional. 🧠",
    "Hoy es un buen día para ser amable contigo mismo/a. 🌟",
    "No eres tus pensamientos. Eres quien los observa. 🌊",
    "Pequeños pasos cada día llevan a grandes cambios. 🚶",
    "Tienes más fuerza de la que crees. 💫",
    "Este momento difícil también pasará. 🌈",
    "Eres suficiente, exactamente como eres. 💚",
]

CONSEJOS_GENERALES = [
    "💧 Bebe agua ahora mismo. La deshidratación leve afecta directamente al estado de ánimo.",
    "🌬️ Prueba la respiración 4-7-8: inspira 4s, mantén 7s, expira 8s. Repite 3 veces.",
    "🚶 Date un paseo de 10 minutos al aire libre. La luz natural y el movimiento liberan serotonina.",
    "📵 Desconéctate del móvil 60 minutos antes de dormir. La luz azul inhibe la melatonina.",
    "✍️ Escribe 3 cosas por las que estás agradecido/a hoy, por pequeñas que sean.",
    "🍵 Prepárate una infusión caliente. El ritual en sí ya tiene efecto calmante.",
    "🎵 Pon música que te guste y no hagas nada más mientras la escuchas.",
    "🌿 Sal al exterior aunque sea 5 minutos. La naturaleza reduce el cortisol.",
    "😴 Intenta acostarte y levantarte a la misma hora cada día, incluso los fines de semana.",
    "🤗 Envía un mensaje a alguien que quieras. La conexión social es una necesidad básica.",
    "🧘 Haz 5 minutos de stretching suave. La tensión emocional se acumula en el cuerpo.",
    "🍎 Come algo nutritivo hoy. El intestino y el cerebro están directamente conectados.",
    "📖 Lee algo que te guste, aunque sean solo 10 páginas.",
    "💆 Date permiso para no ser productivo/a hoy. El descanso también es trabajo.",
    "🌙 Si no puedes dormir, no te quedes en la cama frustrado/a. Levántate y haz algo tranquilo.",
]

NOMBRES_TECNICAS = {
    "respiracion":   "🫁 Respiración guiada",
    "sonidos":       "🎵 Sonidos relajantes",
    "diario":        "📓 Diario de gratitud",
    "infusiones":    "🍵 Infusiones",
    "ejercicio":     "🏃 Ejercicio suave",
    "meditacion":    "🧘 Meditación guiada",
    "yoga":          "🤸 Yoga suave",
    "tapping":       "👆 EFT Tapping",
    "visualizacion": "🌈 Visualización guiada",
    "journaling":    "📝 Journaling libre",
    "grounding":     "🌍 Grounding 5-4-3-2-1",
}

RUTAS_TECNICAS = {
    "respiracion":   "/respiracion",
    "sonidos":       "/sonidos",
    "diario":        "/diario",
    "infusiones":    "/infusiones",
    "ejercicio":     "/ejercicio",
    "meditacion":    "/meditacion",
    "yoga":          "/yoga",
    "tapping":       "/tapping",
    "visualizacion": "/visualizacion",
    "journaling":    "/journaling",
    "grounding":     "/tecnica-5-4-3-2-1",
}