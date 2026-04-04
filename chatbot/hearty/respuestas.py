RESPUESTAS = {
    # ── Según emoción inicial ──
    "😊 Bien": {
        "respuesta": "¡Me alegra mucho escucharlo! 😊 Mantener ese estado positivo es muy importante. ¿Quieres explorar alguna técnica para seguir cuidando tu bienestar?",
        "tecnicas": ["diario", "ejercicio"],
        "recursos": ["gratitud", "bienestar"]
    },
    "😌 Tranquilo/a": {
        "respuesta": "Qué bien que te sientas tranquilo/a 😌 Es un estado valioso. Podemos aprovecharlo para practicar algo de mindfulness.",
        "tecnicas": ["respiracion", "sonidos"],
        "recursos": ["meditacion", "mindfulness"]
    },
    "😰 Ansioso/a": {
        "respuesta": "Entiendo que la ansiedad puede ser agotadora 💙 No estás solo/a en esto. Tengo algunas técnicas que pueden ayudarte a calmarte ahora mismo.",
        "tecnicas": ["respiracion", "sonidos"],
        "recursos": ["ansiedad", "tecnicas_calma"]
    },
    "😢 Triste": {
        "respuesta": "Siento mucho que estés pasando por un momento difícil 💙 La tristeza es válida y merece ser escuchada. Estoy aquí contigo.",
        "tecnicas": ["diario", "ejercicio"],
        "recursos": ["depresion", "autocuidado"]
    },
    "😠 Enfadado/a": {
        "respuesta": "El enfado es una emoción completamente válida 💪 Lo importante es encontrar una salida saludable. Vamos a trabajar en eso juntos.",
        "tecnicas": ["ejercicio", "respiracion"],
        "recursos": ["gestion_emocional", "tecnicas_calma"]
    },
    "😴 Cansado/a": {
        "respuesta": "El cansancio acumulado afecta mucho a nuestro bienestar 😴 Tu cuerpo y mente necesitan descanso. Vamos a ver qué puede ayudarte.",
        "tecnicas": ["sonidos", "infusiones"],
        "recursos": ["sueno", "autocuidado"]
    },

    # ── Según duración ──
    "Hoy solamente": {
        "respuesta": "Que sea algo puntual de hoy es una buena señal. A veces el día a día nos pasa factura. Vamos a ver cómo recuperarte."
    },
    "Unos días": {
        "respuesta": "Llevar unos días así puede ser agotador. Es importante que te cuides ahora antes de que se acumule más."
    },
    "Varias semanas": {
        "respuesta": "Varias semanas es bastante tiempo cargando con eso. Me alegra que estés aquí buscando apoyo. Vamos paso a paso."
    },
    "Hace mucho tiempo": {
        "respuesta": "Llevar mucho tiempo sintiéndote así requiere atención especial. Además de lo que trabajemos juntos, considera hablar con un profesional si no lo has hecho. 💙"
    },

    # ── Según causa ──
    "El trabajo o estudios": {
        "respuesta": "El estrés académico y laboral es muy común hoy en día. La clave está en establecer límites y momentos de desconexión."
    },
    "Mis relaciones personales": {
        "respuesta": "Las relaciones son parte fundamental de nuestro bienestar. Es normal que nos afecten profundamente cuando no van bien."
    },
    "Mi salud": {
        "respuesta": "La salud física y mental están muy conectadas. Cuidar una ayuda a la otra. Vamos a ver qué podemos hacer."
    },
    "No lo sé": {
        "respuesta": "No siempre tenemos que tener una respuesta clara. A veces simplemente nos sentimos mal y está bien no saber por qué."
    },
    "Varias cosas a la vez": {
        "respuesta": "Cuando todo se acumula puede ser abrumador. Lo importante es ir de una en una, sin presión."
    },

    # ── Según tipo de apoyo ──
    "Técnicas para calmarme": {
        "respuesta": "Perfecto, tengo varias técnicas que pueden ayudarte ahora mismo 🌿",
        "tecnicas": ["respiracion", "sonidos", "infusiones"],
        "accion": "mostrar_tecnicas"
    },
    "Recursos informativos": {
        "respuesta": "Aquí tienes algunos recursos que pueden orientarte 📚",
        "accion": "mostrar_recursos"
    },
    "Solo quiero desahogarme": {
        "respuesta": "Estoy aquí para escucharte 💙 Cuéntame lo que necesites, sin juicios. Aquí es un espacio seguro."
    },
    "Consejos prácticos": {
        "respuesta": "Te doy algunos consejos concretos que puedes aplicar hoy mismo 💡",
        "tecnicas": ["ejercicio", "diario"],
        "accion": "mostrar_consejos"
    },
}

CONSEJOS_GENERALES = [
    "💧 Bebe agua. La deshidratación afecta directamente al estado de ánimo.",
    "🌬️ Practica la respiración 4-4-6: inspira 4 segundos, mantén 4, espira 6.",
    "🚶 Date un paseo corto de 10 minutos. El movimiento libera endorfinas.",
    "📵 Desconéctate del móvil al menos 30 minutos antes de dormir.",
    "✍️ Escribe 3 cosas por las que estar agradecido/a hoy.",
    "🍵 Prepárate una infusión caliente de manzanilla o tila.",
    "🎵 Pon música que te guste y simplemente escúchala sin hacer nada más.",
]

NOMBRES_TECNICAS = {
    "respiracion": "🫁 Respiración guiada",
    "sonidos": "🎵 Sonidos relajantes",
    "diario": "📓 Diario de gratitud",
    "infusiones": "🍵 Infusiones",
    "ejercicio": "🏃 Ejercicio suave",
}

RUTAS_TECNICAS = {
    "respiracion": "/respiracion",
    "sonidos": "/sonidos",
    "diario": "/diario",
    "infusiones": "/infusiones",
    "ejercicio": "/ejercicio",
}